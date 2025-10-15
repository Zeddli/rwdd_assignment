 <?php
//  <!-- filename save as user upload, file path save with fileID and extension -->
    session_start();
    include "../Database/Database.php";

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        if(!isset($_FILES["file"]) || $_FILES["file"]["error"] !== UPLOAD_ERR_OK){
            echo json_encode(["success" => false]);
            exit();
        }

        //UserID TaskID FileName CreatedAt 
        $userID = $_SESSION["userInfo"]["userID"];
        // $taskID = intval($_POST["taskID"]);
        $taskID = intval($_SESSION["taskID"]);
        $originalName = basename($_FILES["file"]["name"]); //with extension
        $filenameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $uploadDir = __DIR__ . "/FileSharing/";

        //insert
        $stmt = $conn->prepare("INSERT into fileshared (UserID, TaskID, FileName, Extension) 
                                VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $userID, $taskID, $filenameWithoutExt, $extension);
        if($stmt->execute()){
            $fileID = $conn->insert_id;
            $newFileName = $fileID . ($extension? ".". $extension : "");
            $uploadPath = $uploadDir . $newFileName;

            if(move_uploaded_file($_FILES["file"]["tmp_name"], $uploadPath)){
                echo json_encode(["success" => true]);
                exit();
            } else{
                // failed to upload
                $conn->query("DELETE FROM fileshared WHERE FileID=$fileID");
                echo json_encode(["success"=> false]);
                exit();
            }
        }
        else{
            // failed to insert
            echo json_encode(["success"=> false]);
            exit();
        }

    }
    $stmt->close();
    $conn->close();
    exit();
 ?>