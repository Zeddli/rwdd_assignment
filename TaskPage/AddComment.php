<?php
// <!-- 需要filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS); -->
// <!-- fetch 多一轮comment刷新 -->
// <!-- expecting the all comment with taskID 1 -->
// .success set to true
    include "../Database/Database.php";
    session_start();

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        //  UserID TaskID Comment CreatedAt
        $comment = filter_input(INPUT_POST, "comment", FILTER_SANITIZE_SPECIAL_CHARS);
        $taskID = $_POST["taskID"];
        $userID = $_SESSION["userInfo"]["userID"];

        if(empty($comment)){
            echo json_encode(["success" => false]);
            exit();
        }

        $query = "INSERT INTO comment (UserID, TaskID, Comment) VALUES ('$userID', '$taskID', '$comment')";
        if(mysqli_query($conn, $query)){
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false]);
        }
    }
    mysqli_close($conn);
?>