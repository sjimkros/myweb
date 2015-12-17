<?php
include_once '_approot.php';
include_once APPROOT . 'settings.php';
include_once APPROOT . 'lib/utility/common.php';

if (session_check_no_redirect() == false) {
	header('location: ' . DOMAIN_NAME . '/common/index.php');
	exit();
} else {
	header('location: ' . DOMAIN_NAME . '/pbs/index.php');
	exit();
}
?>