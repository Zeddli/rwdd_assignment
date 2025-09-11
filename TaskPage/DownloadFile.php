<?php
    include "../Database/Database.php";

    //does not have id
    if (!isset($_GET['id'])) {
        http_response_code(400);
        exit('Missing file ID.');
    }
    $fileID = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT FileName, Extension FROM fileshared WHERE FileID = ?");
    $stmt->bind_param("i", $fileID);
    $stmt->execute();
    $stmt->bind_result($fileName, $extension);
    if (!$stmt->fetch()) {
        http_response_code(404);
        exit('File not found.');
    }
    $stmt->close();
    $serverFile = __DIR__ . "/FileSharing/{$fileID}" . ($extension ? ".{$extension}" : "");
    if (!file_exists($serverFile)) {
        http_response_code(404);
        exit('File not found on server.');
    }
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $fileName . ($extension ? ".{$extension}" : "") . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($serverFile));
    readfile($serverFile);
    exit;
?>