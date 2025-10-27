<?php
session_start();
include "../Database/Database.php";
header('Content-Type: application/json');

$userID = $_SESSION["userInfo"]["userID"] ?? null;
$query = trim($_GET["q"] ?? $_POST["q"] ?? "");

if (!$userID || $query === "") {
    echo json_encode([]);
    exit;
}

$queryLower = strtolower($query);

// Results arrays
$results = [];
$taskIDs = [];
$goalIDs = [];
$workspaceIDs = [];

// If query matches type, show all of that type
if (stripos($queryLower, 'task') !== false) {
    $stmt = $conn->prepare(
        "SELECT t.TaskID, t.Title, w.Name
         FROM task t
         JOIN workspace w ON t.WorkSpaceID = w.WorkSpaceID
         JOIN taskaccess ta ON t.TaskID = ta.TaskID
         WHERE ta.UserID = ?"
    );
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $results[] = [
            "type" => "task",
            "id" => $row["TaskID"],
            "name" => $row["Title"],
            "workspace" => $row["Name"],
            "link" => "../TaskPage/Task.php?taskid=" . $row["TaskID"]
        ];
        $taskIDs[] = $row["TaskID"];
    }
    $stmt->close();
}

if (stripos($queryLower, 'goal') !== false) {
    $stmt = $conn->prepare(
        "SELECT g.GoalID, g.GoalTitle, w.Name, g.WorkSpaceID
         FROM goal g
         JOIN workspace w ON g.WorkSpaceID = w.WorkSpaceID
         JOIN workspacemember wm ON g.WorkSpaceID = wm.WorkSpaceID
         WHERE wm.UserID = ?"
    );
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $results[] = [
            "type" => "goal",
            "id" => $row["GoalID"],
            "name" => $row["GoalTitle"],
            "workspace" => $row["Name"],
            "workspaceid" => $row["WorkSpaceID"],
            "link" => "../GoalPage/Goal.php?goalid=" . $row["GoalID"]
        ];
        $goalIDs[] = $row["GoalID"];
    }
    $stmt->close();
}

if (stripos($queryLower, 'workspace') !== false) {
    $stmt = $conn->prepare(
        "SELECT w.WorkSpaceID, w.Name
         FROM workspace w
         JOIN workspacemember wm ON w.WorkSpaceID = wm.WorkSpaceID
         WHERE wm.UserID = ?"
    );
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $results[] = [
            "type" => "workspace",
            "id" => $row["WorkSpaceID"],
            "name" => $row["Name"],
            "link" => "../HomePage/home.php?workspace=" . $row["WorkSpaceID"]
        ];
        $workspaceIDs[] = $row["WorkSpaceID"];
    }
    $stmt->close();
}

// Normal search: title/desc/name
// TASKS
$taskStmt = $conn->prepare(
    "SELECT t.TaskID, t.Title, w.Name
     FROM task t
     JOIN workspace w ON t.WorkSpaceID = w.WorkSpaceID
     JOIN taskaccess ta ON t.TaskID = ta.TaskID
     WHERE ta.UserID = ? AND LOWER(t.Title) LIKE CONCAT('%', ?, '%')"
);
$taskStmt->bind_param("is", $userID, $queryLower);
$taskStmt->execute();
$taskRes = $taskStmt->get_result();
while ($row = $taskRes->fetch_assoc()) {
    if (!in_array($row["TaskID"], $taskIDs)) { // Avoid duplicates
        $results[] = [
            "type" => "task",
            "id" => $row["TaskID"],
            "name" => $row["Title"],
            "workspace" => $row["Name"],
            "link" => "../TaskPage/Task.php?taskid=" . $row["TaskID"]
        ];
    }
}
$taskStmt->close();

// GOALS
$goalStmt = $conn->prepare(
    "SELECT g.GoalID, g.WorkSpaceID, g.GoalTitle, w.Name
     FROM goal g
     JOIN workspace w ON g.WorkSpaceID = w.WorkSpaceID
     JOIN workspacemember wm ON g.WorkSpaceID = wm.WorkSpaceID
     WHERE wm.UserID = ? AND LOWER(g.GoalTitle) LIKE CONCAT('%', ?, '%')"
);
$goalStmt->bind_param("is", $userID, $queryLower);
$goalStmt->execute();
$goalRes = $goalStmt->get_result();
while ($row = $goalRes->fetch_assoc()) {
    if (!in_array($row["GoalID"], $goalIDs)) {
        $results[] = [
            "type" => "goal",
            "id" => $row["GoalID"],
            "name" => $row["GoalTitle"],
            "workspace" => $row["Name"],
            "workspaceid" => $row["WorkSpaceID"],
            "link" => "../GoalPage/Goal.php?goalid=" . $row["GoalID"]
        ];
    }
}
$goalStmt->close();

// WORKSPACES
$wsStmt = $conn->prepare(
    "SELECT w.WorkSpaceID, w.Name
     FROM workspace w
     JOIN workspacemember wm ON w.WorkSpaceID = wm.WorkSpaceID
     WHERE wm.UserID = ? AND LOWER(w.Name) LIKE CONCAT('%', ?, '%')"
);
$wsStmt->bind_param("is", $userID, $queryLower);
$wsStmt->execute();
$wsRes = $wsStmt->get_result();
while ($row = $wsRes->fetch_assoc()) {
    if (!in_array($row["WorkSpaceID"], $workspaceIDs)) {
        $results[] = [
            "type" => "workspace",
            "id" => $row["WorkSpaceID"],
            "name" => $row["Name"],
            "link" => "../HomePage/home.php?workspace=" . $row["WorkSpaceID"]
        ];
    }
}
$wsStmt->close();

echo json_encode($results);
?>