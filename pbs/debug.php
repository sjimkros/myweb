<?php
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

echo 'begin';

$accountService = new AccountService();
$result = $accountService->getAccountList(1, null);

echo $result;
echo 2;
?>