<?php
/**
 * Created by PhpStorm.
 * User: voiu
 * Date: 1/15/17
 * Time: 12:17 PM
 * Date: 4/08/17 ngocle (teresa)  added status, Modes: Allocate and Modify Allocation
 */

require_once "db.php";
require_once "site_functions.php";
require_once  "other_functions.php";

session_start();

// check if user loged in
check_login();

// check if login user has the privileges
check_authorize(true);

// get logged-in user info
$login_user = $_SESSION['user_info'];

$conn = db_connect();


////////////// ADD PAGING AND SEARCHING FEATURE /////////
// 10 row per page
$item_per_page = 5;
$page = 0;

// get search criteria
$search_criteria = "";

if (isset($_REQUEST['search_criteria'])) {
    $search_criteria = test_input($_REQUEST['search_criteria']);
}

if (isset($_REQUEST['page'])) {
    $page = intval($_REQUEST['page']);
}

// calculate number of pages
$count = count_sites_of_manager_with_criteria($conn, $login_user['user_id'], $search_criteria);

////////////// ADD PAGING AND SEARCHING FEATURE /////////

$start_idx = $page * $item_per_page;

$sites = null;
if ($count > 0) {

    $num_page = ceil($count / $item_per_page);

    $sites = get_sites_of_manager_with_paging($conn, $login_user['user_id'], $search_criteria, $page * $item_per_page, $item_per_page);

}

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

    <title>Sites Management</title>
</head>
<body>
<?php include_once 'header.php';?>
<?php include_once 'nav.php';?>

<section class="main-content">
    <div class="container">
        <div  class="row">
            <div class="col-xs-offset-2 col-xs-8 col-md-offset-0 col-md-3">
                <div class="left-panel text-center">
                    <a href="site_modify.php?mode=new" class="btn btn-primary btn-addnew">Add Site</a>
                </div>
            </div>
            <div class="col-xs-12  col-md-9">
                <div class="right-panel">
                    <div class="page-title"><span>Site List</span></div>
                    <div class="page-content">
                        <div class="search-form">
                            <form class="form-inline" action="site_manage.php" method="get">
                                <div class="form-group">
                                    <label for="search_criteria">Search for </label>
                                    <input type="text" class="form-control" id="search_criteria" name="search_criteria" value="<?php echo $search_criteria; ?>">
                                    <button type="submit" class="btn btn-default" id="search">Search</button>
                                </div>
                            </form>
                        </div>
                        <div class="site-list-container">
                            <table class="table-bordered table-striped table-hover table-responsive site-table">
                                <thead>
                                    <td>No.</td>
                                    <td>Site Information</td>
                                    <td>Action</td>
                                </thead>
                                <?php
                                    if (!empty($sites)) {
                                        foreach ($sites as $site) {
                                            ?>
                                            <tr>
                                                <td class="index-column"><?php echo($start_idx + $idx + 1) ?></td>
                                                <td class="site-info-column">
                                                    <div>
                                                        <div><strong><?php echo $site['site_name']; ?></strong> - <span>Tel: <?php echo $site['telephone']; ?></span>
                                                        </div>
                                                        <div>
                                                            <spa>Address: <?php echo $site['address']; ?></spa>
                                                        </div>
                                                        <!--teresa edited-->
                                                        <div>
                                                            <?php if ($site['site_rep_active_status'] == '1') { ?>
                                                                <span>Status:
                                                                    <?php echo 'Is represented by '; ?>
                                                                    <strong><?php echo $site['firstname'] . ' ' . $site['lastname']; ?></strong>
                                                        </span>
                                                            <?php } else { ?>
                                                                <span>Status: Available for representation</span>
                                                            <?php } ?>
                                                        </div>
                                                        <!--end teresa edited-->
                                                    </div>
                                                </td>
                                                <td class="action-column">
                                                    <a href="site_modify.php?mode=modify&site_id=<?php echo $site['site_id'] ?>"
                                                       class="btn btn-block btn-default eddo">Edit</a>

                                                    <!--teresa edited-->
                                                    <?php if ($site['site_rep_active_status'] != '1') { ?>

                                                        <a href="site_allocation.php?mode=new&site_id=<?php echo $site['site_id'] ?>"
                                                           class="btn btn-block btn-default allo">Allocate</a>

                                                    <?php } else { ?>

                                                        <a href="site_allocation.php?mode=modify&site_id=<?php echo $site['site_id'] ?>&allocate_id=<?php echo $site['site_alloc_id'] ?>"
                                                           class="btn btn-block btn-default moddo">Modify Allocation</a>

                                                    <?php } ?>
                                                    <!--end teresa edited-->
                                                </td>
                                            </tr>
                                            <?php
                                            $idx++;
                                        }
                                    } else {
                                ?>
                                <tr><td colspan="3">No data found.</td></tr>
                                <?php } ?>
                            </table>
                        </div>
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
        </div>
    </div>
</section>


<script src="js/jquery-1.12.3.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/script.js"></script>
</body>
</html>