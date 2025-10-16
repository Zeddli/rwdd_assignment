<?php
include "../Head/Head.php";
include "../Database/Database.php";

// Start session and check authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Require login
if (!isset($_SESSION['userInfo']) || !isset($_SESSION['userInfo']['userID'])) {
    header("Location: ../LandingPage/landing.php");
    exit();
}

// Get workspace ID from URL parameter
$workspace_id = isset($_GET['workspace_id']) ? (int)$_GET['workspace_id'] : null;

if (!$workspace_id) {
    // Redirect to home page if no workspace ID provided
    header("Location: ../HomePage/home.php");
    exit();
}

$userID = (int)$_SESSION['userInfo']['userID'];

// Function to get workspace information
function getWorkspaceInfo($conn, $workspace_id, $userID) {
    $stmt = $conn->prepare("
        SELECT w.Name as WorkspaceName, w.WorkSpaceID 
        FROM workspace w
        INNER JOIN workspacemember wm ON w.WorkSpaceID = wm.WorkSpaceID
        WHERE w.WorkSpaceID = ? AND wm.UserID = ?
    ");
    $stmt->bind_param("ii", $workspace_id, $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

// Function to get goals for a specific workspace
function getWorkspaceGoals($conn, $workspace_id) {
    $stmt = $conn->prepare("
        SELECT GoalID, Description as GoalDescription, Progress as GoalStatus, StartTime as CreatedDate, Deadline as DueDate
        FROM goal 
        WHERE WorkSpaceID = ?
        ORDER BY StartTime DESC
    ");
    $stmt->bind_param("i", $workspace_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $goals = [];
    while ($row = $result->fetch_assoc()) {
        // Add a title field derived from description for compatibility
        $row['GoalTitle'] = strlen($row['GoalDescription']) > 50 ? 
            substr($row['GoalDescription'], 0, 50) . '...' : 
            $row['GoalDescription'];
        $goals[] = $row;
    }
    return $goals;
}

// Get workspace information
$workspace_info = getWorkspaceInfo($conn, $workspace_id, $userID);

if (!$workspace_info) {
    // User doesn't have access to this workspace
    header("Location: ../HomePage/home.php");
    exit();
}

// Get goals for this workspace
$goals = getWorkspaceGoals($conn, $workspace_id);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/GoalPage.css">
    <title>Goal Page</title>
</head>
<body>

    <?php include "../Navbar/navbar.php"; ?>
    <?php include "../GoalPage/createGoal.php"; ?>
    <?php include "../GoalPage/getGoals.php"; ?>
    <?php include "../GoalPage/updateGoal.php"; ?>
    <?php include "../GoalPage/deleteGoal.php"; ?>

    <div class="main-content">
        <div class="goal-container">
            <!-- Header with Create Goal Button -->
            <div class="goal-header">
                <button class="create-goal-btn" id="createGoalBtn">Create goal</button>
            </div>

            <?php if (empty($goal)): ?>
                    <!-- Show this message if user has no goals yet -->
                    <div class="no-goal-message">
                        <p>You don't have any goals yet.</p>
                        <button class="create-first-goal-btn" onclick="addNewGoal()">Create Goal</button>
                    </div>

            <?php else: ?>
                <?php foreach ($goal as $goal): ?>
                <!-- Long-term Goals Section -->
                    <div class="goals-section" data-goal-id="<?php echo $goal['GoalID']; ?>">
                        <h2 class="section-title">Long-term Goal</h2>
                        <div class="goals-container long-term-goals" id="longTermGoals">
                            <?php 
                            // Filter and display long-term goals
                            $long_term_goals = array_filter($goals, function($goal) {
                                return isset($goal['Type']) && $goal['Type'] === 'Long';
                            });
                            ?>
                            
                            <?php if (empty($long_term_goals)): ?>
                                <!-- Sample long-term goals for demonstration -->
                                <div class="goal-card long-term">
                                    <div class="goal-header-card">
                                        <div class="goal-icon">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <circle cx="12" cy="12" r="6"></circle>
                                                <circle cx="12" cy="12" r="2"></circle>
                                            </svg>
                                        </div>
                                        <h3 class="goal-title"><?php echo htmlspecialchars($workspace['GoalTitle']); ?></h3>
                                        <span><img src="../GoalPage/icon/three-dots.png" alt="Goal Image"></span>
                                    </div>
                                    <div class="goal-progress">
                                        <div class="progress-status" >Pending</div>
                                    </div>
                                    <div class="goal-dates">
                                        <div class="date-label">Date range:</div>
                                        <div class="date-range">February 4, 2024 → February 8, 2024</div>
                                    </div>
                                </div>
                                
                            
                            <?php else: ?>
                                <?php foreach ($long_term_goals as $goal): ?>
                                    <div class="goal-card long-term" data-goal-id="<?php echo $goal['GoalID']; ?>">
                                        <div class="goal-header-card">
                                            <div class="goal-icon">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <circle cx="12" cy="12" r="6"></circle>
                                                    <circle cx="12" cy="12" r="2"></circle>
                                                </svg>
                                            </div>
                                            <h3 class="goal-title"><?php echo htmlspecialchars($goal['GoalDescription']); ?></h3>
                                        </div>
                                        <div class="goal-progress">
                                            <div class="progress-status">Pending</div>
                                        </div>
                                        <div class="goal-dates">
                                            <div class="date-label">Date range:</div>
                                            <div class="date-range">
                                                <?php 
                                                $start_date = isset($goal['StartTime']) ? date('F j, Y', strtotime($goal['StartTime'])) : 'February 4, 2024';
                                                $end_date = isset($goal['Deadline']) ? date('F j, Y', strtotime($goal['Deadline'])) : 'February 8, 2024';
                                                echo $start_date . ' → ' . $end_date;
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Short-term Goals Section -->
                    <div class="goals-section">
                        <h2 class="section-title">Short-term Goal</h2>
                        <div class="goals-container short-term-goals" id="shortTermGoals">
                            <?php 
                            // Filter and display short-term goals
                            $short_term_goals = array_filter($goals, function($goal) {
                                return isset($goal['Type']) && $goal['Type'] === 'Short';
                            });
                            ?>
                            
                            <?php if (empty($short_term_goals)): ?>
                                <!-- Sample short-term goals for demonstration -->
                                <div class="goal-card short-term">
                                    <div class="goal-header-card">
                                        <div class="goal-icon">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <circle cx="12" cy="12" r="6"></circle>
                                                <circle cx="12" cy="12" r="2"></circle>
                                            </svg>
                                        </div>
                                        <h3 class="goal-title">goal title</h3>
                                    </div>
                                    <div class="goal-progress">
                                        <div class="progress-status">Pending</div>
                                    </div>
                                    <div class="goal-dates">
                                        <div class="date-label">Date range:</div>
                                        <div class="date-range">February 4, 2024 → February 8, 2024</div>
                                    </div>
                                </div>
                                
                                
                            <?php else: ?>
                                <?php foreach ($short_term_goals as $goal): ?>
                                    <div class="goal-card short-term" data-goal-id="<?php echo $goal['GoalID']; ?>">
                                        <div class="goal-header-card">
                                            <div class="goal-icon">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <circle cx="12" cy="12" r="6"></circle>
                                                    <circle cx="12" cy="12" r="2"></circle>
                                                </svg>
                                            </div>
                                            <h3 class="goal-title"><?php echo htmlspecialchars($goal['GoalDescription']); ?></h3>
                                        </div>
                                        <div class="goal-progress">
                                            <div class="progress-status">Pending</div>
                                        </div>
                                        <div class="goal-dates">
                                            <div class="date-label">Date range:</div>
                                            <div class="date-range">
                                                <?php 
                                                $start_date = isset($goal['StartTime']) ? date('F j, Y', strtotime($goal['StartTime'])) : 'February 4, 2024';
                                                $end_date = isset($goal['Deadline']) ? date('F j, Y', strtotime($goal['Deadline'])) : 'February 8, 2024';
                                                echo $start_date . ' → ' . $end_date;
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
        </div>
    </div>
</body>

    <script src="../Navbar/scripts/core.js?v=2"></script>
    <script src="../Navbar/scripts/dropdowns.js?v=2"></script>
    <script src="../Navbar/scripts/editing.js?v=2"></script>
    <script src="../Navbar/scripts/workspaces.js?v=2"></script>
    <script src="../Navbar/scripts/tasks.js?v=2"></script>
    <script src="../Navbar/scripts/sidebar.js?v=2"></script>
    <script src="../Navbar/scripts/main.js?v=2"></script>
    <!-- Goal page specific JavaScript -->
    <script src="scripts/goalPage.js"></script>
</html>