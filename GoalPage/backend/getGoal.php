<?php

include "../../Database/Database.php";

// Start session and check authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set content type to JSON
header('Content-Type: application/json');

// Require login
if (!isset($_SESSION['userInfo']) || !isset($_SESSION['userInfo']['userID'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['userInfo']['userID'];

try {
    $query = "SELECT g.GoalID as id, g.GoalTitle as title, g.Description as description, 
                     g.Type as type, g.StartTime as start_time, g.Deadline as deadline, 
                     g.Progress as progress
              FROM goal g
              INNER JOIN goalaccess ga ON g.GoalID = ga.GoalID
              WHERE ga.UserID = ?
              ORDER BY 
                CASE 
                    WHEN g.Progress = 'Pending' THEN 0 
                    WHEN g.Progress = 'InProgress' THEN 1 
                    ELSE 2 
                END,
                g.Deadline ASC";

    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        throw new Exception('Query preparation failed');
    }
    
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $goal = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Map database progress to frontend progress
        $progress = 'pending';
        if ($row['progress'] === 'InProgress') {
            $progress = 'in progress';
        } elseif ($row['progress'] === 'Completed') {
            $progress = 'completed';
        }

        $goal[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'description' => $row['description'],
            'type' => strtolower(str_replace(' ', '-', $row['type'])), // e.g. "Long-term" -> "long-term"
            'start_time' => $row['start_time'] ? date('Y-m-d', strtotime($row['start_time'])) : null,
            'deadline' => $row['deadline'] ? date('Y-m-d', strtotime($row['deadline'])) : null,
            'progress' => $progress
        ];
    }

    echo json_encode(['success' => true, 'data' => $goal]);
    mysqli_stmt_close($stmt);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database query error: ' . $e->getMessage()]);
    exit();
}

mysqli_close($conn);
?>