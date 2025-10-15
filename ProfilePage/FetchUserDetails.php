<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include "../Database/Database.php";
    header('Content-Type: application/json');
    session_start();

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        $userID = $_SESSION["userInfo"]["userID"];

        $stmt = $conn->prepare("SELECT Email, Username, PictureName FROM user WHERE UserID = ?");
        $stmt->bind_param("i", $userID);
        if($stmt->execute()){
            $result = $stmt->get_result();
            if($result->num_rows === 1){
                $userDetails = $result->fetch_assoc();
                if($userDetails["PictureName"] === null) {
                    $userDetails["PictureName"] = "anonymous.jpg";
                }
                echo json_encode(["success"=>true, "user"=>$userDetails]);
                exit();
            } else {
                echo json_encode(["success"=>false, "error" => "User not found"]);
                exit();
            }
        } else {
            echo json_encode(["success"=>false, "error" => "Failed to fetch user details"]);
            exit();
        }
    }
?>