<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include "../Database/Database.php";
    header('Content-Type: application/json');

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        // check is from task or workspace
        // tableID: id,
        // type: type,
        // userID: member.UserID

        //flow:
        // type == task:
        // table included: taskaccess
        // delete from taskaccess where userid = userID and taskid = tableid

        // type == workspace
        //  table included: taskacess workspacemember
        //  get taskid from task where workspaceid = tableID -> for loop the task iD: delete from taskaccess where userID = userID and TaskID = taskID -> delete from workspace member where workspaceid = tableID and userID = userID

        $tableID = filter_input(INPUT_POST, 'tableID', FILTER_SANITIZE_NUMBER_INT);
        $type = htmlspecialchars(strip_tags($_POST['type']), ENT_QUOTES, 'UTF-8');
        $userID = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_NUMBER_INT);

        if($type == "task"){
            $stmt = $conn->prepare("DELETE FROM taskaccess WHERE UserID = ? and TaskID = ?");
            $stmt->bind_param("ii", $userID, $tableID);
            if($stmt->execute()){
                echo json_encode(["success"=>true]);
                exit();
            } else {
                echo json_encode(["success"=>false, "error"=>"Failed to kick member"]);
                exit();
            }
        }
        else if($type == "workspace"){
            $taskIDStmt = $conn->prepare("SELECT TaskID FROM task WHERE WorkSpaceID = ?");
            $taskIDStmt->bind_param("i", $tableID);
            if($taskIDStmt->execute()){
                $taskIDResult = $taskIDStmt->get_result();
                $taskID = [];
                while($row = $taskIDResult->fetch_assoc()){
                    $taskID[] = $row["TaskID"];
                }
                $taskIDStmt->close();
            } else {
                echo json_encode(["success"=>false, "error"=>"Failed to get task id"]);
                exit();
            }

            //after get taskID
            // dlt taskaccess
            foreach ($taskID as $id){
                $taskaccessDltStmt = $conn->prepare("DELETE FROM taskaccess WHERE UserID = ? and TaskID = ?");
                $taskaccessDltStmt->bind_param("ii", $userID, $id);
                if($taskaccessDltStmt->execute()){
                    // delete success
                    $taskaccessDltStmt->close();
                } else {
                    echo json_encode(["success"=>false, "error"=>"Failed to execute deletion at taskaccess"]);
                    exit();
                }
            }

            // dlt workspace
            $workspacememberDltStmt = $conn->prepare("DELETE FROM workspacemember WHERE WorkSpaceID = ? and UserID = ?");
            $workspacememberDltStmt->bind_param("ii", $tableID, $userID);
            if($workspacememberDltStmt->execute()){
                echo json_encode(["success"=>true, "rowAffected"=>$workspacememberDltStmt->affected_rows]);
                exit();
            } else{
                echo json_encode(["success"=>false, "error"=>"Failed to execute deletion at workspacemember"]);
                exit();
            }
        }
        else {
            echo json_encode(["success"=>false, "error"=>"Invalid Type"]);
            exit();
        }

    }
?>