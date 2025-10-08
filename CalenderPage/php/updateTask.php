<?php
/**
 * Update Task API
 * Updates a task's status in the existing task table
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
if (!isset($input['id']) || !isset($input['status'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Task ID and status are required'
    ]);
    exit();
}

$user_id = $_SESSION['userInfo']['userID'];
$task_id = intval($input['id']);
$status = $input['status'];

// Map frontend status to database status
$db_status = 'Pending';
if ($status === 'completed') {
    $db_status = 'Completed';
}

try {
    // Update task status (verify user has access via taskaccess)
    $query = "UPDATE task t
              INNER JOIN taskaccess ta ON t.TaskID = ta.TaskID
              SET t.Status = ?
              WHERE t.TaskID = ? AND ta.UserID = ?";
    
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        throw new Exception('Query preparation failed');
    }
    
    mysqli_stmt_bind_param($stmt, "sii", $db_status, $task_id, $user_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $affected_rows = mysqli_stmt_affected_rows($stmt);
        
        if ($affected_rows > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Task updated successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Task not found or no changes made'
            ]);
        }
    } else {
        throw new Exception('Failed to update task');
    }
    
    mysqli_stmt_close($stmt);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error updating task: ' . $e->getMessage()
    ]);
}

mysqli_close($conn);
?>

