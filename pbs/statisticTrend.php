<?php  //主页
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

session_check(get_current_url());

$base = new PageBase(TEMPLATE,' 个人记账系统', PageBase::$LAYOUT_COLUMN2_NARROWLEFT);
$base->setPgeSubTitle('收支趋势统计');
$base->setActiveMenuId('menuStatistic');

$base->addCss(array (
		'/css/bootstrap-datepicker3.min.css'
));
$base->addScript(array (
		'/js/pbs/dateQuery.js',
		'/js/bootstrap-datepicker/bootstrap-datepicker.min.js',
		'/js/bootstrap-datepicker/bootstrap-datepicker.zh-CN.min.js',
		'/js/highcharts/highcharts.js'
));

$navPart = new NavPart();
$base->setNavPart($navPart);

$accountQuickListPart = new AccountQuickListPart();
$base->addPart(PageBase::$BODY_LEF, $accountQuickListPart);

$statisticTrendPart = new StatisticTrendPart();
$base->addPart(PageBase::$BODY_CENTER, $statisticTrendPart);


$base->show();

?>