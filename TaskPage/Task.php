<!-- Need to get task ID -->
<!-- Need the task status to be in progress?? -->

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "../Head/Head.php"?>
    
    <link rel="stylesheet" href="../Navbar/base.css">
    <link rel="stylesheet" href="../Navbar/navbar.css"> 
    <link rel="stylesheet" href="Task.css">    
</head>
<body>
    <?php include "../Navbar/navbar.php"; ?>

    <div class="main-content">
        
        <div class="task-header">
            <h1 class="workspace">"WorkSpaces Name"</h1>
            <div class="sub-header">
                <span class="task">"Task Name"</span>  <!-- maybe require js to create this one for different colour based on the status -->
                <span class="status">"Status"</span>
                <span class="priority">"Priority"</span>
            </div>

        </div>
        <div class="description-section">
            <textarea readonly class="description-text">This is a detailed description of the task. It provides all the necessary information that the user needs to understand what the task entails. Lorem, ipsum dolor sit amet consectetur adipisicing elit. Possimus minima reiciendis enim aliquid, quos voluptates maiores. Nisi adipisci maiores ut. Fugiat rem voluptatibus necessitatibus magnam atque ab, saepe sequi illo.</textarea>
            <div class="time">
                <span id="start-time" class="start-time">
                    Start Time:
                    <span id="start">2024-01-01 10:00 AM</span>
                </span>
                <span id="deadline" class="deadline">
                    Deadline:
                    <span id="end">2024-01-01 10:00 AM</span>
                </span>
                
                <span class="countdown-time" id="countdown-time">
                    Time Left: 
                    <span class="countdown" id="countdown"></span>  
                </span>
                
            </div>
        </div>
        <div class="content-section">
            <div class="file-sharing-container">
                <div class="file" id="file">
                    "file-sharing-container"
                </div>
                
                <button id="new-file" class="new-file">Add A New File</button>
            </div>
            <div class="comment-container">
                <div class="comment" id="comment">
                    "comment container"
                </div>
                
                <div class="comment-section" id="comment-section">
                    <textarea class="comment-box" id="comment-box" placeholder="Write a comment..."></textarea>
                    <img src="/RWDD_ASSIGNMENT/Assets/send-icon.png" class="send" id="send"></img>
                </div>
            </div>
        </div>

    </div>


    <script>
    // Set the target date and time
    const targetDate = new Date("2025-09-10T00:00:00").getTime(); // example: Sept 10, 2025

    const countdownEl = document.getElementById("countdown");

    function updateCountdown() {
        const now = new Date().getTime();
        const distance = targetDate - now;

        if (distance < 0) {
        countdownEl.innerHTML = "Time's up!";
        clearInterval(timer);
        return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor(
        (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
        );
        const minutes = Math.floor(
        (distance % (1000 * 60 * 60)) / (1000 * 60)
        );

        countdownEl.innerHTML =
        `${days} days, ${hours} hours, ${minutes} minutes`;
    }

    // Update every 1 second
    const timer = setInterval(updateCountdown, 1000);
    updateCountdown(); // run once immediately
    </script>
    <!-- JavaScript Files -->
    <script src="../Navbar/core.js"></script>
    <script src="../Navbar/dropdowns.js"></script>
    <script src="../Navbar/editing.js"></script>
    <script src="../Navbar/workspaces.js"></script>
    <script src="../Navbar/tasks.js"></script>
    <script src="../Navbar/sidebar.js"></script>
    <script src="../Navbar/main.js"></script>
</body>
</html>