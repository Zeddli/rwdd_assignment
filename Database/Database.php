<?php
    $db_server = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "assignment";

    global $conn;
    try {
        $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
    }
    catch (mysqli_sql_exception) {
        // Do not echo anything here to keep API responses as valid JSON
        $conn = null;
    }
?>