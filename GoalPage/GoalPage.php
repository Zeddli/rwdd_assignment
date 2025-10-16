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

// Check if database connection is valid
if (!$conn) {
    echo "<div style='color:red;'>Database connection failed! Please check your database configuration.</div>";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goals</title>
    <link rel="stylesheet" href="../Navbar/styles/base.css">
    <link rel="stylesheet" href="../Navbar/styles/navbar.css">
    <link rel="stylesheet" href="css/GoalPage.css">
</head>
<body>
    <?php include "../Navbar/navbar.php"; ?>

    <div class="main-content">
      <main class="goal-page">
        <header class="goal-header">
            <h1>Goals</h1>
            <button id="create-goal-btn" class="create-goal-btn">Create goal</button>
        </header>

        <section class="goal-section" data-type="Long">
            <div class="goal-section-title">Long-term Goal</div>
            <div id="long-goal-row" class="goal-row" aria-label="Long-term goals" role="list"></div>
        </section>

        <section class="goal-section" data-type="Short">
            <div class="goal-section-title">Short-term Goal</div>
            <div id="short-goal-row" class="goal-row" aria-label="Short-term goals" role="list"></div>
        </section>
      </main>
    </div>

    <?php include __DIR__ . "/createGoalCard.php"; ?>
    <?php include __DIR__ . "/editGoalCard.php"; ?>

    <script src="../Navbar/scripts/core.js"></script>                      <!-- Global state and DOM cache -->
    <script src="../Navbar/scripts/delete.js"></script>                    <!-- Delete functionality -->
    <script src="../Navbar/scripts/dropdowns.js"></script>                 <!-- Dropdown menu functionality -->
    <script src="../Navbar/scripts/editing.js"></script>                   <!-- Inline rename functionality -->
    <script src="../Navbar/scripts/inviteMember.js"></script>             <!-- Invite member functionality -->
    <script src="../Navbar/scripts/workspaces.js"></script>                <!-- Workspace creation/management -->
    <script src="../Navbar/scripts/tasks.js"></script>                     <!-- Task operations -->
    <script src="../Navbar/scripts/sidebar.js"></script>                   <!-- Main sidebar functionality -->
    <script src="../Navbar/scripts/main.js"></script>                      <!-- Entry point that starts everything -->
    <script src="scripts/goalPage.js"></script>
</body>
</html>