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
if (!$goalId) { echo json_encode([ 'ok' => false, 'message' => 'Missing goalId' ]); exit; }

// Access check
$sqlCheck = "SELECT g.WorkSpaceID FROM goal g JOIN workspacemember wm ON wm.WorkSpaceID = g.WorkSpaceID AND wm.UserID = ? WHERE g.GoalID = ? LIMIT 1";
$stmtC = mysqli_prepare($conn, $sqlCheck);
mysqli_stmt_bind_param($stmtC, 'ii', $userID, $goalId);
mysqli_stmt_execute($stmtC);
$resC = mysqli_stmt_get_result($stmtC);
if (!$resC || mysqli_num_rows($resC) === 0) { echo json_encode([ 'ok' => false, 'message' => 'No access' ]); exit; }

$stmt = mysqli_prepare($conn, "DELETE FROM goal WHERE GoalID = ?");
mysqli_stmt_bind_param($stmt, 'i', $goalId);
$ok = mysqli_stmt_execute($stmt);
echo json_encode([ 'ok' => (bool)$ok ]);

