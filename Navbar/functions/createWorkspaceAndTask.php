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

function createTask($userID, $workspaceID, $taskName, $taskDescription = '', $startDate = '', $deadline = '', $priority = 'Medium', $status = 'Pending') {
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
    
    // Prepare the task data with proper date handling
    $currentTime = date('Y-m-d H:i:s');
    
    // Normalize HTML datetime-local values (e.g., 2025-10-16T12:34)
    $normalizeDateTime = function ($value) {
        $value = trim((string)$value);
        if ($value === '') { return ''; }
        // Replace 'T' with space
        $value = str_replace('T', ' ', $value);
        // If missing seconds, append :00
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $value)) {
            $value .= ':00';
        }
        // Validate/format to MySQL datetime
        $ts = strtotime($value);
        if ($ts === false) { return ''; }
        return date('Y-m-d H:i:s', $ts);
    };
    
    $startTime = $normalizeDateTime($startDate);
    $deadlineTime = $normalizeDateTime($deadline);
    // Schema requires EndTime NOT NULL; set it equal to StartTime if no separate end provided
    $endTime = $startTime;
    
    if ($startDate && !$startTime) {
        return ['success' => false, 'message' => 'Invalid start date'];
    }
    if ($deadline && !$deadlineTime) {
        return ['success' => false, 'message' => 'Invalid deadline'];
    }
    
    // Validate that deadline is after start date using timestamps
    if ($startTime && $deadlineTime && strtotime($deadlineTime) <= strtotime($startTime)) {
        return ['success' => false, 'message' => 'Deadline must be after the start date'];
    }
    
    // Set default description if empty
    if (empty($taskDescription)) {
        $taskDescription = 'New task description';
    }
    
    // Map UI status to DB enum values
    $statusMap = [
        'Pending' => 'Pending',
        'In Progress' => 'InProgress',
        'InProgress' => 'InProgress',
        'Completed' => 'Completed'
    ];
    $statusForDb = $statusMap[$status] ?? 'Pending';

    // create the task with all the provided values
    $insertTask = "
        INSERT INTO task (WorkSpaceID, Title, Description, StartTime, EndTime, Deadline, Priority, Status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ";
    $stmt = mysqli_prepare($conn, $insertTask);
    mysqli_stmt_bind_param($stmt, "isssssss", $workspaceID, $taskName, $taskDescription, $startTime, $endTime, $deadlineTime, $priority, $statusForDb);
    
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

