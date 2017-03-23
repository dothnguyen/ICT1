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
    $sql = "SELECT * FROM site WHERE manager_id = $managerId
    and active_status = 1";

    return $conn->query($sql);
}

/**
 * @param $conn
 * @param $managerId
 * @param $skip
 * @param $count
 */
function get_sites_of_manager_with_paging($conn, $managerId, $search, $skip, $count) {
    $sql = "SELECT * FROM site WHERE manager_id = $managerId
            and active_status = 1";

    if (!empty($search)) {
        $sql .= " and site_name LIKE %$search% ";
    }

    $sql .=  " LIMIT $skip, $count";

    return $conn->query($sql);
}

/**
 * @param $conn
 * @param $managerId
 * @param $search
 * @return mixed
 */
function count_sites_of_manager_with_criteria($conn, $managerId, $search) {
    $sql = "SELECT COUNT(*) as c FROM site WHERE manager_id = $managerId
            and active_status = 1";

    if (!empty($search)) {
        $sql .= " and site_name LIKE %$search% ";
    }

    $ret = mysqli_fetch_assoc($conn->query($sql));

    return $ret['c'];
}

/**
 * @param $conn
 * @param $manager_id
 */
function get_unallocated_sites($conn, $manager_id) {
    $sql = "SELECT * FROM site
            WHERE manager_id = $manager_id
                AND active_status = 1
                AND site_id NOT IN (SELECT site_id FROM representative_allocated
                                        WHERE site_rep_active_status = 1)";

    return $conn->query($sql);
}

/**
 * @param $conn
 * @param $allocated_id
 */
function get_allocation_info($conn, $allocate_id) {
    $sql = "SELECT * FROM site, representative_allocated
             WHERE site.site_id = representative_allocated.site_id
              AND representative_allocated.site_alloc_id = $allocate_id";

    $ret = $conn->query($sql);

    return mysqli_fetch_assoc($ret);
}

/**
 * @param $conn
 * @param $site_id
 * @param $user_id
 */
function insert_site_allocation($conn, $site_id, $user_id) {
    $sql = "INSERT INTO representative_allocated(site_rep_allocated_date, site_rep_active_status, site_id, user_id)
              VALUES(NOW(), 1, $site_id, $user_id)";

    return mysqli_query($conn, $sql);
}

/**
 * @param $conn
 * @param $allocate_id
 */
function deactivate_site_allocation($conn, $allocate_id) {
    $sql = "UPDATE representative_allocated set site_rep_active_status = 0
            WHERE site_alloc_id = $allocate_id";

    return mysqli_query($conn, $sql);
}


function get_representative($conn,$managerId){
	$sql= "SELECT a.*, b.site_name, b.site_rep_active_status, b.site_alloc_id FROM user_tbl a
           LEFT JOIN (SELECT s.*, ra.user_id, ra.site_rep_active_status, ra.site_alloc_id FROM site s, representative_allocated ra
                     WHERE s.site_id = ra.site_id
                     and ra.site_rep_active_status = 1) b
                on a.user_id = b.user_id
            WHERE a.manager_id = $managerId";

return $conn->query($sql);
}

/**
 * get all users managed by a manager
 * @param $conn
 * @param $managerId
 * @return mixed
 */
function get_allusers($conn,$managerId){
	$sql= "SELECT * FROM user_tbl where manager_id= $managerId and active_status = 1";

return $conn->query($sql);
}

/**
 * @param $conn
 * @param $managerId
 * @return mixed
 */
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

    return mysqli_fetch_assoc($ret);
}

/**
 * @param $conn
 * @param $site_id
 */
function is_site_allocated($conn, $site_id) {
    $sql = "SELECT * FROM representative_allocated 
            WHERE site_id = $site_id
               AND site_rep_active_status = 1 ";

    $ret = $conn->query($sql);

    return $ret->num_rows > 0;
}

/**
 * @param $conn
 * @param $user_id
 * @return bool
 */
function is_user_allocated($conn, $user_id) {
    $sql = "SELECT * FROM representative_allocated 
            WHERE user_id = $user_id
               AND site_rep_active_status = 1 ";

    $ret = $conn->query($sql);

    return $ret->num_rows > 0;
}

function get_user_by_id($conn, $user_id){
	$sql= "select *from user_tbl where user_id=$user_id";
	 $ret = $conn->query($sql);

    return mysqli_fetch_assoc($ret);
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
 * @param $user_id
 * @param $firstname
 * @param $lastname
 * @param $email
 * @return bool|mysqli_result
 */
function modify_user($conn, $user_id, $firstname, $lastname, $email) {
    $sql = "UPDATE user_tbl SET firstname='$firstname', lastname='$lastname', email='$email'
            WHERE user_id=$user_id";

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

/**
 * @param $conn
 * @param $firstname
 * @param $lastname
 * @param $email
 * @param $manager_id
 * @return bool|mysqli_result
 */


function insert_new_user($conn, $firstname, $lastname,$username, $generatepassword,$email, $manager_id){
	
	$sql="INSERT INTO user_tbl (firstname, lastname, username, password, email, manager_id, created_date)
	values ('$firstname', '$lastname', '$generatepassword','$email','$username', $manager_id ,NOW());";
	return mysqli_query($conn, $sql);
}
/**
 * @param $conn
 * @param $firstname
 * @param $lastname
 * @param $email
 * @param $username
 * @param $manager_id
 */



/**
 * @param $conn
 * @param $site_id
 * @return bool|mysqli_result
 */
function inactivate_site($conn, $site_id) {
    $sql = "UPDATE site SET active_status = 0
            WHERE site_id=$site_id";

    return mysqli_query($conn, $sql);
}

