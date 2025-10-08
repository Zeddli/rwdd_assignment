<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include "../Database/Database.php";
    header('Content-Type: application/json');

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $userID = filter_input(INPUT_POST, 'userID', FILTER_SANITIZE_NUMBER_INT);
        // will be in taskID or workspace ID
        $taskID = filter_input(INPUT_POST, 'taskID', FILTER_SANITIZE_NUMBER_INT);
        $workspaceID = filter_input(INPUT_POST, 'workspaceID', FILTER_SANITIZE_NUMBER_INT);
        $userRole = "Manager";
        
        if(empty($workspaceID)){
            // get workspace id use taskID then update workspace member where workspaceid and userid same
            $workspaceIdStmt = $conn->prepare("SELECT WorkSpaceID FROM task WHERE TaskID = ?");
            $workspaceIdStmt->bind_param("i", $taskID);
            if($workspaceIdStmt->execute()){
                $workspaceResult = $workspaceIdStmt->get_result();
                if($workspaceResult->num_rows==1){
                    $workspaceRow = $workspaceResult->fetch_assoc();
                    $workspaceID = $workspaceRow["WorkSpaceID"];
                    $workspaceIdStmt->close();
                } else {
                    echo json_encode(["success"=>false, "error"=>"Error when getting workspace ID"]);
                    exit();
                }
            } else {
                echo json_encode(["success"=>false, "error"=>"Failed to get workspace ID"]);
                exit();
            }
        }

        // update workspacemember
        $stmt = $conn->prepare("UPDATE workspacemember SET UserRole = ? WHERE WorkSpaceID = ? and UserID = ?");
        $stmt->bind_param("sii", $userRole, $workspaceID, $userID);
        if($stmt->execute()){
            echo json_encode(["success" => true]);
            exit();
        } else {
            echo json_encode(["success" => false, "error" => "Failed to update table"]);
            exit();
        }
    }

    $stmt->close();
    $conn->close();
?>