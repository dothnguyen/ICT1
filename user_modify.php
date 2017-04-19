<?php
/**
 * Created by PhpStorm.
 * User: voiu
 * Date: 1/23/17
 * Time: 8:31 PM
 * User: Shanil Frank added email functionality, generateRandomPassword, generateuser
 */

require_once "db.php";
require_once "site_functions.php";
require_once "other_functions.php";

session_start();

// check if user loged in
check_login();

// check if user can access manager's page
check_authorize(true);

// get logged-in user info
$login_user = $_SESSION['user_info'];

$msg = array();

if (isset($_POST['btnCancel'])) {
    header("Location:user_manage.php");
}

$mode = test_input($_REQUEST['mode']);

$user_id = "";
$firstname = "";
$lastname = "";
$email = "";
$new_username="";
$active_status = "1";
$is_allocated = false;
$generate_password="";

//function to generate random 8 character password
function generateRandomPassword($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $count = mb_strlen($chars);

    for ($i = 0, $result = ''; $i < $length; $i++) {
        $index = rand(0, $count - 1);
        $result .= mb_substr($chars, $index, 1);
    }

    return $result;
}

//function generate username

function generateuser ($firstname,$lastname){
	
	$generateduser=$firstname.$lastname;
	return $generateduser;
}

// submitted
if (isset($_POST['btnSave'])) {
    $firstname = test_input($_POST['txtFirstName']);
    $lastname = test_input($_POST['txtLastName']);
    $email = test_input($_POST['txtEmail']);
    $username = test_input($_POST['txtUsername']);
    $user_id = test_input($_POST['user_id']);
	$new_username= test_input($_POST['txtNewUsername']);
	$generate_password=md5(test_input($_POST['txtPassword']));
	

    // validation
    if ($firstname == "") {
        $msg['firstname'] = "First name can not be empty.";
    }

    if ($lastname == "") {
        $msg['lastname'] = "Last name can not be empty.";
    }

	
	// email validation final
    if ($email == "") {
        $msg['email'] = "Email can not be empty.";
    } else{
		if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
		
		} else {
		$msg['email'] = "Email is not a valid email address";
		}
	}
	
	
	

			
		
    

    if (empty($msg)) {
        $conn = db_connect();
        if ($mode == 'modify') {

            modify_user($conn, $user_id, $firstname, $lastname, $email, $username);

        } else if ($mode == 'new') {
			
			//$new_username=generateuser($firstname,$lastname);
			// the below call checks for the username taken or not.
			$temp_username= test_input($_POST['txtNewUsername']);


			$booln=is_username_taken($conn,$temp_username);
			
			if($booln == 0) {
				
				insert_new_user($conn, $firstname, $lastname, $email, $new_username,  $generate_password, $login_user['user_id']);
			
				//send email to the new user
				$emailpass = test_input($_POST['txtPassword']);
				$email_msg="Hello $firstname $lastname. \n Username: $new_username \n Password: $emailpass";
				$final_email= wordwrap($email_msg,70);
				
				//send email
				mail($email,"Welcome New User",$final_email,"Boral");
				header("Location:user_manage.php");
			}
			// 
			else {
			$msg['new_username'] = "Username taken, change the username";
			}
			
			
        }

        mysqli_close($conn);

        
    }
} else if (isset($_POST['btnRemove'])) {
    // inactivate site
    $user_id = test_input($_POST['user_id']);
    $conn = db_connect();

    inactivate_site($conn, $user_id);

    mysqli_close($conn);

    header("Location:user_manage.php");
} else {
    // check if mode is modify,
    // then load the current site
    if ($mode == 'modify') {
        $user_id = test_input($_REQUEST['user_id']);

        $conn = db_connect();

        $user_info = get_user_by_id($conn, $user_id);

        $firstname = $user_info['firstname'];
        $lastname = $user_info['lastname'];
        $email = $user_info['email'];
        $username = $user_info['username'];
        $active_status = $user_info['active_status'];

        // get site allocation
        $is_allocated = is_user_allocated($conn, $user_id);

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

    <title><?php if ($mode == 'new') echo 'Add User'; else echo 'Modify User';?></title>
</head>
<body>
<?php include_once 'header.php'; ?>
<?php include_once 'nav.php'; ?>



<section class="main-content">
    <div class="container">
        <div class="col-xs-12 col-md-9 col-md-push-2">
            <div class="right-panel">
                <div class="page-title"><span><?php if ($mode == 'new') echo 'Add User'; else echo 'Modify User';?></span></div>
                <div class="page-content">
                    <form action="user_modify.php" class="form-horizontal" method="post">
                        <div class="form-group <?php if (!empty($msg['firstname'])) echo "has-error"; ?>">
                            <label for="txtFirstName" class="col-sm-3 control-label">First Name</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="txtFirstName" name="txtFirstName"
                                       value="<?php echo $firstname ?>"/>
                            </div>
                            <?php if (!empty($msg['firstname'])) { ?>
                                <div class="col-sm-offset-3 col-sm-8">
                                    <span class="help-block"><?php echo $msg['firstname'] ?></span>
                                </div>
                            <?php } ?>

                        </div>
                        <div class="form-group <?php if (!empty($msg['lastname'])) echo "has-error"; ?>">
                            <label for="txtLastName" class="col-sm-3 control-label">Last Name</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="txtLastName" name="txtLastName"
                                       value="<?php echo $lastname ?>"/>
                            </div>
                            <?php if (!empty($msg['lastname'])) { ?>
                                <div class="col-sm-offset-3 col-sm-8">
                                    <span class="help-block"><?php echo $msg['lastname'] ?></span>
                                </div>
                            <?php } ?>

                        </div>

                        <div class="form-group <?php if (!empty($msg['email'])) echo "has-error"; ?>">
                            <label for="txtEmail" class="col-sm-3 control-label">Email</label>

                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="txtEmail" name="txtEmail"
                                       value="<?php echo $email ?>" />
                            </div>

                            <?php if (!empty($msg['email'])) { ?>
                                <div class="col-sm-offset-3 col-sm-8">
                                    <span class="help-block"><?php echo $msg['email'] ?></span>
                                </div>
                            <?php } ?>
                        </div>


                        <?php if ($mode == 'new') { ?>

                            <div class="form-group <?php if (!empty($msg['new_username'])) echo "has-error"; ?>">
                                <label for="txtNewUsername" class="col-sm-3 control-label">Username</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="txtNewUsername" name="txtNewUsername" value="<?php echo $new_username;?>"/>
                                </div>
                                <?php if (!empty($msg['new_username'])) { ?>
                                    <div class="col-sm-offset-3 col-sm-8">
                                        <span class="help-block"><?php echo $msg['new_username'] ?></span>
                                    </div>
                                <?php } ?>
								
								
                            </div>
							
							<?php $generate_password=generateRandomPassword(); ?>
							<div class="form-group <?php if (!empty($msg['generate_password'])) echo "has-error"; ?>">
                                <label for="txtPassword" class="col-sm-3 control-label">Password</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="txtPassword" name="txtPassword" value="<?php echo $generate_password;?>"/>
                                </div>
                                <?php if (!empty($msg['generate_password'])) { ?>
                                    <div class="col-sm-offset-3 col-sm-8">
                                        <span class="help-block"><?php echo $msg['generate_password'] ?></span>
                                    </div>
                                <?php } ?>
								
								
                            </div>

                            <div class="form-group button-group">
                                <div class="col-sm-offset-5 col-sm-3 col-xs-offset-3 col-xs-3">
                                    <button type="submit" class="btn btn-primary btn-block" name="btnSave">Save</button>
                                </div>


                                <div class="col-sm-3  col-xs-3">
                                    <button type="submit" class="btn btn-default btn-block" name="btnCancel">Cancel
                                    </button>
                                </div>
                            </div>


                        <?php } ?>

                        <?php if ($mode == 'modify') { ?>

                            <?php if ($is_allocated) { ?>
                                <div class="form-group button-group">
                                    <div class="col-sm-offset-5 col-sm-3 col-xs-offset-4 col-xs-4">
                                        <button type="submit" class="btn btn-primary btn-block" name="btnSave">Save
                                        </button>
                                    </div>
                                    <div class="col-sm-3  col-xs-4">
                                        <button type="submit" class="btn btn-default btn-block" name="btnCancel">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="form-group button-group">
                                    <div class="col-sm-offset-2 col-sm-3 col-xs-offset-3 col-xs-3">
                                        <button type="submit" class="btn btn-primary btn-block" name="btnSave">Save
                                        </button>
                                    </div>

                                    <div class="col-sm-3 col-xs-3">
                                        <button type="submit" class="btn btn-danger btn-block" name="btnRemove">Remove
                                        </button>
                                    </div>

                                    <div class="col-sm-3  col-xs-3">
                                        <button type="submit" class="btn btn-default btn-block" name="btnCancel">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            <?php } ?>

                        <?php } ?>

                        <input type="hidden" name="mode" value="<?php echo $mode; ?>"/>
                        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>"/>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-3 col-md-pull-9">
            <div class="text-center">

            </div>
        </div>
    </div>
</section>

<script src="js/jquery-1.12.3.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/script.js"></script>


<script>
    function myFunction(){
        //var x =document.getElementById("txtFirstName").value;
        //var y = document.getElementById("txtLastName").value;
        //document.getElementById("txtNewUsername").innerHtml = x + y;

        $("#txtNewUsername").val($("#txtFirstName").val().toLowerCase() + "_" + $("#txtLastName").val().toLowerCase());
    }

    $("#txtFirstName").change(myFunction);
    $("#txtLastName").change(myFunction);

</script>

</body>
</html>
