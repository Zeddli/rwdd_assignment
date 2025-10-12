<?php
/**
 * Get workspace members function
 * Retrieves all members of a specific workspace
 */

/**
 * Get all members of a workspace
 */
function getWorkspaceMembers($userID, $workspaceID) {
    global $conn;
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    // Check if user has access to this workspace
    $checkAccess = "SELECT 1 FROM workspacemember WHERE WorkSpaceID = ? AND UserID = ?";
    $stmt = mysqli_prepare($conn, $checkAccess);
    mysqli_stmt_bind_param($stmt, "ii", $workspaceID, $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        return ['success' => false, 'message' => 'No access to workspace'];
    }
    
    // Get all members of the workspace with their user details
    $getMembers = "
        SELECT 
            wm.UserID,
            wm.UserRole,
            u.UserName,
            u.Email
        FROM workspacemember wm
        JOIN user u ON wm.UserID = u.UserID
        WHERE wm.WorkSpaceID = ?
        ORDER BY wm.UserRole DESC, u.UserName ASC
    ";
    
    $stmt = mysqli_prepare($conn, $getMembers);
    mysqli_stmt_bind_param($stmt, "i", $workspaceID);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $members = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $members[] = [
                'UserID' => $row['UserID'],
                'UserName' => $row['UserName'],
                'Email' => $row['Email'],
                'UserRole' => $row['UserRole'],
                'hasTaskAccess' => false // Default value, can be enhanced later
            ];
        }
        
        return [
            'success' => true,
            'members' => $members,
            'count' => count($members)
        ];
    }
    
    return ['success' => false, 'message' => 'Failed to get workspace members'];
}
?>
