<?php
    include("../../database/database.php");
    session_start();

    // $_SESSION["emailError"] = "*Email Exists"
    // $_SESSION["usernameError"] = "*Username Exists"
    // $_SESSION["email"] = $_POST["email"]   (previos email)
    // $_SESSION["username"] = $_POST["username"]
    // $_SESSION["password"] = $_POST["password"]
    // header("Location: signup.php");
    // hased password

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //get variable
        $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL); //will return empty
        $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
        $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);

        $_SESSION["email"] = $email;
        $_SESSION["username"] = $username;
        $_SESSION["password"] = $password;

        //checking valid email
        if(empty($email)) {
            $_SESSION["emailError"] = "*Invalid Email";
            header("Location: signup.php");
            exit();
        }

        //checking exits email / username
        $query =  "SELECT * from user WHERE Username='$username' or Email='$email'";
        $result = mysqli_query($conn, $query); 

        if(mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)){
                if($row["Email"] == $email){
                    $_SESSION["emailError"] = "*Email Exists";
                    header("Location: signup.php");
                    exit();
                }
                $_SESSION["emailError"] = ""; //clear after checking is valid
                if($row["Username"] == $username){
                    $_SESSION["usernameError"] = "*Username Exists";
                    header("Location: signup.php");
                    exit();
                }
                $_SESSION["usernameError"] = "";
            };
        }
        //if valid
        else{
            $hashedPass = password_hash($password, PASSWORD_DEFAULT);
            $insert = "INSERT INTO user (Username, Email, HasedPassword) VALUES ('$username', '$email', '$hashedPass') ";
            mysqli_query($conn, $insert);
            session_destroy();
            header("Location: ../login/login.php");
        }
    }

    

    mysqli_close($conn);
?>