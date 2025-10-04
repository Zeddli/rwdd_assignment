<?php
    // need to check if the user is manager for the workspace in workspacemember, check cookie

    //return data.sucess and data.error, if not success, alert you have no access to this function

    // do all checking
    // input filter_input to prevent alert("You have virus");
    // edit task name, status, priority, start time, deadline, description
    // when changing status to completed, insert end time
    // when chaging status to pending or in progress, set end time to null

    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include "../Database/Database.php";
    header('Content-Type: application/json');

    //$_POST[] workspaceID, taskID, workspace, title, description starttime deadline priority status
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $workspaceID = filter_input(INPUT_POST, 'workspaceID', FILTER_SANITIZE_NUMBER_INT);
        $taskID = filter_input(INPUT_POST, 'taskID', FILTER_SANITIZE_NUMBER_INT);
        $workspace = htmlspecialchars(strip_tags($_POST['workspace']), ENT_QUOTES, 'UTF-8');
        $title = htmlspecialchars(strip_tags($_POST['title']), ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars(strip_tags($_POST['description']), ENT_QUOTES, 'UTF-8');
        $starttime = htmlspecialchars(strip_tags($_POST['starttime']), ENT_QUOTES, 'UTF-8');
        $deadline = htmlspecialchars(strip_tags($_POST['deadline']), ENT_QUOTES, 'UTF-8');
        $priority = htmlspecialchars(strip_tags($_POST['priority']), ENT_QUOTES, 'UTF-8');
        $status = htmlspecialchars(strip_tags($_POST['status']), ENT_QUOTES, 'UTF-8');

        $haveEndTime = $status == "Completed" ? 1 : 0;

        $taskStmt = $conn->prepare("UPDATE task
                                           SET Title = ?, Description = ?, StartTime = ?, EndTime = IF(?, NOW(), NULL), Deadline = ?, Priority = ?, Status = ?
                                           WHERE TaskID = ? AND WorkspaceID = ?");
        $taskStmt->bind_param("sssisssii", $title, $description, $starttime, $haveEndTime, $deadline, $priority, $status, $taskID, $workspaceID);
        
        $workspaceStmt = $conn->prepare("UPDATE workspace
                                               SET Name = ?
                                               WHERE WorkSpaceID = ?");
        $workspaceStmt->bind_param("si", $workspace, $workspaceID);
        
        if($taskStmt->execute() && $workspaceStmt->execute()){
            echo json_encode(["success" => true]);
            $taskStmt->close();
            $workspaceStmt->close();
            $conn->close();
            exit();
        }
        else{
            echo json_encode(["success" => false, "error" => "Failed to update task and workspace"]);
            $taskStmt->close();
            $workspaceStmt->close();
            $conn->close();
            exit();
        }

    }
?>