<?php
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

/**
 * 记账服务类
 *
 * @author sjimkros
 *        
 */
class BillService extends ServiceBase {
	
	/**
	 * 获取给定用户、账户和类别下的记账数
	 *
	 * @param unknown $accountId        	
	 */
	public function countBill($userId, $accountId, $billTypeId) {
		$params = array (
				'userId' => $userId,
				'accountId' => $accountId,
				'billTypeId' => $billTypeId 
		);
		
		$rows = $this->getDAO()->select(PbsBillSQL::$SQL_COUNT_BY_IDS, $params);
		return $rows[0]['bill_count'];
	}
	
	/**
	 * 根据bill_related查询收款、还款数据条目数
	 *
	 * @param unknown $billId        	
	 */
	public function countBillRepayRelated($billId) {
		$params = array (
				'billId' => $billId 
		);
		
		$rows = $this->getDAO()->select(PbsBillSQL::$SQL_COUNT_REPAY_BY_RELATED, $params);
		return $rows[0]['bill_count'];
	}
	
	/**
	 * 根据bill_related查询债务、债权数据条目数
	 *
	 * @param unknown $billId
	 */
	public function countBillDebtRelated($accountId) {
		$params = array (
				'accountId' => $accountId
		);
	
		$rows = $this->getDAO()->select(PbsBillSQL::$SQL_COUNT_DEBT_BY_RELATED, $params);
		return $rows[0]['bill_count'];
	}
	
	
	/**
	 * 获取记账列表
	 *
	 * @param unknown $userId        	
	 * @param unknown $accountId        	
	 * @param unknown $billTypeId        	
	 * @param unknown $startDate        	
	 * @param unknown $endDate        	
	 * @param unknown $curPageIndex        	
	 * @param unknown $pageSize        	
	 */
	public function getBillListPage($userId, $accountId, $billTypeId, $startDate, $endDate, $billTypeFlag, $curPageIndex, $pageSize) {
		$params = array (
				'userId' => $userId,
				'accountId' => $accountId,
				'billTypeId' => $billTypeId,
				'startDate' => $startDate,
				'endDate' => $endDate,
				'billTypeFlag' => $billTypeFlag 
		);
		
		$page = $this->getDAO()->selectForPage(PbsBillSQL::$SQL_SELECT_BY_CONDITIONS, $params, $curPageIndex, $pageSize);
		return $page;
	}
	
	/**
	 * 获取账目详情
	 *
	 * @param unknown $billId        	
	 * @return Ambigous <>|NULL
	 */
	public function getBill($billId) {
		$params = array (
				'billId' => $billId 
		);
		
		$rows = $this->getDAO()->select(PbsBillSQL::$SQL_SELECT_BY_BILLID, $params);
		if ($rows != null) {
			return $rows[0];
		}
		return null;
	}
	
	/**
	 * 获取关联账目列表
	 *
	 * @param unknown $userId        	
	 * @param unknown $billTypeFlag        	
	 */
	public function getBillRelatedList($userId, $billTypeFlag) {
		$params = array (
				'userId' => $userId,
				'billTypeFlag' => $billTypeFlag 
		);
		
		$rows = $this->getDAO()->select(PbsBillSQL::$SQL_SELECT_RELATED_BY_CONDITIONS, $params);
		return $rows;
	}
	
	/**
	 * 添加普通收支记录
	 *
	 * @param unknown $userId        	
	 * @param unknown $billTypeFlag        	
	 * @param unknown $billTime        	
	 * @param unknown $billType        	
	 * @param unknown $acccountId        	
	 * @param unknown $billSum        	
	 * @param unknown $billDesc        	
	 */
	public function insertNormalBill($userId, $billTypeFlag, $billTime, $billTypeId, $accountId, $billSum, $billDesc) {
		$params = array (
				'userId' => $userId,
				'billTypeFlag' => $billTypeFlag,
				'billTime' => $billTime,
				'billTypeId' => $billTypeId,
				'accountId' => $accountId,
				'billSum' => $billSum,
				'billDesc' => $billDesc 
		);
		
		$count = $this->getDAO()->proceedTransaction(PbsBillSQL::$SQL_INSERT_NORMAL, $params);
		return $count;
	}
	
