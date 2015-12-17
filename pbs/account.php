<?php  //主页
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

session_check(get_current_url());

$base = new PageBase(TEMPLATE,' 个人记账系统', PageBase::$LAYOUT_COLUMN2_NARROWLEFT);
$base->setPgeSubTitle('账户管理');

$base->addScript(array (
		'/js/validate.js',
		'/js/pbs/account.js'
));

$navPart = new NavPart();
$base->setNavPart($navPart);

$accountQuickListPart = new AccountQuickListPart();
$base->addPart(PageBase::$BODY_LEF, $accountQuickListPart);

$accountManagePart = new AccountManagePart();
$base->addPart(PageBase::$BODY_CENTER, $accountManagePart);


$base->show();

?>