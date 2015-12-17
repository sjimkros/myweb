<?php
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

session_check(null);

$method = $_REQUEST['method'];

switch ($method) {
	case 'getBillTypeList' :
		get_bill_type_list();
		break;
	case 'getBillType' :
		get_bill_type();
		break;
	case 'updateBillType' :
		update_bill_type();
		break;
	case 'deleteBillType' :
		delete_bill_type();
	default :
		break;
}

/**
 * 获取收支类别列表
 */
function get_bill_type_list() {
	$userId = $_SESSION['userId'];
	$billTypeFlag = $_REQUEST['billTypeFlag'];
	
	$billTypeService = new BillTypeService();
	$result = $billTypeService->getBillTypeList($userId, $billTypeFlag);
	
	$code = '0';
	if ($result == null) {
		$code = '-1';
	}
	
	$output = array (
			'retCode' => $code,
			'billTypeList' => $result 
	);
	
	echo get_json($output);
}

/**
 * 获取收支类别
 */
function get_bill_type() {
	$billTypeId = $_REQUEST['billTypeId'];
	
	$billTypeService = new BillTypeService();
	$result = $billTypeService->getBillType($billTypeId);
	
	$code = '0';
	if ($result == null) {
		$code = '-1';
	}
	
	$output = array (
			'retCode' => $code,
			'billType' => $result 
	);
	
	echo get_json($output);
}

/**
 * 更新收支类别
 */
function update_bill_type() {
	$userId = $_SESSION['userId'];
	$op = $_REQUEST['op'];
	$billTypeId = $_REQUEST['billTypeId'];
	$billTypeName = $_REQUEST['billTypeName'];
	$billTypeDesc = $_REQUEST['billTypeDesc'];
	$billTypeFlag = (int)$_REQUEST['billTypeFlag'];
	$systemFlag = "0"; // 用户自建
	
	if ($op == 'add')
		$billTypeId = - 1;
	
	$code = '0';
	$billTypeService = new BillTypeService();
	// 校验是否重名
	if (! $billTypeService->checkBillTypeName($userId, $billTypeId, $billTypeName)) {
		$code = 'existBillTypeName';
	} else {
		if ($op == 'add') { // 新增
			$billTypeService->insertBillType($userId, $billTypeName, $billTypeDesc, $billTypeFlag, $systemFlag);
		} else if ($op == 'edit') { // 修改
			$billTypeService->updateBillType($userId, $billTypeId, $billTypeName, $billTypeDesc);
		}
	}
	
	$output = array (
			'retCode' => $code 
	);
	
	echo get_json($output);
}

/**
 * 删除收支类别
 */
function delete_bill_type() {
	$billTypeId = $_REQUEST['billTypeId'];
	
	$code = '0';
	
	$billService = new BillService();
	// 校验类别下是否有记账
	if ($billService->countBill(null, null, $billTypeId) > 0) {
		$code = 'existBillInBillType';
	} else {
		$billTypeService = new BillTypeService();
		$billTypeService->deleteBillType($billTypeId);
	}
	
	$output = array (
			'retCode' => $code 
	);
	
	echo get_json($output);
}

?>