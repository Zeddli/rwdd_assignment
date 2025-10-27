<?php
/**
 * Delete functions for workspaces and tasks
 * Handles removal of workspaces, tasks, and related data
 */

/**
 * delete an entire workspace and everything in it
 * only managers can do this - be careful, this removes EVERYTHING!
 */
function deleteWorkspace($userID, $workspaceID) {
    global $conn;
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    // double-check that user is a manager before letting them delete everything
    $checkManager = "SELECT 1 FROM workspacemember WHERE WorkSpaceID = ? AND UserID = ? AND UserRole = 'Manager'";
    $stmt = mysqli_prepare($conn, $checkManager);
    mysqli_stmt_bind_param($stmt, "ii", $workspaceID, $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        return ['success' => false, 'message' => 'Only managers can delete workspace'];
    }
    
    // delete the workspace - database will cascade delete everything else
    $deleteWorkspace = "DELETE FROM workspace WHERE WorkSpaceID = ?";
    $stmt = mysqli_prepare($conn, $deleteWorkspace);
    mysqli_stmt_bind_param($stmt, "i", $workspaceID);
    
    if (mysqli_stmt_execute($stmt)) {
        return ['success' => true];
    }
    
    return ['success' => false, 'message' => 'Failed to delete workspace'];
}

/**
 * delete a single task - REMOVED
 * Delete functionality has been removed and will be reimplemented
 */
function deleteTask($userID, $taskID) {
    // Function disabled - delete functionality removed
    return ['success' => false, 'message' => 'Delete functionality not implemented'];
}

/**
 * Delete task from database with proper cleanup
 * Removes task and all related data (comments, files, access permissions)
 */
function deleteTaskFromDB($conn, $userID, $taskID) {
    if ($taskID <= 0) {
        return ['success' => false, 'message' => 'Invalid task ID'];
    }
    
    // Check if user has permission to delete task
    $checkSQL = "
        SELECT t.TaskID, t.Title, t.WorkSpaceID, w.Name as WorkspaceName
        FROM task t
        JOIN workspace w ON t.WorkSpaceID = w.WorkSpaceID
        LEFT JOIN workspacemember wm ON w.WorkSpaceID = wm.WorkSpaceID AND wm.UserID = ?
        LEFT JOIN taskaccess ta ON t.TaskID = ta.TaskID AND ta.UserID = ?
        WHERE t.TaskID = ? 
        AND (w.UserID = ? OR wm.UserRole = 'Manager' OR ta.UserID IS NOT NULL)
    ";
    
    $stmt = mysqli_prepare($conn, $checkSQL);
    mysqli_stmt_bind_param($stmt, "iiii", $userID, $userID, $taskID, $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) === 0) {
        mysqli_stmt_close($stmt);
        return ['success' => false, 'message' => 'Task not found or no permission'];
    }
    
    $taskData = mysqli_fetch_assoc($result);
    $taskTitle = $taskData['Title'] ?? 'Unknown Task';
    $workspaceID = $taskData['WorkSpaceID'];
    $workspaceName = $taskData['WorkspaceName'] ?? 'Unknown Workspace';
    mysqli_stmt_close($stmt);
    
    // Create notification for task deletion before deleting
    try {
        // Prepare notification data
        $relatedID = $taskID;
        $relatedTable = "task";
        $title = "Task deleted";
        $desc = "The task: ". $taskTitle . " has been deleted from workspace '$workspaceName'.";
        
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
        
    } catch (Exception $e) {
        // Notification creation failed, but deletion will continue
        error_log("Failed to create notification for task deletion: " . $e->getMessage());
    }
    
    // Delete related records in correct order (respecting foreign key constraints)
    // NOTE: We don't delete task notifications to preserve history (create/update notifications)
    $deleteQueries = [
        "DELETE FROM comment WHERE TaskID = ?",
        "DELETE FROM fileshared WHERE TaskID = ?", 
        "DELETE FROM taskaccess WHERE TaskID = ?",
        "DELETE FROM task WHERE TaskID = ?"
    ];
    
    foreach ($deleteQueries as $query) {
        $stmt = mysqli_prepare($conn, $query);
        if (!$stmt) {
            error_log("Failed to prepare delete query: " . $query . " - " . mysqli_error($conn));
            return ['success' => false, 'message' => 'Database error during deletion'];
        }
        
        mysqli_stmt_bind_param($stmt, "i", $taskID);
        if (!mysqli_stmt_execute($stmt)) {
            error_log("Failed to execute delete query: " . $query . " - " . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return ['success' => false, 'message' => 'Database error during deletion'];
        }
        mysqli_stmt_close($stmt);
    }
    
    return ['success' => true, 'message' => 'Task deleted successfully'];
}

/**
 * Delete workspace from database with proper cleanup
 * Removes workspace and all related data (tasks, goals, members, notifications)
 */
