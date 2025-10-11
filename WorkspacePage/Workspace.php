<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
        include "../Head/Head.php";
        // if(!isset($_SESSION["workspaceID"])){
        //     header("Location: ../HomePage/Home.php");
        // }
    ?>
    
    <link rel="stylesheet" href="../Navbar/styles/base.css">
    <link rel="stylesheet" href="../Navbar/styles/navbar.css"> 
    <link rel="stylesheet" href="Workspace.css">    
</head>
<body>
    <?php include "../Navbar/navbar.php"; ?>

    <!-- main content -->
    <div class="main-content">
        <p>hhahahaha</p>
    </div>

    <!-- navbar -->
    <script src="../Navbar/scripts/core.js"></script>                      <!-- Global state and DOM cache -->
    <script src="../Navbar/scripts/delete.js"></script>                    <!-- Delete functionality -->
    <script src="../Navbar/scripts/dropdowns.js"></script>                 <!-- Dropdown menu functionality -->
    <script src="../Navbar/scripts/editing.js"></script>                   <!-- Inline rename functionality -->
    <script src="../Navbar/scripts/inviteMember.js"></script>             <!-- Invite member functionality -->
    <script src="../Navbar/scripts/workspaces.js"></script>                <!-- Workspace creation/management -->
    <script src="../Navbar/scripts/tasks.js"></script>                     <!-- Task operations -->
    <script src="../Navbar/scripts/sidebar.js"></script>                   <!-- Main sidebar functionality -->
    <script src="../Navbar/scripts/main.js"></script>                      <!-- Entry point that starts everything -->

</body>
</html>