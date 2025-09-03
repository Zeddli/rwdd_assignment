<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="../Navbar/base.css">
    <link rel="stylesheet" href="../Navbar/navbar.css">
    <link rel="stylesheet" href="search.css">
    
</head>
<body>
    <!-- Main Navigation Sidebar Container -->
    <?php include "../Navbar/navbar.php"; ?>

    <div class="main-content">
        <h1>Search</h1>
        <div class="search-container">
            <input type="text" id="search-bar" class="search-bar" placeholder="Enter to search...">
        </div>
        <div id="search-prompt" class="search-prompt"></div>
        <div id="search-results" class="search-results"></div>
    </div>

    <!-- JS modules for sidebar -->
    <script src="../Navbar/core.js"></script>
    <script src="../Navbar/dropdowns.js"></script>
    <script src="../Navbar/editing.js"></script>
    <script src="../Navbar/workspaces.js"></script>
    <script src="../Navbar/tasks.js"></script>
    <script src="../Navbar/sidebar.js"></script>
    <script src="../Navbar/main.js"></script>
    <script src="search.js"></script>
</body>
</html>