<?php
/**
 * Created by PhpStorm.
 * User: voiu
 * Date: 1/24/17
 * Time: 8:22 PM
 */

require_once "db.php";
require_once "site_functions.php";
require_once "user_functions.php";
require_once "other_functions.php";


if (isset($_POST['btnCancel'])) {
    header("Location:user_manage.php");
}

session_start();

// get logged-in user info
$login_user = $_SESSION['user_info'];

$conn = db_connect();

$allocate_id = test_input($_REQUEST['allocate_id']);
$user_id = test_input($_REQUEST['user_id']);

$mode = test_input($_REQUEST['mode']);

// Unallocated sites of the manager
$unallocated_sites = get_unallocated_sites($conn, $login_user['user_id']);

if (!isset($_POST['btnSave']) && !isset($_POST['btnRemove'])) {

    // get user info
    $user_info = get_user_from_id($conn, $user_id);
    
    if ($mode == 'modify') {
        $cur_allocation = get_allocation_info($conn, $allocate_id);
    }

} else if (isset($_POST['btnSave'])) {
    // insert new allocation
    if ($mode == 'new') {
        $site_id = $_POST['slSites'];

        insert_site_allocation($conn, $site_id, $user_id);

        header("Location:user_manage.php");
    }

    if ($mode == 'modify') {
        // deactivate the old allocation
        $allocate_id = $_POST['allocate_id'];

        deactivate_site_allocation($conn, $allocate_id);

        // add new allocation
        $site_id = $_POST['slSites'];

        insert_site_allocation($conn, $site_id, $user_id);

        header("Location:user_manage.php");
    }
} else if (isset($_POST['btnRemove'])) {
    $allocate_id = $_POST['allocate_id'];
    deactivate_site_allocation($conn, $allocate_id);
    header("Location:user_manage.php");
}


mysqli_close($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">

    <title>User Allocation</title>
</head>
<body>
<?php include_once 'header.php';?>
<?php include_once 'nav.php';?>

<section class="main-content">
    <div class="container">
        <div class="col-xs-12 col-md-9 col-md-push-3">
            <div class="right-panel">
                <div class="page-title"><span>User Allocation</span></div>
                <div class="page-content">
                    <form action="user_allocation.php" class="form-horizontal" method="post">
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">User Name:</label>
                            <div class="col-sm-8">
                                <span><?php echo $user_info['firstname'] . ' ' . $user_info['lastname']?></span>
                            </div>
                        </div>
                        <?php if ($mode == 'modify') { ?>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Current Allocated Site: </label>
                            <div class="col-sm-8">
                                <span><?php echo $cur_allocation['site_name']?></span>
                            </div>
                        </div>
                        <?php } ?>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Allocate Sites</label>
                            <div class="col-sm-8">
                                <select name="slSites" class="form-control">
                                    <?php foreach ($unallocated_sites as $site) { ?>
                                        <option value="<?php echo $site['site_id'];?>"><?php echo $site['site_name'];?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group button-group">
                            <?php if ($mode == 'new') { ?>
                            <div class="col-sm-offset-5 col-sm-3 col-xs-offset-4 col-xs-4">
                                <button type="submit" class="btn btn-default btn-block" name="btnSave">Save</button>
                            </div>
                            <div class="col-sm-3  col-xs-4">
                                <button type="submit" class="btn btn-default btn-block" name="btnCancel">Cancel</button>
                            </div>
                            <?php } else if ($mode == 'modify') { ?>
                                <div class="col-sm-offset-3 col-sm-3 col-xs-4">
                                    <button type="submit" class="btn btn-default btn-block" name="btnSave">Change</button>
                                </div>
                                <div class="col-sm-3  col-xs-4">
                                    <button type="submit" class="btn btn-default btn-block" name="btnRemove">Delete Allocation</button>
                                </div>
                                <div class="col-sm-3  col-xs-4">
                                    <button type="submit" class="btn btn-default btn-block" name="btnCancel">Cancel</button>
                                </div>
                            <?php } ?>
                        </div>

                        <input type="hidden" value="<?php echo $mode?>" name="mode">
                        <input type="hidden" value="<?php echo $allocate_id?>" name="allocate_id">
                        <input type="hidden" value="<?php echo $user_id?>" name="user_id">
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-3 col-md-pull-9">
            <div class="left-panel text-center">

            </div>
        </div>
    </div>
</section>

<script src="js/jquery-1.12.3.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/script.js"></script>
</body>
</html>
