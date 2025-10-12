<?php
/**
 * Check workspace permission for invite member functionality
 * 
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1);
error_reporting(E_ALL);

// absolute path 
require_once __DIR__ . '/../../Database/Database.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get user ID from session 
    if (!isset($_SESSION['userInfo']) || !isset($_SESSION['userInfo']['userID'])) {
        echo json_encode(["success" => false, "error" => "User not logged in"]);
        exit();
    }
    $userID = (int)$_SESSION['userInfo']['userID'];
    
    if (isset($_POST["workspaceID"])) {
        $workspaceId = intval($_POST["workspaceID"]);
        
        // Check permission for the workspace - only managers can invite
        $stmt = $conn->prepare(
            "SELECT UserRole FROM workspacemember 
             WHERE UserID = ? AND WorkSpaceID = ?"
        );
        $stmt->bind_param("ii", $userID, $workspaceId);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();

            if (isset($row["UserRole"])) {
                $role = $row["UserRole"];
                if ($role === "Manager") {
                    echo json_encode(["success" => true]);
                } else {
                    echo json_encode(["success" => false, "error" => "You are not manager"]);
                }
            } else {
                echo json_encode(["success" => false, "error" => "No such user in workspace"]);
            }
        } else {
            if (isset($stmt)) { $stmt->close(); }
            echo json_encode(["success" => false, "error" => "Failed to execute"]);
        }
        if (isset($conn)) { $conn->close(); }
        exit();
    } else {
        echo json_encode(["success" => false, "error" => "No workspaceID provided"]);
        if (isset($conn)) { $conn->close(); }
        exit();
    }
}

// No POST: nothing to do
http_response_code(405);
echo json_encode(["success" => false, "error" => "Method not allowed"]);
if (isset($conn)) { $conn->close(); }
?>