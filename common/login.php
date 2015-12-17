<?php 
// 主页
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

$base = new PageBase(TEMPLATE, '主页', PageBase::$LAYOUT_COLUMN1_NARROW);
$base->addScript(array (
		'/js/validate.js',
		'/js/login.js'
));

$loginPart = new LoginPart();
$base->addPart(PageBase::$BODY_CENTER, $loginPart);

$base->show();

?>