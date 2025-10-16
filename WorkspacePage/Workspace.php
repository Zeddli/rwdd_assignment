<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
        include "../Head/Head.php";

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // $_SESSION["workspaceID"] = 1; //CHANGEEEEEEEEEEEEE!!!!!!!!!!!!
        if(!isset($_SESSION["workspaceID"])){
            header("Location: ../HomePage/Home.php");
        }
    ?>
    
    <link rel="stylesheet" href="../Navbar/styles/base.css">
    <link rel="stylesheet" href="../Navbar/styles/navbar.css"> 
    <link rel="stylesheet" href="Workspace.css">    
</head>
<body>
    <?php include "../Navbar/navbar.php"; ?>

    <!-- main content -->
    <div class="main-content">
        <!-- header -->
        <div class="header" id="header"> 
            <div class="workspace-menu" id="workspace-menu"></div>
            <div class="header-content">
                <!-- background img and workspace name -->
                <p class="workspace-name" id="workspace-name">Workspace name</p>
                <div class="workspace-id" id="workspace-id" hidden></div>
            </div>
            <p class="workspace-created-by" id="workspace-created-by">Created by</p>
        </div>

        <!-- Recent task -->
         <div class="recent" id="recent">
            <h2 class="recent-title" id="recent-title">Due Soon</h2>
            <div class="container" id="recent-task-container"></div>
         </div>

         <!-- upcoming task -->
        <div class="upcoming" id="upcoming">
            <h2 class="upcoming-title" id="upcoming-title">Upcoming Task</h2>
            <div class="container" id="upcoming-task-container"></div>
        </div>

        <!-- completed task -->
        <div class="completed" id="completed">
            <h2 class="completed-title" id="completed-title">Completed Task</h2>
            <div class="container" id="completed-task-container"></div>
        </div>

        <!-- all task -->
         <div class="all" id="all">
            <h2 class="all-title" id="all-title">All Task</h2>
            <div class="all-task-container" id="all-task-container"></div>
         </div>
    </div>

    <script>
        // fetch workspace
        fetch("FetchWorkspace.php", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
        }).then(data => data.json())
        .then(data => {
            if(data.success){
                document.getElementById("workspace-name").textContent = data.workspace["Name"];
                document.getElementById("workspace-created-by").textContent = `Created by ${data.workspace["Username"]}`;
                document.getElementById("workspace-id").textContent = data.workspace["WorkSpaceID"];
            } else {
                alert(`Failed to fetch workspace: ${data.error}`);
            }
        }).catch((err)=>{
            alert(`Error when fetching workspace: ${err}`);
        });


        // fetch task
        // if no task, show no task
        // show all grid for allTask
        // need to redirect(/rwdd_assignment/TaskPage/Task.php) and set SESSION["taskID"] when click
        // add threedotmenu for each task
        // if overdue for Due Soon part and all task part, need to add a red sign mention it is overdue
        // dueSoon upcoming completed allTask
        let currentdata = {
            success: true,
            allTask: [],
            dueSoon: [],
            upcoming: [],
            completed: []
        };
        let isEditing = false;
        function fetchTask(){
            console.log("isEditing: " + isEditing);
            if(isEditing){
                console.log("add listener");       
                try {
                    document.getElementById("cancel-button").addEventListener("click", ()=>{
                        isEditing = false;
                    });
                } catch (error) {
                    console.log(error);
                    isEditing = false;
                }
                return;
            }
            if(!isEditing){
                console.log("start to fetch");
                fetch("FetchRelatedTask.php", {
                    method: "POST",
                    headers: {"Content-Type": "application/x-www-form-urlencoded"}
                }).then(data => data.json())
                .then(data => {
                    // console.log("current: " + JSON.stringify(currentdata));
                    // console.log("new: " + JSON.stringify(data));
                    if(data.success){
                        if(!(JSON.stringify(currentdata) === JSON.stringify(data))){
                            currentdata = data;
                            const { allTask, dueSoon, completed, upcoming } = data;

                            // Get container references
                            const allTaskContainer = document.getElementById("all-task-container");
                            const dueSoonContainer = document.getElementById("recent-task-container");
                            const completedContainer = document.getElementById("completed-task-container");
                            const upcomingContainer = document.getElementById("upcoming-task-container");

                            // Clear previous content
                            [allTaskContainer, dueSoonContainer, completedContainer, upcomingContainer].forEach(c => c.innerHTML = "");

                            // Helper: create a task card
                            const createTaskCard = (task, section) => {
                                const card = document.createElement("div");
                                card.id = task.TaskID;
                                card.className = "task-card";
                                card.style.border = "1px solid #ddd";
                                card.style.borderRadius = "10px";
                                card.style.padding = "10px";
                                card.style.margin = "5px";
                                card.style.background = "#fefefe";
                                card.style.display = "flex";
                                card.style.flexDirection = "column";
                                card.style.justifyContent = "space-between";
                                card.style.position = "relative";
                                card.style.cursor = "pointer";
                                card.style.transition = "0.3s";

                                card.addEventListener("mouseover", () => {
                                    card.style.backgroundColor = "#f3f3f3";
                                });
                                card.addEventListener("mouseout", () => {
                                    card.style.backgroundColor = "#fefefe";
                                });

                                // Redirect when clicked
                                card.addEventListener("click", () => {
                                    fetch('../Navbar/navbar_api.php', {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                        body: 'action=set_task_session&task_id=' + task.TaskID
                                    })
                                    .then(res => res.json())
                                    .then(data => {
                                        if (data.success) {
                                            window.location.href = '../TaskPage/Task.php';
                                        } else {
                                            alert('Failed to open task');
                                        }
                                    });
                                });

                                // Title
                                const titleContainer = document.createElement("div");
                                titleContainer.className = "title-container";
                                titleContainer.style.display = "flex";
                                titleContainer.style.gap = "5px";
                                titleContainer.style.alignItems = "center";
                                titleContainer.style.justifyContent = "flex-start";

                                const title = document.createElement("h3");
                                title.textContent = task.Title;
                                titleContainer.appendChild(title);

                                // Description
                                const desc = document.createElement("p");
                                desc.className = "task-desc";
                                // more details
                                desc.textContent = task.Description || "No description.";


                                // Deadline
                                const deadline = document.createElement("p");
                                deadline.className = "task-detail";
                                deadline.textContent = "Deadline: " + (task.Deadline || "N/A");

                                //start
                                const start = document.createElement("p");
                                start.className = "task-detail";
                                start.textContent = "Start At: " + (task.StartTime || "N/A");

                                // status
                                const status = document.createElement("p");
                                status.className = "task-detail";
                                status.textContent = "Status: " + (task.Status || "N/A");

                                // Overdue indicator
                                if (task.isOverdue) {
                                    const overdueTag = document.createElement("img");
                                    overdueTag.src = "../Assets/overdue.png";
                                    overdueTag.alt = "Overdue";
                                    overdueTag.style.width = "30px";
                                    overdueTag.style.height = "30px";
                                    overdueTag.style.objectFit = "contain";
                                    titleContainer.appendChild(overdueTag);
                                }

                                // Three-dot menu
                                import("../ManagerFunction/Main.js").then(func => {
                                    const edit = func.edit;
                                    const member = func.member;
                                    const dlt = func.dlt;

                                    import("../ManagerFunction/menu.js").then(module => {
                                        const menu = module.createThreeDotMenu([
                                            { label: "Edit", onClick: () =>  {
                                                // edit(task.TaskID);
                                                // isEditing = true;
                                                async function handleEdit(task) {
                                                    try {
                                                        const result = await edit(task.TaskID);
                                                        isEditing = true; // only after full success
                                                    } catch (error) {
                                                        console.error(error);
                                                        isEditing = false;
                                                    }
                                                }
                                                handleEdit(task);
                                            } 
                                            },
                                            { label: "Member", onClick: () => member(task.TaskID, "task") },
                                            { label: "Delete Task", onClick: () => dlt(task.TaskID, "task") }
                                        ]);
                                        menu.setAttribute('id', "task-menu");
                                        menu.style.position = "absolute";
                                        menu.style.top = "10px";
                                        menu.style.right = "10px";
                                        menu.style.margin = "0 0 0 auto";
                                        // card.appendChild(menu);
                                        titleContainer.appendChild(menu);
                                    });
                                });

                                card.appendChild(titleContainer);
                                card.appendChild(desc);
                                card.appendChild(start);
                                card.appendChild(deadline);
                                card.appendChild(status);

                                return card;
                            };

                            // Helper: display tasks
                            const renderTasks = (tasks, container, layout) => {
                                if (!tasks || tasks.length === 0) {
                                    const msg = document.createElement("p");
                                    msg.textContent = "No tasks available.";
                                    msg.style.color = "#777";
                                    msg.style.textAlign = "center";
                                    msg.style.margin = "10px";
                                    container.appendChild(msg);
                                    return;
                                }

                                // Set layout style
                                if (layout === "scroll") {
                                    container.style.display = "flex";
                                    container.style.flexDirection = "column";
                                    container.style.overflowY = "auto";
                                    container.style.maxHeight = "250px";
                                } else if (layout === "grid") {
                                    container.style.display = "grid";
                                    container.style.gridTemplateColumns = "repeat(auto-fit, minmax(200px, 1fr))";
                                    container.style.gap = "10px";
                                }

                                tasks.forEach(task => {
                                    const card = createTaskCard(task, layout);
                                    container.appendChild(card);
                                });
                            };

                            // Render each section
                            renderTasks(dueSoon, dueSoonContainer, "scroll");
                            renderTasks(upcoming, upcomingContainer, "scroll");
                            renderTasks(completed, completedContainer, "scroll");
                            renderTasks(allTask, allTaskContainer, "grid");
                        }
                    } else{
                        alert(`Error when getting related task: ${data.error}`);
                        return;
                    }
                }).catch((err)=>{
                    alert(`Failed to fetch related task: ${err}`);
                })
            }
        }
        setInterval(fetchTask, 1000);
        
        fetchTask();
        
    </script>


