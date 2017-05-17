<?php
/**
 * Created by PhpStorm.
 * User: voiu
 * Date: 1/15/17
 * Time: 12:17 PM
 * Date: 5/3/17, ngoc le added protptype
 */

require_once "db.php";
require_once "other_functions.php";
require_once "site_functions.php";

session_start();

// check if user loged in
check_login();

// check if user can access manager's page
check_authorize(true);

// get logged-in user info
$login_user = $_SESSION['user_info'];

$conn = db_connect();

$sites = get_sites_of_manager($conn, $login_user['user_id']);

$selected_sites = array();

$chklist_types = array();

$report_types = array();

if (isset($_REQUEST['search'])) {

    if (isset($_REQUEST['sites'])) {
        $selected_sites = $_REQUEST['sites'];
    }

    if (isset($_REQUEST['chklist_types'])) {
        $chklist_types = $_REQUEST['chklist_types'];
    }

    if (isset($_REQUEST['report_types'])) {
        $report_types = $_REQUEST['report_types'];
    }

}

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
    <link rel="stylesheet" href="css/bootstrap-select.css">


    <title>Reports</title>
</head>
<body>
<?php include_once 'header.php'; ?>
<?php include_once 'nav.php'; ?>
<div class="main-content">
    <form method="post" action="manager_report.php">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-md-offset-2 col-md-8">
                    <div class="page-title"><span>Search for checklists</span></div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-offset-2 col-md-8">
                    <div class="form-group">
                        <p><b>Of Sites: </b><span><select name="sites[]" class="selectpicker col-md-10" multiple
                                                          required
                                                          data-live-search="true">
                                    <option
                                        value="-1" <?php if (empty($selected_sites) || in_array(-1, $selected_sites)) echo 'selected' ?>>
                                        All sites
                                    </option>
                                    <?php
                                    if (!empty($sites)) {
                                        foreach ($sites as $site) { ?>

                                            <option
                                                value="<?php echo $site['site_id'] ?>"
                                                <?php if (in_array($site['site_id'], $selected_sites)) echo 'selected' ?>><?php echo $site['site_name'] ?></option>

                                        <?php }
                                    } ?>
                                </select>
                                    </span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-md-offset-2 col-md-4">
                    <div class="form-group">
                        <p><b>Checklist: </b> &nbsp;<span><select name="chklist_types[]" class="selectpicker" multiple
                                                                  required>
                                    <option
                                        value="1" <?php if (empty($chklist_types) || in_array(1, $chklist_types)) echo 'selected' ?>>
                                        Daily
                                    </option>
                                    <option value="2" <?php if (in_array(2, $chklist_types)) echo 'selected' ?>>Weekly
                                    </option>
                                    <option value="3" <?php if (in_array(3, $chklist_types)) echo 'selected' ?>>
                                        Monthly
                                    </option>
                                </select>
                                    </span>
                    <span>

                        </p>
                    </div>
                </div>
                <div class="col-xs-12 col-md-4">
                    <div class="form-group">
                        <p><b>Report type: </b><span><select class="selectpicker" name="report_types[]" required>
                                    <option
                                        value="1" <?php if (empty($report_types) || in_array(1, $report_types)) echo 'selected' ?>>
                                        Regular
                                    </option>
                                    <option value="2" <?php if (in_array(2, $report_types)) echo 'selected' ?>>Attention
                                        Items
                                    </option>
                                </select></span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-md-offset-2 col-md-4">
                    <div class="form-group">
                        <p><span class="add-on" style="vertical-align: top;height:20px"><b>From date: </b> </span>
                            <input class="datepicker" type="date" id="txt_fromDate"/></p>
                    </div>
                </div>
                <div class="col-xs-12 col-md-4">
                    <div class="form-group">
                        <p><span class="add-on" style="vertical-align: top;height:20px"><b>To date: </b> </span>
                            <input class="datepicker" type="date" id="txt_toDate"/></p>
                    </div>
                </div>
            </div>

            <div class="row form-group">
                <div class="col-xs-12 col-md-offset-2 col-md-4">
                    <p style="text-align: right;">
                        <button type="submit" name="search" class="btn btn-primary active" id="search">Search
                        </button>
                    </p>
                </div>
            </div>

    </form>
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
                        } ?>
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
