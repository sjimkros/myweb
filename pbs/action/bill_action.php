<?php
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

session_check(null);

$method = $_REQUEST['method'];

switch ($method) {
	case 'initBillManageData' :
		init_bill_manage_data();
		break;
	case 'getBillList' :
		get_bill_list();
		break;
	case 'getBillRelatedList' :
		get_bill_related_list();
		break;
	case 'getBill' :
		get_bill();
		break;
	case 'updateBill' :
		update_bill();
		break;
	case 'deleteBill' :
		delete_bill();
		break;
	default :
		break;
}

/**
 * 初始化记账管理基本数据
 */
function init_bill_manage_data() {
	$userId = (int) $_SESSION['userId'];
	
	$code = '0';
	// 加载账户列表
	$accountDefault = get_user_config('accountDefault');
	
	$accountService = new AccountService();
	$accountList = $accountService->getAccountSimpleList($userId, null);
	$accountDebtList = $accountService->getAccountSimpleList($userId, 2);
	$accountCreditList = $accountService->getAccountSimpleList($userId, 3);
	
	// 加载收支类别列表
	$billTypeService = new BillTypeService();
	$billTypeList = $billTypeService->getBillTypeSimpleList($userId, null);
	
	$billTypeInList = array ();
	$billTypeOutList = array ();
	foreach ($billTypeList as $row) {
		// print_r($row);
		$flag = (int) $row['bill_type_flag'];
		if ($flag == 1) {
			$billTypeInList[] = $row;
		} else if ($flag == 0) {
			$billTypeOutList[] = $row;
		}
	}
	
	$output = array (
			'retCode' => $code,
			'accountDefault' => $accountDefault,
			'accountList' => $accountList,
			'accountDebtList' => $accountDebtList,
			'accountCreditList' => $accountCreditList,
			'billTypeList' => $billTypeList,
			'billTypeInList' => $billTypeInList,
			'billTypeOutList' => $billTypeOutList 
	);
	
	echo get_json($output);
}

/**
 * 获取记账条目列表
 */
function get_bill_list() {
	$userId = (int) $_SESSION['userId'];
	
	$accountId = is_empty($_REQUEST['accountId']) ? null : (int) $_REQUEST['accountId'];
	$billTypeId = is_empty($_REQUEST['billTypeId']) ? null : (int) $_REQUEST['billTypeId'];
	$startDate = is_empty($_REQUEST['startDate']) ? null : $_REQUEST['startDate'];
	$endDate = is_empty($_REQUEST['endDate']) ? null : $_REQUEST['endDate'];
	$billTypeFlag = is_empty($_REQUEST['billTypeFlag']) ? null : (int) $_REQUEST['billTypeFlag'];
	
	$curPageIndex = is_empty($_REQUEST['curPageIndex']) ? 1 : (int) $_REQUEST['curPageIndex'];
	$pageSize = get_page_size();
	
	$code = '0';
	
	$billService = new BillService();
	$page = $billService->getBillListPage($userId, $accountId, $billTypeId, $startDate, $endDate, $billTypeFlag, $curPageIndex, $pageSize);
	if ($page != null) {
		$totalCount = $page['totalCount'];
		$pageCount = $page['pageCount'];
		$rows = $page['page'];
	} else {
		$code = '-1';
	}
	
	$output = array (
			'retCode' => $code,
			'curPageIndex' => $curPageIndex,
			'totalCount' => $totalCount,
			'pageCount' => $pageCount,
			'billList' => $rows 
	);
	
	echo get_json($output);
}

/**
 * 获取关联账目列表
 */
function get_bill_related_list() {
	$userId = (int) $_SESSION['userId'];
	$billTypeFlag = (int) $_REQUEST['billTypeFlag'];
	
	$code = '0';
	
	$billService = new BillService();
	$result = $billService->getBillRelatedList($userId, $billTypeFlag);
	
	$output = array (
			'retCode' => $code,
			'billRelatedList' => $result 
	);
	
	echo get_json($output);
}

/**
 * 获取账目信息
 */
function get_bill() {
	$billId = (int) $_REQUEST['billId'];
	$billTypeFlag = (int) $_REQUEST['billTypeFlag'];
	
	$code = '0';
	
	$billService = new BillService();
	$result = $billService->getBill($billId);
	
	switch ($billTypeFlag){
		case 2:
		case 3:
			//获取转账对应记录
			$result2 = $billService->getBill((int)$result['bill_related']);
			
			if($billTypeFlag == 2) {  //转出
				$transferAccountId = $result2['account_id'];
				$result['bill_desc'] = $result2['bill_desc'];
			} else {
				$transferAccountId = $result['account_id'];
				$result2['bill_desc'] = $result['bill_desc'];
				$result = $result2;  //让前台始终获取到的是转出记录
			}
			
			break;
	}
	
	$output = array (
			'retCode' => $code,
			'bill' => $result,
			'transferAccountId' => $transferAccountId
	);
	
	echo get_json($output);
}

