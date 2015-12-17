<?php  //主页
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

session_check(get_current_url());

$base = new PageBase(TEMPLATE,' 个人记账系统', PageBase::$LAYOUT_COLUMN2_NARROWLEFT);
$base->setPgeSubTitle('收支类别管理');

$base->addScript(array (
		'/js/validate.js',
		'/js/pbs/billType.js'
));

$navPart = new NavPart();
$base->setNavPart($navPart);

$accountQuickListPart = new AccountQuickListPart();
$base->addPart(PageBase::$BODY_LEF, $accountQuickListPart);

$billTypeManagePart = new BillTypeManagePart();
$base->addPart(PageBase::$BODY_CENTER, $billTypeManagePart);


$base->show();

?>