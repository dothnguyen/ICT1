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
	$sql= "SELECT *from user_tbl
          left join representative_allocated
              on representative_allocated.site_id = User_tbl.user_id
          LEFT JOIN site 
	          on site.site_id = representative_allocated.site_id
    
          WHERE User_tbl.manager_id = $managerId
		  AND (representative_allocated.site_rep_active_status is null 
                OR representative_allocated.site_rep_active_status = 1)";

return $conn->query($sql);
}

/**
 * get all users managed by a manager
 * @param $conn
 * @param $managerId
 * @return mixed
 */
function get_allusers($conn,$managerId){
	$sql= "SELECT * FROM user_tbl where manager_id= $managerId";

return $conn->query($sql);
}

function get_allsites($conn,$managerId){
	$sql= "SELECT * FROM site";

return $conn->query($sql);
}

/**
 * @param $conn
 * @param $site_id
 * @return mixed
 */
function get_site_by_id($conn, $site_id) {
    $sql = "SELECT * FROM site WHERE site_id = $site_id";

    $ret = $conn->query($sql);

    return $ret->fetch_assoc();
}

/**
 * @param $conn
 * @param $site_id
 * @param $site_name
 * @param $site_address
 * @param $site_tel
 */
function modify_site($conn, $site_id, $site_name, $site_address, $site_tel) {
    $sql = "UPDATE site SET site_name='$site_name', address='$site_address', telephone='$site_tel'
            WHERE site_id=$site_id";

    return mysqli_query($conn, $sql);
}

/**
 * @param $conn
 * @param $site_name
 * @param $site_address
 * @param $site_tel
 * @param $manager_id
 */
function insert_site($conn, $site_name, $site_address, $site_tel, $manager_id) {
    $sql = "INSERT INTO site(site_name, address, telephone, manager_id, site_created_date) 
                      VALUES('$site_name', '$site_address', '$site_tel', $manager_id, NOW());";

    return mysqli_query($conn, $sql);
}

