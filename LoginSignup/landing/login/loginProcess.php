<?php
    include "../../database/database.php";

    // $_SESSION["emailError"] = "*Email Do Not Exists"
    // $_SESSION["email"] = $_POST["email"]   
    // $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL); //will return empty
    // $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
    // header("Location: login.php");
    // hased password
    // setCookie
    // Landing Page read Cookie
    // verify hased password

    // delete cookie after logout or change password
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        // if(isset($_COOKIE["loginInfo"])){
        //     //from landing page
        //     $info = json_decode($_COOKIE["loginInfo"]);
        //     $email = $info["email"];
        //     $password = $info["password"];

        //     //checking for password only
        //     $selectEmailQuery = "SELECT * FROM user WHERE Email='$email'";
        //     $result = mysqli_query($conn, $selectEmailQuery);

        //     $row = mysqli_fetch_assoc($result);
        //     if(!password_verify($password, $row["HasedPaword"])){
        //         //wrong pass
        //         header("Location: ../landing.html");
        //         exit();
        //     }
        //     mysqli_close($conn);
        //     header("../../home/home.html");
        // }
        // else{
            session_start();
            //from login form
            //get input
            $email = filter_input(INPUT_POST,"email", FILTER_VALIDATE_EMAIL);
            $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);

            //set input
            $_SESSION["email"] = $email;
            $_SESSION["password"] = $password;

            //checking valid email
            if(empty($email)) {
                $_SESSION["emailError"] = "*Invalid Email";
                header("Location: login.php");
                exit();
            }

            // Checking exits email and correct password
            $selectEmailQuery = "SELECT * FROM user WHERE Email='$email'";
            $result1 = mysqli_query($conn, $selectEmailQuery);
            if(mysqli_num_rows($result1) == 0) {
                //do not exist
                $_SESSION["emailError"] = "*Email does not exist";
                header("Location: login.php");
                exit();
            }
            // pass email check
            $_SESSION["emailError"] = "";

            //checking password
            $row = mysqli_fetch_assoc($result1);
            if(password_verify($password, $row["HasedPassword"])){
                //correct pass
                //setCookie
                $cookieValue = [
                    "email" => $row["Email"],
                    "username" => $row["Username"],
                    "pictureName" => $row["PictureName"],
                    "picturePath" => $row["PicturePath"]
                ];
                setcookie("loginInfo", json_encode($cookieValue), time() + (86400 * 30), "/");
                mysqli_close($conn);
                unset($_SESSION["password"]);
                $_SESSION["userInfo"] = [
                    "email" => $row["Email"],
                    "username" => $row["Username"],
                    "pictureName" => $row["PictureName"],
                    "picturePath" => $row["PicturePath"]
                ];
                header("Location: ../../home/home.php");
            } else{
                //wrong pass
                $_SESSION["passwordError"] = "*Wrong Password";
                header("Location: login.php");
                exit();
            }
            

        }
    // }
?>