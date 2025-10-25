<?php
// session_start();
// // Check if user is logged in
// if (!isset($_SESSION['userInfo']['userID'])) {
//     header("Location: ../LoginSignup/landing/login/login.php");
//     exit();
// }
    include "../Head/Head.php";
 
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    // $_SESSION["workspaceID"] = 1; //CHANGEEEEEEEEEEEEE!!!!!!!!!!!!
    if(!isset($_SESSION["workspaceID"])){
        header("Location: ../HomePage/Home.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- <title>Calendar - ProTask</title> -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="../Navbar/styles/base.css">
    <link rel="stylesheet" href="../Navbar/styles/navbar.css">
    <link rel="stylesheet" href="css/calendar.css">
    <link rel="stylesheet" href="css/to-do-list.css">
    
    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.css' rel='stylesheet' />
</head>
<body class="calendar-page">
    <!-- Main Navigation Sidebar Container -->
    <?php include "../Navbar/navbar.php"; ?>

    <!-- Main Content Area -->
    <div class="main-content" id="mainContent">
        <h1>Calendar</h1>
        
        <!-- FullCalendar Container -->
        <div id="calendar"></div>
    </div>

    <!-- Include To-Do Sidebar Component -->
    <?php include "todoSidebarComponent.php"; ?>

    <!-- Include Task Detail Modal Components -->
    <?php include "../Navbar/modals/taskDetailWindow.php"; ?>
    <?php include "../Navbar/modals/grantAccessWindow.php"; ?>

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


    <!-- Modal Scripts (load first) -->
    <!-- <script type="module" src="../Navbar/scripts/taskDetailWindow.js"></script> -->
    <script type="module" src="../Navbar/scripts/grantAccessWindow.js"></script>
    
    <!-- FullCalendar JavaScript -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.js'></script>
    
    <!-- Calendar Integration JavaScript -->
    <script src="js/fullcalendar-integration.js"></script>
    
    <!-- To-Do List Client-Side Script -->
    <script src="js/to-do-list.js"></script>

    <!-- Initialize Task Detail Modal -->
    <script>
        // Set current workspace ID for task creation
        window.currentWorkspaceId = <?php echo isset($_SESSION['workspaceID']) ? $_SESSION['workspaceID'] : 1; ?>;
        
        // Test function to manually load workspaces
        window.testLoadWorkspaces = async function() {
            console.log('Testing workspace loading...');
            try {
                const response = await fetch('../Navbar/navbar_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=get_workspaces'
                });
                
                const data = await response.json();
                console.log('Test workspace response:', data);
                
                if (data.success && data.workspaces) {
                    console.log('Workspaces found:', data.workspaces);
                    console.log('First workspace structure:', data.workspaces[0]);
                } else {
                    console.error('No workspaces found or error:', data.message);
                }
            } catch (error) {
                console.error('Test workspace loading error:', error);
            }
        };
        
        // Initialize the task detail modal after DOM is ready
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof initializeTaskDetailWindow === 'function') {
                initializeTaskDetailWindow();
                console.log('Task detail modal initialized');
                console.log('showTaskDetailWindow available:', typeof window.showTaskDetailWindow === 'function');
                
                // Ensure modal close functionality is properly set up
                setTimeout(() => {
                    const modal = document.getElementById('taskDetailModal');
                    if (modal) {
                        console.log('Ensuring modal close functionality on page load...');
                        
                        // Force re-setup of close functionality
                        const closeBtn = document.getElementById('closeTaskDetailModal');
                        const cancelBtn = document.getElementById('cancelTaskBtn');
                        
                        if (closeBtn) {
                            closeBtn.removeAttribute('data-listener-added');
                        }
                        if (cancelBtn) {
                            cancelBtn.removeAttribute('data-listener-added');
                        }
                        if (modal) {
                            modal.removeAttribute('data-listener-added');
                        }
                        
                        // Call the ensure function if it exists
                        if (typeof ensureModalCloseFunctionality === 'function') {
                            ensureModalCloseFunctionality();
                        }
                    }
                }, 500);
            } else {
                console.error('initializeTaskDetailWindow function not found');
            }
        });
    </script>
</body>
</html>