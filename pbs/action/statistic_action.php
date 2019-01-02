<?php
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';
include_once APPROOT . '/lib/utility/chart.php';

session_check(null);

$method = $_REQUEST['method'];

switch ($method) {
	case 'getIntroStatistic' :
		get_intro_statistic();
		break;
	case 'getBillStatistic' :
		get_bill_statistic();
		break;
	case 'initTrendStatisticData' :
		init_trend_statistic_data();
		break;
	case 'getTrendStatistic' :
		get_trend_statistic();
		break;
	default :
		break;
}

/**
 * 获取获取首页基本统计
 */
function get_intro_statistic() {
	$userId = $_SESSION['userId'];
	
	$now = date('Y-m-d');
	$startDate = date('Y-m-01', strtotime($now));
	$endDate = date('Y-m-d', strtotime($startDate . '+1 month -1 day'));
	
	$code = '0';
	
	$statisticService = new StatisticService();
	$result = $statisticService->getIntroStatistic($userId, $startDate, $endDate);
	
	$asset = $statisticService->getAssetStatistic($userId);
	$assetTotalSum = array_shift($asset);
	
	$debt = $statisticService->getDebtStatistic($userId);
	$debtTotalSum = array_shift($debt);
	
	$billOut = $statisticService->getBillStatistic($userId, 0, $startDate, $endDate);
	
	if ($result == null || $asset == null || $debt == null) {
		$code = '-1';
	}
	
	$revenue = number_format((float) $assetTotalSum['asset_total_sum'] - (float) $debtTotalSum['debt_total_sum'], 2);
	$output = array (
			'retCode' => $code,
			'billStatistic' => $result,
			'assetTotalSum' => number_format($assetTotalSum['asset_total_sum'], 2),
			'assetStatistic' => $asset,
			'debtTotalSum' => number_format($debtTotalSum['debt_total_sum'], 2),
			'debtStatistic' => $debt,
			'revenueTotalSum' => $revenue,
			'billOutStatistic' => get_pin_chart_array($billOut, 'bill_type_name', 'bill_sum') 
	);
	
	echo get_json($output);
}

/**
 * 获取收支情况统计
 */
function get_bill_statistic() {
	$userId = $_SESSION['userId'];
	
	$startDate = is_empty($_REQUEST['startDate']) ? null : $_REQUEST['startDate'];
	$endDate = is_empty($_REQUEST['endDate']) ? null : $_REQUEST['endDate'];
	
	$code = '0';
	
	$statisticService = new StatisticService();
	$billIn = $statisticService->getBillStatistic($userId, 1, $startDate, $endDate);
	$billOut = $statisticService->getBillStatistic($userId, 0, $startDate, $endDate);
	
	$revenue = 0.0;

 	//获取图表插件数据
 	$billInChartData = get_pin_chart_array($billIn, 'bill_type_name', 'bill_sum');
 	$billOutChartData = get_pin_chart_array($billOut, 'bill_type_name', 'bill_sum');

	// 计算总数
	if (count($billIn) > 0) {
		$totalIn = 0.0;
		foreach ($billIn as &$row) {
			$totalIn += (float) $row['bill_sum'];
		}
		
		$billIn[] = array (
				'bill_type_id' => '0',
				'bill_type_name' => '合计',
				'bill_sum_f' => number_format($totalIn, 2)
		);
		$revenue += $totalIn;
		//$totalIn = number_format($totalIn, 2);
	}
	if (count($billOut) > 0) {
		$totalOut = 0.0;
		foreach ($billOut as &$row) {
			$totalOut += (float) $row['bill_sum'];
		}
		
		$billOut[] = array (
				'bill_type_id' => '0',
				'bill_type_name' => '合计',
				'bill_sum_f' => number_format($totalOut, 2)
		);
		$revenue -= $totalOut;
		//$totalOut = number_format($totalOut, 2);
	}
	
	$revenue = number_format($revenue, 2);
	$output = array (
			'retCode' => $code,
			'billInChart' => $billInChartData,
			'billOutChart' => $billOutChartData,
			'billInStatistic' => $billIn,
			'billOutStatistic' => $billOut,
			'billRevenue' => $revenue 
	);
	
	echo get_json($output);
}

/**
 * 收支趋势基础数据初始化
 */
