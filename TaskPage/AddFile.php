 <?php
//  <!-- filename save as user upload, file path save with fileID and extension -->
    session_start();
    include "../Database/Database.php";

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        if(!isset($_FILES["file"]) || $_FILES["file"]["error"] !== UPLOAD_ERR_OK){
            echo json_encode(["success" => false]);
            exit();
        }

        // Check if any error occurred
        if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            switch ($_FILES['file']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    $message = "File is too large. Please upload a smaller file.";
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $message = "File is too large. Please upload a smaller file.";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $message = "File was only partially uploaded. Try again.";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $message = "No file was uploaded.";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $message = "Missing a temporary folder on the server.";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $message = "Failed to write file to disk.";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $message = "A PHP extension stopped the upload.";
                    break;
                default:
                    $message = "Unknown upload error occurred.";
            }

            echo json_encode(["success" => false, "error" => $message]);
            exit;
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