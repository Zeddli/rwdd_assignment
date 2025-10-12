<?php
include "../Head/Head.php";
include "../Database/Database.php";

// Get userID from session
$userID = $_SESSION["userInfo"]["userID"] ?? null;
if (!$userID) {
    echo "<div style='color:red;'>Session not set!</div>";
    exit;
}

// Workspace selection (like HomePage)
$selectedWorkspaceID = isset($_GET['workspace']) ? intval($_GET['workspace']) : null;

// Fetch all workspaces for this user
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
if (!$selectedWorkspaceID && count($workspaces) > 0) {
    $selectedWorkspaceID = $workspaces[0]['WorkSpaceID'];
}

// Members table
$members = [];
if ($selectedWorkspaceID) {
    $memberQuery = "
        SELECT u.Username, u.Email, wm.UserRole
        FROM workspacemember wm
        JOIN user u ON wm.UserID = u.UserID
        WHERE wm.WorkSpaceID = $selectedWorkspaceID
    ";
    $memberResult = mysqli_query($conn, $memberQuery);
    while ($row = mysqli_fetch_assoc($memberResult)) {
        $members[] = $row;
    }
}

// Total members
$totalMembers = count($members);

// Tasks
$tasks = [];
$completedTasks = [];
$taskStatusCounts = ['Pending'=>0, 'InProgress'=>0, 'Completed'=>0];
$taskOnTime = 0;
$taskOverdue = 0;
$totalTaskTime = 0; // in seconds
$numCompletedTasks = 0;
if ($selectedWorkspaceID) {
    $taskQuery = "
        SELECT t.TaskID, t.Title, t.Description, t.StartTime, t.EndTime, t.Deadline, t.Priority, t.Status
        FROM task t
        JOIN taskaccess ta ON t.TaskID = ta.TaskID
        WHERE t.WorkSpaceID = $selectedWorkspaceID
        AND ta.UserID = $userID
    ";
    $taskResult = mysqli_query($conn, $taskQuery);
    while ($row = mysqli_fetch_assoc($taskResult)) {
        $tasks[] = $row;
        $status = $row['Status'];
        if (isset($taskStatusCounts[$status])) $taskStatusCounts[$status]++;
        // Completed tasks for analytics
        if ($status === "Completed" && !empty($row['EndTime']) && !empty($row['StartTime'])) {
            $completedTasks[] = $row;
            $numCompletedTasks++;
            $start = strtotime($row['StartTime']);
            $end = strtotime($row['EndTime']);
            if ($start && $end && $end >= $start) {
                $totalTaskTime += ($end - $start);
            }
            // On-time vs overdue
            $deadline = strtotime($row['Deadline']);
            if ($end <= $deadline) $taskOnTime++;
            else $taskOverdue++;
        }
    }
}

// Average time (in seconds) PHP will pass seconds and JS will format
$avgTaskTimeSeconds = $numCompletedTasks ? intval($totalTaskTime / $numCompletedTasks) : 0;

