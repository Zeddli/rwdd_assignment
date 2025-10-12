<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
        include "../Head/Head.php";
        $_SESSION["workspaceID"] = 1; //CHANGEEEEEEEEEEEEE!!!!!!!!!!!!
        if(!isset($_SESSION["workspaceID"])){
            header("Location: ../HomePage/Home.php");
        }
    ?>
    
    <link rel="stylesheet" href="../Navbar/base.css">
    <link rel="stylesheet" href="../Navbar/navbar.css"> 
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
            } else {
                alert(`Failed to fetch workspace: ${data.error}`);
            }
        }).catch((err)=>{
            alert(`Error when fetching workspace: ${err}`);
        });


        // fetch task
        // if no task, show no task
        // vertical scroll for dueSoon upcoming completed
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
        function fetchTask(){
            fetch("FetchRelatedTask.php", {
                method: "POST",
                headers: {"Content-Type": "application/x-www-form-urlencoded"}
            }).then(data => data.json())
            .then(data => {
                console.log("current: " + JSON.stringify(currentdata));
                console.log("new: " + JSON.stringify(data));
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
                            const title = document.createElement("h3");
                            title.textContent = task.Title;
                            title.style.margin = "0 0 5px 0";

                            // Description
                            const desc = document.createElement("p");
                            desc.textContent = task.Description || "No description.";
                            desc.style.fontSize = "14px";
                            desc.style.color = "#555";

                            // Deadline
                            const deadline = document.createElement("p");
                            deadline.textContent = "Deadline: " + (task.Deadline || "N/A");
                            deadline.style.fontSize = "13px";
                            deadline.style.color = "#777";

                            // Overdue indicator
                            if (!task.Status === "Completed" && task.isOverdue) {
                                const overdueTag = document.createElement("span");
                                overdueTag.textContent = "⚠ Overdue";
                                overdueTag.style.color = "red";
                                overdueTag.style.fontWeight = "bold";
                                overdueTag.style.fontSize = "13px";
                                overdueTag.style.position = "absolute";
                                overdueTag.style.top = "10px";
                                overdueTag.style.right = "10px";
                                card.appendChild(overdueTag);
                            }

                            // Three-dot menu (imported)
                            import("../ManagerFunction/menu.js").then(module => {
                                const menu = module.createThreeDotMenu([
                                    { label: "Edit", onClick: () => alert(`Editing task: ${task.Title}`) },
                                    { label: "Member", onClick: () => alert(`Member: ${task.Title}`) },
                                    { label: "Delete Task", onClick: () => alert(`Deleting task: ${task.Title}`) }
                                ]);
                                menu.setAttribute('id', "task-menu");
                                menu.style.position = "absolute";
                                menu.style.top = "10px";
                                menu.style.right = "10px";
                                card.appendChild(menu);
                            });

                            card.appendChild(title);
                            card.appendChild(desc);
                            card.appendChild(deadline);

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
                                container.style.gridTemplateColumns = "repeat(auto-fit, minmax(250px, 1fr))";
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
        setInterval(fetchTask, 1000);
        
        fetchTask();
        
    </script>

    <script type = "module">
        import {createThreeDotMenu} from "../ManagerFunction/menu.js";

        const workspaceMenu = createThreeDotMenu([
            {label: "Rename", onClick: () => alert("You click on edit button")},
            {label: "Add Task", onClick: () => alert("You click on add task button")},
            {label: "Member", onClick: () => alert("You click on member button")},
            {label: "Delete Workspace", onClick: () => alert("You click on delete button")}

        ]);

        document.getElementById("workspace-menu").appendChild(workspaceMenu);
    </script>

    <!-- navbar -->
    <script src="../Navbar/core.js"></script>
    <script src="../Navbar/dropdowns.js"></script>
    <script src="../Navbar/editing.js"></script>
    <script src="../Navbar/workspaces.js"></script>
    <script src="../Navbar/tasks.js"></script>
    <script src="../Navbar/sidebar.js"></script>
    <script src="../Navbar/main.js"></script>
</body>
</html>