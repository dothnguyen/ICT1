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

$report_type = 1;

////////////// ADD PAGING AND SEARCHING FEATURE /////////
// 10 row per page
$item_per_page = 5;
$page = 0;


if (isset($_GET['search'])) {
    if (isset($_GET['sites'])) {
        $selected_sites = $_GET['sites'];
    }

    if (isset($_GET['chklist_types'])) {
        $chklist_types = $_GET['chklist_types'];
    }

    if (isset($_GET['report_type'])) {
        $report_type = $_GET['report_type'];
    }

    if (isset($_GET['txt_fromDate'])) {
        $fromdate = $_GET['txt_fromDate'];
    }

    if (isset($_GET['txt_toDate'])) {
        $todate = $_GET['txt_toDate'];
    }

    if (isset($_REQUEST['page'])) {
        $page = intval($_GET['page']);
    }

// calculate number of pages
    $count = count_reports($conn, $login_user['user_id'], $selected_sites, $chklist_types, $report_type, $fromdate, $todate);

    $start_idx = $page * $item_per_page;

    $reports = null;
    if ($count > 0) {

        $num_page = ceil($count / $item_per_page);

        $reports = get_reports_with_paging($conn, $login_user['user_id'], $selected_sites, $chklist_types, $report_type, $fromdate, $todate, $page * $item_per_page, $item_per_page);

    }

    $idx = 0;

}


