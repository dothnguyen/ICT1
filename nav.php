<?php
/**
 * Created by PhpStorm.
 * User: voiu
 * Date: 1/15/17
 * Time: 4:15 PM
 */


// get logged-in user info
$login_user = $_SESSION['user_info'];

$is_manager = $login_user['role'] == 'manager';

$page_name = basename($_SERVER['PHP_SELF']);

?>

<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
<nav class = "navbar navbar-default" role = "navigation">
    <div class = "navbar-header">
        <button type = "button" class = "navbar-toggle"
                data-toggle = "collapse" data-target = "#example-navbar-collapse">
            <span class = "sr-only">Toggle navigation</span>
            <span class = "icon-bar"></span>
            <span class = "icon-bar"></span>
            <span class = "icon-bar"></span>
        </button>

        <span class = "navbar-brand visible-xs" href = "#">Menu</span>
    </div>

    <?php if ($is_manager) {?>
        <div class = "collapse navbar-collapse" id = "example-navbar-collapse">
            <ul class = "nav navbar-nav">
                <?php if ($page_name == 'manager_home.php') {?>
                    <li class = "active"><a href = "#">Home</a></li>
                <?php } else {?>
                    <li><a href = "manager_home.php">Home</a></li>
                <?php }?>

                <?php if ($page_name == 'manager_report.php') {?>
                    <li class = "active"><a href = "#">Reports</a></li>
                <?php } else {?>
                    <li><a href = "manager_report.php">Reports</a></li>
                <?php }?>

                <?php if ($page_name == 'site_manage.php' || $page_name == 'site_modify.php') {?>
                    <li class = "active"><a href = "#">Site Management</a></li>
                <?php } else {?>
                    <li><a href = "site_manage.php">Site Management</a></li>
                <?php }?>
				
				
                <?php if ($page_name == 'user_manage.php' || $page_name == 'user_modify.php') {?>
                    <li class = "active"><a href = "#">User Management</a></li>
                <?php } else {?>
                    <li><a href = "user_manage.php">User Management</a></li>
                <?php }?>

            </ul>
        </div>
    <?php } else { ?>
        <div class = "collapse navbar-collapse" id = "example-navbar-collapse">

            <ul class = "nav navbar-nav">
                <?php if ($page_name == 'rep_home.php') {?>
                    <li class = "active"><a href = "#">Home</a></li>
                <?php } else {?>
                    <li><a href = "rep_home.php">Home</a></li>
                <?php }?>
                <?php if ($page_name == 'rep_daily_chklist.php') {?>
                    <li class = "active"><a href = "#">Daily</a></li>
                <?php } else {?>
                    <li><a href = "rep_daily_chklist.php">Daily</a></li>
                <?php }?>
                <?php if ($page_name == 'rep_weekly_chklist.php') {?>
                    <li class = "active"><a href = "#">Weekly</a></li>
                <?php } else {?>
                    <li><a href = "rep_weekly_chklist.php">Weekly</a></li>
                <?php }?>
                <?php if ($page_name == 'rep_monthly_chklist.php') {?>
                    <li class = "active"><a href = "#">Monthly</a></li>
                <?php } else {?>
                    <li><a href = "rep_monthly_chklist.php">Monthly</a></li>
                <?php }?>

            </ul>
        </div>
    <?php } ?>


</nav>
        </div>
    </div>

</div>
