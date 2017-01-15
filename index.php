<?php
// check for login
require_once "db.php";
require_once "user_functions.php";
require_once "other_functions.php";

session_start();

// if not logged in -> redirect to login page
if (!$_SESSION['logged_in']) {
    header("Location:login.php");
}

// get logged-in user info
$login_user = $_SESSION['user_info'];

$is_manager = $login_user['role'] == 'manager';

if ($is_manager) {
    // redirect to manager home page
    header("Location:manager_home.php");
} else {
    header("Location:rep_home.php");
}

?>
