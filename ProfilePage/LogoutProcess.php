<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    header('Content-Type: application/json');
    include "../Database/Database.php";

    session_start();

    $_SESSION = [];

    session_destroy();

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    if (isset($_COOKIE["loginInfo"])) {
        setcookie("loginInfo", "", time() - 3600, "/");
    }

    echo json_encode(["success" => true]);
?>