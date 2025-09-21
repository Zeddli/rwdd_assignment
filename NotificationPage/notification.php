<?php
include "../Head/Head.php";
include "../Database/Database.php";

$userID = $_SESSION["userInfo"]["userID"] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['timezone'])) {
    $_SESSION['timezone'] = $_POST['timezone'];
    // Optionally redirect to avoid resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
$userTimeZone = $_SESSION['timezone'] ?? 'UTC';
date_default_timezone_set($userTimeZone);

// Fetch tasks due within a week OR overdue for current user, only pending/in progress
$reminderTasks = [];
if ($userID && isset($conn)) {
    $today = date('Y-m-d');
    $weekLater = date('Y-m-d', strtotime('+7 days'));

    // Filter for status and deadline
    $reminderQuery = "
        SELECT t.TaskID, t.Title, t.Description, t.Deadline, t.Status
        FROM task t
        JOIN taskaccess ta ON t.TaskID = ta.TaskID
        WHERE ta.UserID = ?
          AND t.Status IN ('pending', 'in progress')
          AND DATE(t.Deadline) <= ?
        ORDER BY t.Deadline ASC
    ";

    $stmt = $conn->prepare($reminderQuery);
    $stmt->bind_param("is", $userID, $weekLater);
    $stmt->execute();
    $reminderResult = $stmt->get_result();

    while ($row = $reminderResult->fetch_assoc()) {
        $reminderTasks[] = $row;
    }
    $stmt->close();
}

// Fetch notifications
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
    <link rel="stylesheet" href="../Navbar/base.css">
    <link rel="stylesheet" href="../Navbar/navbar.css">
    <link rel="stylesheet" href="notification.css">
</head>
<body>
    <!-- Sidebar -->
    <!-- Main Navigation Sidebar Container -->
    <?php include "../Navbar/navbar.php"; ?>
    <?php require_once "../Navbar/navbar_functions.php"; ?>
    
    <!-- Main Content Area -->
    <div class="main-content notification-flex">
    <div class="notification-panel">
        <h1>Notification</h1>
        <div class="notification-scroll-area">
            <div id="notification-list"></div>
        </div>
        <div id="pagination-controls"></div>
    </div>
    <div class="reminder-panel">
        <h2>Reminder</h2>
        <div id="reminder-list">
            <?php if (count($reminderTasks) === 0): ?>
                <div class="no-reminder">No upcoming or overdue tasks (pending/in progress) due within a week</div>
            <?php else: ?>
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
                         onclick="window.location.href='../TaskPage/Task.php?taskid=<?= $task['TaskID'] ?>'">
                        <strong><?= htmlspecialchars($task['Title']) ?></strong><br>
                        Description: <?= htmlspecialchars($task['Description']) ?><br>
                        Status: <?= htmlspecialchars($task['Status']) ?><br>
                        <span class="due-time">Calculating...</span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
    
    <!-- JS modules for sidebar -->
   <script src="../Navbar/core.js"></script>
    <script src="../Navbar/dropdowns.js"></script>
    <script src="../Navbar/editing.js"></script>
    <script src="../Navbar/workspaces.js"></script>
    <script src="../Navbar/tasks.js"></script>
    <script src="../Navbar/sidebar.js"></script>
    <script src="../Navbar/main.js"></script>
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