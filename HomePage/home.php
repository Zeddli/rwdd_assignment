<?php
// ===== FAKE DATA FOR DEMO =====
// Workspaces
$workspaces = [
    ['WorkSpaceID' => 1, 'Name' => 'Frontend Team'],
    ['WorkSpaceID' => 2, 'Name' => 'Backend Team'],
    ['WorkSpaceID' => 3, 'Name' => 'Design Team']
];

// Get selected workspace (simulate GET param)
$selectedWorkspaceID = isset($_GET['workspace']) ? intval($_GET['workspace']) : $workspaces[0]['WorkSpaceID'];
// Tasks (by workspace)
$tasks = [
    // workspace 1
    1 => [
        [
            'TaskID' => 101,
            'Title' => 'Design Login UI',
            'Deadline' => '2025-09-09 18:00',
            'GoalType' => 'Short',
            'Status' => 'Pending'
        ],
        [
            'TaskID' => 102,
            'Title' => 'Implement Sidebar',
            'Deadline' => '2025-09-10 20:00',
            'GoalType' => 'Long',
            'Status' => 'InProgress'
        ],
        [
            'TaskID' => 103,
            'Title' => 'Fix CSS Bugs',
            'Deadline' => '2025-09-08 12:00',
            'GoalType' => 'Short',
            'Status' => 'Completed'
        ]
    ],
    // workspace 2
    2 => [
        [
            'TaskID' => 201,
            'Title' => 'API Auth',
            'Deadline' => '2025-09-15 10:00',
            'GoalType' => 'Long',
            'Status' => 'Pending'
        ]
    ],
    // workspace 3
    3 => [
        [
            'TaskID' => 301,
            'Title' => 'Logo Design',
            'Deadline' => '2025-09-18 16:00',
            'GoalType' => 'Short',
            'Status' => 'Pending'
        ],
        [
            'TaskID' => 302,
            'Title' => 'Banner Illustration',
            'Deadline' => '2025-09-20 14:00',
            'GoalType' => 'Long',
            'Status' => 'InProgress'
        ]
    ]
];

// Group tasks by status for selected workspace
$statuses = [
    "Pending" => [],
    "InProgress" => [],
    "Completed" => []
];

foreach ($tasks[$selectedWorkspaceID] ?? [] as $task) {
    $statuses[$task['Status']][] = $task;
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
    <!-- Main Navigation Sidebar Container -->
    <?php include "../Navbar/navbar.php"; ?>
    
    <!-- Main Content -->
    <div class="main-content">
        <h1>Home</h1>
        <div class="workspace-picker">
            <form method="get" action="home.php">
                <label for="workspace" class="workspace">Select workspace:</label>
                <select name="workspace" id="workspace" onchange="this.form.submit()">
                    <?php foreach ($workspaces as $ws): ?>
                        <option value="<?= $ws['WorkSpaceID'] ?>" <?= ($ws['WorkSpaceID'] == $selectedWorkspaceID) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ws['Name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
        <h2 style="text-align:center;">Recent Tasks</h2>
        <div class="task-board">
            <?php
            foreach (["Pending", "InProgress", "Completed"] as $statusKey):
            $statusLabel = $statusKey === "InProgress" ? "In Progress" : $statusKey;
            ?>
            <div class="task-column">
                <div class="task-column-header"><?= $statusLabel ?></div>
                <div class="task-list">
                <?php if (!empty($statuses[$statusKey])): ?>
                    <?php foreach ($statuses[$statusKey] as $task): ?>
                        <div class="task-card"
                             onclick="window.location.href='../TaskPage/Task.php?taskid=<?= $task['TaskID'] ?>'">
                            <div class="task-card-content">
                                <strong><?= htmlspecialchars($task['Title']) ?></strong><br>
                                Due: <?= htmlspecialchars(date("Y-m-d H:i", strtotime($task['Deadline']))) ?><br>
                                Goal Type: <?= htmlspecialchars($task['GoalType'] ?? 'N/A') ?>
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
</body>
</html>