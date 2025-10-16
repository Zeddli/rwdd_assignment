<?php
/**
 * Delete Goal Backend API
 * Handles deletion of goals
 */

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

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (!isset($input['goal_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Goal ID is required']);
    exit();
}

$userID = (int)$_SESSION['userInfo']['userID'];
$goal_id = (int)$input['goal_id'];

try {
    // Check if user has access to the goal (through workspace membership)
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM goal g
        INNER JOIN workspacemember wm ON g.WorkSpaceID = wm.WorkSpaceID
        WHERE g.GoalID = ? AND wm.UserID = ?
    ");
    $stmt->bind_param("ii", $goal_id, $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] == 0) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Access denied to goal']);
        exit();
    }

    // Delete goal
    $stmt = $conn->prepare("DELETE FROM goal WHERE GoalID = ?");
    $stmt->bind_param("i", $goal_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Goal deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Goal not found']);
        }
    } else {
        throw new Exception('Failed to delete goal');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>
