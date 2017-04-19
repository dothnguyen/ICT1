<?php
/**
 * Created by PhpStorm.
 * User: voiu
 * Date: 1/15/17
 * Time: 12:17 PM
 * Date: 4/17/17 ngocle added List of sites has not submitted daily checklists
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

/**
 * @param $conn
 * @param $managerId
 */
function get_unsubmited_daily_checklist_sites($conn, $managerId) {
    $sql ="SELECT a.*, b.firstname, b.lastname, b.site_rep_active_status, b.site_alloc_id 
            FROM site a 
            INNER JOIN ( SELECT R.site_rep_active_status, R.site_id, site_alloc_id, U.firstname, U.lastname 
                          FROM representative_allocated R 
                          LEFT JOIN user_tbl U ON R.user_id =U.user_id 
                          WHERE R.site_rep_active_status = 1
                                AND R.site_alloc_id NOT IN (SELECT D.site_alloc_id 
                                                            FROM daily D 
                                                            WHERE DATE(d_created_date) = CURDATE()) )b on a.site_id = b.site_id 
            WHERE a.manager_id =$managerId";
    return $conn->query($sql);
}
$sites = get_unsubmited_daily_checklist_sites($conn, $login_user['user_id']);
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

    <title>Home</title>
</head>
<body>
<?php include_once 'header.php';?>
<?php include_once 'nav.php';?>

<div class="container">
    <div  class="row">
        <div class="col-xs-12 col-md-offset-2 col-md-8">
        <div class="page-title"><span>List of sites has not submitted daily checklists</span></div>
            <div class="site-list-container">
                <?php
                if (!empty($sites)) {
                    foreach ($sites as $site) {
                        ?>
                        <p><?php echo($idx + 1) ?>.
                                    <strong><?php echo $site['site_name']; ?></strong>
                                        <spa>, is represented by <strong><?php echo  $site['firstname'] . ' ' . $site['lastname']; ?></strong></spa>
                                        - <span>Tel: <?php echo $site['telephone']; ?></span>
                                    </p>
                    <?php
                        $idx++;
                    }
                } else {
                    ?>
                    <p>No data found</p>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<script src="js/jquery-1.12.3.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/script.js"></script>
</body>
</html>
