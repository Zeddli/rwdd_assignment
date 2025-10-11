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
