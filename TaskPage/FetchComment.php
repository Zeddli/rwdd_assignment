<!-- 需要filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS); -->
<!-- If no comment return "No Comment" -->



<!-- expecting the all comment with taskID 1 -->
<!-- CommentID UserID TaskID Comment CreatedAt Username PictureName PicturePath -->


<?php
    header('Content-Type: text/event-stream'); //this is server sent event
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');
    ob_end_flush(); 
    flush(); //send header first

    include "../Database/Database.php";

    $TaskID = 1; //CHANGE!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    $currentID = 0;
    $newID = 0;
    while (true) {
        $query = "SELECT comment.CommentID, comment.UserID, comment.Comment, comment.CreatedAt, user.Username, user.PictureName, user.PicturePath 
                FROM comment 
                JOIN user ON comment.UserID = user.UserID
                WHERE TaskID=$TaskID 
                ORDER BY CreatedAt DESC";
        $result = mysqli_query($conn, $query);

        $comments = [];
        while($row = mysqli_fetch_assoc($result)) {
            $comments[] = $row;
        }
        if(!empty($comments)){
            $newID = $comments[0]['CommentID'];
        }

        if($newID != $currentID){
            $currentID = $newID;
            echo "data: " . json_encode($comments) . "\n\n";
            flush(); //send data to client
            sleep(2); // wait 2s before next push
        }
    }
?>