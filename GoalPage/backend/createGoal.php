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

$workspaceId = intval($payload['workspaceId'] ?? 0);
$type = $payload['type'] ?? '';
$title = trim($payload['goalTitle'] ?? '');
$description = trim($payload['description'] ?? '');
$start = $payload['startTime'] ?? '';
$end = $payload['endTime'] ?? '';
$deadline = $payload['deadline'] ?? '';
$progress = $payload['progress'] ?? 'Pending';

if (!$workspaceId || !$type || !$title || !$start || !$end) {
    echo json_encode([ 'ok' => false, 'message' => 'Missing required fields' ]);
    exit;
}

// Access check
$check = mysqli_prepare($conn, "SELECT 1 FROM workspacemember WHERE UserID = ? AND WorkSpaceID = ? LIMIT 1");
mysqli_stmt_bind_param($check, 'ii', $userID, $workspaceId);
mysqli_stmt_execute($check);
$has = mysqli_stmt_get_result($check);
if (!$has || mysqli_num_rows($has) === 0) { echo json_encode([ 'ok' => false, 'message' => 'No access to workspace' ]); exit; }

$sql = "INSERT INTO goal (WorkSpaceID, GoalTitle, Description, Type, StartTime, EndTime, Deadline, Progress) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'isssssss', $workspaceId, $title, $description, $type, $start, $end, $deadline, $progress);
$ok = mysqli_stmt_execute($stmt);

echo json_encode([ 'ok' => (bool)$ok, 'id' => mysqli_insert_id($conn) ]);

