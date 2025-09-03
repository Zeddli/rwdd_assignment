<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        include("../../head/head.php");
    ?>
    <link rel="stylesheet" href="signup.css">
</head>
<body>
    <div class="logo">
        <img src="../../assets/logo.png" alt="">
        <h1>ProTask</h1>
    </div>

    <div class="container">
        <h2>Sign Up</h2>
        <form id="signupForm" action="signupProcess.php" method="post">
            <label for="email">Email <span id="emailAlert" style="color:red; font-size:20px;"><?php if(isset($_SESSION["emailError"])){echo $_SESSION["emailError"];}?></span></label><br>
            <input type="email" id="email" name="email" value="<?php if(isset($_SESSION["email"])){echo $_SESSION["email"];}?>" required><br>
            <label for="username">Username <span id="usernameAlert" style="color:red; font-size:20px;"><?php if(isset($_SESSION["usernameError"])){echo $_SESSION["usernameError"];}?></span></label><br>
            <input type="text" id="username" name="username" value="<?php if(isset($_SESSION["username"])){echo $_SESSION["username"];}?>" required><br>
            <label for="password">Password <span id="passwordAlert" style="color:red; font-size:20px;"></span></label><br>
            <input type="password" id="password" name="password" value="<?php if(isset($_SESSION["password"])){echo $_SESSION["password"];}?>" required><br>
            <p>Already have an account? Proceed to <a href="../Login/login.php">Login</a></p><br>
            <input type="submit" value="Sign Up">
        </form>
    </div>
    <script>
        document.getElementById("signupForm").addEventListener("submit", (e) =>{
            let email = document.getElementById("email").value;
            let username = document.getElementById("username").value;
            let password = document.getElementById("password").value;

            let emailAlert = document.getElementById("emailAlert");
            let usernameAlert = document.getElementById("usernameAlert");
            let passwordAlert = document.getElementById("passwordAlert");            

            if (!email || !email.includes("@")){
                e.preventDefault();
                emailAlert.textContent = "*Invalid Email";
            }
            if (!username){
                e.preventDefault();
                usernameAlert.textContent = "*Invalid Username";
            }    
            if (!password) {
                e.preventDefault();
                passwordAlert.textContent = "*Enter Your Password";
            }        
        })
    </script>
</body>
</html>