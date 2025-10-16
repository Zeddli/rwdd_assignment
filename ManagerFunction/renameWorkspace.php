<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include "../Database/Database.php";
    header('Content-Type: application/json');
    //id: id,
    //type: type
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $workspaceID = filter_input(INPUT_POST, 'workspaceID', FILTER_SANITIZE_NUMBER_INT);
        $newName = htmlspecialchars(strip_tags($_POST['newName']), ENT_QUOTES, 'UTF-8');

        $originalNameStmt = $conn->prepare("SELECT Name FROM workspace WHERE WorkSpaceID = ?");
        $originalNameStmt->bind_param("i", $workspaceID);  
        if($originalNameStmt->execute()){
            $originalNameResult = $originalNameStmt->get_result();
            if($originalNameResult->num_rows==1){
                $originalNameRow = $originalNameResult->fetch_assoc();
                $originalName = $originalNameRow["Name"];
                $originalNameStmt->close();
            } else {
                echo json_encode(["success"=>false, "error"=>"Error when getting original workspace name"]);
                exit();
            }
        } else {
            echo json_encode(["success"=>false, "error"=>"Failed to get original workspace name"]);
            exit();

        }
    
        $stmt = $conn->prepare("UPDATE workspace SET Name = ? WHERE WorkSpaceID = ?");
        $stmt->bind_param("si", $newName, $workspaceID);
        if($stmt->execute()){
            //notifications
            if($originalName != $newName){
                $relatedID = $workspaceID;
                $relatedTable = "workspace";
                $title = "Workspace renamed";
                $desc = "The workspace: ". $originalName . " has been renamed to: " . $newName;
                $insertNoti = $conn->prepare("INSERT INTO notification (RelatedID, RelatedTable, Title, Description) VALUES (?, ?, ?, ?)");
                $insertNoti->bind_param("isss", $relatedID, $relatedTable, $title, $desc);
                if(!$insertNoti->execute()){
                    echo json_encode(value: ["success"=>false, "error"=>"Failed to insert notification"]);
                    exit();
                }

                //receiver
                $getMemberStmt = $conn->prepare("SELECT UserID FROM workspacemember WHERE WorkSpaceID = ?");
                $getMemberStmt->bind_param("i", $workspaceID);
                if($getMemberStmt->execute()){
                    $memberResult = $getMemberStmt->get_result();
                    $receivers = [];
                    while($memberRow = $memberResult->fetch_assoc()){
                        $receivers[] = $memberRow["UserID"];
                        //insert receiver
                        $notiID = $insertNoti->insert_id;
                        $insertReceiver = $conn->prepare("INSERT INTO receiver (NotificationID, UserID) VALUES (?, ?)");
                    }
                    foreach($receivers as $receiver){
                        $insertReceiver->bind_param("ii", $notiID, $receiver);
                        $insertReceiver->execute();
                    }
                    $getMemberStmt->close();
                    echo json_encode(value: ["success"=>true]);
                    exit();
                } else {
                    echo json_encode(["success"=>false, "error"=>"Failed to get workspace members"]);
                    exit();
                }
            } else {
                echo json_encode(value: ["success"=>false, "error"=>"Workspace name is the same as the original name"]);
                exit();
            }
        } else {
            echo json_encode(value: ["success"=>false, "error"=>"Failed to execute rename stmt"]);
            exit();
        }
    }
?>