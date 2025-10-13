<!-- Calendar main view with To-Do Sidebar -->
<?php
session_start();
// Check if user is logged in
if (!isset($_SESSION['userInfo']['userID'])) {
    header("Location: ../LoginSignup/landing/login/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Calendar - ProTask</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="../Navbar/styles/base.css">
    <link rel="stylesheet" href="../Navbar/styles/navbar.css">
    <link rel="stylesheet" href="css/calendar.css">
    <link rel="stylesheet" href="css/to-do-list.css">
</head>
<body class="calendar-page">
    <!-- Main Navigation Sidebar Container -->
    <?php include "../Navbar/navbar.php"; ?>

    <!-- Main Content Area -->
    <div class="main-content" id="mainContent">
        <h1>Calendar</h1>
        
        <!-- Include Monthly Calendar View -->
        <?php include "monthlyView.php"; ?>
    </div>

    <!-- Include To-Do Sidebar Component -->
    <?php include "todoSidebarComponent.php"; ?>

    <!-- JS modules for navbar -->
    <script src="../Navbar/scripts/core.js"></script>                      <!-- Global state and DOM cache -->
    <script src="../Navbar/scripts/delete.js"></script>                    <!-- Delete functionality -->
    <script src="../Navbar/scripts/dropdowns.js"></script>                 <!-- Dropdown menu functionality -->
    <script src="../Navbar/scripts/editing.js"></script>                   <!-- Inline rename functionality -->
    <script src="../Navbar/scripts/inviteMember.js"></script>             <!-- Invite member functionality -->
    <script src="../Navbar/scripts/workspaces.js"></script>                <!-- Workspace creation/management -->
    <script src="../Navbar/scripts/tasks.js"></script>                     <!-- Task operations -->
    <script src="../Navbar/scripts/sidebar.js"></script>                   <!-- Main sidebar functionality -->
    <script src="../Navbar/scripts/main.js"></script>                      <!-- Entry point that starts everything -->


    <!-- Calendar JavaScript -->
    <script src="js/monthlyCalendar.js"></script>
    
    <!-- To-Do List Client-Side Script -->
    <script src="js/to-do-list.js"></script>
</body>
</html>