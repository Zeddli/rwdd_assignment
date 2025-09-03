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
        <h2 style="text-align:center;">Recent Task</h2>
        <div class="task-board">
            <div class="task-column">
                <div class="task-column-header">Pending</div>
                <div class="task-card">
                    <div class="task-card-content">
                        Showing task with duedate and goal category
                    </div>
                </div>
            </div>
            <div class="task-column">
                <div class="task-column-header">In Progress</div>
                <div class="task-card">
                    <div class="task-card-content">
                        Showing task with duedate and goal category
                    </div>
                </div>
            </div>
            <div class="task-column">
                <div class="task-column-header">Completed</div>
                <div class="task-card">
                    <div class="task-card-content">
                        Showing task with duedate and goal category
                    </div>
                </div>
            </div>
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