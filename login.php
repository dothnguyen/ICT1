<?php
    // check for login
    require_once "db.php";
    require_once "user_functions.php";
    require_once "other_functions.php";

    session_start();

    // check if user already logged in, then redirect to index page
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {

        header("Location:index.php");

    } else {

        $msg = array();

        if (isset($_POST['login'])) {

            // get login information
            $username = test_input($_POST['inputUsername']);
            $pwd = test_input($_POST['inputPassword']);

            if (empty($username)) {
                array_push($msg, "Please input your username.");
            }

            if (empty(($pwd))) {
                array_push($msg, "Please input your password.");
            }


            if (empty($msg)) {
                // check username and password
                $conn = db_connect();

                $ret = check_user($conn, $username, $pwd);

                if (!$ret) {
                    array_push($msg, "Invalid username or password.");
                }

                mysqli_close($conn);
            }

            if (empty($msg)) {

                // check email and password
                $conn = db_connect();
                // get user info
                $cus_row = get_user($conn, $username);
                mysqli_close($conn);

                $_SESSION['logged_in'] = true;
                $_SESSION['user_info'] = $cus_row;

                // login successful
                // redirect to home page
                header("Location:index.php");
            }
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
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">

    <title>Login</title>
</head>
<body>

<div class="container login-container">
    <div class="row ">
        <div class="col-xs-12 col-sm-offset-3 col-sm-6 ">
            <div class="panel panel-default login-panel">
                <div class="panel-body">

                    <img src="images/logo.png" class="img-responsive center-block logo-img">

                    <form class="form-horizontal" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                        <?php if (!empty($msg))  { ?>
                        <div class="alert alert-danger">
                            <strong>Error!</strong><br/>
                            <?php foreach ($msg as $err)  { ?>
                                 &nbsp;&nbsp;<?php echo $err?>.<br/>
                            <?php }?>
                        </div>
                        <?php }?>
                        <div class="form-group">
                            <label for="inputUsername" class="col-sm-3 control-label">Username</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="inputUsername" name="inputUsername" placeholder="Username">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword" class="col-sm-3 control-label">Password</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" id="inputPassword" name="inputPassword" placeholder="Password">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-6">
                                <button type="submit" class="btn btn-default btn-block" name="login">Login</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/jquery-1.12.3.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/script.js"></script>
</body>
</html>