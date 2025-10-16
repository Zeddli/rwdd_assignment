<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Need to get task ID -->
    <!-- Need the task status to be in progress?? -->
    <?php 
        include "../Head/Head.php";
        if(!isset($_SESSION["taskID"])){
            header("Location: ../HomePage/Home.php");
        }
    ?>
    
    <link rel="stylesheet" href="../Navbar/styles/base.css">
    <link rel="stylesheet" href="../Navbar/styles/navbar.css"> 
    <link rel="stylesheet" href="Task.css">    
</head>
<body>
    <?php include "../Navbar/navbar.php"; ?>

    <div class="main-content">
        
        <div class="task-header">
            <div class="header">
                <h1 class="workspace" id="workspace"><!--"WorkSpaces Name" -->/</h1>
                <div class="task-menu" id="task-menu"></div>
            </div>
            
            <div class="sub-header">
                <span class="task" id="task"><!--"Task Name"--></span>  <!-- maybe require js to create this one for different colour based on the status -->
                <span class="status" id="status"><!--"Status"--></span>
                <span class="priority" id="priority"><!--"Priority"--></span>
            </div>

        </div>
        <div class="description-section">
            <textarea readonly class="description-text" id="description-text"><!--REMEMBER TO CHANGE DESCRIPTION, TIME, TASKID FOR READ/SENDCOMMENT AND FILESHARED, SEARCH "CHANGE" will see!!!!!!!!!!!!!!!!!!--></textarea>
            <div class="time">
                <span id="start-time" class="start-time">
                    Start Time:
                    <span id="start"><!--2024-01-01 10:00 AM--></span>
                </span>
                <span id="deadline" class="deadline">
                    Deadline:
                    <span id="end"><!--2024-01-01 10:00 AM--></span>
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
                    <!-- "file-sharing-container" -->
                </div>
                <input type="file" id="choose-file" style="display:none;"> 
                <button id="new-file" class="new-file">Add A New File</button>
            </div>
            <div class="comment-container">
                <div class="comment" id="comment">
                    <!-- comment -->
                </div>
                
                <div class="comment-section" id="comment-section">
                    <textarea class="comment-box" id="comment-box" placeholder="Write a comment..."></textarea>
                    <img src="/rwdd_assignment/Assets/send-icon.png" class="send" id="send"></img>
                </div>
            </div>
        </div>

    </div>

    
    <script>
        //fetch task info
        <?php
        // $_SESSION["taskID"] = 1; //CHANGE!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        ?>

        //countdown
            //countdown: if completed, time left change to completed
            // if in progress, normal countdown
            // if overdue, show overdue how many time
            // if pending, show pending
            // endtime may be 0000-00-00 00:00:00

        // const targetDate = new Date("2025-10-10T00:00:00").getTime(); 

        function updateCountdown(targetDate, stat) {
            const countdownValue = document.getElementById("countdown");
            const timer = setInterval(() => updateCountdown(deadline, stat), 1000);   

            if(stat === "pending"){
                countdownValue.innerHTML = "The task is pending";
                return;
            } else if (stat === "completed"){
                countdownValue.innerHTML = "The task is completed";
                clearInterval(timer);
                return;
            } else {
                //In Progress
                const now = new Date().getTime();
                const dis = targetDate - now;
                const distance = Math.abs(dis);

                let status = "";
                // overude
                if (dis < 0) {
                    status = "Overdue";
                }

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor(
                (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
                );
                const minutes = Math.floor(
                (distance % (1000 * 60 * 60)) / (1000 * 60)
                );

                countdownValue.innerHTML =
                `${status} ${days} days ${hours}h ${minutes}min`;
            }
        }

        // fetch task info
        let deadline;
        let stat;
        fetch("FetchTask.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: new URLSearchParams({
                taskID: <?php echo $_SESSION["taskID"]; ?>
            })
        }).then(data => data.json())
          .then(data => {
            if(data.success){
                //data.task[] task.Title Description StartTime EndTime Deadline Priority Status workspace.Name

                document.getElementById("workspace").textContent = data.task["Name"];
                document.getElementById("task").textContent = data.task["Title"];
                document.getElementById("description-text").textContent = data.task["Description"];
                document.getElementById("start").textContent = data.task["StartTime"];
                document.getElementById("end").textContent = data.task["Deadline"];
                document.getElementById("status").textContent = data.task["Status"];
                document.getElementById("priority").textContent = data.task["Priority"];

                //diff colour for status and priority
                const statusValue = data.task["Status"].toLowerCase();
                const priorityValue = data.task["Priority"].toLowerCase();

                if(statusValue === "completed"){
                    document.getElementById("status").style.backgroundColor = "green"; 
                } else if (statusValue === "in progress"){
                    document.getElementById("status").style.backgroundColor = "red";
                } else if (statusValue === "pending"){
                    document.getElementById("status").style.backgroundColor = "yellow"; 
                } 

                if(priorityValue === "low"){
                    document.getElementById("priority").style.backgroundColor = "green"; 
                } else if (priorityValue === "high"){
                    document.getElementById("priority").style.backgroundColor = "red";
                } else if (priorityValue === "medium"){
                    document.getElementById("priority").style.backgroundColor = "yellow";
                } 

                deadline = new Date(data.task["Deadline"].replace(" ", "T"));
                deadline = deadline.getTime();
                stat = data.task["Status"].toLowerCase();
                // Update every 1 second
                updateCountdown(deadline, stat); // run once immediately 
                // const timer = setInterval(() => updateCountdown(deadline, stat), 1000);               
                
            
            } else {
                alert("Task not found");
                window.location.href = "../HomePage/Home.php"; //go back to home if task not found
            }
        })
        .catch((err) => {
            alert("FetchTask failed: " + err);
        });

        // task menu

       

        //download file
        // listen to file
        const fileSource = new EventSource("FetchFile.php");
        fileSource.onmessage = (e) => {
            const files = JSON.parse(e.data);
            // FileID(for finding file, need to combine with FileName extension name) FileName(for showing and download name) Extension CreatedAt Username
            const filesContainer = document.getElementById("file");

            //main container
            filesContainer.innerHTML = "";

            //No file
            if(files.length == 0){
                const noFile = document.createElement("p");
                noFile.className = "no-file";
                noFile.textContent = "No File Shared";
                filesContainer.appendChild(noFile);
                return;
            }

            //have file
            files.forEach((file) => {
                // each file container
                const fileContainer = document.createElement("div");
                fileContainer.className = "file-item-container";

                //file name and download icon (pressing file name go to new tab?, pressing dl icon download)
                const fileHead = document.createElement("div");
                fileHead.className = "file-head";
                
                const filename = document.createElement("strong");
                filename.className = "file-name";
                filename.textContent = `${file.FileName}.${file.Extension}`;

                const dlIcon = document.createElement("img");
                dlIcon.className = "download-icon";
                dlIcon.src = "/rwdd_assignment/Assets/download-icon.png"
                dlIcon.alt = "download-icon.png"
                
                // dlIcon.addEventListener("click", ()=>{
                //     fetch(`CheckFile.php?id=${file.fileID}`)
                //         .then(res => res.json())
                //         .then(data => {
                //             if (data.success){
                //                 window.location.href = `DownloadFile.php?id=${file.FileID}`;
                //             } else {
                //                 alert(data.error || "File not found");
                //             }
                //         })
                //         .catch(() => {
                //             alert("An error occured when checking the file");
                //         })
                        
                // });
                dlIcon.addEventListener("click", ()=>{
                    window.location.href = `DownloadFile.php?id=${file.FileID}`;
                });

                fileHead.appendChild(filename);
                fileHead.appendChild(dlIcon);

                //By username and created at
                const fileBottom = document.createElement("div");
                fileBottom.className = "file-bottom";

                const username = document.createElement("p");
                username.className = "file-username";
                username.textContent = `by ${file.Username}`;

                const fileCreatedAt = document.createElement("p");
                fileCreatedAt.className = "file-created-at";
                fileCreatedAt.textContent = file.CreatedAt;

                fileBottom.appendChild(username);
                fileBottom.appendChild(fileCreatedAt);

                fileContainer.appendChild(fileHead);
                fileContainer.appendChild(fileBottom);

                filesContainer.appendChild(fileContainer);
            });

            

        }

        // add file
        document.getElementById("new-file").addEventListener("click", () => {
            document.getElementById("choose-file").click();
        });

        document.getElementById("choose-file").addEventListener("change", (event) => {
            const file = event.target.files[0]; //first file only
            if(!file) return;
            const maxSize = 5 * 1024 * 1024; // 5MB in bytes
            if (file.size > maxSize) {
                alert("File size exceeds 5MB limit");
            }

            const formData = new FormData();
            formData.append("file", file);
            // formData.append("taskID", 1); //CHANGE!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            console.log(Object.fromEntries(formData.entries()));
            

            fetch("AddFile.php", {
                method: "POST",
                body: formData
            }).then(response => response.json())
              .then(data => {
                if(data.success){
                    alert("File uploaded successfully");
                } else {
                    alert("File upload failed: " + data.error);
                }
            })

        });

        //send comment
        document.getElementById("comment-box").addEventListener("keydown", (event) => {
            if(event.key === "Enter" && !event.shiftKey){
                event.preventDefault(); //prevent new line
                document.getElementById("send").click();
            }
        });

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
                    // taskID: 1 //CHANGE!!!!!!!!!!!!!!!!!!!!!!!!!!!!
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
                //CommentID UserID TaskID Comment CreatedAt Username PictureName 

                //pic name time
                const commentHeader = document.createElement("div");
                commentHeader.className = "comment-item-header";

                //each comment
                const commentItem = document.createElement("div");
                commentItem.className = "comment-item";

                //picture
                const profilePic = document.createElement("img");
                profilePic.className = "profile-pic";
                if(comment.PictureName === null || comment.PictureName === ""){
                    profilePic.src = "/rwdd_assignment/Assets/ProfilePic/anonymous.jpg";
                } else {
                    profilePic.src = `/rwdd_assignment/Assets/ProfilePic/${comment.PictureName}`;
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

        window.addEventListener("beforeunload", () => {
            commentSource.close();
            fileSource.close();
        });

    </script>

    <!-- js Files -->
    <script type = "module">
        //menu
        import {createThreeDotMenu} from '../ManagerFunction/menu.js';
        import {edit, member, dlt} from '../ManagerFunction/Main.js';

        // need to check if the user is manager for the workspace in workspacemember, check cookie
        
        //return data.sucess, if not success, alert you have no access to this function
        const menu = createThreeDotMenu([
            // edit task name, status, priority, start time, deadline, description
            // when changing status to completed, insert end time
            // when chaging status to pending or in progress, set end time to null
            {label: "Edit", onClick: () => edit(<?php echo $_SESSION["taskID"]; ?>)},

            //invite member or kick member to the task, not for workspace
            {label: "Member", onClick: () => member(<?php echo $_SESSION["taskID"]; ?>, "task")},

            //delete all in comment, fileshared, file in FileSharing folder, task, taskaccess
            {label: "Delete Task", onClick: () => dlt(<?php echo $_SESSION["taskID"]; ?>, "task")},

        ]);
        document.getElementById("task-menu").appendChild(menu);
    </script>

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