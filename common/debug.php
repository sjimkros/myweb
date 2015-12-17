<?php
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

header('Content-Type: text/html; charset=utf-8');

echo md5('admin');

?>