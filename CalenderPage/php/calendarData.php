<?php
/**
 * Calendar Data Backend Functions
 * Provides calendar data retrieval and task management for calendar views
 */

// Include database connection
include_once __DIR__ . '/../../Database/Database.php';

/**
 * Get tasks for a specific month
 * @param int $userID - User ID
 * @param int $year - Year
 * @param int $month - Month (1-12)
 * @return array - Array of tasks with date information
 */
function getMonthTasks($userID, $year, $month) {
    global $conn;
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    // Get tasks that fall within the month
    $startDate = "$year-" . sprintf('%02d', $month) . "-01";
    $endDate = date('Y-m-t', strtotime($startDate)); // Last day of month
    
    $query = "
        SELECT 
            t.TaskID,
            t.Title,
            t.Description,
            t.StartTime,
            t.EndTime,
            t.Deadline,
            t.Priority,
            t.Status,
            w.Name as WorkspaceName,
            ws.UserRole
        FROM task t
        INNER JOIN taskaccess ta ON t.TaskID = ta.TaskID
        INNER JOIN workspace w ON t.WorkSpaceID = w.WorkSpaceID
        INNER JOIN workspacemember ws ON w.WorkSpaceID = ws.WorkSpaceID
        WHERE ta.UserID = ? 
        AND ws.UserID = ?
        AND (
            DATE(t.StartTime) BETWEEN ? AND ?
            OR DATE(t.EndTime) BETWEEN ? AND ?
            OR DATE(t.Deadline) BETWEEN ? AND ?
            OR (DATE(t.StartTime) <= ? AND DATE(t.EndTime) >= ?)
        )
        ORDER BY t.StartTime ASC
    ";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iiisssssss", 
        $userID, $userID, $startDate, $endDate, 
        $startDate, $endDate, $startDate, $endDate,
        $endDate, $startDate
    );
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $tasks = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $tasks[] = [
                'id' => $row['TaskID'],
                'title' => $row['Title'],
                'description' => $row['Description'],
                'startTime' => $row['StartTime'],
                'endTime' => $row['EndTime'],
                'deadline' => $row['Deadline'],
                'priority' => $row['Priority'],
                'status' => $row['Status'],
                'workspaceName' => $row['WorkspaceName'],
                'userRole' => $row['UserRole']
            ];
        }
        
        return ['success' => true, 'tasks' => $tasks];
    }
    
    return ['success' => false, 'message' => 'Failed to retrieve tasks'];
}

/**
 * Get tasks for a specific date
 * @param int $userID - User ID
 * @param string $date - Date in YYYY-MM-DD format
 * @return array - Array of tasks for the date
 */
function getDayTasks($userID, $date) {
    global $conn;
    
    if (!$conn) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    $query = "
        SELECT 
            t.TaskID,
            t.Title,
            t.Description,
            t.StartTime,
            t.EndTime,
            t.Deadline,
            t.Priority,
            t.Status,
            w.Name as WorkspaceName
        FROM task t
        INNER JOIN taskaccess ta ON t.TaskID = ta.TaskID
        INNER JOIN workspace w ON t.WorkSpaceID = w.WorkSpaceID
        WHERE ta.UserID = ?
        AND (
            DATE(t.StartTime) = ?
            OR DATE(t.EndTime) = ?
            OR DATE(t.Deadline) = ?
            OR (? BETWEEN DATE(t.StartTime) AND DATE(t.EndTime))
        )
        ORDER BY t.StartTime ASC
    ";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "issss", $userID, $date, $date, $date, $date);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $tasks = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $tasks[] = [
                'id' => $row['TaskID'],
                'title' => $row['Title'],
                'description' => $row['Description'],
                'startTime' => $row['StartTime'],
                'endTime' => $row['EndTime'],
                'deadline' => $row['Deadline'],
                'priority' => $row['Priority'],
                'status' => $row['Status'],
                'workspaceName' => $row['WorkspaceName']
            ];
        }
        
        return ['success' => true, 'tasks' => $tasks];
    }
    
    return ['success' => false, 'message' => 'Failed to retrieve tasks'];
}

/**
 * Get calendar data for API endpoint
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    session_start();
    
    if (!isset($_SESSION['userInfo']['userID'])) {
        echo json_encode(['success' => false, 'message' => 'User not logged in']);
        exit;
    }
    
    $userID = (int)$_SESSION['userInfo']['userID'];
    $action = $_POST['action'];
    
    header('Content-Type: application/json');
    
    switch ($action) {
        case 'get_month_tasks':
            $year = (int)($_POST['year'] ?? date('Y'));
            $month = (int)($_POST['month'] ?? date('n'));
            $result = getMonthTasks($userID, $year, $month);
            echo json_encode($result);
            break;
            
        case 'get_day_tasks':
            $date = $_POST['date'] ?? date('Y-m-d');
            $result = getDayTasks($userID, $date);
            echo json_encode($result);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
}
?>
