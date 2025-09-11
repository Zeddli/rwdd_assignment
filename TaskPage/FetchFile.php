<!-- show file with filename, fetch using filePath -->
<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');
    ob_end_flush();
    flush();

    include "../Database/Database.php";
    $TaskID = 1; //CHANGE!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    $currentID = 0;

    $stmt = $conn->prepare("SELECT fileshared.FileID, fileshared.UserID, fileshared.TaskID, fileshared.FileName, fileshared.Extension, fileshared.CreatedAt, user.Username
                            FROM fileshared
                            JOIN user ON fileshared.UserID = user.UserID
                            WHERE fileshared.TaskID = ?
                            ORDER BY fileshared.CreatedAt DESC");
    $stmt->bind_param("i", $TaskID);

    while (true) {
        $stmt->execute();
        $result = $stmt->get_result();

        $files = [];
        while ($row = $result->fetch_assoc()) {
            $files[] = $row;
        }

        if (!empty($files)) {
            $newID = $files[0]['FileID'];
            if ($currentID != $newID) {
                $currentID = $newID;
                echo "data: " . json_encode($files) . "\n\n";
                flush();
            }
        } else {
            // Always send at least an empty array so client can clear UI if needed
            echo "data: []\n\n";
            flush();
        }
        sleep(2);
    }
    $stmt->close();
    $conn->close();
?>