<?php 
// 主页
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

$base = new PageBase(TEMPLATE, '主页', PageBase::$LAYOUT_COLUMN2_NARROWLEFT);
$base->addScript(array (
		'/js/validate.js',
		'/js/login.js'
));

$loginPart = new LoginPart();
$base->addPart(PageBase::$BODY_LEF, $loginPart);

$introPart = new IntroPart();
$base->addPart(PageBase::$BODY_CENTER, $introPart);

$base->show();

?>