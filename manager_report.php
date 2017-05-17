<?php
/**
 * Created by PhpStorm.
 * User: voiu
 * Date: 1/15/17
 * Time: 12:17 PM
 * Date: 5/3/17, ngoc le added protptype
 */

require_once "other_functions.php";

session_start();

// check if user loged in
check_login();

// check if user can access manager's page
check_authorize(true);

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
    <link rel="stylesheet" href="css/bootstrap-select.css">


    <title>Reports</title>
</head>
<body>
<?php include_once 'header.php'; ?>
<?php include_once 'nav.php'; ?>
<div class="main-content">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-offset-2 col-md-8">
                <div class="page-title"><span>Search for checklists</span></div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-offset-2 col-md-8">
                <div class="form-group"
                <p><b>Of Sites: </b><span><select class="selectpicker col-md-10" multiple data-live-search="true" >
                                            <option>Site 1</option>
                                            <option>Site 2</option>
                                            <option>Site 3</option>
                                        </select>
                                    </span>
                </p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-md-offset-2 col-md-4">
            <div class="form-group"
                <p><b>Checklist: </b> &nbsp;<span><select class="selectpicker">
                                                <option>Daily</option>
                                                <option>Weekly</option>
                                                <option>Monthly</option>
                                            </select>
                                    </span>
                    <span>

                </p>
            </div>
        </div>

        <div class="col-xs-12 col-md-4">
            <div class="form-group"
                <p><b>Report type: </b><span><select class="selectpicker">
                                                            <option>Regular</option>
                                                            <option>Attention Items</option>
                                                        </select></span>
                    </span>
                </p>
            </div>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-xs-12 col-md-offset-2 col-md-4">
            <p><span class="add-on" style="vertical-align: top;height:20px"><b>From date: </b> </span>
                <input class="datepicker" type="date" id="txt_fromDate"/></p>

        </div>
        <div class="col-xs-12 col-md-4">
            <p><span class="add-on" style="vertical-align: top;height:20px"><b>To date: </b> </span>
                <input class="datepicker" type="date" id="txt_toDate"/></p>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-xs-12 col-md-offset-2 col-md-4">
            <p style="text-align: right;"><button type="submit" class="btn btn-primary active" id="search" >Search</button></p>
        </div>
    </div>
</div>

<div class="container">
    <div class="row form-group">
        <div class="col-xs-12 col-md-offset-2 col-md-8">
            <table class="table-bordered table-striped table-hover table-responsive site-table">
                <thead>
                <td>Checklist</td>
                <td>Site/Prep</td>
                <td>Type</td>
                </thead>

            </table>
            <?php if ($num_page > 1) { ?>
                <div class="pagination-container">
                    <ul class="pagination">
                        <?php for ($i = 0; $i < $num_page; $i++) {
                            if ($i == $page) {
                                ?>
                                <li class="active"><a href="#"><?php echo($i + 1); ?></a></li>
                            <?php } else { ?>
                                <li>
                                    <a href="site_manage.php?search_criteria=<?php echo $search_criteria; ?>&page=<?php echo $i; ?>"><?php echo($i + 1); ?></a>
                                </li>
                            <?php }
                        }?>
                    </ul>
                </div>
                <?php
            } ?>
        </div>
    </div>
</div>


<script src="js/jquery-1.12.3.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/script.js"></script>
<script src="js/bootstrap-select.js"></script>


</body>
</html>
