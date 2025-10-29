<?php
    include "../Database/Database.php";

    //does not have id
    if (!isset($_GET['id'])) {
        echo json_encode(["success" => false, "error"=> "Missing File ID"]);
        exit();
    }
    $fileID = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM fileshared WHERE FileID = ?");
    $stmt->bind_param("i", $fileID);
    $stmt->execute();
    // $stmt->bind_result($fileName, $extension);
    // if (!$stmt->fetch()) {
    //     echo json_encode(["success" => false, "error"=> "File not found in database"]);
    //     exit();
    // }
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        echo json_encode(["success" => false, "error"=> "File not found in database"]);
        exit();
    }
    $fileData = $result->fetch_assoc();
    $stmt->close();

    $serverFile = __DIR__ . "/FileSharing/{$fileData['FileID']}" . ($fileData['Extension']  ? ".{$fileData['Extension'] }" : "");
    if (!file_exists($serverFile)) {
        echo json_encode(["success" => false, "error" => "File not found on server"]);
        exit();
    }
    echo json_encode(["success" => true]);
    exit();
?>