/**
 * 更新账目
 */
function update_bill() {
	$userId = (int) $_SESSION['userId'];
	$op = $_REQUEST['op'];
	$billTypeFlag = (int) $_REQUEST['billTypeFlag'];
	$billId = is_empty($_REQUEST['billId']) ? null : (int) $_REQUEST['billId'];
	$billTime = $_REQUEST['billTime'];
	$billTypeId = is_empty($_REQUEST['billTypeId']) ? null : (int) $_REQUEST['billTypeId'];
	$accountId = (int) $_REQUEST['accountId'];
	$account2Id = is_empty($_REQUEST['account2Id']) ? null : $_REQUEST['account2Id'];
	$billSum = is_empty($_REQUEST['billSum']) ? null : (float) $_REQUEST['billSum'];
	$billDesc = is_empty($_REQUEST['billDesc']) ? null : $_REQUEST['billDesc'];
	$repayArray = is_empty($_REQUEST['repayArray']) ? null :$_REQUEST['repayArray'];
	
	$code = '0';
	
	if ($op == 'add')
		$billId = - 1;
	
	$billService = new BillService();
	
	if ($op == 'add') { // 新增
		switch ($billTypeFlag) {
			case 0 :
			case 1 :
				$billService->insertNormalBill($userId, $billTypeFlag, $billTime, $billTypeId, $accountId, $billSum, $billDesc);
				break;
			case 2:
			case 3:
				$billService->insertTransferBill($userId, $billTypeFlag, $billTime, $billTypeId, $accountId, $account2Id, $billSum, $billDesc);
				break;
			case 5:
			case 6:
				$billService->insertDebtBill($userId, $billTypeFlag, $billTime, $billTypeId, $accountId, $account2Id, $billSum, $billDesc);
				break;
			case 4:
			case 7:
				$repayArray = json_to_array(urldecode($repayArray));
				//校验还款、收款合法性
				foreach($repayArray as $repay) {
					if($billService->checkRepay((int)$repay['billId'], (float)$repay['repaySum']) == false) {
						$code = 'mustRefreshRepayList';
						break;
					}
				}
				if($code == '0') {
					//插入还款、收款记录
					foreach($repayArray as $repay) {
						$billService->insertRepayBill($userId, $billTypeFlag, $billTime, $accountId, $billDesc, (int)$repay['billId'], (float)$repay['repaySum']);
					}
				}
				break;
		}
	} else if ($op == 'edit') { // 修改
		switch ($billTypeFlag) {
			case 0 :
			case 1 :
				$billService->updateNormalBill($userId, $billTypeFlag, $billId, $billTime, $billTypeId, $accountId, $billSum, $billDesc);
				break;
			case 2:
			case 3:
				$billService->updateTransferOutBill($userId, $billTypeFlag, $billId, $billTime, $billTypeId, $accountId, $account2Id, $billSum, $billDesc);
				break;
			case 5:
			case 6:
				//校验要修改的记录是否已经有了关联记录
				if ($billService->countBillRepayRelated($billId) > 0) {
					$code = 'existBillRelated';
				} else {
					$billService->updateDebtBill($userId, $billTypeFlag, $billId, $billTime, $billTypeId, $accountId, $account2Id, $billSum, $billDesc);
				}
				break;
			case 4:
			case 7:
				$billService->updateRepayBill($userId, $billTypeFlag, $billId, $billTime, $accountId, $billSum, $billDesc);
				break;
		}
	}
	
	$output = array (
			'retCode' => $code 
	);
	
	echo get_json($output);
}

/**
 * 删除账目
 */
function delete_bill() {
	$billTypeFlag = (int) $_REQUEST['billTypeFlag'];
	$billId = (int) $_REQUEST['billId'];
	
	$code = '0';
	$billService = new BillService();
	
	switch ($billTypeFlag) {
		case 0 :
		case 1 :
			$billService->deleteNormalBill($billId, $billTypeFlag);
			break;
		case 2:
		case 3:
			$billService->deleteTransferBill($billId, $billTypeFlag);
			break;
		case 5:
		case 6:
			$billService->deleteDebtBill($billId, $billTypeFlag);
			break;
		case 4:
		case 7:
			$billService->deleteRepayBill($billId, $billTypeFlag);
			break;
	}
	
	$output = array (
			'retCode' => $code
	);
	
	echo get_json($output);
}
?>