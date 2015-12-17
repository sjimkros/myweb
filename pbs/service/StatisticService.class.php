<?php
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

/**
 * 统计服务类
 *
 * @author sjimkros
 *        
 */
class StatisticService extends ServiceBase {
	
	/**
	 * 获取首页统计
	 *
	 * @param unknown $userId        	
	 * @param unknown $accountTypeFlag        	
	 */
	public function getIntroStatistic($userId, $startDate, $endDate) {
		$params = array (
				'userId' => $userId,
				'startDate' => $startDate,
				'endDate' => $endDate 
		);
		
		$rows = $this->getDAO()->select(PbsStatisticSQL::$SQL_STATISTIC_INTRO, $params);
		return $rows[2];
	}
	
	/**
	 * 获取资产状况统计，第一行为总额，第二行开始为每项的统计
	 *
	 * @param unknown $userId        	
	 */
	public function getAssetStatistic($userId) {
		$params = array (
				'userId' => $userId 
		);
		
		$rows = $this->getDAO()->select(PbsStatisticSQL::$SQL_STATISTIC_ASSET, $params);
		return $rows;
	}
	
	/**
	 * 获取债务状况统计，第一行为总额，第二行开始为每项的统计
	 *
	 * @param unknown $userId        	
	 */
	public function getDebtStatistic($userId) {
		$params = array (
				'userId' => $userId 
		);
		
		$rows = $this->getDAO()->select(PbsStatisticSQL::$SQL_STATISTIC_DEBT, $params);
		return $rows;
	}
	
	/**
	 * 获取收支情况统计，按照收支类别分组
	 *
	 * @param unknown $userId        	
	 * @param unknown $billTypeFlag        	
	 * @param unknown $startDate        	
	 * @param unknown $endDate        	
	 */
	public function getBillStatistic($userId, $billTypeFlag, $startDate, $endDate) {
		$params = array (
				'userId' => $userId,
				'billTypeFlag' => $billTypeFlag,
				'startDate' => $startDate,
				'endDate' => $endDate 
		);
		
		$rows = $this->getDAO()->select(PbsStatisticSQL::$SQL_STATISTIC_BILL, $params);
		return $rows;
	}
	
	/**
	 * 获取收支趋势年度统计表
	 *
	 * @param unknown $userId        	
	 * @param unknown $accountId        	
	 * @param unknown $billTypeId        	
	 * @param unknown $billTypeFlag        	
	 * @param unknown $year        	
	 */
	public function getTrendStatisticYear($userId, $accountId, $billTypeId, $billTypeFlag, $year) {
		$params = array (
				'userId' => $userId,
				'accountId' => $accountId,
				'billTypeId' => $billTypeId,
				'billTypeFlag' => $billTypeFlag,
				'year' => $year 
		);
		
		$rows = $this->getDAO()->select(PbsStatisticSQL::$SQL_STATISTIC_TREND_YEAR, $params);
		$table = null;
		if ($rows != null) {
			// 无数据的月份，填充0
			$table = array ();
			for($i = 1; $i <= 12; $i ++) {
				$table[] = array (
						'month' => $i,
						'total_sum' => 0.0 
				);
			}
			foreach ($rows as $row) {
				$month = (int) $row['month'] - 1;
				$table[$month]['total_sum'] = (float) $row['total_sum'];
			}
		}
		
		return $table;
	}
	
	/**
	 * 获取收支趋势月度统计表
	 *
	 * @param unknown $userId        	
	 * @param unknown $accountId        	
	 * @param unknown $billTypeId        	
	 * @param unknown $billTypeFlag        	
	 * @param unknown $year        	
	 * @param unknown $month        	
	 */
	public function getTrendStatisticMonth($userId, $accountId, $billTypeId, $billTypeFlag, $year, $month) {
		$params = array (
				'userId' => $userId,
				'accountId' => $accountId,
				'billTypeId' => $billTypeId,
				'billTypeFlag' => $billTypeFlag,
				'year' => $year,
				'month' => $month 
		);
		
		$rows = $this->getDAO()->select(PbsStatisticSQL::$SQL_STATISTIC_TREND_MONTH, $params);
		$table = null;
		if ($rows != null) {
			// 无数据的月份，填充0
			$table = array ();
			$monthDays = get_month_days((int)$year, (int)$month);
			for($i = 1; $i <= $monthDays; $i ++) {
				$table[] = array (
						'day' => $i,
						'total_sum' => 0.0 
				);
			}
			foreach ($rows as $row) {
				$day = (int) $row['day'] - 1;
				$table[$day]['total_sum'] = (float) $row['total_sum'];
			}
		}
		return $table;
	}
}

?>