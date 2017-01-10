<?php
/**
 * Created by PhpStorm.
 * User: voiu
 * Date: 5/12/16
 * Time: 11:33 AM
 */

/**
 * @param $data
 * @return string
 */
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}