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
if (!$goalId) { echo json_encode([ 'ok' => false, 'message' => 'Missing goalId' ]); exit; }

// Check access AND retrieve required data before deletion
$dataQuery = "
    SELECT 
        g.WorkSpaceID, 
        g.GoalTitle,
        w.Name AS WorkSpaceName
    FROM goal g 
    JOIN workspace w ON g.WorkSpaceID = w.WorkSpaceID
    JOIN workspacemember wm ON wm.WorkSpaceID = g.WorkSpaceID AND wm.UserID = ? 
    WHERE g.GoalID = ? 
    LIMIT 1
";
$stmtC = mysqli_prepare($conn, $dataQuery);
mysqli_stmt_bind_param($stmtC, 'ii', $userID, $goalId);
mysqli_stmt_execute($stmtC);
$resC = mysqli_stmt_get_result($stmtC);

if (!$resC || mysqli_num_rows($resC) === 0) { 
    echo json_encode([ 'ok' => false, 'message' => 'Goal not found or no access' ]); 
    exit; 
}

$goalData = mysqli_fetch_assoc($resC);
$workspaceId = $goalData['WorkSpaceID'];
$goalTitle = $goalData['GoalTitle'];
$workspaceName = $goalData['WorkSpaceName'];
mysqli_stmt_close($stmtC);

// Create Notification for all Workspace Members
try {
    // Prepare notification data
    $relatedID = $goalId;
    $relatedTable = "goal";
    $title = "Goal deleted";
    $desc = "The goal: ". $goalTitle . " has been deleted from workspace '$workspaceName'.";
    
    // Insert notification
    $insertNoti = mysqli_prepare($conn, "INSERT INTO notification (RelatedID, RelatedTable, Title, Description) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($insertNoti, "isss", $relatedID, $relatedTable, $title, $desc);
    mysqli_stmt_execute($insertNoti);
    $notiID = mysqli_insert_id($conn);
    mysqli_stmt_close($insertNoti);
    
    // Get all workspace members
    $membersQuery = mysqli_prepare($conn, "SELECT UserID FROM workspacemember WHERE WorkSpaceID = ?");
    mysqli_stmt_bind_param($membersQuery, 'i', $workspaceId);
    mysqli_stmt_execute($membersQuery);
    $membersResult = mysqli_stmt_get_result($membersQuery);
    mysqli_stmt_close($membersQuery);
    
    // Insert receiver for every member
    $insertReceiver = mysqli_prepare($conn, "INSERT INTO receiver (NotificationID, UserID) VALUES (?, ?)");
    while ($member = mysqli_fetch_assoc($membersResult)) {
        $receiver = $member['UserID'];
        mysqli_stmt_bind_param($insertReceiver, "ii", $notiID, $receiver);
        mysqli_stmt_execute($insertReceiver);
    }
    mysqli_stmt_close($insertReceiver);
    
} catch (Exception $e) {
    // Notification creation failed, but deletion will continue
    error_log("Failed to create notification for goal deletion: " . $e->getMessage());
}

$stmt = mysqli_prepare($conn, "DELETE FROM goal WHERE GoalID = ?");
mysqli_stmt_bind_param($stmt, 'i', $goalId);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
echo json_encode([ 'ok' => (bool)$ok ]);

