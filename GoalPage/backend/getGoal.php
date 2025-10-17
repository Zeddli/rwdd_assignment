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
if (!$userID) {
    echo json_encode([ 'ok' => false, 'message' => 'Unauthenticated' ]);
    exit;
}

global $conn;
if (!$conn) { echo json_encode([ 'ok' => false, 'message' => 'DB connection failed' ]); exit; }

// Get workspace from session (set when user selects a workspace)
$workspaceId = $_SESSION['selectedWorkspaceId'] ?? null;

// If no workspace in session, try to use one the user belongs to
if ($workspaceId === null) {
    $fallback = mysqli_query($conn, "SELECT WorkSpaceID FROM workspacemember WHERE UserID = " . intval($userID) . " LIMIT 1");
    if ($fallback && mysqli_num_rows($fallback) > 0) {
        $workspaceId = intval(mysqli_fetch_assoc($fallback)['WorkSpaceID']);
        // Store in session for future requests
        $_SESSION['selectedWorkspaceId'] = $workspaceId;
    }
}

if ($workspaceId === null) {
    echo json_encode([ 'ok' => true, 'data' => [] ]);
    exit;
}

// Ensure user has access to workspace
$check = mysqli_prepare($conn, "SELECT 1 FROM workspacemember WHERE UserID = ? AND WorkSpaceID = ? LIMIT 1");
mysqli_stmt_bind_param($check, 'ii', $userID, $workspaceId);
mysqli_stmt_execute($check);
$has = mysqli_stmt_get_result($check);
if (!$has || mysqli_num_rows($has) === 0) {
    echo json_encode([ 'ok' => false, 'message' => 'No access to workspace' ]);
    exit;
}

$sql = "SELECT GoalID, WorkSpaceID, GoalTitle, Description, Type, StartTime, EndTime, Deadline, Progress FROM goal WHERE WorkSpaceID = ? ORDER BY GoalID DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $workspaceId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$rows = [];
while ($row = mysqli_fetch_assoc($res)) { $rows[] = $row; }

echo json_encode([ 'ok' => true, 'data' => $rows ]);

