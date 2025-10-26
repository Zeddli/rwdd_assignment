<?php
    session_start();
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include "../Database/Database.php";
    // delete original profile picture if not anonymous

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        if(!isset($_FILES["file"]) || $_FILES["file"]["error"] !== UPLOAD_ERR_OK){
            echo json_encode(["success" => false, "error"=> "Invalid file upload"]);
            exit();
        }

        $userID = $_SESSION["userInfo"]["userID"];

        // Fetch current profile picture name
        $oldProfileStmt = $conn->prepare("SELECT PictureName FROM user WHERE UserID = ?");
        $oldProfileStmt->bind_param("i", $userID);
        if($oldProfileStmt->execute()){
            $oldProfileResult = $oldProfileStmt->get_result();
            $oldProfileName = $oldProfileResult->num_rows === 1? $oldProfileResult->fetch_assoc() : null;
        } else {
            echo json_encode(["success" => false, "error"=> "Failed to get old profile picture"]);
            exit();
        }

        // delete old profile picture file
        $uploadDir = __DIR__ . "/../Assets/ProfilePic/";
        if($oldProfileName){
            $filepath = $uploadDir . $oldProfileName["PictureName"];
            // if exists and not null
            if (isset($oldProfileName["PictureName"]) && file_exists($filepath) && $oldProfileName["PictureName"] !== "anonymous.jpg") {
                unlink($filepath);
            }
        }

        // update new profile picture to database and server
        $originalName = basename($_FILES["file"]["name"]); //with extension
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $newName = $userID . ($extension? ".". $extension : "");

        $stmt = $conn->prepare("UPDATE user SET PictureName = ? WHERE UserID = ?");
        $stmt->bind_param("si", $newName, $userID);
        if($stmt->execute()){
            $uploadPath = $uploadDir . $newName;
            if(move_uploaded_file($_FILES["file"]["tmp_name"], $uploadPath)){
                echo json_encode(["success" => true]);
                exit();
            } else{
                // failed to upload
                echo json_encode(["success"=> false, "error"=> "Failed to move uploaded file"]);
                exit();
            }
        }
        else{
            // failed to update database
            echo json_encode(["success"=> false, "error"=> "Failed to update database"]);
            exit();
        }

        
    }
?>