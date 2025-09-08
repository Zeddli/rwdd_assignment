<?php
    // session_start();
    // if(isset($_COOKIE["loginInfo"])){
    //     $info = json_decode($_COOKIE["loginInfo"],true);
    //     $_SESSION["userInfo"] = $info;
    //     header("Location: ../HomePage/home.php");
    //     exit();
    // }
    // redirect的只有在login， landing不用，test session 有没有问题，redirect 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/RWDD_ASSIGNMENT/Assets/logo.png">
    <title>ProTask</title>
</head>
<body>
    <img src="../Assets/logo.png" alt="ProTask Logo">
    <h1>Welcome to ProTask</h1>
    <button onclick="window.location.href='../LoginSignup/landing/login/login.php'">Log In</button>
</body>
</html>
