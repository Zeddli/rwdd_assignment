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

    // Get original name before renaming
    $originalQuery = mysqli_prepare($conn, "SELECT Name FROM workspace WHERE WorkSpaceID = ?");
    mysqli_stmt_bind_param($originalQuery, "i", $workspaceID);
    mysqli_stmt_execute($originalQuery);
    $originalResult = mysqli_stmt_get_result($originalQuery);
    $originalName = mysqli_fetch_assoc($originalResult)['Name'] ?? '(unknown name)';
    
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
            
            // Insert notification
            $insertNoti = mysqli_prepare($conn, "INSERT INTO notification (RelatedID, RelatedTable, Title, Description) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($insertNoti, "isss", $relatedID, $relatedTable, $title, $desc);
            mysqli_stmt_execute($insertNoti);
            $notiID = mysqli_insert_id($conn);
            mysqli_stmt_close($insertNoti);
            
            // Insert receivers for all workspace members
            $insertReceiver = mysqli_prepare($conn, "INSERT INTO receiver (NotificationID, UserID) VALUES (?, ?)");
            
            while ($member = mysqli_fetch_assoc($membersResult)) {
                mysqli_stmt_bind_param($insertReceiver, "ii", $notiID, $member['UserID']);
                mysqli_stmt_execute($insertReceiver);
            }
            mysqli_stmt_close($insertReceiver);
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

    // Get original name
    $originalQuery = mysqli_prepare($conn, "SELECT Title FROM task WHERE TaskID = ?");
    mysqli_stmt_bind_param($originalQuery, "i", $taskID);
    mysqli_stmt_execute($originalQuery);
    $originalResult = mysqli_stmt_get_result($originalQuery);
    $originalName = mysqli_fetch_assoc($originalResult)['Title'] ?? '(unknown title)';

    
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
            
            // Get all task members to notify them
            $membersQuery = mysqli_prepare($conn, "SELECT UserID FROM taskaccess WHERE TaskID = ?");
            mysqli_stmt_bind_param($membersQuery, 'i', $taskID);
            mysqli_stmt_execute($membersQuery);
            $membersResult = mysqli_stmt_get_result($membersQuery);
            
            // Insert notification
            $insertNoti = mysqli_prepare($conn, "INSERT INTO notification (RelatedID, RelatedTable, Title, Description) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($insertNoti, "isss", $relatedID, $relatedTable, $title, $desc);
            mysqli_stmt_execute($insertNoti);
            $notiID = mysqli_insert_id($conn);
            mysqli_stmt_close($insertNoti);
            
            // Insert receivers for all task members
            $insertReceiver = mysqli_prepare($conn, "INSERT INTO receiver (NotificationID, UserID) VALUES (?, ?)");
            
            while ($member = mysqli_fetch_assoc($membersResult)) {
                mysqli_stmt_bind_param($insertReceiver, "ii", $notiID, $member['UserID']);
                mysqli_stmt_execute($insertReceiver);
            }
            mysqli_stmt_close($insertReceiver);
            mysqli_stmt_close($membersQuery);
            mysqli_stmt_close($workspaceQuery);
            
        } catch (Exception $e) {
            // Notification creation failed, but rename was successful
            error_log("Failed to create notification for task rename: " . $e->getMessage());
        }
        
        return ['success' => true];
    }
    
    return ['success' => false, 'message' => 'Failed to rename task'];
}

/**
 * rename goal
 * only workspace managers can rename goals
 */
function renameGoal($userID, $goalID, $newName) {
    global $conn;
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    // check if user is manager of the workspace that owns this goal
    $checkManager = "
        SELECT 1 FROM goal g 
        INNER JOIN workspacemember wm ON g.WorkSpaceID = wm.WorkSpaceID 
        WHERE g.GoalID = ? AND wm.UserID = ? AND wm.UserRole = 'Manager'
    ";
    $stmt = mysqli_prepare($conn, $checkManager);
    mysqli_stmt_bind_param($stmt, "ii", $goalID, $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    
    if (!$data) {
        return ['success' => false, 'message' => 'Only managers can rename goals'];
    }

    $workspaceID = $data['WorkSpaceID'];
    $originalName = $data['OriginalName'] ?? '(unknown goal)';
    
    // update goal description
    $updateGoal = "UPDATE goal SET Description = ? WHERE GoalID = ?";
    $stmt = mysqli_prepare($conn, $updateGoal);
    mysqli_stmt_bind_param($stmt, "si", $newName, $goalID);
    
    if (mysqli_stmt_execute($stmt)) {
        // Create notification for goal rename
        try {
            $relatedID = $goalID;
            $relatedTable = "goal";
            $title = "Goal renamed";
            $desc = "The goal '{$originalName}' has been renamed to '{$newName}'.";

            // Notify all workspace members
            $membersQuery = mysqli_prepare($conn, "SELECT UserID FROM workspacemember WHERE WorkSpaceID = ?");
            mysqli_stmt_bind_param($membersQuery, 'i', $workspaceID);
            mysqli_stmt_execute($membersQuery);
            $membersResult = mysqli_stmt_get_result($membersQuery);

            $insertNoti = mysqli_prepare($conn, "INSERT INTO notification (RelatedID, RelatedTable, Title, Description) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($insertNoti, "isss", $relatedID, $relatedTable, $title, $desc);
            mysqli_stmt_execute($insertNoti);
            $notiID = mysqli_insert_id($conn);

            $insertReceiver = mysqli_prepare($conn, "INSERT INTO receiver (NotificationID, UserID) VALUES (?, ?)");
            while ($member = mysqli_fetch_assoc($membersResult)) {
                mysqli_stmt_bind_param($insertReceiver, "ii", $notiID, $member['UserID']);
                mysqli_stmt_execute($insertReceiver);
            }
        } catch (Exception $e) {
            error_log("Failed to create notification for goal rename: " . $e->getMessage());
        }

        return ['success' => true, 'message' => 'Goal renamed successfully'];
    }
    
    return ['success' => false, 'message' => 'Failed to rename goal'];
}
?>

