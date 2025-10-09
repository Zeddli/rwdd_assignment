<?php
/**
 * navbar api endpoint
 * receives ajax requests from the js
 * and calls the appropriate database functions to do the actual work
 * 
 * 1. js sends post request with 'action' parameter
 * 2. this file looks at the action and calls the right function
 * 3. results are sent back as json for js to handle
 */

// get our helper functions
require_once 'navbar_functions.php';

// tell browser we're sending json back
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

// make sure user is logged in first (matching navbar.php session handling)
if (!isset($_SESSION['userInfo']) || !isset($_SESSION['userInfo']['userID'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$userID = (int)$_SESSION['userInfo']['userID'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// figure out what the user wants to do
switch ($action) {
    // get all workspaces for this user
    case 'get_workspaces':
        $workspaces = getUserWorkspaces($userID);
        echo json_encode(['success' => true, 'workspaces' => $workspaces]);
        break;
        
    // create a new workspace
    case 'create_workspace':
        $workspaceName = trim($_POST['workspace_name'] ?? 'New Workspace');
        
        if (empty($workspaceName)) {
            echo json_encode(['success' => false, 'message' => 'Workspace name cannot be empty']);
            break;
        }
        
        $result = createWorkspace($userID, $workspaceName);
        echo json_encode($result);
        break;
        
    // create a new task in a workspace
    case 'create_task':
        $workspaceID = intval($_POST['workspace_id'] ?? 0);
        $taskName = trim($_POST['task_name'] ?? 'New Task');
        
        if ($workspaceID <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid workspace ID']);
            break;
        }
        
        if (empty($taskName)) {
            echo json_encode(['success' => false, 'message' => 'Task name cannot be empty']);
            break;
        }
        
        $result = createTask($userID, $workspaceID, $taskName);
        echo json_encode($result);
        break;
        
    // change workspace name (only managers can do this)
    case 'rename_workspace':
        $workspaceID = intval($_POST['workspace_id'] ?? 0);
        $newName = trim($_POST['new_name'] ?? '');
        
        if ($workspaceID <= 0 || empty($newName)) {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
            break;
        }
        
        $result = renameWorkspace($userID, $workspaceID, $newName);
        echo json_encode($result);
        break;
        
    // change task name
    case 'rename_task':
        $taskID = intval($_POST['task_id'] ?? 0);
        $newName = trim($_POST['new_name'] ?? '');
        
        if ($taskID <= 0 || empty($newName)) {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
            break;
        }
        
        $result = renameTask($userID, $taskID, $newName);
        echo json_encode($result);
        break;
        
    // change goal name/description
    case 'rename_goal':
        $goalID = intval($_POST['goal_id'] ?? 0);
        $newName = trim($_POST['new_name'] ?? '');
        
        if ($goalID <= 0 || empty($newName)) {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
            break;
        }
        
        $result = renameGoal($userID, $goalID, $newName);
        echo json_encode($result);
        break;
        
    // delete a single task
    case 'delete_task':
        $taskID = intval($_POST['task_id'] ?? 0);
        
        if ($taskID <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid task ID']);
            break;
        }
        
        // Start transaction for data integrity
        mysqli_begin_transaction($conn);
        
        try {
            $result = deleteTaskFromDB($conn, $userID, $taskID);
            
            if ($result['success']) {
                mysqli_commit($conn);
            } else {
                mysqli_rollback($conn);
            }
            
            echo json_encode($result);
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo json_encode(['success' => false, 'message' => 'Database error occurred']);
        }
        break;
        
    // delete entire workspace (only managers can do this - careful!)
    case 'delete_workspace':
        $workspaceID = intval($_POST['workspace_id'] ?? 0);
        
        if ($workspaceID <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid workspace ID']);
            break;
        }
        
        // Start transaction for data integrity
        mysqli_begin_transaction($conn);
        
        try {
            $result = deleteWorkspaceFromDB($conn, $userID, $workspaceID);
            
            if ($result['success']) {
                mysqli_commit($conn);
            } else {
                mysqli_rollback($conn);
            }
            
            echo json_encode($result);
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo json_encode(['success' => false, 'message' => 'Database error occurred']);
        }
        break;
        
    // set task ID in session for task page
    case 'set_task_session':
        $taskID = intval($_POST['task_id'] ?? 0);
        
        if ($taskID <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid task ID']);
            break;
        }
        
        // Store task ID in session for FetchTask.php to use
        $_SESSION['taskID'] = $taskID;
        echo json_encode(['success' => true, 'taskID' => $taskID]);
        break;
        
    // search for users by email
    case 'search_user':
        $email = trim($_POST['email'] ?? $_GET['email'] ?? '');
        
        if (empty($email)) {
            echo json_encode(['success' => false, 'message' => 'Email cannot be empty']);
            break;
        }
        
        $result = searchUserByEmail($email);
        echo json_encode($result);
        break;
        
    // invite user to workspace
    case 'invite_to_workspace':
        $workspaceID = intval($_POST['workspace_id'] ?? 0);
        $invitedUserID = intval($_POST['invited_user_id'] ?? 0);
        
        if ($workspaceID <= 0 || $invitedUserID <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
            break;
        }
        
        $result = inviteToWorkspace($userID, $workspaceID, $invitedUserID);
        echo json_encode($result);
        break;
        
    // grant task access to user
    case 'invite_to_task':
        $taskID = intval($_POST['task_id'] ?? 0);
        $invitedUserID = intval($_POST['invited_user_id'] ?? 0);
        $workspaceID = intval($_POST['workspace_id'] ?? 0);
        
        if ($taskID <= 0 || $invitedUserID <= 0 || $workspaceID <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
            break;
        }
        
        $result = inviteToTask($userID, $taskID, $invitedUserID, $workspaceID);
        echo json_encode($result);
        break;
        
    // get workspace members
    case 'get_workspace_members':
        $workspaceID = intval($_POST['workspace_id'] ?? $_GET['workspace_id'] ?? 0);
        
        if ($workspaceID <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid workspace ID']);
            break;
        }
        
        $result = getWorkspaceMembers($userID, $workspaceID);
        echo json_encode($result);
        break;
        
    // unknown action
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?> 