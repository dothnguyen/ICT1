<?php
/**
 * Created by PhpStorm.
 * User: voiu
 * Date: 1/15/17
 * Time: 12:17 PM
 */

require_once "db.php";
require_once "site_functions.php";
require_once "other_functions.php";

session_start();

// check if user loged in
check_login();

// check if user can access manager's page
check_authorize(true);


$conn = db_connect();

// get logged-in user info
$login_user = $_SESSION['user_info'];

$represents = get_allusers($conn, $login_user['user_id']);
$allsites= get_allsites ($conn, $login_user['user_id']);

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

    <title>Add New Representative</title>
</head>
<body>
<?php include_once 'header.php';?>
<?php include_once 'nav.php';?>

<section class="main-content">
    <div class="container">
	<form>
        <div  class="row">
            <div class="col-sm-12 col-md-3">
                <div class="left-panel text-center">
                    <a href="/ict1/site_representative.php" class="btn btn-primary btn-addnew">Go back</a>
                </div>
            </div>
            <div class="col-sm-12 col-md-9">
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
														<strong>Name: <?php echo $represent['firstname'];?> <?php echo $represent['lastname']; ?> &nbsp; ID:  
														<input type="text" name="id" class="form-control" value="<?php echo $represent['user_id']; ?>" readonly/> </strong>
													</div>
														<div><strong>Allocate:</strong></div>
														<div>
														<select name="siteslisting" class="form-control">
															<?php foreach ($allsites as $allsite) {
															?>
														 
														<option> <?php echo $allsite['site_id'];?>-<?php echo $allsite['site_name'];?></option>
																	
														<?php
														}
														?>											  
														</option>
														</select>
														&nbsp;
														
													</div>
													
                                                </div>
                                            </td>
											
                                            <td class="action-column">
                                                <a href="#" class="btn btn-block btn-default" name="allocate">Allocate</a>
												
                                               
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
		
		
		</form>
    </div>
</section>


<script src="js/jquery-1.12.3.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/script.js"></script>
</body>
</html>