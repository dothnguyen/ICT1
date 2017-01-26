<?php
/**
 * Created by PhpStorm.
 * User: voiu
 * Date: 1/15/17
 * Time: 12:17 PM
 */

require_once "db.php";
require_once "site_functions.php";

session_start();

$conn = db_connect();

// get logged-in user info
$login_user = $_SESSION['user_info'];

$represents = get_representative($conn, $login_user['user_id']);

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

    <title>User List</title>
</head>
<body>
<?php include_once 'header.php';?>
<?php include_once 'nav.php';?>

<section class="main-content">
    <div class="container">
        <div  class="row">
            <div class="col-xs-offset-2 col-xs-8 col-md-offset-0  col-md-3">
                <div class="left-panel text-center">
                    <a href="user_modify.php?mode=new" class="btn btn-primary btn-addnew">Add User</a>
                </div>
            </div>
            <div class="col-xs-12 col-md-9">
                <div class="right-panel">
                    <div class="page-title"><span>User List</span></div>
                    <div class="page-content">
                        <div class="site-list-container">
                            <table class="table-bordered table-striped table-hover site-table">
                                <thead>
                                    <td>No.</td>
                                    <td>User Information</td>
                                    <td>Action</td>
                                </thead>
                                <?php
                                    foreach ($represents as $represent) {
                                ?>
                                        <tr>
                                            <td class="index-column"><?php echo ($idx + 1)?></td>
                                            <td class="site-info-column">
                                                <div>
													
													<div>
														<span>Name: <strong> <?php echo $represent['firstname'];?> <?php echo $represent['lastname']; ?></strong> </span>
													</div>
													<div>
														<span>Email: <?php echo $represent['email'];?>  </span>
													</div>
													<div>
                                                        <?php if ($represent['site_rep_active_status'] == '1') { ?>
														<span>Status:
									                    <?php  echo 'Is allocated to ';?><strong><?php  echo $represent['site_name'];?></strong>
                                                        </span>
                                                        <?php } else { ?>
                                                            <span>Status: Available for allocation</span>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="action-column">
                                                <a href="user_modify.php?mode=modify&user_id=<?php echo $represent['user_id']?>" class="btn btn-block btn-default eddo">Edit</a>
                                                <?php if ($represent['site_rep_active_status'] != '1') { ?>
												
                                                    <a href="user_allocation.php?mode=new&user_id=<?php echo $represent['user_id']?>" class="btn btn-block btn-default allo">Allocate</a>
												
                                                <?php } else { ?>
													
                                                    <a href="user_allocation.php?mode=modify&user_id=<?php echo $represent['user_id']?>&allocate_id=<?php echo $represent['site_alloc_id']?>" class="btn btn-block btn-default moddo">Modify Allocation</a>
													
												<?php } ?>
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
	&nbsp;
	&nbsp;
</section>


<script src="js/jquery-1.12.3.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/script.js"></script>
</body>
</html>