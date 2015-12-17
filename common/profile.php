<?php 
// 主页
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

$base = new PageBase(TEMPLATE, '主页', PageBase::$LAYOUT_COLUMN3);
$base->setPgeSubTitle('个人中心');

$base->addScript(array (
		'/js/validate.js'
));

$navPart = new NavPart();
$base->setNavPart($navPart);

$profilePart = new ProfilePart();
$base->addPart(PageBase::$BODY_CENTER, $profilePart);

$base->show();

?>