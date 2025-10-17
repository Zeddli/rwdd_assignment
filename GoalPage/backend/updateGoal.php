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

$goalId = intval($payload['goalId'] ?? 0);
$type = $payload['type'] ?? null;
$title = isset($payload['goalTitle']) ? trim($payload['goalTitle']) : null;
$description = isset($payload['description']) ? trim($payload['description']) : null;
$start = $payload['startTime'] ?? null;
$end = null; // cannot be set directly
$deadline = $payload['deadline'] ?? null;
$progress = $payload['progress'] ?? null;

if (!$goalId) { echo json_encode([ 'ok' => false, 'message' => 'Missing goalId' ]); exit; }

// Check access by joining goal->workspace->workspacemember
$sqlCheck = "SELECT g.WorkSpaceID FROM goal g JOIN workspacemember wm ON wm.WorkSpaceID = g.WorkSpaceID AND wm.UserID = ? WHERE g.GoalID = ? LIMIT 1";
$stmtC = mysqli_prepare($conn, $sqlCheck);
mysqli_stmt_bind_param($stmtC, 'ii', $userID, $goalId);
mysqli_stmt_execute($stmtC);
$resC = mysqli_stmt_get_result($stmtC);
if (!$resC || mysqli_num_rows($resC) === 0) { echo json_encode([ 'ok' => false, 'message' => 'No access' ]); exit; }

$fields = [];
$params = [];
$types = '';
function add(&$fields, &$params, &$types, $field, $value, $typeChar='s') { if ($value !== null) { $fields[] = "$field = ?"; $params[] = $value; $types .= $typeChar; } }
add($fields, $params, $types, 'Type', $type);
add($fields, $params, $types, 'GoalTitle', $title);
add($fields, $params, $types, 'Description', $description);
add($fields, $params, $types, 'StartTime', $start);
// Handle EndTime based on progress status
if ($progress === 'Completed') {
  // If marking as Completed and EndTime is null, set it to current time
  $sqlEnd = "UPDATE goal SET EndTime = IFNULL(EndTime, NOW()) WHERE GoalID = ?";
  $stmtEnd = mysqli_prepare($conn, $sqlEnd);
  mysqli_stmt_bind_param($stmtEnd, 'i', $goalId);
  mysqli_stmt_execute($stmtEnd);
} elseif ($progress === 'Pending' || $progress === 'In Progress') {
  // If changing from Completed to Pending/In Progress, clear EndTime
  $sqlEnd = "UPDATE goal SET EndTime = NULL WHERE GoalID = ?";
  $stmtEnd = mysqli_prepare($conn, $sqlEnd);
  mysqli_stmt_bind_param($stmtEnd, 'i', $goalId);
  mysqli_stmt_execute($stmtEnd);
}
add($fields, $params, $types, 'Deadline', $deadline);
add($fields, $params, $types, 'Progress', $progress);

if (empty($fields)) { echo json_encode([ 'ok' => true ]); exit; }

$sql = "UPDATE goal SET " . implode(', ', $fields) . " WHERE GoalID = ?";
$types .= 'i';
$params[] = $goalId;
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $types, ...$params);
$ok = mysqli_stmt_execute($stmt);

echo json_encode([ 'ok' => (bool)$ok ]);

