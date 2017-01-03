<?php
/**
 * Created by PhpStorm.
 * User: voiu
 * Date: 5/15/16
 * Time: 3:56 PM
 */

// clear session
session_start();

if (session_destroy()) {
    header("Location:index.php");
}