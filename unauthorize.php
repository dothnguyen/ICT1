<?php
// check for login
require_once "db.php";
require_once "user_functions.php";
require_once "other_functions.php";


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

    <title>Login</title>
</head>
<body>

<div class="container login-container">
    <div class="row ">
        <div class="col-xs-12 col-sm-offset-3 col-sm-6 ">
            <div class="panel panel-default login-panel">
                <div class="panel-body">

                    <div class="col-xs-12">
                        <span>You are not authorized to access the page. Please choose one option below</span>
                    </div>
                    <div class="col-xs-6">
                        <a href="#" onclick="window.history.back();" class="btn btn-default">Go Back</a>
                    </div>
                    <div class="col-xs-6">
                        <a href="logout.php" class="btn btn-default">Logout</a>
                    </div>
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