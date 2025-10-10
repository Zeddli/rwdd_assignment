<!-- filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS); -->
<!-- If no comment return "No Comment" -->

<!-- expecting the all comment with taskID 1 -->
<!-- CommentID UserID TaskID Comment CreatedAt Username PictureName PicturePath -->
<!-- Calendar main view with To-Do Sidebar -->
<?php
session_start();
// Check if user is logged in

?>

<?php
    header('Content-Type: text/event-stream'); //server sent event
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');
    ob_end_flush(); 
    flush(); //send header first

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    include "../Database/Database.php";

    $TaskID = $_SESSION['taskID'];
    $currentID = 0;
    $newID = 0;
    $query = "SELECT comment.CommentID, comment.UserID, comment.Comment, comment.CreatedAt, user.Username, user.PictureName 
            FROM comment 
            JOIN user ON comment.UserID = user.UserID
            WHERE TaskID=$TaskID 
            ORDER BY CreatedAt DESC";

    $result = mysqli_query($conn, $query);

    // if ($result === false) {
    //     // Send error as SSE event
    //     echo "event: error\n";
    //     echo "data: " . json_encode(["error" => mysqli_error($conn)]) . "\n\n";
    //     flush();
    //     sleep(2);
    // }

    $comments = [];
    while($row = mysqli_fetch_assoc($result)) {
        $comments[] = $row;
    }
    if(!empty($comments)){
        $newID = $comments[0]['CommentID'];
        if($newID != $currentID){
            $currentID = $newID;
            echo "data: " . json_encode($comments) . "\n\n";
            flush(); //send data to client
        }
    }
    else {
        // Always send at least an empty array so client can clear UI if needed
        echo "data: []\n\n";
        flush();
    }
    // sleep(2);

    mysqli_close($conn);    
?>