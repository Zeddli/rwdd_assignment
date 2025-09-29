<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/RWDD_ASSIGNMENT/Assets/logo.png">
    <title>ProTask</title>
</head>
</html>
<?php
    session_start();
    //if no session for page inside, check its cookie
    if(!isset($_SESSION["userInfo"])) {
        if(isset($_COOKIE["loginInfo"])){
            // if have cookie, set session
            $info = json_decode($_COOKIE["loginInfo"],true);
            $_SESSION["userInfo"] = $info;
        } else {
            // no cookie, go to landing page
            header("Location: /RWDD_ASSIGNMENT/LandingPage/landing.php");
            exit();
        }
    }

?>