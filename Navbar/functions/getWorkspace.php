<?php
/**
 * Workspace retrieval functions
 * Handles fetching workspace data and related information
 */

/**
 * get all workspaces the user can access, plus their tasks and goals
 */
function getUserWorkspaces($userID) {
    global $conn;
    
    // if no connection, return empty array
    if (!$conn) {
        return [];
    }
    
    // find all workspaces where this user is a member, including their role
    $workspaceQuery = "
        SELECT w.WorkSpaceID, w.Name as WorkspaceName, wm.UserRole 
        FROM workspace w 
        INNER JOIN workspacemember wm ON w.WorkSpaceID = wm.WorkSpaceID 
        WHERE wm.UserID = ? 
        ORDER BY w.Name
    ";
    
    $stmt = mysqli_prepare($conn, $workspaceQuery);
    mysqli_stmt_bind_param($stmt, "i", $userID);
    mysqli_stmt_execute($stmt);
    $workspaceResult = mysqli_stmt_get_result($stmt);
    
    $workspaces = [];
    while ($workspace = mysqli_fetch_assoc($workspaceResult)) {
        // Goals are managed through the dedicated goal page, not loaded here
        
        // for each workspace, get all tasks this user can see
        $taskQuery = "
            SELECT t.TaskID, t.Title as TaskName, t.Status 
            FROM task t 
            INNER JOIN taskaccess ta ON t.TaskID = ta.TaskID 
            WHERE t.WorkSpaceID = ? AND ta.UserID = ? 
            ORDER BY t.Title
        ";
        
        $taskStmt = mysqli_prepare($conn, $taskQuery);
        mysqli_stmt_bind_param($taskStmt, "ii", $workspace['WorkSpaceID'], $userID);
        mysqli_stmt_execute($taskStmt);
        $taskResult = mysqli_stmt_get_result($taskStmt);
        
        // collect all tasks for this workspace
        $tasks = [];
        while ($task = mysqli_fetch_assoc($taskResult)) {
            $tasks[] = $task;
        }
        
        // add tasks to workspace and add to our list
        $workspace['tasks'] = $tasks;
        $workspaces[] = $workspace;
    }
    
    return $workspaces;
}
?>

