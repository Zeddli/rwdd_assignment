<?php
    session_start();
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include "../Database/Database.php";
    header('Content-Type: application/json');

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $workspaceID = $_SESSION["workspaceID"];
        $userInfo = json_decode($_COOKIE["loginInfo"],true);
        $userID = $userInfo["userID"];

        // only show task with access
        // all task
        $allTask = [];
        $allTaskStmt = $conn->prepare("SELECT * FROM task 
                                              JOIN taskaccess ON task.TaskID = taskaccess.TaskID
                                              WHERE WorkspaceID = ? and UserID = ?
                                              ORDER BY task.TaskID DESC");
        $allTaskStmt->bind_param("ii", $workspaceID, $userID);
        if($allTaskStmt->execute()){
            $allResult = $allTaskStmt->get_result();
            while($row = $allResult->fetch_assoc()){
                $allTask[] = $row;
            }
        } else {
            echo json_encode(["success"=>false, "error"=>"Failed to get all task"]);
            exit();
        }
        $allTaskStmt->close();

        $now = new DateTime();
        // Due soon
        // Status = In Progress
        // ORDER: overdue -> near Deadline
        $dueSoon = [];
        // upcoming task
        // Status =  pending
        // ORDER: near StartTime
        $upcoming = [];
        // completed task
        // status = completed
        $completed = [];

        // Categorize tasks
        foreach ($allTask as $task) {
            $deadline = new DateTime($task['Deadline']);
            $startTime = new DateTime($task['StartTime']);
            
            // Check if overdue
            $isOverdue = ($deadline < $now);
            $task['isOverdue'] = $isOverdue;
            
            // Categorize based on status
            if ($task['Status'] === 'In Progress'){
                $dueSoon[] = $task;
            } elseif ($task['Status'] === 'Pending'){
                $upcoming[] = $task;
            } elseif ($task['Status'] === "Completed"){
                $completed[] = $task;
            }
        }

        // Sort dueSoon
        usort($dueSoon, function($a, $b) {
            // overdue and not
            if ($a['isOverdue'] && !$b['isOverdue']) {
                // first
                return -1;
            }
            // not overdue and overdue
            if (!$a['isOverdue'] && $b['isOverdue']) {
                // second
                return 1;
            }
            
            // Both overdue or not overdue: will return negative or po num
            return strtotime($a['Deadline']) - strtotime($b['Deadline']);
        });

        // Sort upcoming: by nearest start time
        usort($upcoming, function($a, $b) {
            return strtotime($a['StartTime']) - strtotime($b['StartTime']);
        });


        echo json_encode(["success"=>true, "allTask"=>$allTask, "dueSoon"=>$dueSoon, "upcoming"=>$upcoming, "completed"=>$completed]);
        exit();

    }
?>