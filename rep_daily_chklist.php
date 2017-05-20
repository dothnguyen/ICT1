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

$site_alloc_id = 0;
$site_name = "";

$checklist1 = 0;
$checklist2 = 0;
$checklist3 = 0;
$checklist4 = 0;
$checklist5 = 0;
$checklist6 = 0;
$checklist7 = 0;
$checklist8 = 0;
$checklist9 = 0;
$comment = "";
$uploaded_images = array();

$chklist_date = "";

$cur_date = "";

// handle form submission
if (isset($_REQUEST['submit'])) {

    $conn = db_connect();

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
    $checklist1 = isset($_POST['checklist1']) ? $_POST['checklist1'] : 0;
    $checklist2 = isset($_POST['checklist2']) ? $_POST['checklist2'] : 0;
    $checklist3 = isset($_POST['checklist3']) ? $_POST['checklist3'] : 0;
    $checklist4 = isset($_POST['checklist4']) ? $_POST['checklist4'] : 0;
    $checklist5 = isset($_POST['checklist5']) ? $_POST['checklist5'] : 0;
    $checklist6 = isset($_POST['checklist6']) ? $_POST['checklist6'] : 0;
    $checklist7 = isset($_POST['checklist7']) ? $_POST['checklist7'] : 0;
    $checklist8 = isset($_POST['checklist8']) ? $_POST['checklist8'] : 0;
    $checklist9 = isset($_POST['checklist9']) ? $_POST['checklist9'] : 0;

    $comment = mysqli_real_escape_string($conn, test_input($_POST['comment']));

    $site_alloc_id = test_input($_POST['site_alloc_id']);


    // save data to daily checklist
    $sql = "INSERT INTO daily(d_created_date, d_comments, d_checklist1, d_checklist2, d_checklist3, d_checklist4, d_checklist5, d_checklist6, d_checklist7, d_checklist8, d_checklist9, site_alloc_id)
                  VALUES(NOW(), '$comment', $checklist1, $checklist2, $checklist3, $checklist4, $checklist5, $checklist6, $checklist7, $checklist8, $checklist9, $site_alloc_id);";

    // insert
    $conn->query($sql);
    $last_id = $conn->insert_id;

    // insert uploaded files
    foreach ($saved_files as $file_path) {
        $sql = "INSERT INTO upload(chklist_id, chklist_type, file_path) VALUES($last_id, 1, '$file_path');";
        $conn->query($sql);
    }

    mysqli_close($conn);

    header("Location:rep_daily_chklist.php");

} else {
    $conn = db_connect();

    $mode = "";

    // get current check list
    $current_checklist = get_current_daily_chklist($conn, $login_user['user_id']);

    if (empty($current_checklist)) {
        // get the current allocation of the user
        $user_allocation = get_user_allocation($conn, $login_user['user_id']);

        if ($user_allocation == null) {
            $has_allocation = false;
        } else {
            $site_name = $user_allocation['site_name'];
            $site_alloc_id = $user_allocation['site_alloc_id'];
        }

        $cur_date = date('d/m/Y');

    } else {

        $mode = "view";

        // init view mode
        $checklist1 = $current_checklist['d_checklist1'];
        $checklist2 = $current_checklist['d_checklist2'];
        $checklist3 = $current_checklist['d_checklist3'];
        $checklist4 = $current_checklist['d_checklist4'];
        $checklist5 = $current_checklist['d_checklist5'];
        $checklist6 = $current_checklist['d_checklist6'];
        $checklist7 = $current_checklist['d_checklist7'];
        $checklist8 = $current_checklist['d_checklist8'];
        $checklist9 = $current_checklist['d_checklist9'];

        $comment = html_escape($current_checklist['d_comments']);

        $site_name = $current_checklist['site_name'];

        $chklist_date = date('d/m/Y H:i:s', strtotime($current_checklist['d_created_date']));

        // get list of images
        $uploaded_images = get_uploaded_images($conn, $current_checklist['daily_id']);

    }
    mysqli_close($conn);
}

