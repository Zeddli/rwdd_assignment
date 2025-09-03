<?php
    $db_server = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "assignment";
    $conn = "";

    try {
        $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
        echo "<script>alert('Connected')</script>";
    }
    catch (mysqli_sql_exception) {
        echo "<script>alert('Database is not connected')</script>";
    }



?>