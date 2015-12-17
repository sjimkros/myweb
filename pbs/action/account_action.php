<?php
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

session_check(null);

$method = $_REQUEST['method'];

switch ($method) {
	case 'getAccountManageData' :
		get_account_manage_data();
		break;
	case 'getAccountList' :
		get_account_list();
		break;
	case 'getAccount' :
		get_account();
		break;
	case 'setAccountDefault' :
		set_account_default();
		break;
	case 'updateAccount' :
		update_account();
		break;
	case 'deleteAccount' :
		delete_account();
		break;
	case 'getAccountSimpleList' :
		get_account_simple_list();
	default :
		break;
}

/**
 * 获取账户管理页面数据
 */
function get_account_manage_data() {
	$userId = $_SESSION['userId'];
	
	// 获取账户列表
	$accountService = new AccountService();
	$result = $accountService->getAccountList($userId, null);
	// 获取账户类别列表
	$accountTypeService = new AccountTypeService();
	$result2 = $accountTypeService->getAccountTypeList($userId, null);
	
	$code = '0';
	$accountDefault = null;
	
	if ($result != null) {
		$code = '0';
		$accountDefault = get_user_config('accountDefault');
	} else {
		$code = '-1';
	}
	
	$output = array (
			'retCode' => $code,
			'accountList' => $result,
			'accountTypeList' => $result2,
			'accountDefault' => $accountDefault 
	);
	
	echo get_json($output);
}

/**
 * 获取账户列表
 */
function get_account_list() {
	$userId = $_SESSION['userId'];
	$accountTypeFlag = isset($_REQUEST['accountTypeFlag']) ? (int)$_REQUEST['accountTypeFlag'] : null; 
	
	$accountService = new AccountService();
	$result = $accountService->getAccountList($userId, $accountTypeFlag);
	
	$code = '0';
	$accountDefault = null;
	if ($result != null) {
		$code = '0';
		$accountDefault = get_user_config('accountDefault');
	} else {
		$code = '-1';
	}
	
	$output = array (
			'retCode' => $code,
			'accountList' => $result,
			'accountDefault' => $accountDefault 
	);
	
	echo get_json($output);
}

/**
 * 获取账户
 */
function get_account() {
	$accountId = $_REQUEST['accountId'];
	
	$accountService = new AccountService();
	$result = $accountService->getAccount($accountId);
	
	$code = '0';
	if ($result == null) {
		$code = '-1';
	}
	
	$output = array (
			'retCode' => $code,
			'account' => $result 
	);
	
	echo get_json($output);
}

/**
 * 设置用户默认账户
 */
function set_account_default() {
	$userId = $_SESSION['userId'];
	$accountDefault = $_REQUEST['accountId'];
	
	set_user_config('accountDefault', $accountDefault);
	$userService = new UserService();
	$userService->updateConfig($userId, $_SESSION['userDetail']['user_config']);
	
	$code = '0';
	$output = array (
			'retCode' => $code 
	);
	
	echo get_json($output);
}

/**
 * 更新账户数据
 */
function update_account() {
	$userId = $_SESSION['userId'];
	$op = $_REQUEST['op'];
	$accountId = $_REQUEST['accountId'];
	$accountName = $_REQUEST['accountName'];
	$accountSum = (float)$_REQUEST['accountSum'];
	$accountDesc = $_REQUEST['accountDesc'];
	$accountType = (int)$_REQUEST['accountType'];
	$accountFlag = (int)$_REQUEST['accountFlag'];
	
	$code = '0';
	
	if ($op == 'add')
		$accountId = - 1;
	
	$accountService = new AccountService();
	// 校验是否重名
	if (! $accountService->checkAccountName($userId, $accountId, $accountName)) {
		$code = 'existAccountName';
	} else {
		if ($op == 'add') { // 新增
			$accountService->insertAccount($userId, $accountName, $accountSum, $accountDesc, $accountType, $accountFlag);
		} else if ($op == 'edit') { // 修改
			$accountService->updateAccount($userId, $accountId, $accountName, $accountSum, $accountDesc, $accountType, $accountFlag);
		}
	}
	
	$output = array (
			'retCode' => $code 
	);
	
	echo get_json($output);
}

/**
 * 删除账户
 */
function delete_account() {
	$userId = $_SESSION['userId'];
	$accountId = $_REQUEST['accountId'];
	$accountTypeFlag = $_REQUEST['accountTypeFlag'];
	
	$code = '0';
	
	// 校验该用户下的账户数
	$accountService = new AccountService();
	$accountCount = $accountService->countAccount($userId, $accountTypeFlag);
	if ($accountCount == 1) { // 只剩一组帐户不能再删除
		$code = 'mustHaveAccount';
	} else {
		
		$billService = new BillService();
		//校验账户下是否有记账
		if ($billService->countBill(null, $accountId, null) > 0 || $billService->countBillDebtRelated($accountId) > 0) {
			$code = 'existBillInAccount';
		} else {
			$accountDefault = get_user_config('accountDefault');
			if($accountId == $accountDefault) {  //如果删除的是默认账户
				//取按照accountId排序最小的账户作为默认
				$minId = $accountService->getMinAccountId($userId);
				set_user_config('accountDefault', $minId);
				//更新到用户表
				$userService = new UserService();
				$userService->updateConfig($userId, $_SESSION['userDetail']['user_config']);
			}
			$accountService->deleteAccount($accountId);
		}
	}
	
	$output = array (
			'retCode' => $code 
	);
	
	echo get_json($output);
}

/**
 * 获取账户简单列表
 */
function get_account_simple_list() {
	$userId = $_SESSION['userId'];
	$accountTypeFlag = isset($_REQUEST['accountTypeFlag']) ? (int)$_REQUEST['accountTypeFlag'] : null;
	
	$accountService = new AccountService();
	$result = $accountService->getAccountSimpleList($userId, $accountTypeFlag);
	
	$code = '0';
	if ($result == null) {
		$code = '-1';
	}
	
	$output = array (
			'retCode' => $code,
			'accountList' => $result
	);
	
	echo get_json($output);
}
?>