<?php
    session_start();
    echo "Username: ". $_SESSION["userInfo"]["username"]. "<br>";
    echo "Email: ". $_SESSION["userInfo"]["email"] . "<br>";
    echo "Picture Path: ". $_SESSION["userInfo"]["picturePath"] . "<br>";
    echo "Picture Name: ". $_SESSION["userInfo"]["pictureName"] . "<br>";
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <p>this is home page</p>
</body>
</html>