<?php
    session_start();
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include "../Database/Database.php";
    header('Content-Type: application/json');

    if($_SERVER["REQUEST_METHOD"] === "POST") {
        $userInfo = json_decode($_COOKIE["loginInfo"],true);
        $userID = $userInfo["userID"];
        //get workspaceId from taskID
        if (isset($_POST["workspaceID"])) {
            // Direct workspace permission check
            $workspaceId = intval($_POST["workspaceID"]);
        } elseif (isset($_POST["taskID"])) {
            // Get workspaceID from taskID
            $getWorkspaceStmt = $conn->prepare("SELECT WorkSpaceID FROM task WHERE TaskID = ?");
            $getWorkspaceStmt->bind_param("i", $_POST["taskID"]);
            if ($getWorkspaceStmt->execute()) {
                $result = $getWorkspaceStmt->get_result();
                if ($result->num_rows === 1) {
                    $workspaceId = $result->fetch_assoc()["WorkSpaceID"];
                } else {
                    echo json_encode(["success" => false, "error" => "No such task"]);
                    $getWorkspaceStmt->close();
                    $conn->close();
                    exit();
                }
            } else {
                echo json_encode(["success" => false, "error" => "Failed to execute"]);
                $getWorkspaceStmt->close();
                $conn->close();
                exit();
            }
            $getWorkspaceStmt->close();
        } else {
            echo json_encode(["success" => false, "error" => "No workspaceID or taskID provided"]);
            $conn->close();
            exit();
        }

        // check permission for the workspace
        $stmt = $conn->prepare("SELECT UserRole FROM workspacemember
                                JOIN task ON workspacemember.WorkSpaceID = task.WorkSpaceID
                                WHERE workspacemember.UserID = ? AND workspacemember.WorkSpaceID = ?");
        $stmt->bind_param("ii", $userID, $workspaceId);
        if($stmt->execute()){
            $result = $stmt->get_result();
            $row = $result->fetch_assoc(); //fetch the first row
            if(isset($row["UserRole"])){
                // got the role
                $role = $row["UserRole"];
                if($role === "Manager"){
                    echo json_encode(["success" => true]);
                }
                else{
                    echo json_encode(["success" => false, "error" => "You are not manager"]);
                    exit();
                }
            }
            else{
                // got no result or more than 1 result
                echo json_encode(["success" => false, "error" => "No such user in workspace"]);
                exit();
            }
            
        }
        else{
            // failed
            echo json_encode(["success" => false, "error" => "Failed to execute"]);
            exit();
        }
    }
    $stmt->close();
    $conn->close();
?>