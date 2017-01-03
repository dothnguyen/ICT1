<?php
    // check for login
    require_once "db.php";
    require_once "customer_functions.php";
    require_once "other_functions.php";

    session_start();

    // check if user already logged in, then redirect to index page
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {

        header("Location:index.php");

    } else {

        $msg = array();

        if (isset($_POST['login'])) {

            // get login information
            $email = test_input($_POST['inputEmail']);
            $pwd = test_input($_POST['inputPassword']);

            if (empty($email)) {
                array_push($msg, "Please input your email.");
            }

            if (empty(($pwd))) {
                array_push($msg, "Please input your password.");
            }

            // check if email or pwd is valid or not
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                array_push($msg, "Invalid email address.");
            }

            if (empty($msg)) {
                // check email and password
                $conn = db_connect();

                $ret = check_customer($conn, $email, $pwd);

                if (!ret) {
                    array_push($msg, "Invalid email address or password.");
                }

                mysqli_close($conn);
            }

            if (empty($msg)) {

                // check email and password
                $conn = db_connect();
                // get user info
                $cus_row = get_customer($conn, $email);
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
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <button type="button" class="navbar-toggle pull-right" data-toggle="collapse" data-target=".navbar-ex1-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <div class="navbar-header">
            <a href="#" class="navbar-brand">MATRIX Chocolates</a>
        </div>

        <div class="navbar-ex1-collapse navbar-collapse collapse" role="navigation">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#">HOME</a></li>
                <li><a href="#">HOW TO ORDER</a></li>
                <li><a href="#">CONTACT US</a></li>
                <li><a href="#">Log in</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <ul class="breadcrumb">
                <li><a href="index.php">Home</a></li>
                <li class="active">Login</li>

            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-offset-3 col-sm-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title">Login </h2>
                </div>
                <div class="panel-body">
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
                            <label for="inputEmail" class="col-sm-2 control-label">Email</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" id="inputEmail" name="inputEmail" placeholder="Email">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword" class="col-sm-2 control-label">Password</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" id="inputPassword" name="inputPassword" placeholder="Password">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="checkRemember"> Remember me
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-8">
                                <button type="submit" class="btn btn-default btn-block" name="login">Login</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</div>

<br><br><br><br><br><br><br>
<footer class="container center">Copyright Matrix Chocolates - 2016</footer>

<script src="js/jquery-1.12.3.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/script.js"></script>
</body>
</html>