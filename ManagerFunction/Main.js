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
import { createThreeDotMenu } from "/rwdd_assignment/ManagerFunction/menu.js";
export function edit(taskID){
    // alert("Edit function called for taskID (Main.js): " + taskID);

    checkPermission(taskID).then(hasPermission => {
        if(hasPermission){
            //proceed to edit
            fetch("../TaskPage/FetchTask.php", {
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
                    form.action = "../ManagerFunction/Edit.php";

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
                    popup.style.position = "fixed";
                    popup.style.top = "0";
                    popup.style.left = "0";
                    popup.style.zIndex = "1000";
                    popup.style.width = "100%";
                    popup.style.height = "100%";
                    popup.style.backgroundColor = "#00000090";
                    popup.style.display = "grid";
                    popup.style.transition = "0.3s";
                    popup.style.opacity = "1";

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
                    submit.style.backgroundColor = "#007bff";
                    submit.style.color = "white";
                    submit.style.border = "none";
                    submit.style.padding = "10px";
                    submit.style.borderRadius = "5px";
                    submit.style.cursor = "pointer";
                    submit.style.fontWeight = "600";
                    submit.style.transition = "0.3s";
                    submit.addEventListener("mouseover", () => {
                        submit.style.backgroundColor = "#26598cff";
                    });
                    submit.addEventListener("mouseout", () => {
                        submit.style.backgroundColor = "#007bff";
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
    
    // document.body.appendChild();
}

export function member(id, type){
    // flow ->
    // click memebr button -> popup window show all mmeber with three dot button 
    // -> kick (check permission)
    // -> Grant Manager Access (check permission)
    // Invite member (workspace) (check permission) -> type email(will show list when typing) -> select role -> invite
    // Invite member (task) (check permission) -> type email(will show list when typing) -> invite
    
    // need to check if the user is manager for the workspace in workspacemember, check cookie    

    //invite member or kick member, need to check is task or workspace
    // when invite member, need to check if the user is already in the workspace

    // FetchMember.php(two type) KickMember.php(two type) GrantAccess.php(workspace only) InviteMember.php(two type, role set default to NULL)

    fetch("../ManagerFunction/FetchMember.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams({
            id: id,
            type: type //workspace or task
        })
    }).then(data => data.json())
    .then(data => {
        if(data.success){
            //data.members[] task: UserID TaskID
            //user: UserID Username Email PictureName
            //top: textarea role(for workspace) select invite button
            //list: picture name email role(for workspace) three dot button (kick, grant access)

            const popup = document.createElement("div");
            popup.className = "popup";
            popup.style.position = "fixed";
            popup.style.top = "0";
            popup.style.left = "0";
            popup.style.zIndex = "1000";
            popup.style.width = "100%";
            popup.style.height = "100%";
            popup.style.backgroundColor = "#00000090";
            popup.style.display = "grid";
            popup.style.animation = "fadeIn 0.5s";
            popup.style.backdropFilter = "blur(2px)";

            // Container
            const popupContainer = document.createElement("div");
            popupContainer.className = "popup-container";
            popupContainer.style.placeSelf = "center";
            popupContainer.style.width = "max(50%, 400px)";
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

            // Title
            const title = document.createElement("h2");
            title.textContent = type === "workspace" ? "Workspace Members" : "Task Members";
            title.style.margin = "0 0 10px 0";
            popupContainer.appendChild(title);

            // Invite section
            const inviteBox = document.createElement("div");
            inviteBox.style.display = "flex";
            inviteBox.style.width = "100%";
            inviteBox.style.justifyContent = "center";
            inviteBox.style.alignItems = "center";

            const inviteForm = document.createElement("form");
            inviteForm.method = "POST";
            inviteForm.action = "../ManagerFunction/InviteMember.php";
            inviteForm.style.display = "flex";
            inviteForm.style.width = "100%";
            inviteForm.style.gap = "10px";
            inviteForm.style.alignItems = "center";

            const inviteInput = document.createElement("input");
            inviteInput.type = "email";
            inviteInput.name = "invite-email";
            inviteInput.id = "invite-email";
            inviteInput.required = true;
            inviteInput.placeholder = "Enter email to invite";
            inviteInput.style.flex = "2";
            inviteInput.style.padding = "5px";
            inviteInput.style.border = "1px solid #ccc";
            inviteInput.style.borderRadius = "4px";
            inviteInput.rows = 1;

            const roleSelect = document.createElement("select");
            roleSelect.style.border = "1px solid #ccc";
            roleSelect.style.borderRadius = "4px";
            roleSelect.style.padding = "5px";
            roleSelect.style.cursor = "pointer";
            roleSelect.style.background = "#fff";
            roleSelect.style.color = "#333";
            roleSelect.style.fontSize = "14px";
            ["Employee", "Manager"].forEach(r => {
                const option = document.createElement("option");
                option.value = r;
                option.textContent = r;
                roleSelect.appendChild(option);
            });

            const inviteBtn = document.createElement("button");
            inviteBtn.type = "submit";
            inviteBtn.textContent = "Invite";
            inviteBtn.style.cursor = "pointer";
            inviteBtn.style.padding = "5px 12px";
            inviteBtn.style.border = "1px solid #ccc";
            inviteBtn.style.borderRadius = "4px";
            inviteBtn.style.background = "#007bff";
            inviteBtn.style.color = "#fff";
            inviteBtn.style.transition = "0.3s";
            inviteBtn.addEventListener("mouseover", () => {
                inviteBtn.style.backgroundColor = "#26598cff";
            });
            inviteBtn.addEventListener("mouseout", () => {
                inviteBtn.style.backgroundColor = "#007bff";
            });

            inviteForm.appendChild(inviteInput);
            inviteForm.appendChild(roleSelect);
            inviteForm.appendChild(inviteBtn);

            inviteBox.appendChild(inviteForm);

            popupContainer.appendChild(inviteBox);

            inviteBtn.addEventListener("click", (e) => {
                // need to check permission
                e.preventDefault();
                checkPermission(id, type).then(hasPermission => {
                    // alert(hasPermission);
                    if(hasPermission){
                        // have permission, proceed to invite
                        inviteInput.value = inviteInput.value.trim(); //这里trim 了 下面的invite form 里面的email 也没有trim到，在report validity 不能detect到
                        if(inviteForm.reportValidity()){
                            // alert(`Inviting ${inviteInput.value} as ${roleSelect.value}`);
                            fetch(inviteForm.action, {
                                method: inviteForm.method,
                                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                                body: new URLSearchParams({
                                    id: id, // to know which workspace or task
                                    type: type, //to know workspace or task
                                    email: inviteInput.value, 
                                    role: roleSelect.value
                                })
                            }).then(data => data.json())
                            .then(data => {
                                if(data.success){
                                    alert(`Success: ${inviteInput.value} invited as ${roleSelect.value}`);
                                    // inviteInput.value = "";
                                    popup.remove();
                                    member(id,type);
                                
                                    return;
                                } else {
                                    alert(`${inviteInput.value} invitation failed: ${data.error}`);
                                    return;
                                }
                            }).catch((err) => {
                                alert(`Fetch InviteMember Failed: ${err}`);
                                return;
                            });
                        }

                    } else {
                        // do not have permission
                        alert("You do not have permission to invite member!");
                        return;
                    }
                });
            });

            // Member list
            const memberList = document.createElement("div");
            memberList.style.display = "flex";
            memberList.style.flexDirection = "column";
            memberList.style.gap = "10px";
            memberList.style.height = "50vh";
            memberList.style.overflowY = "auto";

            data.members.forEach(member => {
                const row = document.createElement("div");
                row.style.display = "flex";
                row.style.alignItems = "center";
                row.style.justifyContent = "space-between";
                row.style.borderBottom = "1px solid #eee";
                row.style.padding = "8px 0";

                // Left side (avatar + info)
                const info = document.createElement("div");
                info.style.display = "flex";
                info.style.alignItems = "center";
                info.style.gap = "10px";

                const img = document.createElement("img");
                img.src = member.PictureName == null? "../Assets/ProfilePic/anonymous.jpg" : `../Assets/ProfilePic/${member.PictureName}`;
                img.style.width = "35px";
                img.style.height = "35px";
                img.style.borderRadius = "50%";
                img.style.objectFit = "cover";

                const details = document.createElement("div");
                const name = document.createElement("div");
                name.textContent = member.Username;
                name.style.fontWeight = "bold";
                const email = document.createElement("div");
                email.textContent = member.Email;
                email.style.fontSize = "12px";
                email.style.color = "gray";

                details.appendChild(name);
                details.appendChild(email);

                info.appendChild(img);
                info.appendChild(details);

                // Right side (role + menu)
                const actions = document.createElement("div");
                actions.style.display = "flex";
                actions.style.alignItems = "center";
                actions.style.gap = "5px";

                if (type === "workspace") {
                    const role = document.createElement("span");
                    role.textContent = member.UserRole;
                    role.style.fontSize = "13px";
                    role.style.color = "gray";
                    actions.appendChild(role);
                }

                const menu = createThreeDotMenu([
                    { label: "Grant Access", onClick: () => {
                        checkPermission(id, type).then(hasPermission => {
                            if(hasPermission){
                                const info = {
                                    userID: member.UserID
                                }
                                if(type === "workspace"){
                                    info.workspaceID = id;
                                } else if (type === "task"){
                                    info.taskID = id;
                                }
                                const params = new URLSearchParams(info);
                                
                                fetch("../ManagerFunction/GrantAccess.php", {
                                    method: "POST",
                                    headers: {"Content-Type": "application/x-www-form-urlencoded"},
                                    body: params
                                }).then(data => data.json())
                                .then(data => {
                                    if(data.success){
                                        alert(`${member.Username} has become a manager.`);
                                        return;
                                    } else {
                                        alert(`Grant access failed: ${data.error}`);
                                        return;
                                    }
                                }).catch((err) => {
                                    alert(`Error occur when fetch GrantAccess: ${err}`);
                                });
                            } else {
                                alert("You do not have permission to grant manager access.");
                                return
                            }
                        })
                    }},
                    { label: "Kick", onClick: () => {
                        checkPermission(id, type).then(hasPermission => {
                            if(hasPermission){
                                if(confirm(`Are you sure you want to kick member: ${member.Username}`)){
                                    fetch("../ManagerFunction/KickMember.php", {
                                        method: "POST",
                                        headers: {"Content-Type": "application/x-www-form-urlencoded"},
                                        body: new URLSearchParams({
                                            tableID: id,
                                            type: type,
                                            userID: member.UserID
                                        })
                                    }).then(data => data.json())
                                    .then(data => {
                                        if(data.success){
                                            alert(`${member.Username} was kicked from this task.`);
                                            row.remove();
                                            return;
                                        } else {
                                            alert(`Kicked failed: ${data.error}`);
                                            return;
                                        }
                                    }).catch((err) => {
                                        alert(`Error occur when fetch GrantAccess: ${err}`);
                                    });
                                }
                            } else {
                                alert("You do not have permission to kick member");
                                return;
                            }
                        })
                    }}
                ]);
                actions.appendChild(menu);

                row.appendChild(info);
                row.appendChild(actions);
                memberList.appendChild(row);
            });

            popupContainer.appendChild(memberList);

            // Cancel button
            const cancelBtn = document.createElement("button");
            cancelBtn.textContent = "Close";
            cancelBtn.style.marginTop = "15px";
            cancelBtn.style.padding = "8px 12px";
            cancelBtn.style.border = "1px solid #ccc";
            cancelBtn.style.borderRadius = "4px";
            cancelBtn.style.cursor = "pointer";
            cancelBtn.style.transition = "0.3s";
            cancelBtn.addEventListener("mouseover", () => {
                cancelBtn.style.backgroundColor = "#999";
            });
            cancelBtn.addEventListener("mouseout", () => {
                cancelBtn.style.backgroundColor = "#ccc";
            });
            cancelBtn.addEventListener("click", () => popup.remove());

            popupContainer.appendChild(cancelBtn);

            //  appendChild
            popup.appendChild(popupContainer);
            document.body.appendChild(popup);
        } else {
            alert("FetchMember failed: " + data.error);
        }
    }).catch((err) => {
        alert("FetchMember fetch failed: " + err);
    });
}

export function dlt(id, type){
    checkPermission(id, type).then(hasPermission => {
        if(hasPermission){
            if(confirm("Are you sure you want to delete this task?")){
                fetch("../ManagerFunction/Delete.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: new URLSearchParams({
                        id: id,
                        type: type
                    })
                }).then(data=>data.json())
                .then(data => {
                    if(data.success){
                        alert("Delete " + type + " successfully");
                        console.log(`file deleted: ${data.deleted}`);
                        data.failed.forEach(element => {
                            console.log(`File failed: ${element}`);
                        });
                        const currentPage = window.location.pathname.split('/').pop();
                        if(type === "task" && currentPage.includes("Task.php")){
                            window.location.href = "../HomePage/home.php";
                        }
                        if(type === "workspace" && currentPage.includes("Workspace.php")){
                            window.location.href = "../HomePage/home.php";
                        }
                        return;
                    } else {
                        alert(`Error occur when deleting task: ${data.error}`);
                        return;
                    }
                }).catch((err)=>{
                    alert(`Error when fetch delete.php: ${err}`);
                    return;
                });
            }
        } else {
            alert("You do not have permission to delete this task.");
            return;
        }
    })
}

export function renameWorkspace(workspaceID){
    checkPermission(workspaceID, "workspace").then(hasPermission=>{
        if(hasPermission){
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
            form.id = "rename-form";
            form.method = "POST";
            form.action = "../ManagerFunction/renameWorkspace.php";

            const workspaceLabel = document.createElement("label");
            workspaceLabel.for = "text";
            workspaceLabel.textContent = "Rename to:";
            const workspaceInput = document.createElement("input");
            workspaceInput.type = "text";
            workspaceInput.name = "workspace";
            workspaceInput.id = "workspace";
            workspaceInput.required = true;

            //hidden input for workspaceID
            const workspaceIDInput = document.createElement("input");
            workspaceIDInput.type = "hidden";
            workspaceIDInput.name = "workspaceID";
            workspaceIDInput.value = workspaceID;

            const submit = document.createElement("input");
            submit.type = "submit";
            submit.value = "Save";
            submit.id = "submit-button";
            submit.className = "button";
            submit.addEventListener("click", (e) => {
                e.preventDefault();

                //trim empty space
                workspaceInput.value = workspaceInput.value.trim();

                if(form.reportValidity()){
                    const newName = workspaceInput.value;
                    const workspaceID = workspaceIDInput.value;
                    fetch(form.action, {
                        method: form.method,
                        headers: {"Content-Type": "application/x-www-form-urlencoded"},
                        body: new URLSearchParams({
                            workspaceID: workspaceID,
                            newName: newName
                        })
                    }).then(data => data.json())
                    .then(data => {
                        if(data.success){
                            alert("Changes Saved!");
                            window.location.href = window.location.href; //reload
                        } else {
                            alert("Rename failed: " + data.error);
                        }
                    }).catch((err) => {
                        alert("Rename fetch failed: " + err);
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
            popup.style.position = "fixed";
            popup.style.top = "0";
            popup.style.left = "0";
            popup.style.zIndex = "1000";
            popup.style.width = "100%";
            popup.style.height = "100%";
            popup.style.backgroundColor = "#00000090";
            popup.style.display = "grid";
            popup.style.transition = "0.3s";
            popup.style.opacity = "1";

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
            workspaceLabel.style.fontWeight = "600";
            workspaceLabel.style.color = "#333";
            
            // Input
            workspaceInput.style.padding = "8px";
            workspaceInput.style.border = "1px solid #ccc";
            workspaceInput.style.borderRadius = "5px";
            workspaceInput.style.fontSize = "14px";
            workspaceInput.style.outline = "none";
            workspaceInput.addEventListener("focus", () => {
                workspaceInput.style.borderColor = "#4A90E2";
                workspaceInput.style.boxShadow = "0 0 5px rgba(74,144,226,0.5)";
            });
            workspaceInput.addEventListener("blur", () => {
                workspaceInput.style.borderColor = "#ccc";
                workspaceInput.style.boxShadow = "none";
            });
            

            // Submit button styling
            submit.style.backgroundColor = "#007bff";
            submit.style.color = "white";
            submit.style.border = "none";
            submit.style.padding = "10px";
            submit.style.borderRadius = "5px";
            submit.style.cursor = "pointer";
            submit.style.fontWeight = "600";
            submit.style.transition = "0.3s";
            submit.addEventListener("mouseover", () => {
                submit.style.backgroundColor = "#26598cff";
            });
            submit.addEventListener("mouseout", () => {
                submit.style.backgroundColor = "#007bff";
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
            form.appendChild(workspaceIDInput);
            form.appendChild(submit);
            form.appendChild(cancel);
            popupContainer.appendChild(form);
            popup.appendChild(popupContainer);
            document.body.appendChild(popup);

        } else {
            alert("You do not have permission to rename workspace");
        }
    })
}

function checkPermission(id, type = "task"){
    // alert("Checking permission for taskID: " + task);
    const paramName = type === "workspace" ? "workspaceID" : "taskID";
    const params = new URLSearchParams();
    params.append(paramName, id);

    return fetch("../ManagerFunction/CheckPermission.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
        },
        body: params
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