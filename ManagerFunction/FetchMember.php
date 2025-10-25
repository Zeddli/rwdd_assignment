<?php
    // flow ->
    // click memebr button -> popup window show all mmeber with three dot button 
    // -> kick (check permission)
    // -> change role (check permission)
    // Invite member (workspace) (check permission) -> type email(will show list when typing) -> select role -> invite
    // Invite member (task) (check permission) -> type email(will show list when typing) -> invite
    
    // need to check if the user is manager for the workspace in workspacemember, check cookie    

    //invite member or kick member, need to check is task or workspace
    // when invite member, need to check if the user is already in the workspace

    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include "../Database/Database.php";
    header('Content-Type: application/json');

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $table = htmlspecialchars(strip_tags($_POST['type']), ENT_QUOTES, 'UTF-8') == "task" ? "taskaccess":"workspacemember";
        $column = $table == "taskaccess" ? "TaskID":"WorkSpaceID";
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

        $stmt = $conn->prepare("SELECT * FROM $table
                                       JOIN user ON $table.UserID = user.UserID
                                       WHERE $column = ?");
        $stmt->bind_param("i", $id);

        if($stmt->execute()){
            $result = $stmt->get_result();
            $members = [];
            while ($row = $result->fetch_assoc()) {
                // check pic
                $filepath = __DIR__ . "/../Assets/ProfilePic/" . $row["PictureName"];
                if($row["PictureName"] === null || !file_exists($filepath)){ 
                    $row["PictureName"] = "anonymous.jpg";
                }
                if($row["HasedPassword"]){
                    $row["HasedPassword"] = "";
                }
                
                
                $members[] = $row;
            }
            echo json_encode(["success" => true, "members" => $members]);
        }
        else{
            echo json_encode(["success" => false, "error" => "Failed to fetch task members"]);
        }
        $stmt->close();
        $conn->close();
        exit();
    }
?>