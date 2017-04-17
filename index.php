<?php
// check for login
require_once "db.php";
require_once "user_functions.php";
require_once "other_functions.php";

session_start();

// check if user loged in
check_login();

// get logged-in user info
$login_user = $_SESSION['user_info'];

$is_manager = $login_user['role'] == 'manager';

$conn = db_connect();
// 2.4. When user login for the first time, ask him to change his password.
// check if user login for the first time
if (is_first_login($conn, $login_user['user_id'])) {

    // redirect to change_pwd page
    header("Location:change_pwd.php");
} else {

    if ($is_manager) {
        // redirect to manager home page
        header("Location:manager_home.php");
    } else {
        header("Location:rep_home.php");
    }
}
mysqli_close($conn);


?>
