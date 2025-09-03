<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        include("../../head/head.php");
    ?>
    <link rel="stylesheet" href="login.css">
    
</head>
<body>
    <div class="logo">
        <img src="../../assets/logo.png" alt="">
        <h1>ProTask</h1>
    </div>

    <div class="container">
        <h2>Log In</h2>
        <form id="loginForm" action="loginProcess.php" method="post">
            <label for="email">Email <span id="emailAlert" style="color:red; font-size:20px;"><?php if(isset($_SESSION["emailError"])){echo $_SESSION["emailError"];}?></span></label><br>
            <input type="email" id="email" name="email" value="<?php if(isset($_SESSION["email"])){echo $_SESSION["email"];}?>" required><br>
            <label for="password">Password <span id="passwordAlert" style="color:red; font-size:20px;"><?php if(isset($_SESSION["passwordError"])){echo $_SESSION["passwordError"];}?></span></label><br>
            <input type="password" id="password" name="password" value="<?php if(isset($_SESSION["password"])){echo $_SESSION["password"];}?>" required><br>
            <p>Do not have an account? Proceed to <a href="../Signup/signup.php">Sign up</a></p><br>
            <input type="submit" value="LOGIN">
        </form>
    </div>
    <script>
        document.getElementById("loginForm").addEventListener("submit", (e) =>{
            let email = document.getElementById("email").value;
            let password = document.getElementById("password").value;

            let emailAlert = document.getElementById("emailAlert");
            let passwordAlert = document.getElementById("passwordAlert");            

            if (!email || !email.includes("@")){
                e.preventDefault();
                emailAlert.textContent = "*Invalid Email";
            }  
            if (!password) {
                e.preventDefault();
                passwordAlert.textContent = "*Enter Your Password";
            }        
        })
    </script>
</body>
</html>