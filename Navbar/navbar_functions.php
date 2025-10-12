<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../Database/Database.php';

// load organized function files
require_once '../Navbar/functions/getWorkspace.php';
require_once '../Navbar/functions/createWorkspaceAndTask.php';
require_once '../Navbar/functions/renameWorkspaceTaskAndGoal.php';
require_once '../Navbar/functions/deleteWorkspaceAndTask.php';
require_once '../Navbar/functions/getWorkspaceMembers.php';
require_once '../Navbar/functions/inviteToTask.php';
// require_once 'functions/inviteMember.php';


?>
