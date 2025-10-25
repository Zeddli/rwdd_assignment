<?php
/**
 * Delete Task API
 * Deletes a task from the existing task table
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
if (!isset($input['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Task ID is required'
    ]);
    exit();
}

$user_id = $_SESSION['userInfo']['userID'];
$task_id = intval($input['id']);

try {
    // Verify user has access to the task AND is a manager in the workspace
    $check_query = "
        SELECT t.TaskID, wm.UserRole 
        FROM task t
        JOIN workspacemember wm ON t.WorkSpaceID = wm.WorkSpaceID
        JOIN taskaccess ta ON t.TaskID = ta.TaskID
        WHERE t.TaskID = ? AND wm.UserID = ? AND ta.UserID = ?
    ";
    $check_stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($check_stmt, "iii", $task_id, $user_id, $user_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($check_result) === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Task not found or access denied'
        ]);
        mysqli_stmt_close($check_stmt);
        mysqli_close($conn);
        exit();
    }
    
    $task_data = mysqli_fetch_assoc($check_result);
    $user_role = $task_data['UserRole'];
    
    // Check if user is a manager
    if ($user_role !== 'Manager') {
        echo json_encode([
            'success' => false,
            'message' => 'Only managers can delete tasks'
        ]);
        mysqli_stmt_close($check_stmt);
        mysqli_close($conn);
        exit();
    }
    
    mysqli_stmt_close($check_stmt);
    
    // Delete from taskaccess first (foreign key constraint)
    $delete_access = "DELETE FROM taskaccess WHERE TaskID = ?";
    $access_stmt = mysqli_prepare($conn, $delete_access);
    mysqli_stmt_bind_param($access_stmt, "i", $task_id);
    mysqli_stmt_execute($access_stmt);
    mysqli_stmt_close($access_stmt);
    
    // Delete from task table
    $delete_task = "DELETE FROM task WHERE TaskID = ?";
    $task_stmt = mysqli_prepare($conn, $delete_task);
    mysqli_stmt_bind_param($task_stmt, "i", $task_id);
    
    if (mysqli_stmt_execute($task_stmt)) {
        echo json_encode([
            'success' => true,
            'message' => 'Task deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete task');
    }
    
    mysqli_stmt_close($task_stmt);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error deleting task: ' . $e->getMessage()
    ]);
}

mysqli_close($conn);
?>

