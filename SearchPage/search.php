<?php
include "../Head/Head.php";
include "../Database/Database.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="../Navbar/styles/base.css">
    <link rel="stylesheet" href="../Navbar/styles/navbar.css">
    <link rel="stylesheet" href="search.css">
    
</head>
<body>
    <!-- Main Navigation Sidebar Container -->
    <?php include "../Navbar/navbar.php"; ?>

    <div class="main-content">
        <div class="header search-header" id="search-header">
            <div class="header-content">
                <p class="search-text" id="search-name">Search</p>
            </div>
        </div>
        <div class="search-container">
            <input type="text" id="search-bar" class="search-bar" placeholder="Enter to search...">
        </div>
        <div id="search-prompt" class="search-prompt"></div>
        <div id="search-results" class="search-results"></div>
    </div>

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

    <script src="search.js"></script>
</body>
</html>