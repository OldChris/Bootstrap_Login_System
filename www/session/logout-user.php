<?php
require_once "sessionControl.php";
log_user_access($_SESSION['email'], $ipaddress, $hostname, "logout");

session_start();
session_unset();
session_destroy();
header('location: login-user.php');
?>