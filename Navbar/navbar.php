

<?php


// Get our database functions for workspaces and tasks
require_once 'navbar_functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Require login
if (!isset($_SESSION['userInfo']) || !isset($_SESSION['userInfo']['userID'])) {
    header("Location: ../LandingPage/landing.php");
    exit();
}

$userID = (int)$_SESSION['userInfo']['userID'];
$workspaces = getUserWorkspaces($userID);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="./logo/logo.png">
    <title>Navigation Sidebar</title>
    <!-- Load base styles first, then our custom navbar styles -->
    <link rel="stylesheet" href="../Navbar/styles/base.css">
    <link rel="stylesheet" href="../Navbar/styles/navbar.css">
</head>
<body>
    <!-- Main Navigation Sidebar Container -->
    <nav class="sidebar" id="sidebar">
        <!-- Top Section: Logo and Toggle Button -->
        <div class="sidebar-header">
            <!-- Simple logo text - could be replaced with actual logo image -->
            <div class="logo">logo</div>
            <!-- Button to collapse/expand the sidebar -->
            <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                <!-- Two different icons that switch based on sidebar state -->
                <img src="../navbar-icon/closed.svg" alt="Toggle sidebar" class="toggle-icon-left" width="16" height="16">
                <img src="../navbar-icon/arrow-right.svg" alt="Toggle sidebar" class="toggle-icon-right" width="16" height="16">
            </button>
        </div>

        <!-- Main Navigation Items -->
        <!-- These are the main app pages - Home, Notifications, etc. -->
        <div class="nav-section">
            <!-- Home page link -->
            <a href="../HomePage/home.php" class="nav-item">
                <img src="../navbar-icon/home.svg" alt="Home" class="nav-icon" width="18" height="18">
                <span class="nav-label">Home</span>
            </a>
            
            <!-- Notifications page link -->
            <a href="../NotificationPage/notification.php" class="nav-item">
                <img src="../navbar-icon/notification.svg" alt="Notification" class="nav-icon" width="18" height="18">
                <span class="nav-label">Notification</span>
            </a>

            <!-- Calendar page link -->
            <a href="../CalenderPage/calendar.php" class="nav-item">
                <img src="../navbar-icon/calender.svg" alt="Calendar" class="nav-icon" width="18" height="18">
                <span class="nav-label">Calendar</span>
            </a>
            
            <!-- Analytics page link -->
            <a href="../AnalyticsPage/analytics.php" class="nav-item">
                <img src="../navbar-icon/analytics.svg" alt="Analytics" class="nav-icon" width="18" height="18">
                <span class="nav-label">Analytics</span>
            </a>
            
            <!-- Search page link -->
            <a href="../SearchPage/search.php" class="nav-item">
                <img src="../navbar-icon/search.svg" alt="Search" class="nav-icon" width="18" height="18">
                <span class="nav-label">Search</span>
            </a>
        </div>

        <!-- Workspace Section -->
        <!-- This is where all the user's workspaces show up with their tasks -->
        <div class="workspace-section">
            <!-- Header with title and "+" button to add new workspace -->
            <div class="workspace-header">
                <span class="section-title">Workspaces</span>
                <!-- Plus button to create new workspace -->
                <button class="add-workspace-btn" id="addWorkspaceBtn" aria-label="Add new workspace">
                    <svg width="16" height="16" viewBox="0 0 16 16">
                        <line x1="8" y1="2" x2="8" y2="14" stroke="currentColor" stroke-width="2"/>
                        <line x1="2" y1="8" x2="14" y2="8" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </button>
            </div>
            
            <!-- Workspaces Container (Scrollable) -->
            <!-- This container holds all the workspaces and can scroll if there are too many -->
            <div class="workspaces-container" id="workspacesContainer">
                <?php if (empty($workspaces)): ?>
                    <!-- Show this message if user has no workspaces yet -->
                    <div class="no-workspace-message">
                        <p>You don't have any workspace yet.</p>
                        <button class="create-first-workspace-btn" onclick="addNewWorkspace()">Create Workspace</button>
                    </div>
                <?php else: ?>
                    <!-- Loop through each workspace the user has access to -->
                    <?php foreach ($workspaces as $workspace): ?>
                        <!-- Each workspace item with its database ID -->
                        <div class="workspace-item" data-workspace-id="<?php echo $workspace['WorkSpaceID']; ?>">
                            <!-- Workspace header with name and action buttons -->
                            <div class="workspace-header-item">
                                <img src="../navbar-icon/workspace.svg" alt="Workspace" class="workspace-icon" width="18" height="18">
                                <!-- Workspace name (can be renamed by clicking dropdown > rename) -->
                                <span class="workspace-name"><?php echo htmlspecialchars($workspace['WorkspaceName']); ?></span>
                                <!-- Action buttons for this workspace -->
                                <div class="workspace-actions">
                                    <!-- Plus button to add new task to this workspace -->
                                    <button class="add-task-btn" aria-label="Add new task">
                                        <svg width="16" height="16" viewBox="0 0 16 16">
                                            <line x1="8" y1="2" x2="8" y2="14" stroke="currentColor" stroke-width="2"/>
                                            <line x1="2" y1="8" x2="14" y2="8" stroke="currentColor" stroke-width="2"/>
                                        </svg>
                                    </button>
                                    <!-- Three dots menu for workspace options -->
                                    <div class="dropdown">
                                        <button class="dropdown-toggle" aria-label="Workspace options">
                                            <svg width="16" height="16" viewBox="0 0 16 16">
                                                <circle cx="8" cy="4" r="1" fill="currentColor"/>
                                                <circle cx="8" cy="8" r="1" fill="currentColor"/>
                                                <circle cx="8" cy="12" r="1" fill="currentColor"/>
                                            </svg>
                                        </button>
                                        <!-- Dropdown menu with workspace actions -->
                                        <div class="dropdown-menu">
                                            <button class="dropdown-item" data-action="invite">Invite member</button>
                                            <button class="dropdown-item" data-action="add-task">Add task</button>
                                            <button class="dropdown-item" data-action="rename">Rename</button>
                                            <button class="dropdown-item" data-action="delete">Delete</button>
                                            <button class="dropdown-item" data-action="hide">Hide</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Workspace Submenu (Goals + Tasks) -->
                            <!-- This shows the goal and all tasks under this workspace -->
                            <div class="workspace-submenu" data-visible="true">
                                <!-- Goal item - clickable link to workspace-specific goal page -->
                                <a href="../GoalPage/GoalPage.php?workspace_id=<?php echo $workspace['WorkSpaceID']; ?>" class="submenu-item goal-link">
                                    <img src="../navbar-icon/goal.svg" alt="Goal" class="submenu-icon" width="16" height="16">
                                    <span class="submenu-label">Goal</span>
                                </a>
                                
                                <!-- Loop through all tasks in this workspace that user can access -->
                                <?php foreach ($workspace['tasks'] as $task): ?>
                                    <!-- Each task item with its database ID -->
                                    <div class="task-item" data-task-id="<?php echo $task['TaskID']; ?>">
                                        <img src="../navbar-icon/task.svg" alt="Task" class="submenu-icon" width="16" height="16">
                                        <!-- Task name (can be renamed via dropdown) -->
                                        <span class="task-name"><?php echo htmlspecialchars($task['TaskName']); ?></span>
                                        <!-- Task options dropdown -->
                                        <div class="dropdown">
                                            <button class="dropdown-toggle" aria-label="Task options">
                                                <svg width="16" height="16" viewBox="0 0 16 16">
                                                    <circle cx="8" cy="4" r="1" fill="currentColor"/>
                                                    <circle cx="8" cy="8" r="1" fill="currentColor"/>
                                                    <circle cx="8" cy="12" r="1" fill="currentColor"/>
                                                </svg>
                                            </button>
                                            <!-- Task dropdown menu options -->
                                            <div class="dropdown-menu">
                                                <button class="dropdown-item" data-action="grant-access">Grant access</button>
                                                <button class="dropdown-item" data-action="rename">Rename</button>
                                                <button class="dropdown-item" data-action="pin">Pin</button>
                                                <button class="dropdown-item" data-action="delete">Delete</button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Profile Section -->
        <!-- Fixed at the bottom - user's profile link -->
        <div class="profile-section">
            <a href="../ProfilePage/ProfilePage.php" class="nav-item">
                <img src="../navbar-icon/profile.svg" alt="Profile" class="nav-icon" width="18" height="18">
                <span class="nav-label">Profile</span>
            </a>
        </div>
    </nav>

    <!-- Include Search Member Modal -->
    <?php include '../Navbar/modals/searchMemberWindow.php'; ?>
    <?php include '../Navbar/modals/taskDetailWindow.php'; ?>
    <?php include '../Navbar/modals/grantAccessWindow.php'; ?>

    <!-- Load js modules in dependency order -->
    <!-- Important: These need to load in this specific order because they depend on each other -->
    <script src="../Navbar/scripts/core.js?v=2"></script>                      <!-- Global state and DOM cache -->
    <script src="../Navbar/scripts/delete.js?v=2"></script>                    <!-- Delete functionality -->
    <script src="../Navbar/scripts/dropdowns.js?v=2"></script>                 <!-- Dropdown menu functionality -->
    <script src="../Navbar/scripts/editing.js?v=2"></script>                   <!-- Inline rename functionality -->
    <script src="../Navbar/scripts/inviteMember.js?v=2"></script>             <!-- Invite member functionality -->
    <script src="../Navbar/scripts/workspaces.js?v=2"></script>                <!-- Workspace creation/management -->
    <script src="../Navbar/scripts/tasks.js?v=2"></script>                     <!-- Task operations -->
    <script src="../Navbar/scripts/sidebar.js?v=2"></script>                   <!-- Main sidebar functionality -->
    <script src="../Navbar/scripts/main.js?v=2"></script>                      <!-- Entry point that starts everything -->

</body>
</html>
