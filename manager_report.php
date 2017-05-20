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
    if (isset($_REQUEST['txt_fromDate'])) {
        $fromdate = $_REQUEST['txt_fromDate'];
    }
    if (isset($_REQUEST['txt_toDate'])) {
        $todate = $_REQUEST['txt_toDate'];
    }


}



///////////////////////////////////////////////////////////////////////////////////
//function get_reports_with_paging($conn, $selected_sites, $chklist_types, $report_types, $fromdate, $todate, $skip, $count) {
////////////// ADD PAGING AND SEARCHING FEATURE /////////
// 10 row per page
$item_per_page = 5;
$page = 0;

function count_reports($conn, $login_user,$selected_sites,$chklist_types, $report_types, $fromdate, $todate){
    //$sql="select count (*) as c from $report_name";
   // $ret = mysqli_fetch_assoc($conn->query($sql));

    $sql_daily = "";
    if (in_array(1, $chklist_types)) {
        $sql_daily = "SELECT COUNT(*) AS c FROM daily d, representative_allocated ra
                      WHERE  d.site_allocate_id = ra.allocate";

        // conditions
        if (in_array(-1, $selected_sites)) {

        } else {
            $sql_daily .= " AND ra.site_id IN (" . $selected_sites . ")";
        }

        //

    }

    $sql_weekly = "";
    if (in_array(2, $chklist_types)) {
        $sql_weekly = "";
    }

    $sql_monthly = "";
    if (in_array(3, $chklist_types)) {
        $sql_monthly = "";
    }

    $final_sql = "";
    if ($sql_daily != "") {
        $final_sql = "(" . $sql_daily . ")";
    }

    if ($sql_weekly != "") {
        if($final_sql != "") {

            $final_sql .= " UNION ";

        }

        $final_sql .= "(" . $sql_weekly . ")";
    }

    if ($sql_monthly != "") {
        if($final_sql != "") {

            $final_sql .= " UNION ";

        }

        $final_sql .= "(" . $sql_monthly . ")";
    }

    return $ret['c'];
}

$search="weekly";
function get_reports_with_paging($conn, $managerId, $search, $skip, $count){

    $sql="select * from $search";

    return $conn->query($sql);

}
// calculate number of pages
$count = count_reports($conn, $login_user['user_id'], 'weekly');

////////////// ADD PAGING AND SEARCHING FEATURE /////////

$start_idx = $page * $item_per_page;

$reports = null;
if ($count > 0) {

    $num_page = ceil($count / $item_per_page);

    $reports = get_reports_with_paging($conn, $login_user['user_id'], $search_criteria, $page * $item_per_page, $item_per_page);

}

$idx = 0;
//////////////////////////////////////////////////////////////////////////////////////////

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
                            <input class="datepicker" type="date" id="txt_fromDate" name="txt_fromDate"/></p>
                    </div>
                </div>
                <div class="col-xs-12 col-md-4">
                    <div class="form-group">
                        <p><span class="add-on" style="vertical-align: top;height:20px"><b>To date: </b> </span>
                            <input class="datepicker" type="date" id="txt_toDate" name="txt_toDate"/></p>
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
                <td>No.</td>
                <td>Checklist</td>
                <td>Site/Prep</td>
                <td>Type</td>
                </thead>

                <?php
                if (!empty($reports)) {
                foreach ($reports as $report) {
                ?>
                <tr>
                    <td class="index-column"><?php echo($start_idx + $idx + 1) ?></td>
                    <td class="checklist-column">

                    </td>

                    <td class="site-column">

                    </td>


                    <td class="type-column">

                    </td>
                </tr>
                    <?php
                    $idx++;
                }
                } else {
                    ?>
                    <tr><td colspan="4">No data found.</td></tr>
                <?php } ?>
            </table>

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
