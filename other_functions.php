<?php
/**
 * Created by PhpStorm.
 * User: voiu
 * Date: 5/12/16
 * Time: 11:33 AM
 */

/**
 * @param $data
 * @return string
 */
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function check_login() {
    // if not logged in -> redirect to login page
    if (!$_SESSION['logged_in']) {
        header("Location:login.php");
        exit();
    }
}

/**
 *
 */
function check_authorize($is_manager_page) {
    // get logged-in user info
    $login_user = $_SESSION['user_info'];

    $is_manager = $login_user['role'] == 'manager';

    if (!$is_manager && $is_manager_page) {
        header("Location:unauthorize.php");
    }
}