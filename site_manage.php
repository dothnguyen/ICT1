<?php
/**
 * Created by PhpStorm.
 * User: voiu
 * Date: 1/15/17
 * Time: 12:17 PM
 */

require_once "db.php";
require_once "site_functions.php";
require_once  "other_functions.php";

session_start();

// check if user loged in
check_login();

// check if login user has the privileges
check_authorize(true);



$conn = db_connect();

// get logged-in user info
$login_user = $_SESSION['user_info'];

$sites = get_sites_of_manager($conn, $login_user['user_id']);

$idx = 0;

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

    <title>Sites Management</title>
</head>
<body>
<?php include_once 'header.php';?>
<?php include_once 'nav.php';?>

<section class="main-content">
    <div class="container">
        <div  class="row">
            <div class="col-xs-offset-2 col-xs-8 col-md-offset-0 col-md-3">
                <div class="left-panel text-center">
                    <a href="site_modify.php?mode=new" class="btn btn-primary btn-addnew">Add Site</a>
                </div>
            </div>
            <div class="col-xs-12  col-md-9">
                <div class="right-panel">
                    <div class="page-title"><span>Site List</span></div>
                    <div class="page-content">
                        <div class="site-list-container">
                            <table class="table-bordered table-striped table-hover table-responsive site-table">
                                <thead>
                                    <td>No.</td>
                                    <td>Site Information</td>
                                    <td>Action</td>
                                </thead>
                                <?php
                                    foreach ($sites as $site) {
                                ?>
                                        <tr>
                                            <td class="index-column"><?php echo ($idx + 1)?></td>
                                            <td class="site-info-column">
                                                <div>
                                                    <div><strong><?php echo $site['site_name'];?></strong> - <span>Tel: <?php echo $site['telephone'];?></span></div>
                                                    <div><spa>Address: <?php echo $site['address'];?></spa></div>
                                                </div>
                                            </td>
                                            <td class="action-column">
                                                <a href="site_modify.php?mode=modify&site_id=<?php echo $site['site_id']?>" class="btn btn-block btn-default eddo">Edit</a>

                                            </td>
                                        </tr>
                                <?php
                                        $idx++;
                                    }
                                ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<script src="js/jquery-1.12.3.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/script.js"></script>
</body>
</html>