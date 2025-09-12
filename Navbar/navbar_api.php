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

// make sure user is logged in first
if (!isset($_SESSION['UserID'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$userID = $_SESSION['UserID'];
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
        
    // delete entire workspace (only managers can do this - careful!)
    case 'delete_workspace':
        $workspaceID = intval($_POST['workspace_id'] ?? 0);
        
        if ($workspaceID <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid workspace ID']);
            break;
        }
        
        $result = deleteWorkspace($userID, $workspaceID);
        echo json_encode($result);
        break;
        
    // delete a single task (only managers can do this)
    case 'delete_task':
        $taskID = intval($_POST['task_id'] ?? 0);
        
        if ($taskID <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid task ID']);
            break;
        }
        
        $result = deleteTask($userID, $taskID);
        echo json_encode($result);
        break;
        
    // unknown action
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?> 