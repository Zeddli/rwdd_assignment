<?php
/**
 * Navbar Database Functions
 * This file contains all the PHP functions that interact with the database
 * for workspace and task management. Think of it as the "backend" for the navbar.
 */

// Start session and connect to database - need these for everything
session_start();
require_once '../Database/Database.php';

/**
 * Gets all workspaces the user can access, plus their tasks and goals
 * This is what populates the navbar workspace dropdown
 */
function getUserWorkspaces($userID) {
    global $conn;
    
    // Bail out if no database connection
    if (!$conn) {
        return [];
    }
    
    // Find all workspaces where this user is a member
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
        // Get the goal for this workspace
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
        
        // For each workspace, get all tasks this user can see
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
        
        // Collect all tasks for this workspace
        $tasks = [];
        while ($task = mysqli_fetch_assoc($taskResult)) {
            $tasks[] = $task;
        }
        
        // Add goal and tasks to workspace and add to our list
        $workspace['goal'] = $goal;
        $workspace['tasks'] = $tasks;
        $workspaces[] = $workspace;
    }
    
    return $workspaces;
}

/**
 * Creates a brand new workspace and makes the user the manager
 * Also creates a default goal for the workspace
 * Returns success/failure info
 */
function createWorkspace($userID, $workspaceName) {
    global $conn;
    
    // Can't do anything without database
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    // Start transaction to ensure all operations succeed or fail together
    mysqli_begin_transaction($conn);
    
    try {
        // Create the workspace first
        $insertWorkspace = "INSERT INTO workspace (Name, UserID) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $insertWorkspace);
        mysqli_stmt_bind_param($stmt, "si", $workspaceName, $userID);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to create workspace');
        }
        
        $workspaceID = mysqli_insert_id($conn);
        
        // Now make this user the manager of their new workspace
        $insertMember = "INSERT INTO workspacemember (WorkSpaceID, UserID, UserRole) VALUES (?, ?, 'Manager')";
        $memberStmt = mysqli_prepare($conn, $insertMember);
        mysqli_stmt_bind_param($memberStmt, "ii", $workspaceID, $userID);
        
        if (!mysqli_stmt_execute($memberStmt)) {
            throw new Exception('Failed to add user as manager');
        }
        
        // Create a default goal for this workspace
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
        
        // Commit the transaction
        mysqli_commit($conn);
        
        return [
            'success' => true, 
            'workspaceID' => $workspaceID,
            'workspaceName' => $workspaceName,
            'goalID' => $goalID,
            'goalName' => $goalDescription
        ];
        
    } catch (Exception $e) {
        // Rollback the transaction on error
        mysqli_rollback($conn);
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Creates a new task inside a workspace
 * User needs access to the workspace to do this
 */
function createTask($userID, $workspaceID, $taskName) {
    global $conn;
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    // Make sure user actually has access to this workspace
    $checkAccess = "SELECT 1 FROM workspacemember WHERE WorkSpaceID = ? AND UserID = ?";
    $stmt = mysqli_prepare($conn, $checkAccess);
    mysqli_stmt_bind_param($stmt, "ii", $workspaceID, $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        return ['success' => false, 'message' => 'No access to workspace'];
    }
    
    // Create the task with some default values
    $currentTime = date('Y-m-d H:i:s');
    $insertTask = "
        INSERT INTO task (WorkSpaceID, Title, Description, StartTime, EndTime, Deadline, Priority, Status) 
        VALUES (?, ?, 'New task description', ?, ?, ?, 'Medium', 'Pending')
    ";
    $stmt = mysqli_prepare($conn, $insertTask);
    mysqli_stmt_bind_param($stmt, "issss", $workspaceID, $taskName, $currentTime, $currentTime, $currentTime);
    
    if (mysqli_stmt_execute($stmt)) {
        $taskID = mysqli_insert_id($conn);
        
        // Give the user access to their new task
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
 * Changes the name of a workspace
 * Only managers can do this - regular members can't
 */
function renameWorkspace($userID, $workspaceID, $newName) {
    global $conn;
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    // Check if this user is actually a manager of this workspace
    $checkManager = "SELECT 1 FROM workspacemember WHERE WorkSpaceID = ? AND UserID = ? AND UserRole = 'Manager'";
    $stmt = mysqli_prepare($conn, $checkManager);
    mysqli_stmt_bind_param($stmt, "ii", $workspaceID, $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        return ['success' => false, 'message' => 'Only managers can rename workspace'];
    }
    
    // Update the workspace name
    $updateWorkspace = "UPDATE workspace SET Name = ? WHERE WorkSpaceID = ?";
    $stmt = mysqli_prepare($conn, $updateWorkspace);
    mysqli_stmt_bind_param($stmt, "si", $newName, $workspaceID);
    
    if (mysqli_stmt_execute($stmt)) {
        return ['success' => true];
    }
    
    return ['success' => false, 'message' => 'Failed to rename workspace'];
}

/**
 * Changes the name/title of a task
 * User just needs access to the task to do this
 */
function renameTask($userID, $taskID, $newName) {
    global $conn;
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    // Make sure user has access to this task
    $checkAccess = "SELECT 1 FROM taskaccess WHERE TaskID = ? AND UserID = ?";
    $stmt = mysqli_prepare($conn, $checkAccess);
    mysqli_stmt_bind_param($stmt, "ii", $taskID, $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        return ['success' => false, 'message' => 'No access to task'];
    }
    
    // Update the task title
    $updateTask = "UPDATE task SET Title = ? WHERE TaskID = ?";
    $stmt = mysqli_prepare($conn, $updateTask);
    mysqli_stmt_bind_param($stmt, "si", $newName, $taskID);
    
    if (mysqli_stmt_execute($stmt)) {
        return ['success' => true];
    }
    
    return ['success' => false, 'message' => 'Failed to rename task'];
}

/**
 * Deletes an entire workspace and everything in it
 * Only managers can do this - be careful, this removes EVERYTHING!
 */
function deleteWorkspace($userID, $workspaceID) {
    global $conn;
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    // Double-check that user is a manager before letting them nuke everything
    $checkManager = "SELECT 1 FROM workspacemember WHERE WorkSpaceID = ? AND UserID = ? AND UserRole = 'Manager'";
    $stmt = mysqli_prepare($conn, $checkManager);
    mysqli_stmt_bind_param($stmt, "ii", $workspaceID, $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        return ['success' => false, 'message' => 'Only managers can delete workspace'];
    }
    
    // Delete the workspace - database will cascade delete everything else
    $deleteWorkspace = "DELETE FROM workspace WHERE WorkSpaceID = ?";
    $stmt = mysqli_prepare($conn, $deleteWorkspace);
    mysqli_stmt_bind_param($stmt, "i", $workspaceID);
    
    if (mysqli_stmt_execute($stmt)) {
        return ['success' => true];
    }
    
    return ['success' => false, 'message' => 'Failed to delete workspace'];
}

/**
 * Deletes a single task
 * Only workspace managers can delete tasks (not just task members)
 */
function deleteTask($userID, $taskID) {
    global $conn;
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    // Complex check: user needs task access AND manager role in the workspace
    $checkAccess = "
        SELECT t.WorkSpaceID 
        FROM task t 
        INNER JOIN taskaccess ta ON t.TaskID = ta.TaskID 
        INNER JOIN workspacemember wm ON t.WorkSpaceID = wm.WorkSpaceID 
        WHERE t.TaskID = ? AND ta.UserID = ? AND wm.UserID = ? AND wm.UserRole = 'Manager'
    ";
    $stmt = mysqli_prepare($conn, $checkAccess);
    mysqli_stmt_bind_param($stmt, "iii", $taskID, $userID, $userID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        return ['success' => false, 'message' => 'Only managers can delete tasks'];
    }
    
    // Delete the task - database will clean up related records
    $deleteTask = "DELETE FROM task WHERE TaskID = ?";
    $stmt = mysqli_prepare($conn, $deleteTask);
    mysqli_stmt_bind_param($stmt, "i", $taskID);
    
    if (mysqli_stmt_execute($stmt)) {
        return ['success' => true];
    }
    
    return ['success' => false, 'message' => 'Failed to delete task'];
}

/**
 * Rename goal
 * Only workspace managers can rename goals
 */
function renameGoal($userID, $goalID, $newName) {
    global $conn;
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    // Check if user is manager of the workspace that owns this goal
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
    
    // Update goal description
    $updateGoal = "UPDATE goal SET Description = ? WHERE GoalID = ?";
    $stmt = mysqli_prepare($conn, $updateGoal);
    mysqli_stmt_bind_param($stmt, "si", $newName, $goalID);
    
    if (mysqli_stmt_execute($stmt)) {
        return ['success' => true];
    }
    
    return ['success' => false, 'message' => 'Failed to rename goal'];
}
?> 