// Goals
$goals = [];
$completedGoals = [];
$goalStatusCounts = ['Pending'=>0, 'InProgress'=>0, 'Completed'=>0];
$goalOnTime = 0;
$goalOverdue = 0;
if ($selectedWorkspaceID) {
    $goalQuery = "
        SELECT GoalID, Description, Type, StartTime, EndTime, Deadline, Progress
        FROM goal
        WHERE WorkSpaceID = $selectedWorkspaceID
    ";
    $goalResult = mysqli_query($conn, $goalQuery);
    while ($row = mysqli_fetch_assoc($goalResult)) {
        $goals[] = $row;
        $progress = $row['Progress'];
        if (isset($goalStatusCounts[$progress])) $goalStatusCounts[$progress]++;
        // Completed goals for analytics
        if ($progress === "Completed" && !empty($row['EndTime']) && !empty($row['StartTime'])) {
            $completedGoals[] = $row;
            $end = strtotime($row['EndTime']);
            $deadline = strtotime($row['Deadline']);
            if ($end && $deadline) {
                if ($end <= $deadline) $goalOnTime++;
                else $goalOverdue++;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Page</title>
    <link rel="stylesheet" href="../Navbar/styles/base.css">
    <link rel="stylesheet" href="../Navbar/styles/navbar.css">
    <link rel="stylesheet" href="analytics.css">
</head>
<body>
    <?php include "../Navbar/navbar.php"; ?>
    <div class="main-content">
        <h1>Analytics</h1>
        <div class="workspace-picker">
            <form method="get" action="analytics.php">
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
        <div class="stat-grid">
            <div class="stat-card">
                Total Members<br>
                <span style="font-size:2em"><?= $totalMembers ?></span>
            </div>
            <div class="stat-card">
                Average Task Completion Time<br>
                <?php
                function fmt_time($secs) {
                    if ($secs <= 0) return "N/A";
                    $days = floor($secs / 86400);
                    $hours = floor(($secs % 86400) / 3600);
                    $mins = floor(($secs % 3600) / 60);
                    $out = [];
                    if ($days > 0) $out[] = "$days days";
                    if ($hours > 0) $out[] = "$hours hrs";
                    if ($mins > 0) $out[] = "$mins mins";
                    return implode(' ', $out);
                }
                echo '<span style="font-size:1.5em">' . fmt_time($avgTaskTimeSeconds) . '</span>';
                ?>
            </div>
        </div>
        <div class="chart-area">
            <div class="chart-block">
                <div class="chart-title">Completed Task: On Time vs Overdue</div>
                <canvas id="taskPie" width="220" height="220" style="margin:auto; display:block;"></canvas>
                <div class="chart-legend">
                    <span style="color:#2196f3;font-weight:600;">&#9632;</span> On time (<?= $taskOnTime ?>)
                    &nbsp; <span style="color:#e74c3c;font-weight:600;">&#9632;</span> Overdue (<?= $taskOverdue ?>)
                </div>
            </div>
            <div class="chart-block">
                <div class="chart-title">Completed Goal: On Time vs Overdue</div>
                <canvas id="goalPie" width="220" height="220" style="margin:auto; display:block;"></canvas>
                <div class="chart-legend">
                    <span style="color:#2196f3;font-weight:600;">&#9632;</span> On time (<?= $goalOnTime ?>)
                    &nbsp; <span style="color:#e74c3c;font-weight:600;">&#9632;</span> Overdue (<?= $goalOverdue ?>)
                </div>
            </div>
            <div class="chart-block">
                <div class="chart-title">Task Distribution</div>
                <canvas id="taskBar" width="320" height="180" style="margin:auto; display:block;"></canvas>
                <div class="chart-legend chart-legend-center">
                <span class="legend-item"><span style="color:#f4b400;font-weight:600;">&#9632;</span> Pending (<?= $taskStatusCounts['Pending'] ?>)</span>
                <span class="legend-item"><span style="color:#2196f3;font-weight:600;">&#9632;</span> In Progress (<?= $taskStatusCounts['InProgress'] ?>)</span>
                <span class="legend-item"><span style="color:#4caf50;font-weight:600;">&#9632;</span> Completed (<?= $taskStatusCounts['Completed'] ?>)</span>
            </div>
            </div>
            <div class="chart-block">
                <div class="chart-title">Goal Distribution</div>
                <canvas id="goalBar" width="320" height="180" style="margin:auto; display:block;"></canvas>
                <div class="chart-legend chart-legend-center">
                    <span class="legend-item"><span style="color:#f4b400;font-weight:600;">&#9632;</span> Pending (<?= $taskStatusCounts['Pending'] ?>)</span>
                    <span class="legend-item"><span style="color:#2196f3;font-weight:600;">&#9632;</span> In Progress (<?= $taskStatusCounts['InProgress'] ?>)</span>
                    <span class="legend-item"><span style="color:#4caf50;font-weight:600;">&#9632;</span> Completed (<?= $taskStatusCounts['Completed'] ?>)</span>
                </div>
            </div>
        </div>
        <h2>Members in Workspace</h2>
        <table class="members-table">
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
            </tr>
            <?php foreach ($members as $m): ?>
                <tr>
                    <td><?= htmlspecialchars($m['Username']) ?></td>
                    <td><?= htmlspecialchars($m['Email']) ?></td>
                    <td><?= htmlspecialchars($m['UserRole']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <script>
        // Data for charts
        const taskPieData = {onTime: <?= $taskOnTime ?>, overdue: <?= $taskOverdue ?>};
        const goalPieData = {onTime: <?= $goalOnTime ?>, overdue: <?= $goalOverdue ?>};
        const taskBarData = {
            pending: <?= $taskStatusCounts['Pending'] ?>,
            inProgress: <?= $taskStatusCounts['InProgress'] ?>,
            completed: <?= $taskStatusCounts['Completed'] ?>
        };
        const goalBarData = {
            pending: <?= $goalStatusCounts['Pending'] ?>,
            inProgress: <?= $goalStatusCounts['InProgress'] ?>,
            completed: <?= $goalStatusCounts['Completed'] ?>
        };

        // Pie chart drawing
        function drawPie(ctx, data, colors, centerText) {
            const total = data.reduce((a,b)=>a+b,0);
            const cx = ctx.canvas.width/2, cy = ctx.canvas.height/2;
            const radius = Math.min(ctx.canvas.width, ctx.canvas.height) / 2 - 8;
            let angle = -Math.PI/2;
            ctx.clearRect(0,0,ctx.canvas.width,ctx.canvas.height);
            if (total === 0) {
                // Draw empty circle with centered text
                ctx.beginPath();
                ctx.arc(cx, cy, radius, 0, 2*Math.PI);
                ctx.fillStyle = "#f3f9ff";
                ctx.fill();
                ctx.strokeStyle = "#ccc";
                ctx.stroke();
                ctx.fillStyle = "#888";
                ctx.font = "bold 17px Arial";
                ctx.textAlign = "center";
                ctx.textBaseline = "middle";
                ctx.fillText(centerText, cx, cy);
                return;
            }
            for (let i=0; i<data.length; i++) {
                const slice = (data[i]/total)*2*Math.PI;
                ctx.beginPath();
                ctx.moveTo(cx, cy);
                ctx.arc(cx, cy, radius, angle, angle+slice);
                ctx.closePath();
                ctx.fillStyle = colors[i];
                ctx.fill();
                angle += slice;
            }
        }
        // Bar chart drawing
        function drawBar(ctx, data, colors, labels) {
            ctx.clearRect(0,0,ctx.canvas.width,ctx.canvas.height);
            const total = data.reduce((a,b)=>a+b,0);
            const barW = 48, gap = 38;
            const numBars = data.length;

            // Calculate total width of all bars and gaps
            const totalBarsWidth = numBars * barW;
            const totalGapsWidth = (numBars - 1) * gap;
            const chartWidth = totalBarsWidth + totalGapsWidth;

            // Calculate the starting X position for the first bar to center the chart
            // Subtract 4 (or a small number) for slight aesthetic adjustment/padding
            const startX = (ctx.canvas.width / 2) - (chartWidth / 2) + 4; 

            const maxVal = Math.max(...data, 1);
            
            for (let i=0; i<data.length; i++) {
                // Calculate the X position based on the new startX
                const x = startX + i * (barW + gap);
                
                const h = Math.round((data[i]/maxVal) * (ctx.canvas.height-60));
                
                // Bar
                ctx.fillStyle = colors[i];
                ctx.fillRect(x, ctx.canvas.height-28-h, barW, h);
                
                // Value
                ctx.fillStyle = "#444";
                ctx.font = "bold 15px Arial";
                ctx.textAlign = "center";
                ctx.fillText(data[i], x+barW/2, ctx.canvas.height-34-h);
                
                // Label
                ctx.font = "bold 15px Arial";
                ctx.fillText(labels[i], x+barW/2, ctx.canvas.height-10);
            }
            
            if (total === 0) {
                ctx.fillStyle = "#888";
                ctx.font = "bold 16px Arial";
                ctx.textAlign = "center";
                ctx.fillText("No data yet", ctx.canvas.width/2, ctx.canvas.height/2);
            }
        }

        window.addEventListener('DOMContentLoaded', ()=>{
            drawPie(
                document.getElementById('taskPie').getContext('2d'),
                [taskPieData.onTime, taskPieData.overdue],
                ["#2196f3","#e74c3c"],
                "No task completed yet"
            );
            drawPie(
                document.getElementById('goalPie').getContext('2d'),
                [goalPieData.onTime, goalPieData.overdue],
                ["#2196f3","#e74c3c"],
                "No goal completed yet"
            );
            drawBar(
                document.getElementById('taskBar').getContext('2d'),
                [taskBarData.pending, taskBarData.inProgress, taskBarData.completed],
                ["#f4b400","#2196f3","#4caf50"],
                ["Pending","In Progress","Completed"]
            );
            drawBar(
                document.getElementById('goalBar').getContext('2d'),
                [goalBarData.pending, goalBarData.inProgress, goalBarData.completed],
                ["#f4b400","#2196f3","#4caf50"],
                ["Pending","In Progress","Completed"]
            );
        });
    </script>
    <!-- JS modules for sidebar -->
    <script src="../Navbar/scripts/core.js"></script>                      <!-- Global state and DOM cache -->
    <script src="../Navbar/scripts/delete.js"></script>                    <!-- Delete functionality -->
    <script src="../Navbar/scripts/dropdowns.js"></script>                 <!-- Dropdown menu functionality -->
    <script src="../Navbar/scripts/editing.js"></script>                   <!-- Inline rename functionality -->
    <script src="../Navbar/scripts/inviteMember.js"></script>             <!-- Invite member functionality -->
    <script src="../Navbar/scripts/workspaces.js"></script>                <!-- Workspace creation/management -->
    <script src="../Navbar/scripts/tasks.js"></script>                     <!-- Task operations -->
    <script src="../Navbar/scripts/sidebar.js"></script>                   <!-- Main sidebar functionality -->
    <script src="../Navbar/scripts/main.js"></script>                      <!-- Entry point that starts everything -->

</body>
</html>