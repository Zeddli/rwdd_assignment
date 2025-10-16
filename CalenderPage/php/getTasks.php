<?php
/**
 * Get Tasks API
 * Fetches all tasks for the logged-in user from existing task table
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

$user_id = $_SESSION['userInfo']['userID'];

try {
    // Fetch tasks that user has access to via taskaccess table
    $query = "SELECT t.TaskID as id, t.Title as title, t.Deadline as task_date, 
                     t.Status as status, t.Description as description
              FROM task t
              INNER JOIN taskaccess ta ON t.TaskID = ta.TaskID
              WHERE ta.UserID = ?
              ORDER BY 
                CASE 
                    WHEN t.Status = 'Pending' THEN 0 
                    WHEN t.Status = 'In Progress' THEN 1 
                    ELSE 2 
                END,
                t.Deadline ASC";
    
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        throw new Exception('Query preparation failed');
    }
    
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $tasks = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Map database status to frontend status
        $status = 'pending';
        if ($row['status'] === 'Completed') {
            $status = 'completed';
        }
        
        $tasks[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'task_date' => $row['task_date'] ? date('Y-m-d', strtotime($row['task_date'])) : null,
            'status' => $status,
            'description' => $row['description']
        ];
    }
    
    mysqli_stmt_close($stmt);
    
    echo json_encode([
        'success' => true,
        'tasks' => $tasks
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching tasks: ' . $e->getMessage()
    ]);
}

mysqli_close($conn);
?>

