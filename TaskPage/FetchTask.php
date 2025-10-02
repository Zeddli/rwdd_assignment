<?php
    session_start();
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include "../Database/Database.php";
    header('Content-Type: application/json');


    if($_SERVER["REQUEST_METHOD"] === "POST"){
        $taskID = $_SESSION["taskID"];
        //check taskID
        if(!isset($taskID)){
            echo json_encode(["success" => false]);
            header("Location: ../HomePage/Home.php"); //if directly access
            exit();
        }

        $stmt = $conn->prepare("SELECT * from task
                                JOIN workspace ON task.WorkSpaceID = workspace.WorkSpaceID
                                WHERE TaskID = ?");
        $stmt->bind_param("i", $taskID);
        if($stmt->execute()){
            $result = $stmt->get_result();
            if($result->num_rows === 1){
                $task = $result->fetch_assoc();
                echo json_encode(["success"=> true, "task" => $task]);
            }
            else{
                // got no result or more than 1 result
                echo json_encode(["success"=> false]);
                exit();
            }
        }
        else{
            // failed
            echo json_encode(["success"=> false]);
            exit();
        }

        $stmt->close();
        $conn->close();
    }
?>