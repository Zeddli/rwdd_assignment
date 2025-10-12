<?php
    session_start();
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include "../Database/Database.php";
    header('Content-Type: application/json');

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $workspaceID = $_SESSION["workspaceID"];

        $stmt = $conn->prepare("SELECT * FROM workspace
                                       JOIN user ON workspace.UserID = user.UserID
                                       WHERE WorkSpaceID = ?");
        $stmt->bind_param("i", $workspaceID);
        if($stmt->execute()){
            $result = $stmt->get_result();
            $workspace = $result->fetch_assoc();
            echo json_encode(["success"=>true, "workspace"=>$workspace]);
            exit();
        } else {
            echo json_encode(["success"=>false, "error"=>"Failed to execute fetchworkspace"]);
            exit();
        }
    }
?>