<script type="module">
    import { createThreeDotMenu } from "../ManagerFunction/menu.js";
    import { member, dlt, renameWorkspace } from "../ManagerFunction/Main.js";
    import { 
        showTaskDetailWindow, 
        showEditTaskWindow, 
        hideTaskDetailWindow, 
        initializeTaskDetailWindow 
    } from "../Navbar/scripts/TaskDetailWindow.js"; 

    // init the task detail window when page loads
    document.addEventListener('DOMContentLoaded', () => {
        initializeTaskDetailWindow();
    });

    const workspaceMenu = createThreeDotMenu([
        {
            label: "Rename", 
            onClick: () => {
                // isEditing = true;
                // renameWorkspace(<?php echo $_SESSION["workspaceID"] ?>);
                
                async function handleRename() {
                    try {
                        const result = await renameWorkspace(<?php echo $_SESSION["workspaceID"] ?>);
                        isEditing = true; // only after full success
                    } catch (error) {
                        console.error(error);
                        isEditing = false;
                    }
                }
                handleRename();
            }
        },
        {
            label: "Add Task",
            onClick: () => {
                if (typeof showTaskDetailWindow === 'function') showTaskDetailWindow(<?php echo $_SESSION["workspaceID"] ?>);
            }
        },
        {
            label: "Member", 
            onClick: () => member(<?php echo $_SESSION["workspaceID"] ?>, "workspace")
        },
        {
            label: "Delete Workspace", 
            onClick: () => dlt(<?php echo $_SESSION["workspaceID"] ?>, "workspace")
        }
    ]);

    document.getElementById("workspace-menu").appendChild(workspaceMenu);

    // Make functions available globally 
    window.showTaskDetailWindow = showTaskDetailWindow;
    window.showEditTaskWindow = showEditTaskWindow;
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