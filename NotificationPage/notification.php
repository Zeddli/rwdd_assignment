<?php
include "../Head/Head.php";
include "../Database/Database.php";

$userID = $_SESSION["userInfo"]["userID"] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['timezone'])) {
    $_SESSION['timezone'] = $_POST['timezone'];
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
$userTimeZone = $_SESSION['timezone'] ?? 'UTC';
date_default_timezone_set($userTimeZone);

// Reminder Panel: Fetch tasks and goals due within a week OR overdue for current user, only pending/in progress
$reminderTasks = [];
$reminderGoals = [];
if ($userID && isset($conn)) {
    $today = date('Y-m-d');
    $weekLater = date('Y-m-d', strtotime('+7 days'));

    $taskReminderQuery = "
        SELECT t.TaskID, t.Title, t.Description, t.Deadline, t.Status
        FROM task t
        JOIN taskaccess ta ON t.TaskID = ta.TaskID
        WHERE ta.UserID = ?
          AND t.Status IN ('Pending', 'In Progress')
          AND DATE(t.Deadline) <= ?
        ORDER BY t.Deadline ASC
    ";

    $stmt = $conn->prepare($taskReminderQuery);
    $stmt->bind_param("is", $userID, $weekLater);
    $stmt->execute();
    $taskResult = $stmt->get_result();

    while ($row = $taskResult->fetch_assoc()) {
        $reminderTasks[] = $row;
    }
    $stmt->close();

    $goalReminderQuery = "
        SELECT g.GoalID, g.WorkSpaceID, g.GoalTitle, g.Description, g.Deadline, g.Progress
        FROM goal g
        JOIN workspacemember wm ON g.WorkSpaceID = wm.WorkSpaceID
        WHERE wm.UserID = ?
          AND g.Progress IN ('Pending', 'In Progress')
          AND DATE(g.Deadline) <= ?
        ORDER BY g.Deadline ASC
    ";
    $stmt2 = $conn->prepare($goalReminderQuery);
    $stmt2->bind_param("is", $userID, $weekLater);
    $stmt2->execute();
    $goalResult = $stmt2->get_result();
    while ($row = $goalResult->fetch_assoc()) {
        $reminderGoals[] = $row;
    }
    $stmt2->close();
}

// Fetch notifications, but only show task notifications if user has access to the task
$notifications = [];
if ($userID && isset($conn)) {
    $notifQuery = "
        SELECT n.NotificationID, n.CreatedAt, n.RelatedID, n.RelatedTable, n.Title, n.Description
        FROM notification n
        JOIN receiver r ON n.NotificationID = r.NotificationID
        WHERE r.UserID = ?
        ORDER BY n.CreatedAt DESC
    ";
    $stmt = $conn->prepare($notifQuery);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $notifResult = $stmt->get_result();
    while ($row = $notifResult->fetch_assoc()) {
        $notifications[] = $row;
    }
    $stmt->close();    
}

// For goal notifications, add workspaceID to the array
foreach ($notifications as &$notif) {
    $relatedTable = $notif['RelatedTable'];
    $relatedID = $notif['RelatedID'];
    $exists = true;
    $workspaceID = null;

    if ($relatedTable === 'goal') {
        // Check if goal exists and get its WorkspaceID
        $stmt2 = $conn->prepare("SELECT WorkSpaceID FROM goal WHERE GoalID = ?");
        $stmt2->bind_param("i", $relatedID);
        $stmt2->execute();
        $res2 = $stmt2->get_result();
        $row2 = $res2->fetch_assoc();
        $stmt2->close();
        
        if ($row2) {
            $workspaceID = $row2['WorkSpaceID'];
        } else {
            $exists = false;
        }

    } else if ($relatedTable === 'task') {
        // Check if task exists and if user has access (though this check is now less critical for *display*, 
        // it's useful for navigation info)
        $stmt2 = $conn->prepare("SELECT 1 FROM taskaccess WHERE UserID = ? AND TaskID = ?");
        $stmt2->bind_param("ii", $userID, $relatedID);
        $stmt2->execute();
        $res2 = $stmt2->get_result();
        $stmt2->close();

        if ($res2->num_rows === 0) {
            // Check task existence only (if user created it, notification should still show)
            $stmt3 = $conn->prepare("SELECT 1 FROM task WHERE TaskID = ?");
            $stmt3->bind_param("i", $relatedID);
            $stmt3->execute();
            $res3 = $stmt3->get_result();
            $stmt3->close();
            if ($res3->num_rows === 0) {
                 $exists = false;
            }
        }

    } else if ($relatedTable === 'workspace') {
        // Check if workspace exists
        $stmt2 = $conn->prepare("SELECT 1 FROM workspace WHERE WorkSpaceID = ?");
        $stmt2->bind_param("i", $relatedID);
        $stmt2->execute();
        $res2 = $stmt2->get_result();
        $stmt2->close();

        if ($res2->num_rows === 0) {
            $exists = false;
        }
        $workspaceID = $relatedID; // WorkspaceID is the RelatedID for 'workspace' notifs
    }

    // Attach existence and WorkspaceID status to the notification object
    $notif['Exists'] = $exists;
    $notif['WorkspaceID'] = $workspaceID;
}
unset($notif); // break reference
?>

<!DOCTYPE html>
<html lang="en">
<form id="tzform" method="post" style="display:none;">
  <input type="hidden" name="timezone" id="timezone">
</form>
<script>
    document.getElementById('timezone').value = Intl.DateTimeFormat().resolvedOptions().timeZone;
    // Submit the form only if timezone not set in session
    if (!document.cookie.includes('tzset=1')) {
    document.getElementById('tzform').submit();
    document.cookie = "tzset=1"; // prevent infinite reload
    }
</script>

<!-- Pass PHP notifications array to JS -->
<script>
const notifications = <?= json_encode($notifications) ?>;
</script>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Page</title>
    <link rel="stylesheet" href="../Navbar/styles/base.css">
    <link rel="stylesheet" href="../Navbar/styles/navbar.css">
    <link rel="stylesheet" href="notification.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include "../Navbar/navbar.php"; ?>
    <?php require_once "../Navbar/navbar_functions.php"; ?>
    
    <!-- Main Content Area -->
        <div class="main-content notification-flex">
    <div class="notification-panel">
            <div class="header notification-header" id="notification-header"> 
        <div class="header-content">
            <p class="notification-text" id="notification-name">Notification</p>
            </div>
        </div>
        <div class="notification-scroll-area">
            <div id="notification-list"></div>
        </div>
        <div id="pagination-controls"></div>
    </div>
    <div class="reminder-panel">
        <h2>Reminder</h2>
        <div id="reminder-list">
            <?php if (count($reminderTasks) === 0 && count($reminderGoals) === 0): ?>
                <div class="no-reminder">No upcoming or overdue task/goal (Pending/In progress) due within a week</div>
            <?php else: ?>
                <?php foreach ($reminderGoals as $goal): 
                    $now = new DateTime('now');
                    $deadline = new DateTime($goal['Deadline']);
                    if ($deadline < $now) {
                        $intervalText = "Overdue!";
                    } else {
                        $interval = $now->diff($deadline);
                        $intervalText = sprintf(
                            'Due in: %dd %dh %dm', 
                            $interval->days, 
                            $interval->h, 
                            $interval->i
                        );
                    }
                ?>
                    <div class="reminder-card" 
                        data-deadline="<?= htmlspecialchars($goal['Deadline']) ?>"
                        data-goalid="<?= $goal['GoalID'] ?>"
                        data-workspaceid="<?= $goal['WorkSpaceID'] ?? '' ?>">
                        <strong><?= htmlspecialchars($goal['GoalTitle']) ?></strong><br>
                        Type: Goal<br>
                        Description: <?= empty($goal['Description']) || $goal['Description'] === "No description provided" 
                            ? 'No description' 
                            : htmlspecialchars($goal['Description']) ?><br>
                        Status: <?= htmlspecialchars($goal['Progress']) ?><br>
                        <span class="due-time">Calculating...</span>
                    </div>
                <?php endforeach; ?>
                <?php foreach ($reminderTasks as $task): 
                    $now = new DateTime('now');
                    $deadline = new DateTime($task['Deadline']);
                    if ($deadline < $now) {
                        $intervalText = "Overdue!";
                    } else {
                        $interval = $now->diff($deadline);
                        $intervalText = sprintf(
                            'Due in: %dd %dh %dm', 
                            $interval->days, 
                            $interval->h, 
                            $interval->i
                        );
                    }
                ?>
                    <div class="reminder-card" 
                        data-deadline="<?= htmlspecialchars($task['Deadline']) ?>" 
                        data-taskid="<?= $task['TaskID'] ?>">
                        <strong><?= htmlspecialchars($task['Title']) ?></strong><br>
                        Type: Task<br>
                        Description: <?= empty($task['Description']) || $task['Description'] === "No description provided" 
                            ? 'No description' 
                            : htmlspecialchars($task['Description']) ?><br>
                        Status: <?= htmlspecialchars($task['Status']) ?><br>
                        <span class="due-time">Calculating...</span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
    
    <!-- JS modules for sidebar -->
        <script src="../Navbar/scripts/core.js"></script>
    <script src="../Navbar/scripts/dropdowns.js"></script>
    <script src="../Navbar/scripts/editing.js"></script>
    <script src="../Navbar/scripts/workspaces.js"></script>
    <script src="../Navbar/scripts/tasks.js"></script>
    <script src="../Navbar/scripts/sidebar.js"></script>
    <script src="../Navbar/scripts/main.js"></script>
    <!-- JS for notifications and reminders -->
    <script src="notification.js"></script>
    <!-- JS to update reminder calculation countdowns -->
    <script>
    function pad(n) { return n < 10 ? '0' + n : n; }

    function updateDueTimes() {
        const now = new Date();
        document.querySelectorAll('.reminder-card').forEach(card => {
            const deadlineStr = card.getAttribute('data-deadline');
            const deadline = new Date(deadlineStr.replace(' ', 'T')); // "YYYY-MM-DD HH:MM:SS" → "YYYY-MM-DDTHH:MM:SS"
            const dueElem = card.querySelector('.due-time');

            let diff = deadline - now;
            if (isNaN(deadline)) {
                dueElem.textContent = "Invalid deadline";
                return;
            }
            if (diff < 0) {
                dueElem.textContent = "Overdue!";
            } else {
                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
                const minutes = Math.floor((diff / (1000 * 60)) % 60);
                const seconds = Math.floor((diff / 1000) % 60);
                dueElem.textContent = `Due in: ${pad(days)}d ${pad(hours)}h ${pad(minutes)}m ${pad(seconds)}s`;
            }
        });
    }

    // Initial call and update every second
    updateDueTimes();
    setInterval(updateDueTimes, 1000);
    </script>
</body>
</html>