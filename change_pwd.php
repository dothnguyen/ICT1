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
check_authorize(true);

// get logged-in user info
$login_user = $_SESSION['user_info'];
$user_id = $login_user['user_id'];

$msg = array();

if (isset($_REQUEST['update'])) {

    // update password
    $pwd1 = test_input($_POST['pwd1']);
    $pwd2 = test_input($_POST['pwd2']);

    if ($pwd1 != $pwd2) {
        array_push($msg, "Retype password must be the same as the New password");
    }

    if (empty($msg)) {
        $conn = db_connect();

        $encode_pwd = md5($pwd1);

        $sql = "UPDATE user_tbl SET password='$encode_pwd', change_pwd=1 WHERE user_id=$user_id";

        mysqli_query($conn, $sql);

        mysqli_close($conn);

        // login successful
        // redirect to home page
        header("Location:index.php");
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

    <title>Reports</title>
</head>
<body>
<?php include_once 'header.php';?>

<section class="main-content">
<div class="container login-container">
    <div class="row ">
        <div class="col-xs-12 col-sm-offset-3 col-sm-7 ">
            <div class="panel panel-default change-pwd-panel">
                <div class="panel-heading">Change Password</div>
                <div class="panel-body">

                    <form id="pwd-form" data-toggle="validator" class="form-horizontal" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                        <?php if (!empty($msg))  { ?>
                            <div class="alert alert-danger">
                                <strong>Error!</strong><br/>
                                <?php foreach ($msg as $err)  { ?>
                                    &nbsp;&nbsp;<?php echo $err?>.<br/>
                                <?php }?>
                            </div>
                        <?php }?>
                        <div class="form-group">
                            <label for="pwd1" class="col-sm-4 control-label">New password</label>
                            <div class="col-sm-8">
                                <input type="password" class="form-control" id="pwd1" name="pwd1" required value="<?php echo $pwd1; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="pwd2" class="col-sm-4 control-label">Retype password</label>
                            <div class="col-sm-8">
                                <input type="password" class="form-control" id="pwd2" name="pwd2" required value="<?php echo $pwd2; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-4 col-sm-5">
                                <button type="submit" class="btn btn-default btn-block" name="update">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</section>

<script src="js/jquery-1.12.3.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/validator.js"></script>
<script src="js/script.js"></script>

</body>
</html>
