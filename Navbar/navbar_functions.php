<?php
/**
 * navbar database functions
 * contains all the php functions that interact with the database
 * for workspace and task management
 */

// start session and connect to database
session_start();
require_once '../Database/Database.php';

/**
 * get all workspaces the user can access, plus their tasks and goals
 */
function getUserWorkspaces($userID) {
    global $conn;
    
    // out if no database connection
    // if no connection, return empty array
    if (!$conn) {
        return [];
    }
    
    // find all workspaces where this user is a member
    $workspaceQuery = "
        SELECT w.WorkSpaceID, w.Name as WorkspaceName 
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
        // get the goal for this workspace
        $goalQuery = "
            SELECT g.GoalID, g.Description as GoalName, g.Progress 
            FROM goal g 
            WHERE g.WorkSpaceID = ? 
            LIMIT 1
        ";
        
        $goalStmt = mysqli_prepare($conn, $goalQuery);
        mysqli_stmt_bind_param($goalStmt, "i", $workspace['WorkSpaceID']);
        mysqli_stmt_execute($goalStmt);
        $goalResult = mysqli_stmt_get_result($goalStmt);
        $goal = mysqli_fetch_assoc($goalResult);
        
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
        
        // add goal and tasks to workspace and add to our list
        $workspace['goal'] = $goal;
        $workspace['tasks'] = $tasks;
        $workspaces[] = $workspace;
    }
    
    return $workspaces;
}

/**
 * create a brand new workspace and make the user the manager
 * also create a default goal for the workspace
 * return success/failure info
 */
function createWorkspace($userID, $workspaceName) {
    global $conn;
    
    // can't do anything without database
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
            // start transaction to ensure all operations succeed or fail together
    mysqli_begin_transaction($conn);
    
    try {
        // create the workspace first
        $insertWorkspace = "INSERT INTO workspace (Name, UserID) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $insertWorkspace);
        mysqli_stmt_bind_param($stmt, "si", $workspaceName, $userID);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to create workspace');
        }
        
        $workspaceID = mysqli_insert_id($conn);
        
        // now make this user the manager of their new workspace
        $insertMember = "INSERT INTO workspacemember (WorkSpaceID, UserID, UserRole) VALUES (?, ?, 'Manager')";
        $memberStmt = mysqli_prepare($conn, $insertMember);
        mysqli_stmt_bind_param($memberStmt, "ii", $workspaceID, $userID);
        
        if (!mysqli_stmt_execute($memberStmt)) {
            throw new Exception('Failed to add user as manager');
        }
        
        // create a default goal for this workspace
        $currentTime = date('Y-m-d H:i:s');
        $goalDescription = "Workspace Goal";
        $insertGoal = "
            INSERT INTO goal (WorkSpaceID, Description, Type, StartTime, EndTime, Deadline, Progress) 
            VALUES (?, ?, 'Long', ?, ?, ?, 'Pending')
        ";
        $goalStmt = mysqli_prepare($conn, $insertGoal);
        mysqli_stmt_bind_param($goalStmt, "issss", $workspaceID, $goalDescription, $currentTime, $currentTime, $currentTime);
        
        if (!mysqli_stmt_execute($goalStmt)) {
            throw new Exception('Failed to create workspace goal');
        }
        
        $goalID = mysqli_insert_id($conn);
        
        // commit the query
        mysqli_commit($conn);
        
        return [
            'success' => true, 
            'workspaceID' => $workspaceID,
            'workspaceName' => $workspaceName,
            'goalID' => $goalID,
            'goalName' => $goalDescription
        ];
        
    } catch (Exception $e) {
        // rollback the query on error
        mysqli_rollback($conn);
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * create a new task inside a workspace
 * user needs access to the workspace to do this
 */
function createTask($userID, $workspaceID, $taskName) {
    global $conn;
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    // make sure user actually has access to this workspace
    $checkAccess = "SELECT 1 FROM workspacemember WHERE WorkSpaceID = ? AND UserID = ?";
    $stmt = mysqli_prepare($conn, $checkAccess);
    mysqli_stmt_bind_param($stmt, "ii", $workspaceID, $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        return ['success' => false, 'message' => 'No access to workspace'];
    }
    
    // create the task with some default values
    $currentTime = date('Y-m-d H:i:s');
    $insertTask = "
        INSERT INTO task (WorkSpaceID, Title, Description, StartTime, EndTime, Deadline, Priority, Status) 
        VALUES (?, ?, 'New task description', ?, ?, ?, 'Medium', 'Pending')
    ";
    $stmt = mysqli_prepare($conn, $insertTask);
    mysqli_stmt_bind_param($stmt, "issss", $workspaceID, $taskName, $currentTime, $currentTime, $currentTime);
    
    if (mysqli_stmt_execute($stmt)) {
        $taskID = mysqli_insert_id($conn);
        
        // give the user access to their new task
        $insertAccess = "INSERT INTO taskaccess (UserID, TaskID) VALUES (?, ?)";
        $accessStmt = mysqli_prepare($conn, $insertAccess);
        mysqli_stmt_bind_param($accessStmt, "ii", $userID, $taskID);
        mysqli_stmt_execute($accessStmt);
        
        return [
            'success' => true, 
            'taskID' => $taskID,
            'taskName' => $taskName
        ];
    }
    
    return ['success' => false, 'message' => 'Failed to create task'];
}

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
        SELECT t.TaskID, t.Title 
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
    
    mysqli_stmt_close($stmt);
    
    // Delete related records in correct order
    $deleteQueries = [
        "DELETE FROM comment WHERE TaskID = ?",
        "DELETE FROM fileshared WHERE TaskID = ?", 
        "DELETE FROM taskaccess WHERE TaskID = ?",
        "DELETE FROM notification WHERE RelatedTable = 'task' AND RelatedID = ?",
        "DELETE FROM task WHERE TaskID = ?"
    ];
    
    foreach ($deleteQueries as $query) {
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $taskID);
        mysqli_stmt_execute($stmt);
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
    
    mysqli_stmt_close($stmt);
    
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
    
    // Delete task-related records first
    if (!empty($taskIDs)) {
        $taskIDList = implode(',', $taskIDs);
        $taskDeleteQueries = [
            "DELETE FROM comment WHERE TaskID IN ($taskIDList)",
            "DELETE FROM fileshared WHERE TaskID IN ($taskIDList)",
            "DELETE FROM taskaccess WHERE TaskID IN ($taskIDList)",
            "DELETE FROM notification WHERE RelatedTable = 'task' AND RelatedID IN ($taskIDList)"
        ];
        
        foreach ($taskDeleteQueries as $query) {
            mysqli_query($conn, $query);
        }
    }
    
    // Delete workspace-related records
    $workspaceDeleteQueries = [
        "DELETE FROM task WHERE WorkSpaceID = ?",
        "DELETE FROM goal WHERE WorkSpaceID = ?",
        "DELETE FROM workspacemember WHERE WorkSpaceID = ?",
        "DELETE FROM notification WHERE RelatedTable = 'workspace' AND RelatedID = ?",
        "DELETE FROM workspace WHERE WorkSpaceID = ?"
    ];
    
    foreach ($workspaceDeleteQueries as $query) {
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $workspaceID);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    
    return ['success' => true, 'message' => 'Workspace deleted successfully'];
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