	/**
	 * 更新普通收支记录
	 *
	 * @param unknown $userId        	
	 * @param unknown $billTypeFlag        	
	 * @param unknown $billId        	
	 * @param unknown $billTime        	
	 * @param unknown $billTypeId        	
	 * @param unknown $accountId        	
	 * @param unknown $billSum        	
	 * @param unknown $billDesc        	
	 */
	public function updateNormalBill($userId, $billTypeFlag, $billId, $billTime, $billTypeId, $accountId, $billSum, $billDesc) {
		$params = array (
				'userId' => $userId,
				'billTypeFlag' => $billTypeFlag,
				'billId' => $billId,
				'billTime' => $billTime,
				'billTypeId' => $billTypeId,
				'accountId' => $accountId,
				'billSum' => $billSum,
				'billDesc' => $billDesc 
		);
		
		$count = $this->getDAO()->proceedTransaction(PbsBillSQL::$SQL_UPDATE_NORMAL, $params);
		return $count;
	}
	
	/**
	 * 删除普通收支记录
	 *
	 * @param unknown $billId        	
	 */
	public function deleteNormalBill($billId, $billTypeFlag) {
		$params = array (
				'billId' => $billId,
				'billTypeFlag' => $billTypeFlag 
		);
		
		$count = $this->getDAO()->proceedTransaction(PbsBillSQL::$SQL_DELETE_NORMAL, $params);
		return $count;
	}
	
	/**
	 * 添加借入/预收、借出/垫付记账记录
	 *
	 * @param unknown $userId        	
	 * @param unknown $billTypeFlag        	
	 * @param unknown $billTime        	
	 * @param unknown $billTypeId        	
	 * @param unknown $accountId        	
	 * @param unknown $account2Id        	
	 * @param unknown $billSum        	
	 * @param unknown $billDesc        	
	 */
	public function insertDebtBill($userId, $billTypeFlag, $billTime, $billTypeId, $accountId, $account2Id, $billSum, $billDesc) {
		$params = array (
				'userId' => $userId,
				'billTypeFlag' => $billTypeFlag,
				'billTime' => $billTime,
				'billTypeId' => $billTypeId,
				'accountId' => $accountId,
				'account2Id' => $account2Id,
				'billSum' => $billSum,
				'billDesc' => $billDesc 
		);
		
		$count = $this->getDAO()->proceedTransaction(PbsBillSQL::$SQL_INSERT_DEBT, $params);
		return $count;
	}
	
	/**
	 * 更新借入/预收、借出/垫付记账记录
	 *
	 * @param unknown $userId        	
	 * @param unknown $billTypeFlag        	
	 * @param unknown $billId        	
	 * @param unknown $billTime        	
	 * @param unknown $billTypeId        	
	 * @param unknown $accountId        	
	 * @param unknown $account2Id        	
	 * @param unknown $billSum        	
	 * @param unknown $billDesc        	
	 */
	public function updateDebtBill($userId, $billTypeFlag, $billId, $billTime, $billTypeId, $accountId, $account2Id, $billSum, $billDesc) {
		$params = array (
				'userId' => $userId,
				'billTypeFlag' => $billTypeFlag,
				'billId' => $billId,
				'billTime' => $billTime,
				'billTypeId' => $billTypeId,
				'accountId' => $accountId,
				'account2Id' => $account2Id,
				'billSum' => $billSum,
				'billDesc' => $billDesc 
		);
		
		$count = $this->getDAO()->proceedTransaction(PbsBillSQL::$SQL_UPDATE_DEBT, $params);
		return $count;
	}
	
	/**
	 * 删除借入/预收、借出/垫付记账记录
	 *
	 * @param unknown $billId        	
	 * @param unknown $billTypeFlag        	
	 */
	public function deleteDebtBill($billId, $billTypeFlag) {
		$params = array (
				'billId' => $billId,
				'billTypeFlag' => $billTypeFlag 
		);
		
		$count = $this->getDAO()->proceedTransaction(PbsBillSQL::$SQL_DELETE_DEBT, $params);
		return $count;
	}
	
	/**
	 * 添加存取款/转账/还信用卡记账记录
	 *
	 * @param unknown $userId        	
	 * @param unknown $billTypeFlag        	
	 * @param unknown $billTime        	
	 * @param unknown $billTypeId        	
	 * @param unknown $accountId        	
	 * @param unknown $account2Id        	
	 * @param unknown $billSum        	
	 * @param unknown $billDesc        	
	 */
	public function insertTransferBill($userId, $billTypeFlag, $billTime, $billTypeId, $accountId, $account2Id, $billSum, $billDesc) {
		$params = array (
				'userId' => $userId,
				'billTypeFlag' => $billTypeFlag,
				'billTime' => $billTime,
				'billTypeId' => $billTypeId,
				'accountId' => $accountId,
				'account2Id' => $account2Id,
				'billSum' => $billSum,
				'billDesc' => $billDesc 
		);
		
		$count = $this->getDAO()->proceedTransaction(PbsBillSQL::$SQL_INSERT_TRANSFER, $params);
		return $count;
	}
	
