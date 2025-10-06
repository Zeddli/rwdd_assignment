<?php
/**
 * Workspace and task creation functions
 * Handles creating new workspaces and tasks
 */

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
?>