///////////////////////////////////////////////////////////////////////////////////
//function get_reports_with_paging($conn, $selected_sites, $chklist_types, $report_types, $fromdate, $todate, $skip, $count) {
function count_reports($conn, $login_user,$selected_sites,$chklist_types, $report_type, $fromdate, $todate){

    $sql_daily = "";
    if (in_array(1, $chklist_types)) {
        $sql_daily = get_sql_daily($login_user, $selected_sites, $report_type, mysqli_real_escape_string($conn, $fromdate), mysqli_real_escape_string($conn, $todate));
    }

    $sql_weekly = "";
    if (in_array(2, $chklist_types)) {
        $sql_weekly = get_sql_weekly($login_user, $selected_sites, $report_type, mysqli_real_escape_string($conn, $fromdate), mysqli_real_escape_string($conn, $todate));
    }

    $sql_monthly = "";
    if (in_array(3, $chklist_types)) {
        $sql_monthly = get_sql_monthly($login_user, $selected_sites, $report_type, mysqli_real_escape_string($conn, $fromdate), mysqli_real_escape_string($conn, $todate));
    }

    $final_sql = "SELECT COUNT(*) as c FROM (";
    if ($sql_daily != "") {
        $final_sql .= "(" . $sql_daily . ")";
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

    $final_sql .= ") x";

    $ret = mysqli_fetch_assoc($conn->query($final_sql));

    return $ret['c'];
}


function get_reports_with_paging($conn, $login_user,$selected_sites,$chklist_types, $report_type, $fromdate, $todate, $skip, $count){

    $sql_daily = "";
    if (in_array(1, $chklist_types)) {
        $sql_daily = get_sql_daily($login_user, $selected_sites, $report_type, mysqli_real_escape_string($conn, $fromdate), mysqli_real_escape_string($conn, $todate));
    }

    $sql_weekly = "";
    if (in_array(2, $chklist_types)) {
        $sql_weekly = get_sql_weekly($login_user, $selected_sites, $report_type, mysqli_real_escape_string($conn, $fromdate), mysqli_real_escape_string($conn, $todate));
    }

    $sql_monthly = "";
    if (in_array(3, $chklist_types)) {
        $sql_monthly = get_sql_monthly($login_user, $selected_sites, $report_type, mysqli_real_escape_string($conn, $fromdate), mysqli_real_escape_string($conn, $todate));
    }

    $final_sql = "";
    if ($sql_daily != "") {
        $final_sql .= "(" . $sql_daily . ")";
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

    $final_sql .=  " ORDER BY created_date LIMIT $skip, $count";

    return $conn->query($final_sql);
}

/**
 *
 */
function get_sql_daily($manager_id, $selected_sites, $report_type, $from_date, $to_date) {

    $sql_daily_l = " SELECT ";
    $sql_daily_l .= " d.daily_id as chk_id, d.d_created_date as created_date, s.site_name as site_name, u.firstname as firstname, u.lastname as lastname, d.d_comments as comments, 1 as type ";

    $sql_daily_l .= " FROM daily d, representative_allocated ra, site s, user_tbl u
                      WHERE  d.site_alloc_id = ra.site_alloc_id AND ra.site_id = s.site_id AND s.manager_id = $manager_id 
                         AND ra.user_id = u.user_id";

    // conditions
    // selected sites
    if (!in_array(-1, $selected_sites)) {
        $sql_daily_l .= " AND ra.site_id IN (" . implode(',', $selected_sites) . ")";
    }

    // report type
    if ($report_type == 2) { // attention reports
        $sql_daily_l .= " AND d.d_comments <> ''";
    } else  if ($report_type == 3) {
        $sql_daily_l .= " AND d.d_comments = ''";
    }

    // from date
    if (!empty($from_date)) {
        $sql_daily_l .= " AND DATE(d.d_created_date, '%d/%m/%Y') >= DATE('$from_date', '%d/%m/%Y')'";
    }

    // to date
    if (!empty($to_date)) {
        $sql_daily_l .= " AND DATE(d.d_created_date, '%d/%m/%Y') <= DATE('$to_date', '%d/%m/%Y')";
    }

    return $sql_daily_l;

}

/**
 *
 */
function get_sql_weekly($manager_id, $selected_sites, $report_type, $from_date, $to_date) {

    $sql_weekly_l = " SELECT ";
    $sql_weekly_l .= " w.weekly_id  as chk_id, w.w_created_date as created_date, s.site_name as site_name, u.firstname as firstname, u.lastname as lastname, w.d_comments as comments, 2 as type ";

    $sql_weekly_l .= " FROM weekly w, representative_allocated ra, site s, user_tbl u
                      WHERE  w.site_alloc_id = ra.site_alloc_id AND ra.site_id = s.site_id AND s.manager_id = $manager_id
                        AND ra.user_id = u.user_id";

    // conditions
    // selected sites
    if (!in_array(-1, $selected_sites)) {
        $sql_weekly_l .= " AND ra.site_id IN (" . implode(',', $selected_sites) . ")";
    }

    // report type
    if ($report_type == 2) { // attention reports
        $sql_weekly_l .= " AND w.d_comments <> ''";
    } else  if ($report_type == 3) {
        $sql_weekly_l .= " AND w.d_comments = ''";
    }

    // from date
    if (!empty($from_date)) {
        $sql_weekly_l .= " AND DATE(w.d_created_date, '%d/%m/%Y') >= DATE('$from_date', '%d/%m/%Y')'";
    }

    // to date
    if (!empty($to_date)) {
        $sql_weekly_l .= " AND DATE(w.d_created_date, '%d/%m/%Y') <= DATE('$to_date', '%d/%m/%Y')";
    }

    return $sql_weekly_l;

}

/**
 *
 */
function get_sql_monthly($manager_id, $selected_sites, $report_type, $from_date, $to_date) {

    $sql_monthly_l = " SELECT ";
    $sql_monthly_l .= " m.monthly_id  as chk_id, m.m_created_date as created_date, s.site_name as site_name, u.firstname as firstname, u.lastname as lastname, m.d_comments as comments, 3 as type ";

    $sql_monthly_l .= " FROM monthly m, representative_allocated ra, site s, user_tbl u
                      WHERE  m.site_alloc_id = ra.site_alloc_id AND ra.site_id = s.site_id AND s.manager_id = $manager_id
                        AND ra.user_id = u.user_id";

    // conditions
    // selected sites
    if (!in_array(-1, $selected_sites)) {
        $sql_monthly_l .= " AND ra.site_id IN (" . implode(',', $selected_sites) . ")";
    }

    // report type
    if ($report_type == 2) { // attention reports
        $sql_monthly_l .= " AND m.d_comments <> ''";
    } else  if ($report_type == 3) {
        $sql_monthly_l .= " AND m.d_comments = ''";
    }

    // from date
    if (!empty($from_date)) {
        $sql_monthly_l .= " AND DATE(m.d_created_date, '%d/%m/%Y') >= DATE('$from_date', '%d/%m/%Y')'";
    }

    // to date
    if (!empty($to_date)) {
        $sql_monthly_l .= " AND DATE(m.d_created_date, '%d/%m/%Y') <= DATE('$to_date', '%d/%m/%Y')";
    }

    return $sql_monthly_l;

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
    <form method="get" action="manager_report.php">
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
                        <p><b>Report type: </b><span><select class="selectpicker" name="report_type" required>
                                    <option
                                        value="1" <?php if (empty($report_type) || $report_type == 1) echo 'selected' ?>>
                                        All
                                    </option>
                                    <option value="3" <?php if ($report_type == 3) echo 'selected' ?>>
                                        Regular
                                    </option>
                                    <option value="2" <?php if ($report_type == 2) echo 'selected' ?>>
                                        Attention
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

<?php if (isset($_GET['search'])) { ?>
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-offset-2 col-md-8">
            <table class="table table-bordered table-striped table-hover table-responsive site-table">
                <thead>
                <td>No.</td>
                <td>Checklist</td>
                <td>Site/Prep</td>
                <td>Type</td>
                </thead>

                <?php
                if (!empty($reports)) {
                foreach ($reports as $report) {
                    $r_type = empty($report['comments']);
                ?>
                <tr class="<?php if (!$r_type) echo "warning"?>">
                    <td class="index-column"><?php echo($start_idx + $idx + 1) ?></td>
                    <td class="checklist-column">
                        <div>
                            <?php if($report['type'] == 1) {?>
                                <a href="rep_daily_chklist.php?mode=view&id=<?php echo $report['chk_id'] ?>">Daily</a>
                            <?php } else if ($report['type'] == 2) { ?>
                                <a href="rep_weekly_chklist.php?mode=view&id=<?php echo $report['chk_id'] ?>">Weekly</a>
                            <?php } else if ($report['type'] == 3) { ?>
                                <a href="rep_monthly_chklist.php?mode=view&id=<?php echo $report['chk_id'] ?>">Monthly</a>
                            <?php } ?>
                        </div>
                        <div>
                            <?php echo date('d/m/Y', strtotime($report['created_date'])) ?>
                        </div>
                    </td>

                    <td class="site-column">
                        <div>
                            Site: <?php echo $report['site_name']?>
                        </div>
                        <div>
                            Rep: <?php echo $report['firstname'] . ' ' . $report['lastname']?>
                        </div>
                    </td>

                    <td class="type-column">
                        <?php if ($r_type) echo "Regular";
                                else echo "Attention Item" ?>
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
                                    <a href="manager_report.php?sites%5B%5D=<?php echo implode('&amp;sites%5B%5D=', $selected_sites)?>&chklist_types%5B%5D=<?php echo implode('&amp;chklist_types%5B%5D=', $chklist_types) ?>&report_type=<?php echo $report_type?>&page=<?php echo $i; ?>&search=1"><?php echo($i + 1); ?></a>
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
<?php } ?>


<script src="js/jquery-1.12.3.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/script.js"></script>
<script src="js/bootstrap-select.js"></script>


</body>
</html>