function deleteWorkspaceFromDB($conn, $userID, $workspaceID) {
    if ($workspaceID <= 0) {
        return ['success' => false, 'message' => 'Invalid workspace ID'];
    }
    
    // Check if user has permission to delete workspace
    $checkSQL = "
        SELECT w.WorkSpaceID, w.Name 
        FROM workspace w
        LEFT JOIN workspacemember wm ON w.WorkSpaceID = wm.WorkSpaceID AND wm.UserID = ?
        WHERE w.WorkSpaceID = ? 
        AND (w.UserID = ? OR wm.UserRole = 'Manager')
    ";
    
    $stmt = mysqli_prepare($conn, $checkSQL);
    mysqli_stmt_bind_param($stmt, "iii", $userID, $workspaceID, $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) === 0) {
        mysqli_stmt_close($stmt);
        return ['success' => false, 'message' => 'Workspace not found or no permission'];
    }
    
    $workspaceData = mysqli_fetch_assoc($result);
    $workspaceName = $workspaceData['Name'] ?? 'Unknown Workspace';
    mysqli_stmt_close($stmt);
    
    // Create notification for workspace deletion before deleting
    try {
        // Prepare notification data
        $relatedID = $workspaceID;
        $relatedTable = "workspace";
        $title = "Workspace deleted";
        $desc = "The workspace: ". $workspaceName . " has been deleted.";
        
        // Get all workspace members to notify them
        $membersQuery = mysqli_prepare($conn, "SELECT UserID FROM workspacemember WHERE WorkSpaceID = ?");
        mysqli_stmt_bind_param($membersQuery, 'i', $workspaceID);
        mysqli_stmt_execute($membersQuery);
        $membersResult = mysqli_stmt_get_result($membersQuery);
        
        // Insert notification
        $insertNoti = mysqli_prepare($conn, "INSERT INTO notification (RelatedID, RelatedTable, Title, Description) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($insertNoti, "isss", $relatedID, $relatedTable, $title, $desc);
        mysqli_stmt_execute($insertNoti);
        
        // Insert receivers for all workspace members
        $notiID = mysqli_insert_id($conn);
        $insertReceiver = mysqli_prepare($conn, "INSERT INTO receiver (NotificationID, UserID) VALUES (?, ?)");
        
        while ($member = mysqli_fetch_assoc($membersResult)) {
            mysqli_stmt_bind_param($insertReceiver, "ii", $notiID, $member['UserID']);
            mysqli_stmt_execute($insertReceiver);
        }
        
    } catch (Exception $e) {
        // Notification creation failed, but deletion will continue
        error_log("Failed to create notification for workspace deletion: " . $e->getMessage());
    }
    
    // Get all task IDs in this workspace for cleanup
    $stmt = mysqli_prepare($conn, "SELECT TaskID FROM task WHERE WorkSpaceID = ?");
    mysqli_stmt_bind_param($stmt, "i", $workspaceID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $taskIDs = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $taskIDs[] = $row['TaskID'];
    }
    mysqli_stmt_close($stmt);
    
    // Delete task-related records first (if any tasks exist)
    // NOTE: We don't delete task/goal notifications to preserve history
    if (!empty($taskIDs)) {
        $taskDeleteQueries = [
            "DELETE FROM comment WHERE TaskID IN (" . implode(',', array_fill(0, count($taskIDs), '?')) . ")",
            "DELETE FROM fileshared WHERE TaskID IN (" . implode(',', array_fill(0, count($taskIDs), '?')) . ")",
            "DELETE FROM taskaccess WHERE TaskID IN (" . implode(',', array_fill(0, count($taskIDs), '?')) . ")",
            "DELETE FROM task WHERE TaskID IN (" . implode(',', array_fill(0, count($taskIDs), '?')) . ")"
        ];
        
        foreach ($taskDeleteQueries as $query) {
            $stmt = mysqli_prepare($conn, $query);
            if (!$stmt) {
                error_log("Failed to prepare task delete query: " . $query . " - " . mysqli_error($conn));
                return ['success' => false, 'message' => 'Database error during task cleanup'];
            }
            
            mysqli_stmt_bind_param($stmt, str_repeat('i', count($taskIDs)), ...$taskIDs);
            if (!mysqli_stmt_execute($stmt)) {
                error_log("Failed to execute task delete query: " . $query . " - " . mysqli_stmt_error($stmt));
                mysqli_stmt_close($stmt);
                return ['success' => false, 'message' => 'Database error during task cleanup'];
            }
            mysqli_stmt_close($stmt);
        }
    }
    
    // Delete workspace-related records
    // NOTE: We don't delete any notifications to preserve full history
    $workspaceDeleteQueries = [
        "DELETE FROM goal WHERE WorkSpaceID = ?",
        "DELETE FROM workspacemember WHERE WorkSpaceID = ?",
        "DELETE FROM workspace WHERE WorkSpaceID = ?"
    ];
    
    foreach ($workspaceDeleteQueries as $query) {
        $stmt = mysqli_prepare($conn, $query);
        if (!$stmt) {
            error_log("Failed to prepare workspace delete query: " . $query . " - " . mysqli_error($conn));
            return ['success' => false, 'message' => 'Database error during workspace deletion'];
        }
        
        mysqli_stmt_bind_param($stmt, "i", $workspaceID);
        if (!mysqli_stmt_execute($stmt)) {
            error_log("Failed to execute workspace delete query: " . $query . " - " . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return ['success' => false, 'message' => 'Database error during workspace deletion'];
        }
        mysqli_stmt_close($stmt);
    }
    
    return ['success' => true, 'message' => 'Workspace deleted successfully'];
}
?>

