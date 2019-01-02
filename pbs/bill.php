<?php  //主页
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

session_check(get_current_url());

$base = new PageBase(TEMPLATE,' 个人记账系统', PageBase::$LAYOUT_COLUMN2_NARROWLEFT);
$base->setPgeSubTitle('日常流水账');
$base->setActiveMenuId('menuBill');

$base->addCss(array (
		'/css/bootstrap-datepicker3.min.css'
));
$base->addScript(array (
		'/js/validate.js',
		'/js/pbs/bill.js',
		'/js/pbs/dateQuery.js',
		'/js/bootstrap-datepicker/bootstrap-datepicker.min.js',
		'/js/bootstrap-datepicker/bootstrap-datepicker.zh-CN.min.js',
		'/js/bootstrap/bootstrap-paginator.js'
));

$navPart = new NavPart();
$base->setNavPart($navPart);

$accountQuickListPart = new AccountQuickListPart();
$base->addPart(PageBase::$BODY_LEF, $accountQuickListPart);

$billManagePart = new BillManagePart();
$base->addPart(PageBase::$BODY_CENTER, $billManagePart);


$base->show();

?>