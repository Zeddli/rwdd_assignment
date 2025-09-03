<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification</title>
    <link rel="stylesheet" href="../Navbar/base.css">
    <link rel="stylesheet" href="../Navbar/navbar.css">
    <link rel="stylesheet" href="notification.css">
</head>
<body>
    <!-- Sidebar -->
    <!-- Main Navigation Sidebar Container -->
    <?php include "../Navbar/navbar.php"; ?>
    
    <!-- Main Content Area -->
    <div class="main-content">
        <h1>Notification</h1>
        <div id="notification-list"></div>
        <div id="pagination-controls"></div>
    </div>
    
    <!-- JS modules for sidebar -->
   <script src="../Navbar/core.js"></script>
    <script src="../Navbar/dropdowns.js"></script>
    <script src="../Navbar/editing.js"></script>
    <script src="../Navbar/workspaces.js"></script>
    <script src="../Navbar/tasks.js"></script>
    <script src="../Navbar/sidebar.js"></script>
    <script src="../Navbar/main.js"></script>
    <script src="notification.js"></script>
</body>
</html>