<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../Database/Database.php';

// Start session and check authentication
session_start();

// Check if user is logged in
if (!isset($_SESSION["userInfo"])) {
    if (isset($_COOKIE["loginInfo"])) {
        // if have cookie, set session
        $info = json_decode($_COOKIE["loginInfo"], true);
        $_SESSION["userInfo"] = $info;
    } else {
        // no cookie, return error
        echo json_encode([ 'ok' => false, 'message' => 'Not logged in' ]);
        exit;
    }
}

$userID = $_SESSION['userInfo']['userID'] ?? null;
if (!$userID) { echo json_encode([ 'ok' => false, 'message' => 'Unauthenticated' ]); exit; }

global $conn;
if (!$conn) { echo json_encode([ 'ok' => false, 'message' => 'DB connection failed' ]); exit; }

$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload)) { $payload = $_POST; }

// Get workspace from session (set when user selects a workspace)
$workspaceId = $_SESSION['workspaceID'] ?? null;

// If no workspace in session, try to use one the user belongs to
if ($workspaceId === null) {
    $fallback = mysqli_query($conn, "SELECT WorkSpaceID FROM workspacemember WHERE UserID = " . intval($userID) . " LIMIT 1");
    if ($fallback && mysqli_num_rows($fallback) > 0) {
        $workspaceId = intval(mysqli_fetch_assoc($fallback)['WorkSpaceID']);
        // Store in session for future requests
        $_SESSION['workspaceID'] = $workspaceId;
    }
}
$type = $payload['type'] ?? '';
$title = trim($payload['goalTitle'] ?? '');
$description = trim($payload['description'] ?? '') ?: 'No description provided';
$start = $payload['startTime'] ?? '';
$end = null; // EndTime should be NULL until goal is marked as Completed
$deadline = $payload['deadline'] ?? '';
$progress = $payload['progress'] ?? 'Pending';

// Normalize values
if ($deadline === '' ) { $deadline = $start; } // Default deadline to start time if not provided
if ($progress === 'InProgress') { $progress = 'In Progress'; }

if (!$workspaceId) {
    echo json_encode([ 'ok' => false, 'message' => 'No workspace selected' ]);
    exit;
}

if (!$type || !$title || !$start) {
    echo json_encode([ 'ok' => false, 'message' => 'Missing required fields', 'debug' => [
        'type' => $type,
        'title' => $title, 
        'start' => $start,
        'workspaceId' => $workspaceId
    ]]);
    exit;
}

// Access check
$check = mysqli_prepare($conn, "SELECT 1 FROM workspacemember WHERE UserID = ? AND WorkSpaceID = ? LIMIT 1");
mysqli_stmt_bind_param($check, 'ii', $userID, $workspaceId);
mysqli_stmt_execute($check);
$has = mysqli_stmt_get_result($check);
if (!$has || mysqli_num_rows($has) === 0) { echo json_encode([ 'ok' => false, 'message' => 'No access to workspace' ]); exit; }

$sql = "INSERT INTO goal (WorkSpaceID, GoalTitle, Description, Type, StartTime, EndTime, Deadline, Progress) VALUES (?, ?, ?, ?, ?, NULL, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'issssss', $workspaceId, $title, $description, $type, $start, $deadline, $progress);
$ok = mysqli_stmt_execute($stmt);

if (!$ok) {
    $error = mysqli_error($conn);
    echo json_encode([ 'ok' => false, 'message' => 'Database error: ' . $error ]);
} else {
    $goalId = mysqli_insert_id($conn);
    
    // Create notification for goal creation
    try {
        // Get workspace name
        $workspaceQuery = mysqli_prepare($conn, "SELECT Name FROM workspace WHERE WorkSpaceID = ?");
        mysqli_stmt_bind_param($workspaceQuery, 'i', $workspaceId);
        mysqli_stmt_execute($workspaceQuery);
        $workspaceResult = mysqli_stmt_get_result($workspaceQuery);
        $workspaceName = mysqli_fetch_assoc($workspaceResult)['Name'] ?? 'Unknown Workspace';
        
        // Prepare notification data
        $relatedID = $workspaceId;
        $relatedTable = "goal";
        $title = "Goal created";
        $desc = "You have created a " . strtolower($type) . " term goal in workspace " . $workspaceName;
        
        // Insert notification
        $insertNoti = mysqli_prepare($conn, "INSERT INTO notification (RelatedID, RelatedTable, Title, Description) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($insertNoti, "isss", $relatedID, $relatedTable, $title, $desc);
        mysqli_stmt_execute($insertNoti);
        
        // Insert receiver
        $receiver = $userID;
        $notiID = mysqli_insert_id($conn);
        $insertReceiver = mysqli_prepare($conn, "INSERT INTO receiver (NotificationID, UserID) VALUES (?, ?)");
        mysqli_stmt_bind_param($insertReceiver, "ii", $notiID, $receiver);
        mysqli_stmt_execute($insertReceiver);
        
    } catch (Exception $e) {
        // Notification creation failed, but goal was created successfully
        error_log("Failed to create notification for goal: " . $e->getMessage());
    }
    
    echo json_encode([ 'ok' => true, 'id' => $goalId ]);
}

