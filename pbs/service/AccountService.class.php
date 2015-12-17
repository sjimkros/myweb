<?php
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

/**
 * 账户服务类
 *
 * @author sjimkros
 *        
 */
class AccountService extends ServiceBase {
	
	/**
	 * 获取账户列表
	 *
	 * @param unknown $userId        	
	 * @param unknown $accountTypeFlag        	
	 */
	public function getAccountList($userId, $accountTypeFlag) {
		$params = array (
				'userId' => $userId,
				'accountTypeFlag' => $accountTypeFlag 
		);
		
		$rows = $this->getDAO()->select(PbsAccountSQL::$SQL_SELECT_BY_USERID, $params);
		return $rows;
	}
	
	/**
	 * 获取账户
	 * @param unknown $accountId
	 */
	public function getAccount($accountId) {
		$params = array (
				'accountId' => $accountId
		);
		
		$rows = $this->getDAO()->select(PbsAccountSQL::$SQL_SELECT_BY_ACCOUNTID, $params);
		if($rows != null) {
			return $rows[0];
		} 
		return null;
	}
	
	/**
	 * 校验账户名称是否重复
	 *
	 * @param unknown $userId        	
	 * @param unknown $accountId        	
	 * @param unknown $accountName        	
	 */
	public function checkAccountName($userId, $accountId, $accountName) {
		$params = array (
				'userId' => $userId,
				'accountId' => $accountId,
				'accountName' => $accountName 
		);
		
		$rows = $this->getDAO()->select(PbsAccountSQL::$SQL_CHECK_ACCOUNTNAME, $params);
		if ($rows[0]['check_count'] > 0) {
			return false;
		}
		return true;
	}
	
	/**
	 * 插入账户
	 * 
	 * @param unknown $accountName        	
	 * @param unknown $accountSum        	
	 * @param unknown $accountDesc        	
	 * @param unknown $accountType        	
	 * @param unknown $accountFlag        	
	 */
	public function insertAccount($userId, $accountName, $accountSum, $accountDesc, $accountType, $accountFlag) {
		$params = array (
				'userId' => $userId,
				'accountName' => $accountName,
				'accountSum' => $accountSum,
				'accountDesc' => $accountDesc,
				'accountType' => $accountType,
				'accountFlag' => $accountFlag 
		);
		
		$count = $this->getDAO()->insert(PbsAccountSQL::$SQL_INSERT, $params);
		return $count;
	}
	
	/**
	 * 更新账户
	 * 
	 * @param unknown $userId        	
	 * @param unknown $accountId        	
	 * @param unknown $accountName        	
	 * @param unknown $accountSum        	
	 * @param unknown $accountDesc        	
	 * @param unknown $accountType        	
	 * @param unknown $accountFlag        	
	 */
	public function updateAccount($userId, $accountId, $accountName, $accountSum, $accountDesc, $accountType, $accountFlag) {
		$params = array (
				'userId' => $userId,
				'accountId' => $accountId,
				'accountName' => $accountName,
				'accountSum' => $accountSum,
				'accountDesc' => $accountDesc,
				'accountType' => $accountType,
				'accountFlag' => $accountFlag 
		);
		
		$count = $this->getDAO()->update(PbsAccountSQL::$SQL_UPDATE, $params);
		return $count;
	}
	
	/**
	 * 删除账户
	 * @param unknown $accountId
	 */
	public function deleteAccount($accountId) {
		$params = array (
				'accountId' => $accountId
		);
		
		$count = $this->getDAO()->delete(PbsAccountSQL::$SQL_DELETE, $params);
		return $count;
	}
	
	/**
	 * 获取给定用户的账户数
	 * @param unknown $userId
	 */
	public function countAccount($userId, $accountTypeFlag) {
		$params = array (
				'userId' => $userId,
				'accountTypeFlag' => $accountTypeFlag
		);
		
		$rows = $this->getDAO()->select(PbsAccountSQL::$SQL_COUNT_BY_USERID, $params);
		return $rows[0]['account_count'];
	}
	
	/**
	 * 获取用户账户中最小的id值
	 * @param unknown $userId
	 */
	public function getMinAccountId($userId) {
		$params = array (
				'userId' => $userId
		);
		
		$rows = $this->getDAO()->select(PbsAccountSQL::$SQL_SELECT_MINID_BY_USERID, $params);
		return $rows[0]['min_id'];
	}
	
	/**
	 * 获取用户的账户列表
	 * @param unknown $userId
	 * @param unknown $accountTypeFlag
	 */
	public function getAccountSimpleList($userId, $accountTypeFlag) {
		$params = array (
				'userId' => $userId,
				'accountTypeFlag' => $accountTypeFlag
		);
		
		$rows = $this->getDAO()->select(PbsAccountSQL::$SQL_SELECT_SIMPLE_BY_USERID, $params);
		return $rows;
	}
}

?>