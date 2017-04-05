<?php
/**
 * Created by PhpStorm.
 * User: voiu
 * Date: 1/15/17
 * Time: 12:17 PM
 */

require_once "db.php";
require_once "site_functions.php";

require_once "other_functions.php";

session_start();

// check if user loged in
check_login();

// check if user can access manager's page
check_authorize(true);

$conn = db_connect();

// get logged-in user info
$login_user = $_SESSION['user_info'];

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
$count = count_users_of_manager($conn, $login_user['user_id'], $search_criteria);


////////////// ADD PAGING AND SEARCHING FEATURE /////////

$represents = null;
if ($count > 0) {

    $num_page = ceil($count / $item_per_page);

    $represents = get_users_of_manager_with_paging($conn, $login_user['user_id'], $search_criteria, $page * $item_per_page, $item_per_page);

}

$idx = 0;

//$represents = get_representative($conn, $login_user['user_id']);

//$idx = 0;

mysqli_close($conn);


/**
 * @param $conn
 * @param $managerId
 * @param $criteria
 */
function count_users_of_manager($conn, $managerId, $search) {
    $sql= "SELECT COUNT(*) as c FROM user_tbl a
           LEFT JOIN (SELECT s.*, ra.user_id, ra.site_rep_active_status, ra.site_alloc_id FROM site s, representative_allocated ra
                     WHERE s.site_id = ra.site_id
                     and ra.site_rep_active_status = 1) b
                on a.user_id = b.user_id
            WHERE a.manager_id = $managerId";

    if (!empty($search)) {
        $sql .= " AND (a.username LIKE '%$search%' OR a.firstname LIKE '%$search%' OR a.lastname LIKE '%$search%')";
    }

    $ret = mysqli_fetch_assoc($conn->query($sql));

    return $ret['c'];
}

/**
 * @param $conn
 * @param $managerId
 * @param $search
 */
function get_users_of_manager_with_paging($conn, $managerId, $search, $skip, $count) {
    $sql= "SELECT a.*, b.site_name, b.site_rep_active_status, b.site_alloc_id FROM user_tbl a
           LEFT JOIN (SELECT s.*, ra.user_id, ra.site_rep_active_status, ra.site_alloc_id FROM site s, representative_allocated ra
                     WHERE s.site_id = ra.site_id
                     and ra.site_rep_active_status = 1) b
                on a.user_id = b.user_id
            WHERE a.manager_id = $managerId";

    if (!empty($search)) {
        $sql .= " AND (a.username LIKE '%$search%' OR a.firstname LIKE '%$search%' OR a.lastname LIKE '%$search%')";
    }

    $sql .=  " LIMIT $skip, $count";

    return $conn->query($sql);
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

    <title>User List</title>
</head>
<body>
<?php include_once 'header.php';?>
<?php include_once 'nav.php';?>

<section class="main-content">
    <div class="container">
        <div  class="row">
            <div class="col-xs-offset-2 col-xs-8 col-md-offset-0  col-md-3">
                <div class="left-panel text-center">
                    <a href="user_modify.php?mode=new" class="btn btn-primary btn-addnew">Add User</a>
                </div>
            </div>
            <div class="col-xs-12 col-md-9">
                <div class="right-panel">
                    <div class="page-title"><span>User List</span></div>
                    <div class="page-content">
                        <div class="search-form">
                            <form class="form-inline" action="user_manage.php" method="get">
                                <div class="form-group">
                                    <label for="search_criteria">Search for </label>
                                    <input type="text" class="form-control" id="search_criteria" name="search_criteria" value="<?php echo $search_criteria; ?>">
                                    <button type="submit" class="btn btn-default" id="search">Search</button>
                                </div>
                            </form>
                        </div>
                        <div class="site-list-container">
                            <table class="table-bordered table-striped table-hover site-table">
                                <thead>
                                    <td>No.</td>
                                    <td>User Information</td>
                                    <td>Action</td>
                                </thead>
                                <?php
                                if (!empty($represents)) {
                                    foreach ($represents as $represent) {
                                        ?>
                                        <tr>
                                            <td class="index-column"><?php echo($idx + 1) ?></td>
                                            <td class="site-info-column">
                                                <div>

                                                    <div>
                                                        <span>Name: <strong> <?php echo $represent['firstname']; ?>&nbsp;<?php echo $represent['lastname']; ?></strong> </span>
                                                    </div>
                                                    <div>
                                                        <span>Email: <?php echo $represent['email']; ?>  </span>
                                                    </div>
                                                    <div>
                                                        <?php if ($represent['site_rep_active_status'] == '1') { ?>
                                                            <span>Status:
                                                                <?php echo 'Is allocated to '; ?>
                                                                <strong><?php echo $represent['site_name']; ?></strong>
                                                        </span>
                                                        <?php } else { ?>
                                                            <span>Status: Available for allocation</span>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="action-column">
                                                <a href="user_modify.php?mode=modify&user_id=<?php echo $represent['user_id'] ?>"
                                                   class="btn btn-block btn-default eddo">Edit</a>
                                                <?php if ($represent['site_rep_active_status'] != '1') { ?>

                                                    <a href="user_allocation.php?mode=new&user_id=<?php echo $represent['user_id'] ?>"
                                                       class="btn btn-block btn-default allo">Allocate</a>

                                                <?php } else { ?>

                                                    <a href="user_allocation.php?mode=modify&user_id=<?php echo $represent['user_id'] ?>&allocate_id=<?php echo $represent['site_alloc_id'] ?>"
                                                       class="btn btn-block btn-default moddo">Modify Allocation</a>

                                                <?php } ?>
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
                                                <a href="user_manage.php?search_criteria=<?php echo $search_criteria; ?>&page=<?php echo $i; ?>"><?php echo($i + 1); ?></a>
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
	&nbsp;
	&nbsp;
</section>


<script src="js/jquery-1.12.3.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/script.js"></script>
</body>
</html>