/**
 * @param $conn
 * @param $chklist_id
 */
function get_uploaded_images($conn, $chklist_id) {
    $sql = "SELECT * FROM upload WHERE chklist_id=$chklist_id AND chklist_type=1";

    $ret = $conn->query($sql);

    $images = array();

    if (!empty($ret)) {
        foreach ($ret as $upload) {
            $images[] = $upload['file_path'];
        }
    }

    return $images;
}

/**
 * @param $conn
 * @param $user_id
 */
function get_current_daily_chklist($conn, $user_id) {
    $sql = "SELECT d.*, s.site_name FROM daily d, representative_allocated rep, site s WHERE d.site_alloc_id = rep.site_alloc_id 
            AND rep.user_id=$user_id AND DATE(d.d_created_date) = DATE(NOW())
            AND rep.site_id = s.site_id";

    return mysqli_fetch_assoc($conn->query($sql));
}

/**
 */
function get_user_allocation($conn, $user_id) {
    $sql = "SELECT rep.*, site.site_name FROM representative_allocated rep, site WHERE user_id=$user_id AND site_rep_active_status = 1 AND rep.site_id = site.site_id;";

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
        <?php if ($mode == 'view') { ?>
            <div  class="row">
                <div class="col-md-2 hidden-sm"></div>
                <div class="col-md-8 col-sm-12"><span class="title">The checklist has been submitted on <strong><?php echo $chklist_date;?></strong></span></div>
            </div>
        <?php } else {?>
        <div  class="row">
            <div class="col-md-2 hidden-sm"></div>
            <div class="col-md-8 col-sm-12"><span class="title">Daily Checklist on: <strong><?php echo $cur_date ?></strong></span></div>
        </div>
        <?php } ?>
        <div  class="row">
            <div class="col-md-2 hidden-sm"></div>
            <div class="col-md-8 col-sm-12"><span class="title">For site: <strong><?php echo $site_name; ?></strong></span></div>
        </div>
        <div class="row checklist-title">
            <div class="col-md-2 hidden-sm"></div>
            <div class="col-md-8 col-sm-12">
                <span class="">Checklist</span>
            </div>
        </div>
        <div class="row checklist-controls">
            <div class="col-md-2 hidden-sm"></div>
            <div class="col-md-8 col-sm-12">
                <div class="row control-wrapper">
                    <div class="col-xs-6 col-sm-4">
                        <input type="checkbox" name="checklist1" value="1" id="check1" <?php if ($mode == 'view') echo 'disabled'; if ($checklist1 == 1) echo ' checked';?>> <label for="check1">Check 1</label>
                    </div>
                    <div class="col-xs-6 col-sm-4">
                        <input type="checkbox" name="checklist2" value="1" id="check2" <?php if ($mode == 'view') echo 'disabled'; if ($checklist2 == 1) echo ' checked';?>> <label for="check2">Check 2</label>
                    </div>
                    <div class="col-xs-6 col-sm-4">
                        <input type="checkbox" name="checklist3" value="1" id="check3" <?php if ($mode == 'view') echo 'disabled'; if ($checklist3 == 1) echo ' checked';?>> <label for="check3">Check 3</label>
                    </div>
                    <div class="col-xs-6 col-sm-4">
                        <input type="checkbox" name="checklist4" value="1" id="check4" <?php if ($mode == 'view') echo 'disabled'; if ($checklist4 == 1) echo ' checked';?>> <label for="check4">Check 4</label>
                    </div>
                    <div class="col-xs-6 col-sm-4">
                        <input type="checkbox" name="checklist5" value="1" id="check5" <?php if ($mode == 'view') echo 'disabled'; if ($checklist5 == 1) echo ' checked';?>> <label for="check5">Check 5</label>
                    </div>
                    <div class="col-xs-6 col-sm-4">
                        <input type="checkbox" name="checklist6" value="1" id="check6" <?php if ($mode == 'view') echo 'disabled'; if ($checklist6 == 1) echo ' checked';?>> <label for="check6">Check 6</label>
                    </div>
                    <div class="col-xs-6 col-sm-4">
                        <input type="checkbox" name="checklist7" value="1" id="check7" <?php if ($mode == 'view') echo 'disabled'; if ($checklist7 == 1) echo ' checked';?>> <label for="check7">Check 7</label>
                    </div>
                    <div class="col-xs-6 col-sm-4">
                        <input type="checkbox" name="checklist8" value="1" id="check8" <?php if ($mode == 'view') echo 'disabled'; if ($checklist8 == 1) echo ' checked';?>> <label for="check8">Check 8</label>
                    </div>
                    <div class="col-xs-6 col-sm-4">
                        <input type="checkbox" name="checklist9" value="1" id="check9" <?php if ($mode == 'view') echo 'disabled'; if ($checklist9 == 1) echo ' checked';?>> <label for="check9">Check 9</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="row checklist-title">
            <div class="col-md-2 hidden-sm"></div>
            <div class="col-md-8 col-sm-12">
                <span>Comment</span>
            </div>
        </div>
        <div class="row checklist-controls">
            <div class="col-md-2"></div>
            <div class="col-md-8 col-sm-12">
                <textarea name="comment" id="comment" rows="10" <?php if ($mode == 'view') echo 'readonly="true"';?>><?php echo $comment?></textarea>
            </div>
        </div>
        <div class="row checklist-title">
            <div class="col-md-2 hidden-sm"></div>
            <div class="col-md-8 col-sm-12">
                <span>Attachments</span>
            </div>
        </div>
        <?php if ($mode != 'view') { ?>
        <div class="row checklist-controls">
            <div class="col-md-2 hidden-sm"></div>
            <div class="col-md-8 col-sm-12">
                <div class="row control-wrapper">
                    <div class="col-xs-12 file-wrapper">
                        <input type='file' name='files[]' accept='.png, .jpg, .jpeg'><a class="btn btn-sm remove"><span class=" fa fa-times"></span></a>
                    </div>
                    <div class="col-xs-12 file-wrapper">
                        <input type='file' name='files[]' accept='.png, .jpg, .jpeg'><a class="btn btn-sm remove"><span class=" fa fa-times"></span></a>
                    </div>
                    <div class="col-xs-12 file-wrapper">
                        <input type='file' name='files[]' accept='.png, .jpg, .jpeg'><a class="btn btn-sm remove"><span class=" fa fa-times"></span></a>
                    </div>
                    <div class="col-xs-12">
                        <a class="btn btn-primary add-file"><span class="fa fa-plus"></span></a>
                    </div>
                </div>
            </div>
        </div>
        <?php } else { ?>
            <div class="row checklist-controls">
                <div class="col-md-2 hidden-sm"></div>
                <div class="col-md-8 col-sm-12">
                    <div class="row control-wrapper">
                        <?php if (!empty($uploaded_images)) { ?>
                            <?php foreach ($uploaded_images as $image) { ?>
                                <img src="<?php echo $image?>"><br/>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="row checklist-controls">
            <div class="col-md-2 hidden-sm"></div>
            <div class="col-md-8 col-sm-12">
                <button type="submit" name="submit" class="btn btn-primary pull-right <?php if ($mode == 'view') echo 'btn-disabled';?>" <?php if ($mode == 'view') echo 'disabled';?>>&nbsp;&nbsp;Submit&nbsp;&nbsp;</button>
            </div>
        </div>
    </div>
        <input type="hidden" name="site_alloc_id" value="<?php echo $site_alloc_id?>">
    </form>
    <?php } ?>
</section>

<script src="js/jquery-1.12.3.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/script.js"></script>

</body>
</html>
