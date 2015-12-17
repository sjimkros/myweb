<?php
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

/**
 * 账户类别服务类
 * 
 * @author sjimkros
 *        
 */
class AccountTypeService extends ServiceBase {
	
	/**
	 * 获取账户类别列表
	 * @param unknown $userId
	 * @param unknown $accountTypeFlag
	 */
	public function getAccountTypeList($userId, $accountTypeFlag) {
		$params = array (
				'userId' => $userId,
				'accountTypeFlag' => $accountTypeFlag
		);
		
		$rows = $this->getDAO()->select(PbsAccountTypeSQL::$SQL_SELECT_SIMPLE_BY_USERID, $params);
		return $rows;
	}
	
}

?>