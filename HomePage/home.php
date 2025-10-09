<?php
include "../Head/Head.php";
include "../Database/Database.php";

// Get userID from session
$userID = $_SESSION["userInfo"]["userID"] ?? null;
if (!$userID) {
    echo "<div style='color:red;'>Session not set!</div>";
    exit;
}

// Get selected workspace from GET (for switching)
$selectedWorkspaceID = isset($_GET['workspace']) ? intval($_GET['workspace']) : null;

// Fetch all workspaces for this user
global $conn;
$workspaceQuery = "
    SELECT workspace.WorkSpaceID, workspace.Name as WorkspaceName
    FROM workspace
    JOIN workspacemember ON workspace.WorkSpaceID = workspacemember.WorkSpaceID
    WHERE workspacemember.UserID = $userID
";
$workspaceResult = mysqli_query($conn, $workspaceQuery);
$workspaces = [];
while ($row = mysqli_fetch_assoc($workspaceResult)) {
    $workspaces[] = $row;
}

// If no workspace selected, default to first one
if (!$selectedWorkspaceID && count($workspaces) > 0) {
    $selectedWorkspaceID = $workspaces[0]['WorkSpaceID'];
}

// Fetch tasks for selected workspace grouped by status
$tasksByStatus = [
    "Pending" => [],
    "InProgress" => [],
    "Completed" => []
];

if ($selectedWorkspaceID) {
    $taskQuery = "
    SELECT task.TaskID, task.Title, task.Description, task.Deadline, task.Priority, task.Status
    FROM task
    WHERE task.WorkSpaceID = $selectedWorkspaceID
    ORDER BY task.Deadline ASC
    ";
    $taskResult = mysqli_query($conn, $taskQuery);
    while ($row = mysqli_fetch_assoc($taskResult)) {
        $status = $row['Status'] ?? 'Pending'; // default fallback
        $tasksByStatus[$status][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" href="../Navbar/base.css">
    <link rel="stylesheet" href="../Navbar/navbar.css">
    <link rel="stylesheet" href="home.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include "../Navbar/navbar.php"; ?>
    
    <!-- Main Content -->
    <div class="main-content">
        <h1>Home</h1>
        <div class="workspace-picker">
            <form method="get" action="home.php">
                <label for="workspace" class="workspace">Select workspace:</label>
                <select name="workspace" id="workspace" onchange="this.form.submit()">
                    <?php foreach ($workspaces as $ws): ?>
                        <option value="<?= htmlspecialchars($ws['WorkSpaceID']) ?>" <?= ($ws['WorkSpaceID'] == $selectedWorkspaceID) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ws['WorkspaceName']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
        <h2 style="text-align:center;">Recent Tasks</h2>
        <div class="task-board">
            <?php
            $statuses = [
                "Pending" => "Pending",
                "InProgress" => "In Progress",
                "Completed" => "Completed"
            ];
            foreach ($statuses as $statusKey => $statusLabel):
            ?>
            <div class="task-column">
                <div class="task-column-header"><?= $statusLabel ?></div>
                <div class="task-list">
                <?php if (!empty($tasksByStatus[$statusKey])): ?>
                    <?php foreach ($tasksByStatus[$statusKey] as $task): ?>
                        <div class="task-card"
                             onclick="openTask(<?= $task['TaskID'] ?>)">
                            <div class="task-card-content">
                                <strong><?= htmlspecialchars($task['Title']) ?></strong><br>
                                Description: <?= htmlspecialchars($task['Description'] ?? 'No description') ?><br>
                                Due: <?= htmlspecialchars(date("Y-m-d H:i:s", strtotime($task['Deadline']))) ?><br>
                                Priority: <?= htmlspecialchars($task['Priority'] ?? 'N/A') ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-task">No tasks</div>
                <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- JS modules -->
    <script src="../Navbar/core.js"></script>
    <script src="../Navbar/dropdowns.js"></script>
    <script src="../Navbar/editing.js"></script>
    <script src="../Navbar/workspaces.js"></script>
    <script src="../Navbar/tasks.js"></script>
    <script src="../Navbar/sidebar.js"></script>
    <script src="../Navbar/main.js"></script>
    <script>
        function openTask(taskID) {
    fetch('../Navbar/navbar_api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=set_task_session&task_id=' + taskID
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.href = '../TaskPage/Task.php';
        } else {
            alert('Failed to open task');
        }
    });
}
    </script>
</body>
</html>