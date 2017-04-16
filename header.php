<?php
/**
 * Created by PhpStorm.
 * User: voiu
 * Date: 1/15/17
 * Time: 12:15 PM
 */

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

$conn = db_connect();

$last_login = get_login_time($conn, $login_user['user_id']);

mysqli_close($conn);

// get user login time
function get_login_time($conn, $user_id) {
    $sql = "SELECT last_login FROM user_tbl WHERE user_id=$user_id";
    $ret = mysqli_fetch_assoc($conn->query($sql));

    return $ret['last_login'];
}
?>

<div class="container">
    <!-- Header -->
    <div class="row header">
        <!-- Logo -->
        <div class="col-xs-12 col-sm-offset-1 col-sm-5">
            <img src="images/logo.png" alt="Boral" class="header-logo">
        </div>
        <!-- User profile -->
        <div class="col-xs-12 col-sm-5 ">
            <div class="pull-right">
                <div class="user-profile">
                    <span class="fa fa-user" aria-hidden="true"></span>&nbsp;<span><?php echo $login_user['firstname']. ' ' . $login_user['lastname'];?></span>
                    &nbsp;<a href="logout.php"><span class="fa fa-sign-out"></span></a>
                </div>
                <div class="date-time">Login at: <?php echo date('d-m-Y H:i', strtotime($last_login));?></div>
            </div>
        </div>
    </div>
</div>
