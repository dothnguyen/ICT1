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

?>

<div class="container">
    <!-- Header -->
    <div class="row header">
        <!-- Logo -->
        <div class="col-xs-12 col-sm-6">
            <img src="images/logo.png" alt="Boral" class="header-logo">
        </div>
        <!-- User profile -->
        <div class="col-xs-12 col-sm-6 ">
            <div class="pull-right">
                <div class="user-profile"><span class="fa fa-user" aria-hidden="true"><?php echo $login_user['firstname']. ' ' . $login_user['lastname'];?></span>
                 <a href="logout.php"><span class="fa fa-sign-out"></span></a></div>
                <div class="date-time"><?php echo date('Y-m-d H:i:s')?></div>
            </div>
        </div>
    </div>
</div>
