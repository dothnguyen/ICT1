<?php
/**
 * Created by PhpStorm.
 * User: ngocle
 * Date: 6/04/2017
 * Time: 8:29 PM
 */


require_once "db.php";
require_once "site_functions.php";
require_once "user_functions.php";
require_once "other_functions.php";

session_start();

// check if user loged in
check_login();

// check if user can access manager's page
check_authorize(true);

if (isset($_POST['btnCancel'])) {
    header("Location:site_manage.php");
}

// get logged-in user info
$login_user = $_SESSION['user_info'];

$conn = db_connect();

$allocate_id = test_input($_REQUEST['allocate_id']);
$site_id = test_input($_REQUEST['site_id']);
$mode = test_input($_REQUEST['mode']);

////fuctions////

/** This function return a list of users who are not assigned to any site
 * @param $conn
 * @param $manager_id
 */

function get_unallocated_users($conn, $manager_id) {
    $sql = "SELECT * FROM user_tbl 
            WHERE manager_id = $manager_id
                AND active_status = 1
                AND user_id NOT IN (SELECT user_id FROM representative_allocated
                                        WHERE site_rep_active_status = 1)";

    return $conn->query($sql);
}//correct

/** This function is used for mode = modify
 * @param $conn
 * @param $allocated_id
 */
function get_current_rep_user_info($conn, $site_id) {
    $sql = "SELECT * FROM user_tbl, representative_allocated
            WHERE user_tbl.user_id = representative_allocated.user_id 
                  AND representative_allocated.site_id = $site_id
                  AND representative_allocated.site_rep_active_status=1";

    $ret = $conn->query($sql);

    return mysqli_fetch_assoc($ret);
}//correct

/**delete a site
 * @param $conn
 * @param $site_id
 */
function deactivate_user_allocation($conn, $allocate_id) {
    $sql = "UPDATE representative_allocated set site_rep_active_status = 0
            WHERE site_alloc_id = $allocate_id";

    return mysqli_query($conn, $sql);
}

/**
 * @param $conn
 * @param $site_id
 * @param $user_id
 */
function insert_user_allocation($conn, $site_id, $user_id) {
    $sql = "INSERT INTO representative_allocated(site_rep_allocated_date, site_rep_active_status, site_id, user_id)
              VALUES(NOW(), 1, $site_id, $user_id)";

    return mysqli_query($conn, $sql);
}

// Unassigned users of the manager
$unallocated_users = get_unallocated_users($conn, $login_user['user_id']); // correct


if (!isset($_POST['btnSave']) && !isset($_POST['btnRemove'])) {

    // get site info_ reuse the function from site_function
    $site_info = get_site_by_id($conn, $site_id);//correct

    if ($mode == 'modify') {
        $cur_user = get_current_rep_user_info($conn, $site_id);//

    }

} else if (isset($_POST['btnSave'])) {
    // insert new allocation_giu nguyen
    if ($mode == 'new') {
        $user_id = $_POST['slUser'];

        //insert_user_allocation($conn, $site_id, $user_id);
        insert_user_allocation($conn, $site_id, $user_id);
        header("Location:site_manage.php");
    }

    if ($mode == 'modify') {
        // deactivate the old user
        $allocate_id = $_POST['allocate_id'];

        deactivate_user_allocation($conn, $allocate_id);

        // add new allocation
        $user_id = $_POST['slUser'];

        //insert_user_allocation($conn, $site_id, $user_id);
        insert_site_allocation($conn, $site_id, $user_id);
        header("Location:site_manage.php");
    }

} else if (isset($_POST['btnRemove'])) {

    $allocate_id = $_POST['allocate_id'];
    deactivate_user_allocation($conn, $allocate_id);
    header("Location:site_manage.php");
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
                <div class="page-title"><span>Site Allocation</span></div>
                <div class="page-content">
                    <form action="site_allocation.php" class="form-horizontal" method="post">
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Site Name:</label>
                            <div class="col-sm-8 username-label">
                                <strong><?php echo $site_info['site_name']?></strong>
                            </div>
                        </div>
                        <?php if ($mode == 'modify') { ?>
                            <div class="form-group">
                                <label for="" class="col-sm-3 control-label">Current Allocated User: </label>
                                <div class="col-sm-8 username-label">
                                    <strong><?php echo html_escape($cur_user['firstname']) . ' ' . html_escape($cur_user['lastname'])?></strong>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Allocate User:</label>
                            <div class="col-sm-8">
                                <select name="slUser" class="form-control" required>

                                    <?php foreach ($unallocated_users as $user) { ?>
                                        <option value="<?php echo $user['user_id'];?>"><?php echo html_escape($user['firstname']) . ' ' . html_escape($user['lastname']);?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group button-group">
                            <?php if ($mode == 'new') { ?>
                                <div class="col-sm-offset-5 col-sm-3 col-xs-offset-4 col-xs-4">
                                    <button type="submit" class="btn btn-primary btn-block" name="btnSave">Save</button>
                                </div>
                                <div class="col-sm-3  col-xs-4">
                                    <button type="submit" class="btn btn-default btn-block" name="btnCancel">Cancel</button>
                                </div>
                            <?php } else if ($mode == 'modify') { ?>
                                <div class="col-sm-offset-3 col-sm-3 col-xs-4">
                                    <button type="submit" class="btn btn-primary btn-block" name="btnSave">Change</button>
                                </div>
                                <div class="col-sm-3  col-xs-4">
                                    <button type="submit" class="btn btn-danger btn-block" name="btnRemove">Delete Allocation</button>
                                </div>
                                <div class="col-sm-3  col-xs-4">
                                    <button type="submit" class="btn btn-default btn-block" name="btnCancel">Cancel</button>
                                </div>
                            <?php } ?>
                        </div>

                        <input type="hidden" value="<?php echo $mode?>" name="mode">
                        <input type="hidden" value="<?php echo $allocate_id?>" name="allocate_id">
                        <input type="hidden" value="<?php echo $site_id?>" name="site_id">
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