<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include "../Database/Database.php";
    header('Content-Type: application/json');
    session_start();

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $newUsername = htmlspecialchars(strip_tags($_POST['newUsername']), ENT_QUOTES, 'UTF-8');
        $userID = $_SESSION["userInfo"]["userID"];

        $oriName = $_SESSION["userInfo"]["username"];

        $checkUsername = $conn->prepare("SELECT Username FROM user");
        $checkUsername->execute();
        $result = $checkUsername->get_result();
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                if($row['Username'] === $newUsername && $newUsername !== $oriName){
                    echo json_encode(["success"=>false, "error" => "Username already taken"]);
                    exit();
                }
            }
        }

        $stmt = $conn->prepare("UPDATE user SET Username = ? WHERE UserID = ?");
        $stmt->bind_param("si", $newUsername, $userID);
        if($stmt->execute()){
            echo json_encode(["success"=>true]);
            exit();
        } else {
            echo json_encode(["success"=>false, "error" => "Failed to update username"]);
            exit();
        }
    }
?>