<?php
    session_start();
    if(isset($_COOKIE["loginInfo"])){
        $info = json_decode($_COOKIE["loginInfo"],true);
        $_SESSION["userInfo"] = $info;
        header("Location: ../home/home.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../assets/logo.png">
    <title>ProTask</title>
</head>
<body>
    <img src="../assets/logo.png" alt="ProTask Logo">
    <h1>Welcome to ProTask</h1>
    <button onclick="window.location.href='login/login.php'">Log In</button>
</body>
</html>
