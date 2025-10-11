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
    
    // check if this user is actually a manager of this workspace
    $checkManager = "SELECT 1 FROM workspacemember WHERE WorkSpaceID = ? AND UserID = ? AND UserRole = 'Manager'";
    $stmt = mysqli_prepare($conn, $checkManager);
    mysqli_stmt_bind_param($stmt, "ii", $workspaceID, $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        return ['success' => false, 'message' => 'Only managers can rename workspace'];
    }
    
    // update the workspace name
    $updateWorkspace = "UPDATE workspace SET Name = ? WHERE WorkSpaceID = ?";
    $stmt = mysqli_prepare($conn, $updateWorkspace);
    mysqli_stmt_bind_param($stmt, "si", $newName, $workspaceID);
    
    if (mysqli_stmt_execute($stmt)) {
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
    
    // make sure user has access to this task
    $checkAccess = "SELECT 1 FROM taskaccess WHERE TaskID = ? AND UserID = ?";
    $stmt = mysqli_prepare($conn, $checkAccess);
    mysqli_stmt_bind_param($stmt, "ii", $taskID, $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        return ['success' => false, 'message' => 'No access to task'];
    }
    
    // update the task title
    $updateTask = "UPDATE task SET Title = ? WHERE TaskID = ?";
    $stmt = mysqli_prepare($conn, $updateTask);
    mysqli_stmt_bind_param($stmt, "si", $newName, $taskID);
    
    if (mysqli_stmt_execute($stmt)) {
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
    
    if (mysqli_num_rows($result) == 0) {
        return ['success' => false, 'message' => 'Only managers can rename goals'];
    }
    
    // update goal description
    $updateGoal = "UPDATE goal SET Description = ? WHERE GoalID = ?";
    $stmt = mysqli_prepare($conn, $updateGoal);
    mysqli_stmt_bind_param($stmt, "si", $newName, $goalID);
    
    if (mysqli_stmt_execute($stmt)) {
        return ['success' => true];
    }
    
    return ['success' => false, 'message' => 'Failed to rename goal'];
}
?>

