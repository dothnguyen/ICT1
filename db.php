<?php
//define constants for connection info

// For remote host
//define("MYSQLUSER","boral");
//define("MYSQLPASS","boral01");
//define("HOSTNAME","localhost");
//define("MYSQLDB","boral");

// remote test  host
//define("MYSQLUSER","boral_test");
//define("MYSQLPASS","boral");
//define("HOSTNAME","localhost");
//define("MYSQLDB","boral_test");

// local test
define("MYSQLUSER","team01");
define("MYSQLPASS","team01");
define("HOSTNAME","localhost");
define("MYSQLDB","boral");

//make connection to database
/**
 * method used to get a connection to mysql db
 * @return a connection to db
 */
function db_connect()
{
    $conn = @new mysqli(HOSTNAME, MYSQLUSER, MYSQLPASS, MYSQLDB);
    if($conn -> connect_error) {
        die('Connect Error: ' . $conn -> connect_error);
    }
    return $conn;
}
