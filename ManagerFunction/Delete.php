<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include "../Database/Database.php";
    header('Content-Type: application/json');
    //id: id,
    //type: type
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $type = htmlspecialchars(strip_tags($_POST['type']), ENT_QUOTES, 'UTF-8');

        //when delte FileSharing folder:
        // if (file_exists($filepath)) {
        //     unlink($filepath)
        // }


        //if type == task
        if($type == "task"){
            //table: comment, fileshared, task, taskaccess, file in FileSharing folder
            //flow:
            // SELECT FileID, Extension FROM fileshared WHERE TaskID = id -> combine FileID.Extension into an array -> for loop delete file in FileSharing folder -> tableInclude = [fileshared, comment, taskaccess, task] -> for loop DELETE FROM tableToDlt WHERE TaskID = id
            $taskID = $id;
            $tableInclude = ["fileshared", "comment", "taskaccess", "task"];

            $taskNameStmt = $conn->prepare("SELECT Title FROM task WHERE TaskID = ?");
            $taskNameStmt->bind_param("i", $taskID);
            if($taskNameStmt->execute()){
                $taskNameResult = $taskNameStmt->get_result();
                if($taskNameResult->num_rows==1){
                    $taskNameRow = $taskNameResult->fetch_assoc();
                    $taskname = $taskNameRow["Title"];
                    $taskNameStmt->close();
                } else {
                    echo json_encode(["success"=>false, "error"=>"Error when getting task name"]);
                    exit();
                }
            } else {
                echo json_encode(["success"=>false, "error"=>"Failed to get task name"]);
                exit();

            }

            //get file name
            $getFileStmt = $conn->prepare("SELECT FileID, Extension FROM fileshared WHERE TaskID = ?");
            $getFileStmt->bind_param("i", $taskID);
            if($getFileStmt->execute()){
                $fileResult = $getFileStmt->get_result();
                $filepath = [];
                while($row = $fileResult->fetch_assoc()){
                    $filepath[] =  "../TaskPage/FileSharing/" . $row["FileID"] . "." . $row["Extension"];
                }
                $getFileStmt->close();
            } else {
                echo json_encode(["success"=>false, "error"=>"Error when get file id"]);
                exit();
            }

            //delete file

            $deleted = 0;
            $failed = [];
            foreach($filepath as $file){
                if (file_exists($file)) {
                    if(unlink($file)){
                        $deleted++;
                    }  else {
                        $failed[] = $file;
                    }
                } else {
                    $failed[] = $file . " not found";
                }
            }

            // notification
            $relatedID = $taskID;
            $relatedTable = "task";
            $title = "Task deleted";
            $desc = "The task: ". $taskname . " has been deleted.";
            $insertNoti = $conn->prepare("INSERT INTO notification (RelatedID, RelatedTable, Title, Description) VALUES (?, ?, ?, ?)");
            $insertNoti->bind_param("isss", $relatedID, $relatedTable, $title, $desc);
            $insertNoti->execute();
            $notiID = $insertNoti->insert_id;

            //receivers
            $receiverStmt = $conn->prepare("SELECT UserID FROM taskaccess WHERE TaskID = ?");
            $receiverStmt->bind_param("i", $taskID);
            if($receiverStmt->execute()){
                $receiverResult = $receiverStmt->get_result();
                $receivers = [];
                while($row = $receiverResult->fetch_assoc()){
                    $receivers[] = $row["UserID"];
                }
                $receiverStmt->close();
            } else {
                echo json_encode(["success"=>false, "error"=>"Failed to get receivers"]);
                exit();
            }
            foreach($receivers as $receiver){
                $insertReceiver = $conn->prepare("INSERT INTO receiver (NotificationID, UserID) VALUES (?, ?)");
                $insertReceiver->bind_param("ii", $notiID, $receiver);
                $insertReceiver->execute();
            }
            foreach($tableInclude as $table){
                $dltStmt = $conn->prepare("DELETE FROM $table WHERE TaskID = ?");
                $dltStmt->bind_param("i", $taskID);
                if($dltStmt->execute()){
                    $dltStmt->close();
                } else {
                    echo json_encode(["success"=>false, "error"=>"Error when deleting task in ".$table]);
                    exit();
                }
            }
            echo json_encode(["success"=>true, "deleted"=>$deleted, "failed"=>$failed]);
            exit();

        } 
        //if type == workspace
        else if ($type == "workspace"){
            //table: file in FileSharing folder, comment, fileshared, taskaccess, task, goal, workspacemember, workspace
            //flow: 
            // SELECT TaskID FROM task WHERE WorkSpaceID = id -> store in taskID[] -> FOR LOOP: SELECT FileID, Extension FROM fileshared WHERE TaskID = id -> combine FileID.Extension into an array -> for loop delete file in FileSharing folder  -> taskIncluded = [comment, fileshared, taskaccess, task] -> FOR LOOP(taskIncluded): FOR LOOP(taskID[]): DELETE FROM taskIncluded WHERE TaskID = taskID[] -> workspaceIncluded = [goal, workspacemember, workspace] -> FOR LOOP(workspaceIncluded): DELETE FROM workspaceIncluded WHERE WorkSpaceID = id
            $workspaceID = $id;
            
            //get taskID
            $taskID = [];
            $taskIDStmt = $conn->prepare("SELECT TaskID FROM task WHERE WorkSpaceID = ?");
            $taskIDStmt->bind_param("i", $workspaceID);
            if($taskIDStmt->execute()){
                $taskIDResult = $taskIDStmt->get_result();
                while($row = $taskIDResult->fetch_assoc()){
                    $taskID[] = $row["TaskID"];
                }
                $taskIDStmt->close();
            } else {
                echo json_encode(["success"=>false, "error"=>"Error when get taskID in Delete.php"]);
                exit();
            }

            $filepath = [];
            // get filename
            foreach($taskID as $id){
                $getFileStmt = $conn->prepare("SELECT FileID, Extension FROM fileshared WHERE TaskID = ?");
                $getFileStmt->bind_param("i", $id);
                if($getFileStmt->execute()){
                    $fileResult = $getFileStmt->get_result();
                    while($row = $fileResult->fetch_assoc()){
                        $filepath[] =  "../TaskPage/FileSharing/" . $row["FileID"] . "." . $row["Extension"];
                    }
                    $getFileStmt->close();
                } else {
                    echo json_encode(["success"=>false, "error"=>"Error when get file id"]);
                    exit();
                }
            }

            //delete file
            $deleted = 0;
            $failed = [];
            foreach($filepath as $file){
                if (file_exists($file)) {
                    if(unlink($file)){
                        $deleted++;
                    }  else {
                        $failed[] = $file;
                    }
                } else {
                    $failed[] = $file . " not found";
                }
            }
            //get workspace name
            $workspacenameStmt = $conn->prepare("SELECT Name FROM workspace WHERE WorkSpaceID = ?");
            $workspacenameStmt->bind_param("i", $workspaceID);
            if($workspacenameStmt->execute()){
                $workspacenameResult = $workspacenameStmt->get_result();
                if($workspacenameResult->num_rows==1){
                    $workspacenameRow = $workspacenameResult->fetch_assoc();
                    $workspacename = $workspacenameRow["Name"];
                    $workspacenameStmt->close();
                } else {
                    echo json_encode(["success"=>false, "error"=>"Error when getting workspace name"]);
                    exit();
                }
            } else {
                echo json_encode(["success"=>false, "error"=>"Failed to get workspace name"]);
                exit();

            }

            // notification
            $relatedID = $workspaceID;
            $relatedTable = "workspace";
            $title = "Workspace deleted";
            $desc = "The workspace: ". $workspacename . " has been deleted.";
            $insertNoti = $conn->prepare("INSERT INTO notification (RelatedID, RelatedTable, Title, Description) VALUES (?, ?, ?, ?)");
            $insertNoti->bind_param("isss", $relatedID, $relatedTable, $title, $desc);
            $insertNoti->execute();

            $notiID = $insertNoti->insert_id;

            //receivers
            $receiverStmt = $conn->prepare("SELECT UserID FROM workspacemember WHERE WorkSpaceID = ?");
            $receiverStmt->bind_param("i", $workspaceID);
            if($receiverStmt->execute()){
                $receiverResult = $receiverStmt->get_result();
                $receivers = [];
                while($row = $receiverResult->fetch_assoc()){
                    $receivers[] = $row["UserID"];
                }
                $receiverStmt->close();
            } else {
                echo json_encode(["success"=>false, "error"=>"Failed to get receivers"]);
                exit();
            }
            foreach($receivers as $receiver){
                $insertReceiver = $conn->prepare("INSERT INTO receiver (NotificationID, UserID) VALUES (?, ?)");
                $insertReceiver->bind_param("ii", $notiID, $receiver);
                $insertReceiver->execute();
            }
            //dlt related table
            $taskIncluded = ["comment", "fileshared", "taskaccess", "task"];
            if(!empty($taskID)){
                $taskIDPlaceholder = implode(',', array_fill(0, count($taskID), '?'));
                $types = str_repeat('i', count($taskID));

                foreach($taskIncluded as $table){
                    $dltStmt = $conn->prepare("DELETE FROM $table WHERE TaskID IN ($taskIDPlaceholder)");
                    $dltStmt->bind_param($types, ...$taskID);
                    if($dltStmt->execute()){
                        //success
                    } else {
                        echo json_encode(["success"=>false, "error"=>"Error when deleting task in ".$table]);
                        exit();
                    }
                    $dltStmt->close();
                }
            }

            $workspaceIncluded = ["goal", "workspacemember", "workspace"];
            foreach($workspaceIncluded as $table){
                $dltStmt = $conn->prepare("DELETE FROM $table WHERE WorkSpaceID = ?");
                $dltStmt->bind_param("i", $workspaceID);
                if($dltStmt->execute()){
                    $dltStmt->close();
                } else {
                    echo json_encode(["success"=>false, "error"=>"Error when deleting workspace related table: ".$table]);
                    exit();
                }
            }

            echo json_encode(["success"=>true, "deleted"=>$deleted, "failed"=>$failed]);
            exit();

        }
    }
?> 