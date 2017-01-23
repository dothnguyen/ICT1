<?php
/**
 * Created by PhpStorm.
 * User: voiu
 * Date: 1/17/17
 * Time: 3:23 PM
 */

require_once ("db.php");

/**
 * @param $managerId
 */
function get_sites_of_manager($conn, $managerId) {
    $sql = "SELECT * FROM site WHERE manager_id = $managerId";

    return $conn->query($sql);
}

function get_representative($conn,$managerId){
	$sql= "SELECT * FROM SITE S, USER_TBL U, REPRESENTATIVE_ALLOCATED R WHERE R.USER_ID = U.USER_ID AND R.SITE_ID = S.SITE_ID AND S.MANAGER_ID = $managerId";

return $conn->query($sql);
}



function get_allusers($conn,$managerId){
	$sql= "SELECT * FROM user_tbl where user_id!= $managerId";

return $conn->query($sql);
}

function get_allsites($conn,$managerId){
	$sql= "SELECT * FROM site";

return $conn->query($sql);
}
