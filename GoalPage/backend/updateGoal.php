<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../Database/Database.php';
require_once __DIR__ . '/../../Head/Head.php';

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
$end = $payload['endTime'] ?? null;
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
add($fields, $params, $types, 'EndTime', $end);
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

