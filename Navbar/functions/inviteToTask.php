<?php
/**
 * Invite user to task function
 * Grants task access to a user
 */

/**
 * Invite a user to collaborate on a task
 */
function inviteToTask($managerUserID, $taskID, $invitedUserID, $workspaceID) {
    global $conn;
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    // Check if manager has access to the workspace
    $checkManagerAccess = "SELECT UserRole FROM workspacemember WHERE WorkSpaceID = ? AND UserID = ?";
    $stmt = mysqli_prepare($conn, $checkManagerAccess);
    mysqli_stmt_bind_param($stmt, "ii", $workspaceID, $managerUserID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        return ['success' => false, 'message' => 'Manager has no access to workspace'];
    }
    
    $managerRow = mysqli_fetch_assoc($result);
    if ($managerRow['UserRole'] !== 'Manager') {
        return ['success' => false, 'message' => 'Only managers can grant task access'];
    }
    
    // Check if the task exists and belongs to the workspace
    $checkTask = "SELECT TaskID FROM task WHERE TaskID = ? AND WorkSpaceID = ?";
    $stmt = mysqli_prepare($conn, $checkTask);
    mysqli_stmt_bind_param($stmt, "ii", $taskID, $workspaceID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        return ['success' => false, 'message' => 'Task not found in workspace'];
    }
    
    // Check if invited user is a member of the workspace
    $checkInvitedUser = "SELECT UserID FROM workspacemember WHERE WorkSpaceID = ? AND UserID = ?";
    $stmt = mysqli_prepare($conn, $checkInvitedUser);
    mysqli_stmt_bind_param($stmt, "ii", $workspaceID, $invitedUserID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        return ['success' => false, 'message' => 'User is not a member of this workspace'];
    }
    
    // Check if user already has access to this task
    $checkExistingAccess = "SELECT UserID FROM taskaccess WHERE TaskID = ? AND UserID = ?";
    $stmt = mysqli_prepare($conn, $checkExistingAccess);
    mysqli_stmt_bind_param($stmt, "ii", $taskID, $invitedUserID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        return ['success' => false, 'message' => 'User already has access to this task'];
    }
    
    // Grant task access
    $grantAccess = "INSERT INTO taskaccess (UserID, TaskID) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $grantAccess);
    mysqli_stmt_bind_param($stmt, "ii", $invitedUserID, $taskID);
    
    if (mysqli_stmt_execute($stmt)) {
        // Create notification for task invitation
        try {
            // Get workspace name and task name
            $workspaceQuery = mysqli_prepare($conn, "SELECT w.Name as WorkspaceName, t.Title as TaskName FROM workspace w JOIN task t ON w.WorkSpaceID = t.WorkSpaceID WHERE w.WorkSpaceID = ? AND t.TaskID = ?");
            mysqli_stmt_bind_param($workspaceQuery, 'ii', $workspaceID, $taskID);
            mysqli_stmt_execute($workspaceQuery);
            $workspaceResult = mysqli_stmt_get_result($workspaceQuery);
            $workspaceData = mysqli_fetch_assoc($workspaceResult);
            $workspaceName = $workspaceData['WorkspaceName'] ?? 'Unknown Workspace';
            $taskName = $workspaceData['TaskName'] ?? 'Unknown Task';
            
            // Prepare notification data
            $relatedID = $taskID;
            $relatedTable = "task";
            $title = "Granted Employee Access";
            $desc = "You have been granted employee access in a workspace: " . $workspaceName;
            
            // Insert notification
            $insertNoti = mysqli_prepare($conn, "INSERT INTO notification (RelatedID, RelatedTable, Title, Description) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($insertNoti, "isss", $relatedID, $relatedTable, $title, $desc);
            mysqli_stmt_execute($insertNoti);
            
            // Insert receiver
            $receiver = $invitedUserID;
            $notiID = mysqli_insert_id($conn);
            $insertReceiver = mysqli_prepare($conn, "INSERT INTO receiver (NotificationID, UserID) VALUES (?, ?)");
            mysqli_stmt_bind_param($insertReceiver, "ii", $notiID, $receiver);
            mysqli_stmt_execute($insertReceiver);
            
        } catch (Exception $e) {
            // Notification creation failed, but invitation was successful
            error_log("Failed to create notification for task invitation: " . $e->getMessage());
        }
        
        return [
            'success' => true,
            'message' => 'Task access granted successfully',
            'taskID' => $taskID,
            'userID' => $invitedUserID
        ];
    }
    
    return ['success' => false, 'message' => 'Failed to grant task access'];
}
?>
