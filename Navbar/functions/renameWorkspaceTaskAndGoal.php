<?php
/**
 * Rename functions for workspaces, tasks, and goals
 * Handles updating names/titles of workspace entities
 */

/**
 * change the name of a workspace
 * only managers can do this - regular members can't
 */
function renameWorkspace($userID, $workspaceID, $newName) {
    global $conn;
    
    // Debug logging to track function calls
    error_log("WORKSPACE RENAME FUNCTION CALLED - WorkspaceID: $workspaceID, NewName: '$newName', Time: " . date('Y-m-d H:i:s'));
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    // check if this user is actually a manager of this workspace AND get original name
    $checkManager = "SELECT w.Name as OriginalName FROM workspace w INNER JOIN workspacemember wm ON w.WorkSpaceID = wm.WorkSpaceID WHERE w.WorkSpaceID = ? AND wm.UserID = ? AND wm.UserRole = 'Manager'";
    $stmt = mysqli_prepare($conn, $checkManager);
    mysqli_stmt_bind_param($stmt, "ii", $workspaceID, $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        return ['success' => false, 'message' => 'Only managers can rename workspace'];
    }

    // Get original name from the result
    $workspaceData = mysqli_fetch_assoc($result);
    $originalName = $workspaceData['OriginalName'] ?? '(unknown name)';
    mysqli_stmt_close($stmt);
    
    // Update the workspace name
    $updateWorkspace = "UPDATE workspace SET Name = ? WHERE WorkSpaceID = ?";
    $stmt = mysqli_prepare($conn, $updateWorkspace);
    mysqli_stmt_bind_param($stmt, "si", $newName, $workspaceID);
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        // Create notification for workspace rename
        try {
            // Prepare notification data
            $relatedID = $workspaceID;
            $relatedTable = "workspace";
            $title = "Workspace renamed";
            $desc = "The workspace '{$originalName}' has been renamed to '{$newName}'.";
            
            // Get all workspace members to notify them
            $membersQuery = mysqli_prepare($conn, "SELECT UserID FROM workspacemember WHERE WorkSpaceID = ?");
            mysqli_stmt_bind_param($membersQuery, 'i', $workspaceID);
            mysqli_stmt_execute($membersQuery);
            $membersResult = mysqli_stmt_get_result($membersQuery);
            
            // Check if notification already exists to prevent duplicates
            $checkNoti = mysqli_prepare($conn, "SELECT NotificationID FROM notification WHERE RelatedID = ? AND RelatedTable = ? AND Title = ? AND Description = ? AND CreatedAt > DATE_SUB(NOW(), INTERVAL 5 SECOND)");
            mysqli_stmt_bind_param($checkNoti, "isss", $relatedID, $relatedTable, $title, $desc);
            mysqli_stmt_execute($checkNoti);
            $checkResult = mysqli_stmt_get_result($checkNoti);
            
            if (mysqli_num_rows($checkResult) == 0) {
                // No duplicate found, insert notification
                $insertNoti = mysqli_prepare($conn, "INSERT INTO notification (RelatedID, RelatedTable, Title, Description) VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($insertNoti, "isss", $relatedID, $relatedTable, $title, $desc);
                mysqli_stmt_execute($insertNoti);
                $notiID = mysqli_insert_id($conn);
                mysqli_stmt_close($insertNoti);
                
                // Debug logging for successful insertion
                error_log("NOTIFICATION INSERTED - WorkspaceID: $workspaceID, NotificationID: $notiID");
                
                // Insert receivers for all workspace members
                $insertReceiver = mysqli_prepare($conn, "INSERT INTO receiver (NotificationID, UserID) VALUES (?, ?)");
                
                while ($member = mysqli_fetch_assoc($membersResult)) {
                    mysqli_stmt_bind_param($insertReceiver, "ii", $notiID, $member['UserID']);
                    mysqli_stmt_execute($insertReceiver);
                }
                mysqli_stmt_close($insertReceiver);
            } else {
                // Duplicate found, skip insertion
                error_log("DUPLICATE NOTIFICATION PREVENTED - WorkspaceID: $workspaceID");
            }
            mysqli_stmt_close($checkNoti);
            mysqli_stmt_close($membersQuery);
            
        } catch (Exception $e) {
            // Notification creation failed, but rename was successful
            error_log("Failed to create notification for workspace rename: " . $e->getMessage());
        }
        
        return ['success' => true];
    }
    
    return ['success' => false, 'message' => 'Failed to rename workspace'];
}

/**
 * change the name/title of a task
 * user just needs access to the task to do this
 */
