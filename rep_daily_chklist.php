<?php
/**
 * Created by PhpStorm.
 * User: voiu
 * Date: 1/15/17
 * Time: 12:17 PM
 */

require_once "db.php";
require_once "other_functions.php";

session_start();

// check if user loged in
check_login();

// check if user can access manager's page
check_authorize(false);

$login_user = $_SESSION['user_info'];

$has_allocation = true;

// handle form submission
if (isset($_REQUEST['submit'])) {

    $saved_files = array();

    // store the file
    $target_dir = 'upload/';

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0700);
    }

    foreach($_FILES['files']['name'] as $key => $tmp_name) {

        // skip empty files
        if ($_FILES['files']['error'][$key] == 4) {
            continue; // Skip file if any error found
        }

        // get file name
        $file_name = $_FILES['files']['name'][$key];
        // get file extension
        $file_ext=strtolower(end(explode('.', $file_name)));

        $file_tmp = $_FILES['files']['tmp_name'][$key];

        // generate upload file name
        $upload_file_name = uniqid(rand(), true) . "." . $file_ext;

        move_uploaded_file($file_tmp, $target_dir . $upload_file_name);

        $saved_files[] = $target_dir . $upload_file_name;
    }

    // save data
    $checklist = $_POST['checklist'];
    $comment = test_input($_POST['comment']);

    // save data to daily checklist
    $sql = "INSERT INTO daily(d_created_date, d_comments, d_checklist1, d_checklist2, d_checklist3, d_checklist4, d_checklist5, d_checklist6, d_checklist7, d_checklist8, d_checklist9, site_alloc_id)";

} else {

    $mode = $_REQUEST['mode'];

    // new mode
    if ($mode != 'view') {

        // get the current allocation of the user
        $conn = db_connect();

        $user_allocation = get_user_allocation($conn, $login_user['user_id']);

        if ($user_allocation == null) {
            $has_allocation = false;
        } else {

        }
    } else {

    }

}


/**
 */
function get_user_allocation($conn, $user_id) {
    $sql = "SELECT rep.*, site.site_name FROM representative_allocated rep, site WHERE user_id=$user_id AND site_rep_active_status = 1 AND rep.site_id = site.site_id";

    $ret = $conn->query($sql);

    if ($ret->num_rows == 1) {
        return mysqli_fetch_assoc($ret);
    }

    return null;
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


    <title>Reports</title>
</head>
<body>
<?php include_once 'header.php';?>
<?php include_once 'nav.php';?>

<section class="main-content">

    <?php if (!$has_allocation) { ?>
    <div class="container">
        <div  class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">You have no allocation at the moment!</div>
        </div>
    </div>
    <?php } else {?>

    <form action="rep_daily_chklist.php" method="POST" enctype="multipart/form-data">
    <div class="container checklist-wrapper">
        <div  class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8"><span class="title">Daily Checklist on: <strong>2017/03/28</strong></span></div>
        </div>
        <div  class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8"><span class="title">For site: <strong><?php echo $user_allocation['site_name']; ?></strong></span></div>
        </div>
        <div class="row checklist-title">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <span class="">Checklist</span>
            </div>
        </div>
        <div class="row checklist-controls">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="row control-wrapper">
                    <div class="col-md-4">
                        <input type="checkbox" name="checklist" id="check1"> <label for="check1">Check 1</label>
                    </div>
                    <div class="col-md-4">
                        <input type="checkbox" name="checklist" id="check2"> <label for="check2">Check 2</label>
                    </div>
                    <div class="col-md-4">
                        <input type="checkbox" name="checklist" id="check3"> <label for="check3">Check 3</label>
                    </div>
                    <div class="col-md-4">
                        <input type="checkbox" name="checklist" id="check4"> <label for="check4">Check 4</label>
                    </div>
                    <div class="col-md-4">
                        <input type="checkbox" name="checklist" id="check5"> <label for="check5">Check 5</label>
                    </div>
                    <div class="col-md-4">
                        <input type="checkbox" name="checklist" id="check6"> <label for="check6">Check 6</label>
                    </div>
                    <div class="col-md-4">
                        <input type="checkbox" name="checklist" id="check7"> <label for="check7">Check 7</label>
                    </div>
                    <div class="col-md-4">
                        <input type="checkbox" name="checklist" id="check8"> <label for="check8">Check 8</label>
                    </div>
                    <div class="col-md-4">
                        <input type="checkbox" name="checklist" id="check9"> <label for="check9">Check 9</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="row checklist-title">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <span>Comment</span>
            </div>
        </div>
        <div class="row checklist-controls">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <textarea name="comment" id="comment" cols="102" rows="10"></textarea>
            </div>
        </div>
        <div class="row checklist-title">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <span>Attachments</span>
            </div>
        </div>
        <div class="row checklist-controls">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="row control-wrapper">
                    <div class="col-xs-12 file-wrapper">
                        <input type='file' name='files[]'><img src='http://images.freescale.com/shared/images/x.gif' class='remove'>
                    </div>
                    <div class="col-xs-12 file-wrapper">
                        <input type='file' name='files[]'><img src='http://images.freescale.com/shared/images/x.gif' class='remove'>
                    </div>
                    <div class="col-xs-12 file-wrapper">
                        <input type='file' name='files[]'><img src='http://images.freescale.com/shared/images/x.gif' class='remove'>
                    </div>
                    <div class="col-xs-12">
                        <a href="#" class="btn btn-primary add-file"><span class="fa fa-plus"></span></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row checklist-controls">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <button type="submit" name="submit" class="btn btn-primary pull-right">&nbsp;&nbsp;Submit&nbsp;&nbsp;</button>
            </div>
        </div>
    </div>
    </form>
    <?php } ?>
</section>

<script src="js/jquery-1.12.3.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/script.js"></script>

</body>
</html>
