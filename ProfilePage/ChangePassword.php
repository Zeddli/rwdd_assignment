<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include "../Database/Database.php";
    header('Content-Type: application/json');
    session_start();

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $currentPassword = htmlspecialchars(strip_tags($_POST['currentPassword']), ENT_QUOTES, 'UTF-8');
        $newPassword = htmlspecialchars(strip_tags($_POST['newPassword']), ENT_QUOTES, 'UTF-8');
        $userID = $_SESSION["userInfo"]["userID"];

        //current pass in db
        $getCurrent = $conn->prepare("SELECT HasedPassword FROM user WHERE UserID = ?");
        $getCurrent->bind_param("i", $userID);
        $getCurrent->execute();
        $result = $getCurrent->get_result();
        if($result->num_rows === 0){
            echo json_encode(["success"=>false, "error" => "User not found"]);
            exit();
        }
        $row = $result->fetch_assoc();
        $hashedPassword = $row['HasedPassword'];
        // check current password
        if(!password_verify($currentPassword, $hashedPassword)){
            echo json_encode(["success"=>false, "error" => "Current password is incorrect"]);
            exit();
        }

        // hash new password
        $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE user SET HasedPassword = ? WHERE UserID = ?");
        $stmt->bind_param("si", $newHashedPassword, $userID);
        if($stmt->execute()){
            echo json_encode(["success"=>true]);
            exit();
        } else {
            echo json_encode(["success"=>false, "error" => "Failed to update password"]);
            exit();
        }
    }

?>