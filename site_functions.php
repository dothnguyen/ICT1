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

