<?php 
// 主页
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

session_check(get_current_url());

$base = new PageBase(TEMPLATE, ' 个人记账系统', PageBase::$LAYOUT_COLUMN3);
$base->setPgeSubTitle('欢迎' . $_SESSION['userDetail']['user_nickname']);
$base->addCss(array (
		'/css/bootstrap-datepicker3.min.css' 
));

$base->addScript(array (
		'/js/bootstrap-datepicker/bootstrap-datepicker.min.js',
		'/js/bootstrap-datepicker/bootstrap-datepicker.zh-CN.min.js',
		'/js/highcharts/highcharts.js'
));

$navPart = new NavPart();
$base->setNavPart($navPart);

$accountQuickListPart = new AccountQuickListPart();
$base->addPart(PageBase::$BODY_LEF, $accountQuickListPart);

$statisticIntroPart = new StatisticIntroPart();
$base->addPart(PageBase::$BODY_CENTER, $statisticIntroPart);

$calendarPart = new CalendarPart();
$base->addPart(PageBase::$BODY_RIGHT, $calendarPart);

$base->show();

?>