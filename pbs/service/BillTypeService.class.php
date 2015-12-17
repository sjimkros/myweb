<?php
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

/**
 * 收支类别服务类
 *
 * @author sjimkros
 *        
 */
class BillTypeService extends ServiceBase {
	
	/**
	 * 获取收支类别列表
	 * 
	 * @param unknown $userId        	
	 * @return string
	 */
	public function getBillTypeList($userId, $billTypeFlag) {
		$params = array (
				'userId' => $userId,
				'billTypeFlag' => $billTypeFlag 
		);
		
		$rows = $this->getDAO()->select(PbsBillTypeSQL::$SQL_SELECT_BY_USERID_BILLTYPEFLAG, $params);
		return $rows;
	}
	
	/**
	 * 获取收支类别
	 * @param unknown $billTypeId
	 */
	public function getBillType($billTypeId) {
		$params = array (
				'billTypeId' => $billTypeId
		);
		
		$rows = $this->getDAO()->select(PbsBillTypeSQL::$SQL_SELECT_BY_BILLTYPEID, $params);
		if($rows != null) {
			return $rows[0];
		}
		return null;
	}
	
	/**
	 * 插入收支类别
	 * @param unknown $billTypeName
	 * @param unknown $billTypeDesc
	 * @param unknown $billTypeFlag
	 * @param unknown $userId
	 * @param unknown $systemFlag
	 */
	public function insertBillType($userId, $billTypeName, $billTypeDesc, $billTypeFlag, $systemFlag) {
		$params = array (
				'userId' => $userId,
				'billTypeName' => $billTypeName,
				'billTypeDesc' => $billTypeDesc,
				'billTypeFlag' => $billTypeFlag,
				'systemFlag' => $systemFlag
		);
		
		$count = $this->getDAO()->insert(PbsBillTypeSQL::$SQL_INSERT, $params);
		return $count;
	}
	
	/**
	 * 更新收支类别
	 * @param unknown $userId
	 * @param unknown $billTypeId
	 * @param unknown $billTypeName
	 * @param unknown $billTypeDesc
	 */
	public function updateBillType($userId, $billTypeId, $billTypeName, $billTypeDesc) {
		$params = array (
				'userId' => $userId,
				'billTypeId' => $billTypeId,
				'billTypeName' => $billTypeName,
				'billTypeDesc' => $billTypeDesc
		);
	
		$count = $this->getDAO()->update(PbsBillTypeSQL::$SQL_UPDATE, $params);
		return $count;
	}
	
	/**
	 * 删除收支类别
	 * @param unknown $billTypeId
	 */
	public function deleteBillType($billTypeId) {
		$params = array (
				'billTypeId' => $billTypeId
		);
	
		$count = $this->getDAO()->delete(PbsBillTypeSQL::$SQL_DELETE, $params);
		return $count;
	}
	
	/**
	 * 校验收支类别名称是否重复
	 * @param unknown $userId
	 * @param unknown $billTypeId
	 * @param unknown $billTypeName
	 * @return boolean
	 */
	public function checkBillTypeName($userId, $billTypeId, $billTypeName) {
		$params = array (
				'userId' => $userId,
				'billTypeId' => $billTypeId,
				'billTypeName' => $billTypeName
		);
	
		$rows = $this->getDAO()->select(PbsBillTypeSQL::$SQL_CHECK_BILLTYPENAME, $params);
		if ($rows[0]['check_count'] > 0) {
			return false;
		}
		return true;
	}
	
	/**
	 * 获取收支类别列表
	 *
	 * @param unknown $userId
	 * @return string
	 */
	public function getBillTypeSimpleList($userId, $billTypeFlag) {
		$params = array (
				'userId' => $userId,
				'billTypeFlag' => $billTypeFlag
		);
	
		$rows = $this->getDAO()->select(PbsBillTypeSQL::$SQL_SELECT_SIMPLE_BY_USERID_BILLTYPEFLAG, $params);
		return $rows;
	}
}
?>