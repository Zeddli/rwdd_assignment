<?php
/**
 * Invite member functions
 * Handles searching for users and inviting them to workspaces and tasks
 */

/**
 * search for users by email
 * returns matching users with their basic info
 */
function searchUserByEmail($email) {
    global $conn;
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    if (empty(trim($email))) {
        return ['success' => false, 'message' => 'Email cannot be empty'];
    }
    
    // search for users whose email contains the search term
    $searchQuery = "
        SELECT UserID, Username, Email, PictureName 
        FROM user 
        WHERE Email LIKE ? 
        LIMIT 10
    ";
    
    $searchTerm = "%" . $email . "%";
    $stmt = mysqli_prepare($conn, $searchQuery);
    mysqli_stmt_bind_param($stmt, "s", $searchTerm);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $users = [];
    while ($user = mysqli_fetch_assoc($result)) {
        $users[] = $user;
    }
    
    return ['success' => true, 'users' => $users];
}

/**
 * invite a user to join a workspace
 * only managers can invite members to workspaces
 */
function inviteToWorkspace($managerID, $workspaceID, $invitedUserID) {
    global $conn;
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    // check if the person doing the inviting is actually a manager
    $checkManager = "
        SELECT 1 FROM workspacemember 
        WHERE WorkSpaceID = ? AND UserID = ? AND UserRole = 'Manager'
    ";
    $stmt = mysqli_prepare($conn, $checkManager);
    mysqli_stmt_bind_param($stmt, "ii", $workspaceID, $managerID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        return ['success' => false, 'message' => 'Only managers can invite members'];
    }
    
    // check if user already in the workspace
    $checkExisting = "
        SELECT 1 FROM workspacemember 
        WHERE WorkSpaceID = ? AND UserID = ?
    ";
    $stmt = mysqli_prepare($conn, $checkExisting);
    mysqli_stmt_bind_param($stmt, "ii", $workspaceID, $invitedUserID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        return ['success' => false, 'message' => 'User already in workspace'];
    }
    
    // add the new member as an employee
    $insertMember = "
        INSERT INTO workspacemember (WorkSpaceID, UserID, UserRole) 
        VALUES (?, ?, 'Employee')
    ";
    $stmt = mysqli_prepare($conn, $insertMember);
    mysqli_stmt_bind_param($stmt, "ii", $workspaceID, $invitedUserID);
    
    if (mysqli_stmt_execute($stmt)) {
        return ['success' => true, 'message' => 'Member invited successfully'];
    }
    
    return ['success' => false, 'message' => 'Failed to invite member'];
}

/**
 * grant access to a user for a specific task
 * user must have access to the workspace to grant task access
 */
function inviteToTask($userID, $taskID, $invitedUserID, $workspaceID) {
    global $conn;
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    // check if the person granting access has workspace access
    $checkAccess = "
        SELECT 1 FROM workspacemember 
        WHERE WorkSpaceID = ? AND UserID = ?
    ";
    $stmt = mysqli_prepare($conn, $checkAccess);
    mysqli_stmt_bind_param($stmt, "ii", $workspaceID, $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        return ['success' => false, 'message' => 'No access to workspace'];
    }
    
    // check if invited user is in the workspace
    $checkInvitedAccess = "
        SELECT 1 FROM workspacemember 
        WHERE WorkSpaceID = ? AND UserID = ?
    ";
    $stmt = mysqli_prepare($conn, $checkInvitedAccess);
    mysqli_stmt_bind_param($stmt, "ii", $workspaceID, $invitedUserID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        return ['success' => false, 'message' => 'User must be workspace member first'];
    }
    
    // check if user already has task access
    $checkTaskAccess = "
        SELECT 1 FROM taskaccess 
        WHERE TaskID = ? AND UserID = ?
    ";
    $stmt = mysqli_prepare($conn, $checkTaskAccess);
    mysqli_stmt_bind_param($stmt, "ii", $taskID, $invitedUserID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        return ['success' => false, 'message' => 'User already has task access'];
    }
    
    // grant task access
    $insertAccess = "
        INSERT INTO taskaccess (UserID, TaskID) 
        VALUES (?, ?)
    ";
    $stmt = mysqli_prepare($conn, $insertAccess);
    mysqli_stmt_bind_param($stmt, "ii", $invitedUserID, $taskID);
    
    if (mysqli_stmt_execute($stmt)) {
        return ['success' => true, 'message' => 'Task access granted successfully'];
    }
    
    return ['success' => false, 'message' => 'Failed to grant task access'];
}

/**
 * get workspace members list
 * returns all members of a workspace
 */
function getWorkspaceMembers($userID, $workspaceID) {
    global $conn;
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    // check if user has access to this workspace
    $checkAccess = "
        SELECT 1 FROM workspacemember 
        WHERE WorkSpaceID = ? AND UserID = ?
    ";
    $stmt = mysqli_prepare($conn, $checkAccess);
    mysqli_stmt_bind_param($stmt, "ii", $workspaceID, $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        return ['success' => false, 'message' => 'No access to workspace'];
    }
    
    // get all workspace members
    $membersQuery = "
        SELECT u.UserID, u.Username, u.Email, u.PictureName, wm.UserRole
        FROM workspacemember wm
        INNER JOIN user u ON wm.UserID = u.UserID
        WHERE wm.WorkSpaceID = ?
        ORDER BY wm.UserRole DESC, u.Username
    ";
    
    $stmt = mysqli_prepare($conn, $membersQuery);
    mysqli_stmt_bind_param($stmt, "i", $workspaceID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $members = [];
    while ($member = mysqli_fetch_assoc($result)) {
        $members[] = $member;
    }
    
    return ['success' => true, 'members' => $members];
}
?>

