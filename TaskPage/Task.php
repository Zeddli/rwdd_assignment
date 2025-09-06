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
            <textarea readonly class="description-text">REMEMBER TO CHANGE DESCRIPTION, TIME, TASKID FOR READ/SENDCOMMENT AND FILESHARED!!!!!!!!!!!!!!!!!!</textarea>
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
                    
                </div>
                
                <div class="comment-section" id="comment-section">
                    <textarea class="comment-box" id="comment-box" placeholder="Write a comment..."></textarea>
                    <img src="/RWDD_ASSIGNMENT/Assets/send-icon.png" class="send" id="send"></img>
                </div>
            </div>
        </div>

    </div>

    
    <script>
        //send comment
        document.getElementById("send").addEventListener("click", () => {
            const text = document.getElementById("comment-box").value.trim();

            if(text === "") return; //No empty comment

            fetch("AddComment.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: new URLSearchParams({
                    comment: text,
                    taskID: 1 //CHANGE!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                })
            }).then(response => response.json())
              .then(data => {
                if(data.success){
                    document.getElementById("comment-box").value = ""; //clear box
                } else {
                    alert("Failed to add comment");
                }
            })
        });

        // listen to comment
        const commentSource = new EventSource("FetchComment.php");

        commentSource.onmessage = (e) => {
            const comments = JSON.parse(e.data);
            const commentContainer = document.getElementById("comment");

            commentContainer.innerHTML = ""; //Clear

            //No comment
            if(comments.length == 0){
                const noComment = document.createElement("p");
                noComment.className = "no-comment";
                noComment.textContent = "No Comment";
                commentContainer.appendChild(noComment);
                return;
            }
            
            //have comment
            comments.forEach((comment) =>{
                //CommentID UserID TaskID Comment CreatedAt Username PictureName PicturePath

                //pic name time
                const commentHeader = document.createElement("div");
                commentHeader.className = "comment-item-header";

                //each comment
                const commentItem = document.createElement("div");
                commentItem.className = "comment-item";

                //picture
                const profilePic = document.createElement("img");
                profilePic.className = "profile-pic";
                if(comment.PicturePath === null || comment.PicturePath === ""){
                    profilePic.src = "/RWDD_ASSIGNMENT/Assets/ProfilePic/anonymous.jpg";
                } else {
                    profilePic.src = `/RWDD_ASSIGNMENT/Assets/ProfilePic/${comment.PicturePath}`;
                }
                
                //Name 
                const username = document.createElement("strong");
                username.className = "username";
                username.textContent = comment.Username;

                //time
                const createdAt = document.createElement("small");
                createdAt.className = "created-at";
                createdAt.textContent = comment.CreatedAt

                //comment
                const commentText = document.createElement("p");
                commentText.className = "comment-text";
                commentText.textContent = comment.Comment;

                const horiLine = document.createElement("hr");

                commentHeader.appendChild(profilePic);
                commentHeader.appendChild(username);
                commentHeader.appendChild(createdAt);
                commentItem.appendChild(commentText);
                commentItem.appendChild(horiLine);

                commentContainer.appendChild(commentHeader);
                commentContainer.appendChild(commentItem);
            });
        }








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