	/**
	 * 更新存取款/转账/还信用卡记账记录（传入转出记录id）
	 *
	 * @param unknown $userId        	
	 * @param unknown $billTypeFlag        	
	 * @param unknown $billId        	
	 * @param unknown $billTime        	
	 * @param unknown $billTypeId        	
	 * @param unknown $accountId        	
	 * @param unknown $account2Id        	
	 * @param unknown $billSum        	
	 * @param unknown $billDesc        	
	 */
	public function updateTransferOutBill($userId, $billTypeFlag, $billId, $billTime, $billTypeId, $accountId, $account2Id, $billSum, $billDesc) {
		$params = array (
				'userId' => $userId,
				'billTypeFlag' => $billTypeFlag,
				'billId' => $billId,
				'billTime' => $billTime,
				'billTypeId' => $billTypeId,
				'accountId' => $accountId,
				'account2Id' => $account2Id,
				'billSum' => $billSum,
				'billDesc' => $billDesc 
		);
		
		$count = $this->getDAO()->proceedTransaction(PbsBillSQL::$SQL_UPDATE_TRNASFER_OUT, $params);
		return $count;
	}
	
	/**
	 * 删除存取款/转账/还信用卡记账记录
	 *
	 * @param unknown $billId        	
	 * @param unknown $billTypeFlag        	
	 */
	public function deleteTransferBill($billId, $billTypeFlag) {
		$params = array (
				'billId' => $billId,
				'billTypeFlag' => $billTypeFlag 
		);
		
		$count = $this->getDAO()->proceedTransaction(PbsBillSQL::$SQL_DELETE_TRANSFER, $params);
		return $count;
	}
	
	/**
	 * 校验还款/收款数据是否有效
	 *
	 * @param unknown $billId        	
	 * @param unknown $repaySum        	
	 */
	public function checkRepay($billId, $repaySum) {
		$params = array (
				'billId' => $billId,
				'repaySum' => $repaySum 
		);
		
		$rows = $this->getDAO()->select(PbsBillSQL::$SQL_CHECK_REPAY, $params);
		$isPass = true;
		if ((int) $rows[0]['bill_repay'] == 1) { // 已经结清
			$isPass = false;
		} else if ((float) $rows[0]['bill_related_sum'] < 0) {
			$isPass = false;
		}
		
		return $isPass;
	}
	
	/**
	 * 添加还款/收款记账记录
	 * 
	 * @param unknown $billId        	
	 * @param unknown $repaySum        	
	 */
	public function insertRepayBill($userId, $billTypeFlag, $billTime, $accountId, $billDesc, $billId, $repaySum) {
		$params = array (
				'userId' => $userId,
				'billTypeFlag' => $billTypeFlag,
				'billTime' => $billTime,
				'accountId' => $accountId,
				'billDesc' => $billDesc,
				'billId' => $billId,
				'repaySum' => $repaySum 
		);
		
		$count = $this->getDAO()->proceedTransaction(PbsBillSQL::$SQL_INSERT_REPAY, $params);
		return $count;
	}
	
	/**
	 * 修改还款/收款记账记录
	 * @param unknown $userId
	 * @param unknown $billTypeFlag
	 * @param unknown $billId
	 * @param unknown $billTime
	 * @param unknown $accountId
	 * @param unknown $billSum
	 * @param unknown $billDesc
	 */
	public function updateRepayBill($userId, $billTypeFlag, $billId, $billTime, $accountId, $billSum, $billDesc) {
		$params = array (
				'userId' => $userId,
				'billTypeFlag' => $billTypeFlag,
				'billId' => $billId,
				'billTime' => $billTime,
				'accountId' => $accountId,
				'billSum' => $billSum,
				'billDesc' => $billDesc
		);
		
		$count = $this->getDAO()->proceedTransaction(PbsBillSQL::$SQ_UPDATE_REPAY, $params);
		return $count;
	}
	
	/**
	 * 删除还款/收款记账记录
	 * 
	 * @param unknown $billId        	
	 * @param unknown $billTypeFlag        	
	 */
	public function deleteRepayBill($billId, $billTypeFlag) {
		$params = array(
				'billId' => $billId,
				'billTypeFlag' => $billTypeFlag
		);
	
		$count = $this->getDAO()->proceedTransaction(PbsBillSQL::$SQL_DELETE_REPAY, $params);
		return $count;
	}
	
	/**
	 * 获取首次记账时间
	 * @param unknown $userId
	 */
	public function getFirstBillTime($userId) {
		$params = array(
				'userId' => $userId
		);
		
		$rows = $this->getDAO()->select(PbsBillSQL::$SQL_SELECT_MIN_BILLTIME_BY_USERID, $params);
		if ($rows != null) {
			return $rows[0]['bill_min_time'];
		}
		return null;
	}
}
?>