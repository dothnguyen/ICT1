<?php
/**
 * Created by PhpStorm.
 * User: voiu
 * Date: 5/11/16
 * Time: 12:22 PM
 */

require_once ("db.php");


/**
 * Check email and password to login
 * @param $conn
 * @param $cus_email
 * @param $cus_pwd
 *
 * @return true cus exist
 */
function check_user($conn, $cus_email, $cus_pwd) {
    $encrypted_pwd = md5($cus_pwd);

    $sql = "SELECT * FROM User_tbl WHERE username = '$cus_email' AND password = '$encrypted_pwd'";

    //echo $sql;

    $ret = $conn->query($sql);

    $row = $ret->fetch_assoc();

    if ($row) {
        return true;
    }

    return false;
}



/**
 * @param $conn
 * @param $email
 * @return customer row
 */
function get_user($conn, $email) {
    $sql = "SELECT * FROM user_tbl WHERE email = '$email'";

    $ret = $conn->query($sql);

    return $ret->fetch_assoc();
}

/**
 * @param $conn
 * @param $cus_lname
 * @param $cus_fname
 * @param $cus_addr_num
 * @param $cus_addr_street
 * @param $cus_addr_suburb
 * @param $cus_addr_state
 * @param $cus_addr_postcode
 * @param $cus_phone
 * @param $cus_email
 * @param $cus_pwd
 * @return bool|mysqli_result
 */
/*
function reg_customer($conn, $cus_lname, $cus_fname, $cus_addr_num, $cus_addr_street,
                      $cus_addr_suburb, $cus_addr_state, $cus_addr_postcode, $cus_phone, $cus_email,
                      $cus_pwd) {

    $encrypted_pwd = md5($cus_pwd);

    $sql = "INSERT INTO Customer(cus_lname, cus_fname, cus_addr_num, cus_addr_street, cus_addr_suburb,
                                cus_addr_state, cus_addr_postcode, cus_phone, cus_email, cus_reg_date, cus_pwd)
            VALUES('$cus_lname', '$cus_fname', $cus_addr_num, '$cus_addr_street',
            '$cus_addr_suburb', '$cus_addr_state', '$cus_addr_postcode', '$cus_phone',
            '$cus_email', NOW(), '$encrypted_pwd');";

    //echo $sql;

    // insert
    return mysqli_query($conn, $sql);
}
*/

/**
 * @param $conn
 * @param $cus_id
 * @param $cus_lname
 * @param $cus_fname
 * @param $cus_addr_num
 * @param $cus_addr_street
 * @param $cus_addr_suburb
 * @param $cus_addr_state
 * @param $cus_addr_postcode
 * @param $cus_phone
 * @param $cus_pwd
 * @return bool|mysqli_result
 */
/*
function update_customer($conn, $cus_id, $cus_lname, $cus_fname, $cus_addr_num, $cus_addr_street,
                         $cus_addr_suburb, $cus_addr_state, $cus_addr_postcode, $cus_phone, $cus_pwd) {

    $encrypted_pwd = md5($cus_pwd);

    $sql = "UPDATE Customer SET cus_lname = $cus_lname, cus_fname=$cus_fname, cus_addr_num=$cus_addr_num,
                cus_addr_street=$cus_addr_street, cus_addr_suburb=$cus_addr_suburb, cus_addr_postcode=$cus_addr_postcode,
                cus_pwd=$encrypted_pwd
                WHERE cus_id=$cus_id";

    echo $sql;

    return mysqli_query($conn, $sql);
}
*/

// test db connect
/*
$conn = db_connect();
if ($conn) {
    echo "OK";
} else  {
    echo "Failed";
}
*/

// Test reg_customer
/*
$conn = db_connect();
$ret = reg_customer($conn, 'Nguyen', 'Do', 41, 'Lawrie' , 'aaaa', 'QLD', '4301', '03939093', 'do@dd.com', 'aaa');
if ($ret) {
    echo "Insert OK";
} else {
    echo "Insert failed";
}
mysqli_close($conn);
*/

// test check customer
/*
$conn = db_connect();
$ret = check_customer($conn, 'do@dd.com', 'aaa');//reg_customer($conn, 'Nguyen', 'Do', 41, 'Lawrie' , 'aaaa', 'QLD', '4301', '03939093', 'do@dd.com', 'aaa');
if ($ret) {
    echo "Insert OK";
} else {
    echo "Insert failed";
}*/
//mysqli_close($conn);
