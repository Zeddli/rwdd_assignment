<?php
/**
 * Create Goal Backend API
 * Handles creation of new goals for a specific workspace
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
if (!isset($input['workspace_id']) || !isset($input['description'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

$userID = (int)$_SESSION['userInfo']['userID'];
$workspace_id = (int)$input['workspace_id'];
$description = trim($input['description']);
$due_date = isset($input['due_date']) && !empty($input['due_date']) ? $input['due_date'] : null;
$status = isset($input['status']) ? $input['status'] : 'Pending';

// Validate input
if (empty($description)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Description cannot be empty']);
    exit();
}

// Check if user has access to the workspace
$stmt = $conn->prepare("
    SELECT COUNT(*) as count 
    FROM workspacemember 
    WHERE WorkSpaceID = ? AND UserID = ?
");
$stmt->bind_param("ii", $workspace_id, $userID);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied to workspace']);
    exit();
}

// Validate due date format if provided
if ($due_date) {
    $date_check = DateTime::createFromFormat('Y-m-d', $due_date);
    if (!$date_check || $date_check->format('Y-m-d') !== $due_date) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid date format. Use YYYY-MM-DD']);
        exit();
    }
}

try {
    // Insert new goal - using current datetime for StartTime and EndTime
    $current_time = date('Y-m-d H:i:s');
    $end_time = $due_date ? $due_date . ' 23:59:59' : null;
    
    $stmt = $conn->prepare("
        INSERT INTO goal (WorkSpaceID, Description, Progress, StartTime, EndTime, Deadline)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("isssss", $workspace_id, $description, $status, $current_time, $end_time, $due_date);
    
    if ($stmt->execute()) {
        $goal_id = $conn->insert_id;
        echo json_encode([
            'success' => true, 
            'message' => 'Goal created successfully',
            'goal_id' => $goal_id
        ]);
    } else {
        throw new Exception('Failed to create goal');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>
