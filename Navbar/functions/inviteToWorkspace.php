<?php
/**
 * Invite user to workspace function
 * Adds a user to a workspace as an employee
 */

/**
 * Invite a user to join a workspace
 */
function inviteToWorkspace($managerUserID, $workspaceID, $invitedUserID) {
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
        return ['success' => false, 'message' => 'Only managers can invite users to workspace'];
    }
    
    // Check if user is already a member of the workspace
    $checkExistingMember = "SELECT UserID FROM workspacemember WHERE WorkSpaceID = ? AND UserID = ?";
    $stmt = mysqli_prepare($conn, $checkExistingMember);
    mysqli_stmt_bind_param($stmt, "ii", $workspaceID, $invitedUserID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        return ['success' => false, 'message' => 'User is already a member of this workspace'];
    }
    
    // Add user to workspace as employee
    $addMember = "INSERT INTO workspacemember (WorkSpaceID, UserID, UserRole) VALUES (?, ?, 'Employee')";
    $stmt = mysqli_prepare($conn, $addMember);
    mysqli_stmt_bind_param($stmt, "ii", $workspaceID, $invitedUserID);
    
    if (mysqli_stmt_execute($stmt)) {
        // Create notification for workspace invitation
        try {
            // Get workspace name
            $workspaceQuery = mysqli_prepare($conn, "SELECT Name FROM workspace WHERE WorkSpaceID = ?");
            mysqli_stmt_bind_param($workspaceQuery, 'i', $workspaceID);
            mysqli_stmt_execute($workspaceQuery);
            $workspaceResult = mysqli_stmt_get_result($workspaceQuery);
            $workspaceData = mysqli_fetch_assoc($workspaceResult);
            $workspaceName = $workspaceData['Name'] ?? 'Unknown Workspace';
            
            // Prepare notification data
            $relatedID = $workspaceID;
            $relatedTable = "workspace";
            $title = "Added to workspace";
            $desc = "You have been added to a new workspace: " . $workspaceName;
            
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
            error_log("Failed to create notification for workspace invitation: " . $e->getMessage());
        }
        
        return [
            'success' => true,
            'message' => 'User invited to workspace successfully',
            'workspaceID' => $workspaceID,
            'userID' => $invitedUserID
        ];
    }
    
    return ['success' => false, 'message' => 'Failed to invite user to workspace'];
}
?>