function renameTask($userID, $taskID, $newName) {
    global $conn;
    
    // Generate unique call ID for tracking
    $callId = uniqid('task_rename_', true);
    
    // Debug logging to track function calls
    error_log("RENAME FUNCTION CALLED [$callId] - TaskID: $taskID, NewName: '$newName', Time: " . date('Y-m-d H:i:s'));
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    // make sure user has access to this task AND get original name
    $checkAccess = "SELECT t.Title as OriginalName FROM task t INNER JOIN taskaccess ta ON t.TaskID = ta.TaskID WHERE t.TaskID = ? AND ta.UserID = ?";
    $stmt = mysqli_prepare($conn, $checkAccess);
    mysqli_stmt_bind_param($stmt, "ii", $taskID, $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        return ['success' => false, 'message' => 'No access to task'];
    }

    // Get original name from the result
    $taskData = mysqli_fetch_assoc($result);
    $originalName = $taskData['OriginalName'] ?? '(unknown title)';
    mysqli_stmt_close($stmt);
    
    // Debug logging
    error_log("RENAME DEBUG [$callId] - TaskID: $taskID, OriginalName: '$originalName', NewName: '$newName'");

    // Add database-level lock to prevent concurrent renames of the same task
    $lockQuery = "SELECT GET_LOCK(CONCAT('task_rename_', ?), 5) as lock_result";
    $lockStmt = mysqli_prepare($conn, $lockQuery);
    mysqli_stmt_bind_param($lockStmt, 'i', $taskID);
    mysqli_stmt_execute($lockStmt);
    $lockResult = mysqli_stmt_get_result($lockStmt);
    $lockData = mysqli_fetch_assoc($lockResult);
    mysqli_stmt_close($lockStmt);
    
    if (!$lockData || $lockData['lock_result'] != 1) {
        error_log("COULD NOT ACQUIRE LOCK [$callId] - TaskID: $taskID");
        return ['success' => false, 'message' => 'Task is being renamed by another process'];
    }
    
    // update the task title
    $updateTask = "UPDATE task SET Title = ? WHERE TaskID = ?";
    $stmt = mysqli_prepare($conn, $updateTask);
    mysqli_stmt_bind_param($stmt, "si", $newName, $taskID);
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        // Create notification for task rename
        try {
            // Get workspace info
            $workspaceQuery = mysqli_prepare($conn, "
                SELECT t.WorkSpaceID, w.Name AS WorkspaceName
                FROM task t 
                JOIN workspace w ON t.WorkSpaceID = w.WorkSpaceID 
                WHERE t.TaskID = ?
            ");
            mysqli_stmt_bind_param($workspaceQuery, 'i', $taskID);
            mysqli_stmt_execute($workspaceQuery);
            $workspaceResult = mysqli_stmt_get_result($workspaceQuery);
            $workspaceData = mysqli_fetch_assoc($workspaceResult);
            $workspaceName = $workspaceData['WorkspaceName'] ?? 'Unknown Workspace';
            
            // Prepare notification data
            $relatedID = $taskID;
            $relatedTable = "task";
            $title = "Task renamed";
            $desc = "The task '{$originalName}' has been renamed to '{$newName}' in workspace '{$workspaceName}'.";
            
            // Debug logging for notification
            error_log("NOTIFICATION DEBUG [$callId] - TaskID: $taskID, OriginalName: '$originalName', NewName: '$newName', Description: '$desc'");
            
            // Get all task members to notify them
            $membersQuery = mysqli_prepare($conn, "SELECT UserID FROM taskaccess WHERE TaskID = ?");
            mysqli_stmt_bind_param($membersQuery, 'i', $taskID);
            mysqli_stmt_execute($membersQuery);
            $membersResult = mysqli_stmt_get_result($membersQuery);
            
            // Check if notification already exists to prevent duplicates
            // Check for any task rename notification for this task in the last 10 seconds
            $checkNoti = mysqli_prepare($conn, "SELECT NotificationID FROM notification WHERE RelatedID = ? AND RelatedTable = ? AND Title = ? AND CreatedAt > DATE_SUB(NOW(), INTERVAL 10 SECOND)");
            mysqli_stmt_bind_param($checkNoti, "iss", $relatedID, $relatedTable, $title);
            mysqli_stmt_execute($checkNoti);
            $checkResult = mysqli_stmt_get_result($checkNoti);
            
            if (mysqli_num_rows($checkResult) == 0) {
                // No duplicate found, insert notification
                $insertNoti = mysqli_prepare($conn, "INSERT INTO notification (RelatedID, RelatedTable, Title, Description) VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($insertNoti, "isss", $relatedID, $relatedTable, $title, $desc);
                mysqli_stmt_execute($insertNoti);
                $notiID = mysqli_insert_id($conn);
                mysqli_stmt_close($insertNoti);
                
                // Debug logging for successful insertion
                error_log("NOTIFICATION INSERTED [$callId] - TaskID: $taskID, NotificationID: $notiID");
                
                // Insert receivers for all task members
                $insertReceiver = mysqli_prepare($conn, "INSERT INTO receiver (NotificationID, UserID) VALUES (?, ?)");
                
                while ($member = mysqli_fetch_assoc($membersResult)) {
                    mysqli_stmt_bind_param($insertReceiver, "ii", $notiID, $member['UserID']);
                    mysqli_stmt_execute($insertReceiver);
                }
                mysqli_stmt_close($insertReceiver);
            } else {
                // Duplicate found, skip insertion
                error_log("DUPLICATE NOTIFICATION PREVENTED [$callId] - TaskID: $taskID");
            }
            mysqli_stmt_close($checkNoti);
            mysqli_stmt_close($membersQuery);
            mysqli_stmt_close($workspaceQuery);
            
        } catch (Exception $e) {
            // Notification creation failed, but rename was successful
            error_log("Failed to create notification for task rename: " . $e->getMessage());
        }
        
        // Release the database lock
        $unlockQuery = "SELECT RELEASE_LOCK(CONCAT('task_rename_', ?))";
        $unlockStmt = mysqli_prepare($conn, $unlockQuery);
        mysqli_stmt_bind_param($unlockStmt, 'i', $taskID);
        mysqli_stmt_execute($unlockStmt);
        mysqli_stmt_close($unlockStmt);
        
        return ['success' => true];
    }
    
    // Release the database lock even if rename failed
    $unlockQuery = "SELECT RELEASE_LOCK(CONCAT('task_rename_', ?))";
    $unlockStmt = mysqli_prepare($conn, $unlockQuery);
    mysqli_stmt_bind_param($unlockStmt, 'i', $taskID);
    mysqli_stmt_execute($unlockStmt);
    mysqli_stmt_close($unlockStmt);
    
    return ['success' => false, 'message' => 'Failed to rename task'];
}


?>

