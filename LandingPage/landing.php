<?php
    session_start();
    if(isset($_COOKIE["loginInfo"])){
        $info = json_decode($_COOKIE["loginInfo"],true);
        $_SESSION["userInfo"] = $info;
        header("Location: ../HomePage/home.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        include("../Head/Head.php");
    ?>
</head>
<body>
    <img src="../Assets/logo.png" alt="ProTask Logo">
    <h1>Welcome to ProTask</h1>
    <button onclick="window.location.href='../LoginSignup/landing/login/login.php'">Log In</button>
</body>
</html>
