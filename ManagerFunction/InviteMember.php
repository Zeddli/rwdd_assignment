<?php
    // diff stmt for task and workspace
    // if invite to task, need to insert to taskaccess and workspacmember
    // invite to workspace, only insert to workspacemember

    // filter input
    //htmlspecialchars(strip_tags($_POST['type']), ENT_QUOTES, 'UTF-8')

    // need to check if user exits in workspacemember or taskaccess

    // id: id, // to know which workspace or task
    // type: type, //to know workspace or task
    // email: inviteInput.value, 
    // role: roleSelect.value
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include "../Database/Database.php";
    header('Content-Type: application/json');

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $type = htmlspecialchars(strip_tags($_POST['type']), ENT_QUOTES, 'UTF-8');
        $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL); //will return empty
        // return success: false, error: Invalid email
        $role = htmlspecialchars(strip_tags($_POST['role']), ENT_QUOTES, 'UTF-8');

        // flow:
        // get the UserID use email ->
            //type = task:
            //table: taskaccess workspacemember
            //  get WorkSpaceID use $id -> check for existance in workspacemember(if not insert to workspacemember) -> check for existance in taskaccess(if yes success: false, error: Member exits)(if no insert)

            //type = workspace
            //table: workspacemember
            //  check for existance in workspacemember(if yes success: false, error: member exits)(if no insert)

        $userIdStmt = $conn->prepare("SELECT UserID FROM user WHERE Email = ?");
        $userIdStmt->bind_param("s", $email);
        if($userIdStmt->execute()){
            $userResult = $userIdStmt->get_result();
            if($userResult->num_rows == 1){
                // got user
                $userRow = $userResult->fetch_assoc();
                $userID = $userRow["UserID"];
                $userIdStmt->close();
            } else{
                echo json_encode(["success" => false, "error" => "User does not exist"]);
                exit();
            }
        } else {
            echo json_encode(["success"=>false, "error" => "Failed to get user ID."]);
            exit();
        }

        // proceed to add member
        if($type == "task"){
            //type = task:
            //table: taskaccess workspacemember
            //  get WorkSpaceID use $id -> check for existance in workspacemember(if not insert to workspacemember) -> check for existance in taskaccess(if yes success: false, error: Member exits)(if no insert)
            $workspaceIdStmt = $conn->prepare("SELECT WorkSpaceID FROM task WHERE TaskID = ?");
            $workspaceIdStmt->bind_param("i", $id);
            if($workspaceIdStmt->execute()){
                $workspaceResult = $workspaceIdStmt->get_result();
                if($workspaceResult->num_rows==1){
                    $workspaceRow = $workspaceResult->fetch_assoc();
                    $workspaceID = $workspaceRow["WorkSpaceID"];
                    $workspaceIdStmt->close();
                } else {
                    echo json_encode(["success"=>false, "error"=>"Error when getting workspace ID"]);
                    exit();
                }
            } else {
                echo json_encode(["success"=>false, "error"=>"Failed to get workspace ID"]);
                exit();
            }
            
            //after workspaceID
            // insert to workspacemember
            $workspacememberStmt = $conn->prepare("SELECT * FROM workspacemember WHERE UserID = ? and WorkSpaceID = ?");
            $workspacememberStmt->bind_param("ii", $userID, $workspaceID);
            if($workspacememberStmt->execute()){
                $workspacememberResult = $workspacememberStmt->get_result();
                if($workspacememberResult->num_rows == 0){
                    //insert if not in the workspace
                    $insertWorkspaceMember = $conn->prepare("INSERT INTO workspacemember VALUES (?, ?, ?)");
                    $insertWorkspaceMember->bind_param("iis", $workspaceID, $userID, $role);
                    if($insertWorkspaceMember->execute()){
                        // do nothing when success
                    } else {
                        echo json_encode(["success"=>false, "error"=>"Failed to insert to workspace member"]);
                        exit();
                    }
                }
            } else {
                echo json_encode(["success"=>false, "error"=>"Failed to get workspace member"]);
                exit();
            }

            //insert to task access
            //check existance
            $taskaccessExistStmt = $conn->prepare("SELECT * FROM taskaccess WHERE UserID = ? and TaskID = ?");
            $taskaccessExistStmt->bind_param("ii", $userID, $id);
            if($taskaccessExistStmt->execute()){
                $taskaccessExistResult = $taskaccessExistStmt->get_result();
                if($taskaccessExistResult->num_rows == 0){
                    // does not exists
                    $insertTaskaccess = $conn->prepare("INSERT INTO taskaccess VALUES (?, ?)");
                    $insertTaskaccess->bind_param("ii", $userID, $id);
                    if($insertTaskaccess->execute()){
                        echo json_encode(["success"=>true]);
                        exit();
                    }else{
                        echo json_encode(["success"=>false, "error"=>"Failed to invite member to task"]);
                        exit();
                    }
                } else {
                    // member exists in taskaccess
                    echo json_encode(["success"=>false, "error"=>"Member exists"]);
                    exit();
                }
            } else {
                echo json_encode(["success"=>false, "error"=>"Failed to check existance in task access"]);
                exit();  
            }


        } else if($type == "workspace"){
            //type = workspace
            //table: workspacemember
            //  check for existance in workspacemember(if yes success: false, error: member exits)(if no insert)

            $workspacememberStmt = $conn->prepare("SELECT * FROM workspacemember WHERE UserID = ? and WorkSpaceID = ?");
            $workspacememberStmt->bind_param("ii", $userID, $id);
            if($workspacememberStmt->execute()){
                $workspacememberResult = $workspacememberStmt->get_result();
                if($workspacememberResult->num_rows == 0){
                    //insert
                    $insertWorkspaceMember = $conn->prepare("INSERT INTO workspacemember VALUES (?, ?, ?)");
                    $insertWorkspaceMember->bind_param("iis", $id, $userID, $role);
                    if($insertWorkspaceMember->execute()){
                        echo json_encode(["success"=>true]);
                        exit();
                        
                    } else {
                        echo json_encode(["success"=>false, "error"=>"Failed to insert to workspace member"]);
                        exit();
                    }
                } else {
                    echo json_encode(["success"=>false, "error"=>"Member exist in this workspace"]);
                    exit();
                }
            } else {
                echo json_encode(["success"=>false, "error"=>"Failed to get workspace member"]);
                exit();
            }
        }
    }
    $conn->close();
?>