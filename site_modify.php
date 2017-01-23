<?php
/**
 * Created by PhpStorm.
 * User: voiu
 * Date: 1/23/17
 * Time: 8:31 PM
 */

require_once "db.php";
require_once "site_functions.php";
require_once "other_functions.php";

session_start();

// get logged-in user info
$login_user = $_SESSION['user_info'];

$msg = array();

if (isset($_POST['btnCancel'])) {
    header("Location:site_manage.php");
}

$mode = test_input($_REQUEST['mode']);

$site_name = "";
$site_addr = "";
$site_tel = "";
$site_id  = "";

// check if mode is modify,
// then load the current site
if ($mode == 'modify') {
    $site_id = test_input($_REQUEST['site_id']);

    $conn = db_connect();

    $site_info = get_site_by_id($conn, $site_id);

    $site_name  = $site_info['site_name'];
    $site_addr = $site_info['address'];
    $site_tel = $site_info['telephone'];

    mysqli_close($conn);
}

// submitted
if (isset($_POST['btnSave'])) {
    $site_name = test_input($_POST['txtSiteName']);
    $site_addr = test_input($_POST['txtAddress']);
    $site_tel = test_input($_POST['txtPhoneNumber']);

    // validation
    if ($site_name == "") {
        $msg['site_name'] = "Site name can not be empty.";
    }

    if ($site_addr == "") {
        $msg['site_address'] = "Site address can not be empty.";
    }

    if ($site_tel == "") {
        $msg['site_tel'] = "Site phone number can not be empty.";
    }

    if (empty($msg)) {
        $conn = db_connect();
        if ($mode == 'modify') {
            modify_site($conn, $site_id, $site_name, $site_addr, $site_tel);
        } else if ($mode == 'new') {
            insert_site($conn, $site_name, $site_addr, $site_tel, $login_user['user_id']);
        }

        header("Location:site_manage.php");
        mysqli_close($conn);
    }
}
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

    <title>Sites Management</title>
</head>
<body>
<?php include_once 'header.php';?>
<?php include_once 'nav.php';?>

<section class="main-content">
    <div class="container">
        <div class="col-xs-12 col-md-9 col-md-push-3">
            <div class="right-panel">
                <div class="page-title"><span>Add / Modify Site</span></div>
                <div class="page-content">
                    <form action="site_modify.php" class="form-horizontal" method="post">
                        <div class="form-group <?php if (!empty($msg['site_name'])) echo "has-error";?>">
                            <label for="txtSiteName" class="col-sm-3 control-label">Site Name</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="txtSiteName" name="txtSiteName" value="<?php echo $site_name ?>"/>
                            </div>
                            <?php if (!empty($msg['site_name'])) {?>
                                <div class="col-sm-offset-3 col-sm-8">
                                    <span class="help-block"><?php echo $msg['site_name']?></span>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="form-group <?php if (!empty($msg['site_address'])) echo "has-error";?>">
                            <label for="txtAddress" class="col-sm-3 control-label">Site Address</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="txtAddress" name="txtAddress" value="<?php echo $site_addr ?>"/>
                            </div>
                            <?php if (!empty($msg['site_address'])) {?>
                                <div class="col-sm-offset-3 col-sm-8">
                                    <span class="help-block"><?php echo $msg['site_address']?></span>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="form-group <?php if (!empty($msg['site_tel'])) echo "has-error";?>">
                            <label for="txtPhoneNumber" class="col-sm-3 control-label">Phone Number</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" maxlength="12" id="txtPhoneNumber" name="txtPhoneNumber" value="<?php echo $site_tel ?>"/>
                            </div>
                            <?php if (!empty($msg['site_tel'])) {?>
                                <div class="col-sm-offset-3 col-sm-8">
                                    <span class="help-block"><?php echo $msg['site_tel']?></span>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="form-group button-group">
                            <div class="col-sm-offset-5 col-sm-3 col-xs-offset-4 col-xs-4">
                                <button type="submit" class="btn btn-default btn-block" name="btnSave">Save</button>
                            </div>
                            <div class="col-sm-3  col-xs-4">
                                <button type="submit" class="btn btn-default btn-block" name="btnCancel">Cancel</button>
                            </div>
                        </div>

                        <input type="hidden" name="mode" value="<?php echo $mode;?>"/>
                        <input type="hidden" name="site_id" value="<?php echo $site_id;?>"/>
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
