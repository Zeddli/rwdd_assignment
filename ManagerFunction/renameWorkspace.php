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
    
        $stmt = $conn->prepare("UPDATE workspace SET Name = ? WHERE WorkSpaceID = ?");
        $stmt->bind_param("si", $newName, $workspaceID);
        if($stmt->execute()){
            echo json_encode(value: ["success"=>true]);
            exit();
        } else {
            echo json_encode(value: ["success"=>false, "error"=>"Failed to execute rename stmt"]);
            exit();
        }
    }
?>