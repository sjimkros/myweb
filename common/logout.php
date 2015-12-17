<?php 
// 主页
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

session_start();

setcookie('rememberMe', '', time() - 3600, '/');
$_SESSION = array();

session_destroy();

header('location: ' . DOMAIN_NAME . '/common/index.php');

?>