function init_trend_statistic_data() {
	$userId = (int) $_SESSION['userId'];
	
	$code = '0';
	
	// 获取首次记账年份，生成年份列表
	$billService = new BillService();
	$firstBillTime = $billService->getFirstBillTime($userId);
	$yearList = array ();
	if ($firstBillTime != null) {
		$firstYear = (int) date('Y', strtotime($firstBillTime));
		$nowYear = (int) date('Y');
		if ($firstYear < $nowYear) {
			for($i = $nowYear; $i >= $firstYear; $i --) {
				array_push($yearList, array (
						'yearVal' => (string) $i,
						'yearStr' => (string) $i
				));
			}
		}
	} else {
		$yearList[] = array (
				'yearVal' => date('Y'),
				'yearStr' => date('Y') 
		);
	}
	
	// 加载账户列表
	$accountService = new AccountService();
	$accountList = $accountService->getAccountSimpleList($userId, null);
	
	// 加载收支类别列表
	$billTypeService = new BillTypeService();
	$billTypeList = $billTypeService->getBillTypeSimpleList($userId, null);
	
	$output = array (
			'retCode' => $code,
			'yearList' => $yearList,
			'accountList' => $accountList,
			'billTypeList' => $billTypeList 
	);
	
	echo get_json($output);
}

/**
 * 获取收支趋势统计
 */
function get_trend_statistic() {
	$userId = (int) $_SESSION['userId'];
	$accountId = is_empty($_REQUEST['accountId']) ? null : (int) $_REQUEST['accountId'];
	$billTypeId = is_empty($_REQUEST['billTypeId']) ? null : (int) $_REQUEST['billTypeId'];
	$billTypeFlag = is_empty($_REQUEST['billTypeFlag']) ? null : (int) $_REQUEST['billTypeFlag'];
	$year = is_empty($_REQUEST['year']) ? date('Y') : $_REQUEST['year'];
	$month = is_empty($_REQUEST['month']) ? null : $_REQUEST['month'];
	
	$code = '0';
	
	$statisticService = new StatisticService();
	
	$chartData1 = null;
	$chartData2 = null;
	$barLabel1 = null;
	$barLabel2 = null;
	$categoriesArray = array();
	
 	//年度统计
	if($month === null) {
		//为选择收支类别，同时显示收入与支出的汇总统计
		if($billTypeId === null && $billTypeFlag === null) {
			$billInRows = $statisticService->getTrendStatisticYear($userId, $accountId, $billTypeId, 1, $year);
			$billOutRows = $statisticService->getTrendStatisticYear($userId, $accountId, $billTypeId, 0, $year);
			$barLabel1 = '收入';
			$barLabel2 = '支出';
			$chartData1 = get_bar_chart_array($billInRows, 'total_sum');
			$chartData2 = get_bar_chart_array($billOutRows, 'total_sum');

		} else {
			$rows = $statisticService->getTrendStatisticYear($userId, $accountId, $billTypeId, $billTypeFlag, $year);
			$barLabel1 = ($billTypeFlag == 1) ? '收入' : '支出';
			$chartData1 = get_bar_chart_array($rows, 'total_sum');
		}

		$categoriesArray = array('1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月');
	
	//月度统计
	} else {
		if($billTypeId === null && $billTypeFlag === null) {
			$billInRows = $statisticService->getTrendStatisticMonth($userId, $accountId, $billTypeId, 1, $year, $month);
			$billOutRows = $statisticService->getTrendStatisticMonth($userId, $accountId, $billTypeId, 0, $year, $month);
			$barLabel1 = '收入';
			$barLabel2 = '支出';
			$chartData1 = get_bar_chart_array($billInRows, 'total_sum');
			$chartData2 = get_bar_chart_array($billOutRows, 'total_sum');

		} else {
			$rows = $statisticService->getTrendStatisticMonth($userId, $accountId, $billTypeId, $billTypeFlag, $year, $month);
			$barLabel1 = ($billTypeFlag == 1) ? '收入' : '支出';
			$chartData1 = get_bar_chart_array($rows, 'total_sum');
		}
		
		$monthDays = get_month_days((int)$year, (int)$month);
		for($i = 1; $i <= $monthDays; $i++) {
			$categoriesArray[] = $i;
		}
	}

	$output = array (
			'retCode' => $code,
			'categories' => $categoriesArray,
			'chartData1' => $chartData1,
			'chartData2' => $chartData2,
			'barLabel1' => $barLabel1,
			'barLabel2' => $barLabel2
	);
	
	echo get_json($output);
}
?>