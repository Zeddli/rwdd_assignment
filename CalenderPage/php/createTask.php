<?php
/**
 * Create Task API
 * Creates a new task in the existing task table with workspace
 */

session_start();
header('Content-Type: application/json');

// Include database connection
require_once '../../Database/Database.php';

// Check if user is logged in
if (!isset($_SESSION['userInfo']['userID'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not authenticated'
    ]);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['title']) || empty(trim($input['title']))) {
    echo json_encode([
        'success' => false,
        'message' => 'Task title is required'
    ]);
    exit();
}

$user_id = $_SESSION['userInfo']['userID'];
$title = trim($input['title']);
$task_date = isset($input['task_date']) && !empty($input['task_date']) ? $input['task_date'] : date('Y-m-d');

try {
    // Get or create "Personal Tasks" workspace for user
    $workspace_query = "SELECT WorkSpaceID FROM workspace WHERE UserID = ? AND Name = 'Personal Tasks' LIMIT 1";
    $ws_stmt = mysqli_prepare($conn, $workspace_query);
    mysqli_stmt_bind_param($ws_stmt, "i", $user_id);
    mysqli_stmt_execute($ws_stmt);
    $ws_result = mysqli_stmt_get_result($ws_stmt);
    
    if ($ws_row = mysqli_fetch_assoc($ws_result)) {
        $workspace_id = $ws_row['WorkSpaceID'];
    } else {
        // Create Personal Tasks workspace
        $create_ws = "INSERT INTO workspace (Name, UserID) VALUES ('Personal Tasks', ?)";
        $create_stmt = mysqli_prepare($conn, $create_ws);
        mysqli_stmt_bind_param($create_stmt, "i", $user_id);
        mysqli_stmt_execute($create_stmt);
        $workspace_id = mysqli_insert_id($conn);
        mysqli_stmt_close($create_stmt);
        
        // Add user as member
        $member_query = "INSERT INTO workspacemember (WorkSpaceID, UserID, UserRole) VALUES (?, ?, 'Manager')";
        $member_stmt = mysqli_prepare($conn, $member_query);
        mysqli_stmt_bind_param($member_stmt, "ii", $workspace_id, $user_id);
        mysqli_stmt_execute($member_stmt);
        mysqli_stmt_close($member_stmt);
    }
    mysqli_stmt_close($ws_stmt);
    
    // Insert task into task table
    $datetime = $task_date . ' 00:00:00';
    $description = '';
    $priority = 'Low';
    $status = 'Pending';
    
    $task_query = "INSERT INTO task (WorkSpaceID, Title, Description, StartTime, EndTime, Deadline, Priority, Status) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $task_stmt = mysqli_prepare($conn, $task_query);
    mysqli_stmt_bind_param($task_stmt, "isssssss", 
        $workspace_id, $title, $description, $datetime, $datetime, $datetime, $priority, $status);
    
    if (mysqli_stmt_execute($task_stmt)) {
        $task_id = mysqli_insert_id($conn);
        
        // Grant user access to the task
        $access_query = "INSERT INTO taskaccess (UserID, TaskID) VALUES (?, ?)";
        $access_stmt = mysqli_prepare($conn, $access_query);
        mysqli_stmt_bind_param($access_stmt, "ii", $user_id, $task_id);
        mysqli_stmt_execute($access_stmt);
        mysqli_stmt_close($access_stmt);
        
        echo json_encode([
            'success' => true,
            'message' => 'Task created successfully',
            'task_id' => $task_id
        ]);
    } else {
        throw new Exception('Failed to create task');
    }
    
    mysqli_stmt_close($task_stmt);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error creating task: ' . $e->getMessage()
    ]);
}

mysqli_close($conn);
?>

