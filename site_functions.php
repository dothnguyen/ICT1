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