<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../Database/Database.php';

// load organized function files
require_once 'Navbar_Function_PHP/getWorkspace.php';
require_once 'Navbar_Function_PHP/createWorkspaceAndTask.php';
require_once 'Navbar_Function_PHP/renameWorkspaceTaskAndGoal.php';
require_once 'Navbar_Function_PHP/deleteWorkspaceAndTask.php';
require_once 'Navbar_Function_PHP/inviteMember.php';


?>
