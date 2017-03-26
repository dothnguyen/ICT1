<?php
/**
 * Created by PhpStorm.
 * User: voiu
 * Date: 1/15/17
 * Time: 12:17 PM
 */

require_once "other_functions.php";

session_start();

// check if user loged in
check_login();

// check if user can access manager's page
check_authorize(false);

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
    <form action="">
    <div class="container">
        <div  class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8"><span class="title">Daily Checklist for: <strong>2017/03/28</strong></span></div>
        </div>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <span>Checklist</span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-4">
                        <input type="checkbox" name="check1" id="check1"> <label for="check1">Check 1</label>
                    </div>
                    <div class="col-md-4">
                        <input type="checkbox" name="check2" id="check2"> <label for="check2">Check 2</label>
                    </div>
                    <div class="col-md-4">
                        <input type="checkbox" name="check3" id="check3"> <label for="check3">Check 3</label>
                    </div>
                    <div class="col-md-4">
                        <input type="checkbox" name="check4" id="check4"> <label for="check4">Check 4</label>
                    </div>
                    <div class="col-md-4">
                        <input type="checkbox" name="check5" id="check5"> <label for="check5">Check 5</label>
                    </div>
                    <div class="col-md-4">
                        <input type="checkbox" name="check6" id="check6"> <label for="check6">Check 6</label>
                    </div>
                    <div class="col-md-4">
                        <input type="checkbox" name="check7" id="check7"> <label for="check7">Check 7</label>
                    </div>
                    <div class="col-md-4">
                        <input type="checkbox" name="check8" id="check8"> <label for="check8">Check 8</label>
                    </div>
                    <div class="col-md-4">
                        <input type="checkbox" name="check9" id="check9"> <label for="check9">Check 9</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <span>Comment</span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <textarea name="comment" id="comment" cols="102" rows="10"></textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <span>Attachments</span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">

            </div>
        </div>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <button type="submit" name="submit" class="btn btn-primary pull-right">&nbsp;&nbsp;Submit&nbsp;&nbsp;</button>
            </div>
        </div>
    </div>
    </form>
</section>

<script src="js/jquery-1.12.3.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/script.js"></script>
</body>
</html>
