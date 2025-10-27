<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
        include "../Head/Head.php";

    ?>
    
    <link rel="stylesheet" href="../Navbar/styles/base.css">
    <link rel="stylesheet" href="../Navbar/styles/navbar.css"> 
    <link rel="stylesheet" href="ProfilePage.css">    
</head>
<body>
    <?php include "../Navbar/navbar.php"; ?>
    <img src="../Assets/profilebg.jpg" alt="" class="profilebg">
    <!-- main content -->
    <div class="main-content">
        <h2 class="profile-page" id="profile-page">Profile</h2>
        <div class="details-container" id="details-container">
            <div class="profile-pic">
                <img src="" alt="" class="profile-picture" id="profile-picture">
                    <button class="profile-pic-button" id="profile-pic-button">
                        <img class="edit-pic-logo" src="/rwdd_assignment/Assets/editProfile.svg"></img>    
                        <!-- <p class="edit-pic">Change Profile Picture</p>  -->
                    </button>
                </img>

                <input class="profile-pic-input" id="profile-pic-input" type="file" accept="image/*" style="display: none;"></input>
            </div>
            <div class="details" id="details">
                <p class="email-label">Email:</p>
                <textarea name="email" id="email" class="details" rows="1" disabled></textarea>
                <p class="username-label">Username:
                    <button class="edit-name" id="edit-name">
                        <img src="../Assets/edit.png" alt="" class="edit-name-icon">Edit
                    </button>
                </p>
                <textarea name="username" id="username" class="username" rows="1" disabled></textarea>
            </div>
        </div>
        <div class="user-func">
            <button class="change-password" id="change-password">Change Password</button>
            <button class="logout" id="logout">Logout</button>
        </div>
    </div>

    <script>
        fetch("FetchUserDetails.php", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"}
        }).then(data=>data.json())
        .then(data=>{
            // Email, Username, PictureName
            const user = data.user;
            document.getElementById("email").value = user.Email;
            document.getElementById("username").value = user.Username;
            document.getElementById("profile-picture").src = `/rwdd_assignment/Assets/ProfilePic/${user.PictureName}`;
        }).catch(err=>
            alert("Error fetching user details: " + err)
        );

        document.getElementById("profile-pic-button").addEventListener("click", ()=>{
            document.getElementById("profile-pic-input").click();
        });
        document.getElementById("profile-pic-input").addEventListener("change", (event)=>{
            const file = event.target.files[0];
            if(file && file.type.startsWith("image/")){
                const formData = new FormData();
                formData.append("file", file);                

                fetch("UpdateProfilePicture.php", {
                    method: "POST",
                    body: formData
                }).then(response => response.json())
                .then(data => {
                    if(data.success){
                        alert("Profile picture updated");
                        window.location.reload();
                        return;
                    } else {
                        alert("Failed to update profile picture");
                        return;
                    }
                })
                .catch(error => {
                    alert("Error uploading file");
                    return;
                });
            }
        });
        document.getElementById("username").addEventListener("keydown", (event)=>{
            if(event.key === "Enter"){
                event.preventDefault();
                document.getElementById("edit-name").click();
            }
        })

        document.getElementById("edit-name").addEventListener("click", ()=>{
            const usernameField = document.getElementById("username");
            if(usernameField.disabled){
                usernameField.disabled = false;
                usernameField.focus();
                document.getElementById("edit-name").innerHTML = `<img src="../Assets/save.png" alt="" class="edit-name-icon">Save`;
            } else {
                const newUsername = usernameField.value.trim();
                fetch("UpdateUsername.php", {
                    method: "POST",
                    headers: {"Content-Type": "application/x-www-form-urlencoded"},
                    body: new URLSearchParams({
                        newUsername: newUsername
                    })
                }).then(data => data.json())
                .then(data => {
                    if(data.success){
                        alert("Username updated");
                        usernameField.disabled = true;
                        document.getElementById("edit-name").innerHTML = `<img src="../Assets/edit.png" alt="" class="edit-name-icon">Edit`;
                    } else {
                        alert("Failed to update username: " + data.error);
                    }
                }).catch(err=>{
                    alert("Error updating username: " + err);
                });
            }
        });
        
        document.getElementById("change-password").addEventListener("click", ()=>{
            // popup with current pass and new pass
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
            form.id = "change-password-form";
            form.method = "POST";
            form.action = "ChangePassword.php";

            const currentPassLabel = document.createElement("label");
            currentPassLabel.for = "text";
            currentPassLabel.textContent = "Current Password:";
            const currentPassInput = document.createElement("input");
            currentPassInput.type = "password";
            currentPassInput.name = "currentPassword";
            currentPassInput.id = "current-password";
            currentPassInput.required = true;

            const newPassLabel = document.createElement("label");
            newPassLabel.for = "text";
            newPassLabel.textContent = "New Password";
            const newPassInput = document.createElement("input");
            newPassInput.type = "password";
            newPassInput.name = "newPassword";
            newPassInput.id = "new-password";
            newPassInput.required = true;

            const submit = document.createElement("input");
            submit.type = "submit";
            submit.value = "Change Password";
            submit.id = "submit-button";
            submit.className = "button";
            submit.addEventListener("click", (e) => {
                e.preventDefault();

                //trim empty space
                currentPassInput.value = currentPassInput.value.trim();
                newPassInput.value = newPassInput.value.trim();

                if(form.reportValidity()){
                    const formData = new FormData(form);
                    fetch(form.action, {
                        method: form.method,
                        headers: {"Content-Type": "application/x-www-form-urlencoded"},
                        body: new URLSearchParams(formData)
                    }).then(data => data.json())
                    .then(data => {
                        if(data.success){
                            alert("Password Changed!");
                            document.getElementById("popup").remove();
                        } else {
                            alert("Failed to change password: " + data.error);
                        }
                    }).catch((err) => {
                        alert("Change password fetch failed: " + err);
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
            [currentPassLabel, newPassLabel].forEach(label => {
                label.style.fontWeight = "600";
                label.style.color = "#333";
            });

            // Input, select, textarea
            [currentPassInput, newPassInput].forEach(input => {
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

            form.appendChild(currentPassLabel);
            form.appendChild(currentPassInput);
            form.appendChild(newPassLabel);
            form.appendChild(newPassInput);
            form.appendChild(submit);
            form.appendChild(cancel);
            popupContainer.appendChild(form);
            popup.appendChild(popupContainer);
            document.body.appendChild(popup);
                
        });

        document.getElementById("logout").addEventListener("click", ()=>{
            if(confirm("Are you sure you want to logout?")){
                fetch("LogoutProcess.php", {
                    method: "POST",
                    headers: {"Content-Type": "application/x-www-form-urlencoded"}
                }).then(data => data.json())
                .then(data => {
                    if(data.success){
                        sessionStorage.clear();
                        localStorage.clear();
                        window.location.href = "/rwdd_assignment/LandingPage/landing.php";
                    } else {
                        alert("Failed to logout: " + data.error);
                    }
                }).catch(err=>{
                    alert("Error logging out: " + err);
                });
            }
        });

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