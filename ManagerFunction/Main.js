//pop up style
    // popup {
    //     position: absolute;
    //     z-index: 1;
    //     width: 100%;
    //     height: 100%;
    //     background-color: #00000090;
    //     display: grid;
    // }
    // popup-container {
    //     place-self: center;
    //     width: max(23vw, 330px);;
    //     color: #80808080;
    //     background-color: white;
    //     display: flex;
    //     flex-direction: column;
    //     gap: 25px;
    //     padding: 25px 30px;
    //     border-radius: 8px;
    //     font-size: 14px;
    //     animation: fadeIn 0.5s;
    // }
    
export function edit(taskID){
    // alert("Edit function called for taskID (Main.js): " + taskID);

    checkPermission(taskID).then(hasPermission => {
        if(hasPermission){
            //proceed to edit
            fetch("/rwdd_assignment/TaskPage/FetchTask.php", {
                method: "POST",
                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                body: new URLSearchParams({
                    taskID: taskID
                })
            })
            .then(data => data.json())
            .then(data => {
                if(data.success){
                    //data.task[] task.Title Description StartTime EndTime Deadline Priority Status workspace.Name             
                    
                    // for background
                    const popup = document.createElement("div");
                    popup.className = "popup";
                    popup.id = "popup";

                    // for form
                    const  popupContainer = document.createElement("div");
                    popupContainer.className = "popup-container";
                    popupContainer.id = "popup-container";

                    //form
                    const form = document.createElement("form");
                    form.id = "edit-form";
                    form.method = "POST";
                    form.action = "/rwdd_assignment/ManagerFunction/Edit.php";

                    const workspaceLabel = document.createElement("label");
                    workspaceLabel.for = "text";
                    workspaceLabel.textContent = "Workspace";
                    const workspaceInput = document.createElement("input");
                    workspaceInput.type = "text";
                    workspaceInput.name = "workspace";
                    workspaceInput.id = "workspace";
                    workspaceInput.value = data.task.Name;
                    workspaceInput.required = true;

                    const titleLabel = document.createElement("label");
                    titleLabel.for = "text";
                    titleLabel.textContent = "Title";
                    const titleInput = document.createElement("input");
                    titleInput.type = "text";
                    titleInput.name = "title";
                    titleInput.id = "title";
                    titleInput.value = data.task.Title;
                    titleInput.required = true;

                    const descLabel = document.createElement("label");
                    descLabel.for = "text";
                    descLabel.textContent = "Description";
                    const descInput = document.createElement("textarea");
                    descInput.name = "description";
                    descInput.id = "description";
                    descInput.rows = 4;
                    descInput.value = data.task.Description;
                    descInput.required = true;

                    const startLabel = document.createElement("label");
                    startLabel.for = "time";
                    startLabel.textContent = "Start Time";
                    const startInput = document.createElement("input");
                    startInput.type = "datetime-local";
                    startInput.name = "starttime";
                    startInput.id = "starttime";
                    startInput.value = data.task.StartTime.replace(" ", "T").slice(0,16);
                    startInput.required = true;

                    const deadlineLabel = document.createElement("label");
                    deadlineLabel.for = "time";
                    deadlineLabel.textContent = "Deadline";
                    const deadlineInput = document.createElement("input");
                    deadlineInput.type = "datetime-local";
                    deadlineInput.name = "deadline";
                    deadlineInput.id = "deadline";
                    deadlineInput.value = data.task.Deadline.replace(" ", "T").slice(0,16);
                    deadlineInput.required = true;
                    //min should be start time
                    deadlineInput.min = startInput.value;
                    startInput.addEventListener("change", () => {
                        deadlineInput.min = startInput.value;
                    });

                    const priorityLabel = document.createElement("label");
                    priorityLabel.for = "priority";
                    priorityLabel.textContent = "Priority";
                    const prioritySelect = document.createElement("select");
                    prioritySelect.name = "priority";
                    prioritySelect.id = "priority";
                    const priorities = ["Low", "Medium", "High"];
                    priorities.forEach(p => {
                        const option = document.createElement("option");
                        option.value = p;
                        option.textContent = p;
                        if(p === data.task.Priority){
                            option.selected = true;
                        }
                        prioritySelect.appendChild(option);
                    });

                    const statusLabel = document.createElement("label");
                    statusLabel.for = "status";
                    statusLabel.textContent = "Status";
                    const statusSelect = document.createElement("select");
                    statusSelect.name = "status";
                    statusSelect.id = "status";
                    const statuses = ["Pending", "In Progress", "Completed"];
                    statuses.forEach(s => {
                        const option = document.createElement("option");
                        option.value = s;
                        option.textContent = s;
                        if(s === data.task.Status){
                            option.selected = true;
                        }
                        statusSelect.appendChild(option);
                    });

                    //hidden input for taskID
                    const taskIDInput = document.createElement("input");
                    taskIDInput.type = "hidden";
                    taskIDInput.name = "taskID";
                    taskIDInput.value = taskID;
                    //hidden input for workspaceID
                    const workspaceIDInput = document.createElement("input");
                    workspaceIDInput.type = "hidden";
                    workspaceIDInput.name = "workspaceID";
                    workspaceIDInput.value = data.task.WorkSpaceID;

                    const submit = document.createElement("input");
                    submit.type = "submit";
                    submit.value = "Save";
                    submit.id = "submit-button";
                    submit.className = "button";
                    submit.addEventListener("click", (e) => {
                        e.preventDefault();

                        //trim empty space
                        workspaceInput.value = workspaceInput.value.trim();
                        titleInput.value = titleInput.value.trim();
                        descInput.value = descInput.value.trim();

                        if(form.reportValidity()){
                            const formData = new FormData(form);
                            fetch(form.action, {
                                method: form.method,
                                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                                body: new URLSearchParams(formData)
                            }).then(data => data.json())
                            .then(data => {
                                if(data.success){
                                    alert("Changes Saved!");
                                    window.location.href = window.location.href; //reload
                                } else {
                                    alert("Edit failed: " + data.error);
                                }
                            }).catch((err) => {
                                alert("Edit fetch failed: " + err);
                            });
                             
                        }
                        
                    });

                    const cancel = document.createElement("button");
                    cancel.type = "button";
                    cancel.id = "cancel-button";
                    cancel.className = "button";
                    cancel.textContent = "Cancel";
                    cancel.addEventListener("click", () => {
                        document.getElementById("popup").remove();
                    });

                    //style
                    // Popup styling
                    popup.style.position = "absolute";
                    popup.style.zIndex = "1000";
                    popup.style.width = "100%";
                    popup.style.height = "100%";
                    popup.style.backgroundColor = "#00000090";
                    popup.style.display = "grid";
                    popup.style.animation = "fadeIn 0.5s";
                    popup.style.backdropFilter = "blur(2px)"; 

                    // Popup container styling
                    popupContainer.style.placeSelf = "center";
                    popupContainer.style.width = "max(23vw, 330px)";
                    popupContainer.style.backgroundColor = "white";
                    popupContainer.style.display = "flex";
                    popupContainer.style.flexDirection = "column";
                    popupContainer.style.gap = "15px";
                    popupContainer.style.padding = "20px 30px";
                    popupContainer.style.borderRadius = "8px";
                    popupContainer.style.fontSize = "15px";
                    popupContainer.style.animation = "fadeIn 0.5s";
                    popupContainer.style.boxShadow = "0 4px 20px rgba(0,0,0,0.2)";
                    popupContainer.style.fontFamily = "Arial, sans-serif";

                    // Form styling
                    form.style.display = "flex";
                    form.style.flexDirection = "column";
                    form.style.gap = "8px";

                    // Label
                    [workspaceLabel, titleLabel, descLabel, startLabel, deadlineLabel, priorityLabel, statusLabel].forEach(label => {
                        label.style.fontWeight = "600";
                        label.style.color = "#333";
                    });

                    // Input, select, textarea
                    [workspaceInput, titleInput, descInput, startInput, deadlineInput, prioritySelect, statusSelect].forEach(input => {
                        input.style.padding = "8px";
                        input.style.border = "1px solid #ccc";
                        input.style.borderRadius = "5px";
                        input.style.fontSize = "14px";
                        input.style.outline = "none";
                        input.addEventListener("focus", () => {
                            input.style.borderColor = "#4A90E2";
                            input.style.boxShadow = "0 0 5px rgba(74,144,226,0.5)";
                        });
                        input.addEventListener("blur", () => {
                            input.style.borderColor = "#ccc";
                            input.style.boxShadow = "none";
                        });
                    });

                    // Submit button styling
                    submit.style.backgroundColor = "#4A90E2";
                    submit.style.color = "white";
                    submit.style.border = "none";
                    submit.style.padding = "10px";
                    submit.style.borderRadius = "5px";
                    submit.style.cursor = "pointer";
                    submit.style.fontWeight = "600";
                    submit.style.transition = "0.3s";
                    submit.addEventListener("mouseover", () => {
                        submit.style.backgroundColor = "#357ABD";
                    });
                    submit.addEventListener("mouseout", () => {
                        submit.style.backgroundColor = "#4A90E2";
                    });

                    // Cancel button styling
                    cancel.style.backgroundColor = "#ccc";
                    cancel.style.color = "#333";
                    cancel.style.border = "none";
                    cancel.style.padding = "10px";
                    cancel.style.borderRadius = "5px";
                    cancel.style.cursor = "pointer";
                    cancel.style.fontWeight = "600";
                    cancel.style.transition = "0.3s";
                    cancel.addEventListener("mouseover", () => {
                        cancel.style.backgroundColor = "#999";
                    });
                    cancel.addEventListener("mouseout", () => {
                        cancel.style.backgroundColor = "#ccc";
                    });

                    form.appendChild(workspaceLabel);
                    form.appendChild(workspaceInput);
                    form.appendChild(titleLabel);
                    form.appendChild(titleInput);
                    form.appendChild(descLabel);
                    form.appendChild(descInput);
                    form.appendChild(startLabel);
                    form.appendChild(startInput);
                    form.appendChild(deadlineLabel);
                    form.appendChild(deadlineInput);
                    form.appendChild(priorityLabel);
                    form.appendChild(prioritySelect);
                    form.appendChild(statusLabel);
                    form.appendChild(statusSelect);
                    form.appendChild(taskIDInput);
                    form.appendChild(workspaceIDInput);
                    form.appendChild(submit);
                    form.appendChild(cancel);
                    popupContainer.appendChild(form);
                    popup.appendChild(popupContainer);
                    document.body.appendChild(popup);
                }
            })
            .catch((err) => {
                alert("FetchTask failed: " + err);
            });





        } else {
            //do not proceed
            alert("You do not have permission to edit this task.");
            return;
        }
    });
    
    document.body.appendChild();
}

export function member(id, type){
    // flow ->
    // click memebr button -> popup window show all mmeber with three dot button 
    // -> kick (check permission)
    // -> change role (check permission)
    // Invite member (workspace) (check permission) -> type email(will show list when typing) -> select role -> invite
    // Invite member (task) (check permission) -> type email(will show list when typing) -> invite
    
    // need to check if the user is manager for the workspace in workspacemember, check cookie    

    //invite member or kick member, need to check is task or workspace
    // when invite member, need to check if the user is already in the workspace
    alert("Member function called");
}

export function deleteTask(){
    alert("Delete Task function called");
}

export function deleteWorkspace(){
    alert("Delete Workspace function called");
}

function checkPermission(task){
    // alert("Checking permission for taskID: " + task);

    return fetch("/rwdd_assignment/ManagerFunction/CheckPermission.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({
            taskID: task
        })
    }).then(data => data.json())
      .then(data => {
        if(data.success){
            // alert("You are manager");
            return true;
        }else{
            // alert("You are not manager");
            // alert(`Error: ${data.error}`);
            return false
        }
    })
    .catch((err) => {
        alert("CheckPermission failed: " + err);
    });
}