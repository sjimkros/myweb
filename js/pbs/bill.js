var _cacheBill = {
	accountDefault : null,
	billTypeList : null,
	billTypeInList : null,
	billTypeOutList : null,
	accountList : null,
	accountDebtList : null,
	accountCreditList : null
};

/**
 * 页面加载后执行函数
 */
$(function() {
	initBillManageData();
});

/**
 * 初始化记账管理基本数据
 */
function initBillManageData() {
	// 初始化时间选择器
	$("#startDate, #endDate, #dialog_billTime").datepicker({
		autoclose : true,
		format : "yyyy-mm-dd",
		todayBtn : true,
		todayHighlight : true,
		language : "zh-CN",
		minView : "month",
		orientation : "bottom",
		weekStart : 7
	});
	// 加载基本数据
	$.ajax({
		url : domainUrl + "pbs/action/bill_action.php",
		type : "POST",
		data : {
			"method" : "initBillManageData"
		},
		dataType : "json",
		success : function(data) {
			if (data.retCode == "0") {
				// 存入缓存
				_cacheBill.accountList = data.accountList;
				_cacheBill.accountDebtList = data.accountDebtList;
				_cacheBill.accountCreditList = data.accountCreditList;
				_cacheBill.billTypeList = data.billTypeList;
				_cacheBill.billTypeInList = data.billTypeInList;
				_cacheBill.billTypeOutList = data.billTypeOutList;

				// 查询条件控件
				bindDataForSelect("account", _cacheBill.accountList, "account_id", "account_name");
				bindDataForSelect("billType", _cacheBill.billTypeList, "bill_type_id", "bill_type_name");
				// 对话框
				bindDataForSelect("dialog_account1", _cacheBill.accountList, "account_id", "account_name");
				bindDataForSelect("dialog_billTypeIn", _cacheBill.billTypeInList, "bill_type_id", "bill_type_name");
				bindDataForSelect("dialog_billTypeOut", _cacheBill.billTypeOutList, "bill_type_id", "bill_type_name");
				// 默认账户
				_cacheBill.accountDefault = data.accountDefault;

				bindBillUrlParam();
				// 获取收支条目
				getBillList();
				initBillDialog();
			}
		}
	});
}

/**
 * 初始化新增对话框
 */
function initBillDialog() {
	var flag = getUrlParam("flag");
	if(flag != "") {
		showBillDialog("add", parseInt(flag));
	}
}

/**
 * 获取账目列表
 */
function getBillList() {
	var startDate = $("#startDate").val();
	var endDate = $("#endDate").val()
	var accountId = $("#account").val();
	var billTypeId = $("#billType").val();
	var billTypeFlag = $("#billTypeFlag").val();
	var curPageIndex = $("#curPageIndex").val();

	$.ajax({
		url : domainUrl + "pbs/action/bill_action.php",
		type : "POST",
		data : {
			"method" : "getBillList",
			"startDate" : startDate,
			"endDate" : endDate,
			"accountId" : accountId,
			"billTypeId" : billTypeId,
			"billTypeFlag" : billTypeFlag,
			"curPageIndex" : curPageIndex
		},
		dataType : "json",
		success : function(data) {
			if (data.retCode == "0") {
				bindData("billListTbl", data.billList, "bill_id", billListBindedEvent);
				setPaginator("billListPaginator", curPageIndex, data.pageCount, getTurnBillPageUrl);
			}
		}
	});
}

/**
 * 账目数据绑定事件
 * 
 * @param jRow
 * @param data
 * @returns
 */
function billListBindedEvent(jRow, data) {
	jRow.find("[data-event='operate'] a").attr("data-id", data.bill_id);
	jRow.find("[data-event='operate'] a:eq(0)").attr("onclick", "showBillDialog('edit', " + data.bill_type_flag + ", this);");
	jRow.find("[data-event='operate'] a:eq(1)").attr("onclick", "confirmDeletBill(" + data.bill_type_flag + ", this);");

	return jRow;
}

/**
 * 获取翻页url
 * 
 * @param type
 * @param page
 * @param current
 * @returns {String}
 */
function getTurnBillPageUrl(type, page, current) {
	return "bill.php?" + genBillUrlParam(page);
}

/**
 * 绑定url参数值到页面控件
 */
function bindBillUrlParam() {
	$("#startDate").val(getUrlParam("startDate"));
	$("#startDate").datepicker('update', getUrlParam("startDate"));
	$("#endDate").val(getUrlParam("endDate"));
	$("#endDate").datepicker('update', getUrlParam("endDate"));
	$("#account").val(getUrlParam("accountId"));
	$("#billType").val(getUrlParam("billTypeId"));
	$("#billTypeFlag").val(getUrlParam("billTypeFlag"));
	if (getUrlParam("curPageIndex") != "")
		$("#curPageIndex").val(getUrlParam("curPageIndex"));
}

/**
 * 生成查询url参数
 * 
 * @returns {String}
 */
function genBillUrlParam(page) {
	var params = "";
	var startDate = $("#startDate").val();
	var endDate = $("#endDate").val();
	var accountId = $("#account").val();
	var billTypeId = $("#billType").val();
	var billTypeFlag = $("#billTypeFlag").val();
	var curPageIndex = 1;
	if (page != undefined) {
		curPageIndex = page;
	}

	params = "startDate=" + startDate + "&endDate=" + endDate + "&accountId=" + accountId + "&billTypeId=" + billTypeId + "&billTypeFlag=" + billTypeFlag + "&curPageIndex=" + curPageIndex;
	return params;
}

/**
 * 执行查询
 */
function queryBill() {
	closeTips([ "mustStartDateBeforEndDate" ]);

	var isVaild = true;
	var startDate = $("#startDate").val();
	var endDate = $("#endDate").val()
	if (startDate != "" && endDate != "" && startDate > endDate) {
		showTip("mustStartDateBeforEndDate");
		isVaild = false;
	}

	if (!isVaild)
		return false;

	$("#curPageIndex").val(1);
	var url = getUrlWithoutParams() + "?" + genBillUrlParam();
	location.href = url;
}

/**
 * 显示账目编辑对话框
 * 
 * @param op
 * @param billTypeFlag
 * @param id
 */
function showBillDialog(op, billTypeFlag, obj) {
	cleanBillErrorTip();
	emptyInputs([ "billId", "op", "dialog_billTypeFlag", "dialog_billTime", "dialog_billType", "dialog_account1", "dialog_account2", "dialog_billSum", "dialog_billDesc" ]);

	var dialogTitle = $("#billDialog .modal-title");
	var billTime = $("#dialog_billTime");
	var billType = $("#dialog_billType");
	var account1 = $("#dialog_account1");
	var account2 = $("#dialog_account2");
	var billSum = $("#dialog_billSum");
	var billDesc = $("#dialog_billDesc");

	var flagStr = "";
	switch (billTypeFlag) {
	case 0: // 支出
	case 1: // 收入
		if (billTypeFlag == 1) {
			bindDataForSelect("dialog_billType", _cacheBill.billTypeInList, "bill_type_id", "bill_type_name");
			flagStr = "收入";
		} else {
			bindDataForSelect("dialog_billType", _cacheBill.billTypeOutList, "bill_type_id", "bill_type_name");
			flagStr = "支出";
		}
		setDisplay("billTypeDiv", true);
		setDisplay("account1Div", true);
		setDisplay("account2Div", false);
		setDisplay("repayDiv", false);
		setDisplay("billSumDiv", true);

		account1.val(_cacheBill.accountDefault);
		$("#billTypeDiv label").text(flagStr + "类别");
		$("#account1Div label").text("账户");
		break;

	case 5: // 借入/预收
	case 6: // 借出/垫付
		var flagStr2;
		var repayStr;
		if (billTypeFlag == 5) {
			flagStr = "借入/预收";
			flagStr2 = "债务";
			repayStr = "还欠款";
			bindDataForSelect("dialog_account2", _cacheBill.accountDebtList, "account_id", "account_name");
		} else {
			flagStr = "借出/垫付";
			flagStr2 = "债权";
			repayStr = "收欠款/报销";
			bindDataForSelect("dialog_account2", _cacheBill.accountCreditList, "account_id", "account_name");
		}
		setDisplay("billTypeDiv", false);
		setDisplay("account1Div", true);
		setDisplay("account2Div", true);
		setDisplay("repayDiv", false);
		setDisplay("billSumDiv", true);

		$("#existBillRelated").text("该条记录已存在相应的“" + repayStr + "”记录，不能修改");

		account1.val(_cacheBill.accountDefault);
		$("#account1Div label").text(flagStr + "账户");
		$("#account2Div label").text(flagStr2 + "账户");
		break;

	case 4: // 还欠款
	case 7: // 收欠款/报销
		if (billTypeFlag == 4) {
			flagStr = "还欠款";
			flagStr2 = "债务";
		} else {
			flagStr = "收欠款/报销";
			flagStr2 = "债权";
		}

		setDisplay("billTypeDiv", false);
		setDisplay("account1Div", true);
		setDisplay("account2Div", false);
		setDisplay("repayDiv", true);

		account1.val(_cacheBill.accountDefault);
		$("#account1Div label").text(flagStr + "账户");

		if (op == "edit") {
			setDisplay("repayDiv", false);
			setDisplay("billSumDiv", true);
		} else {
			setDisplay("repayDiv", true);
			setDisplay("billSumDiv", false);
			var relatedList = $("#relatedList");
			relatedList.find("th:eq(1)").text(flagStr2 + "账户");
		}
		break;

	case 2: // 存取款/转账/还信用卡
	case 3:
		flagStr = "存取款/转账/还信用卡";

		setDisplay("billTypeDiv", false);
		setDisplay("account1Div", true);
		setDisplay("account2Div", true);
		setDisplay("repayDiv", false);
		setDisplay("billSumDiv", true);

		bindDataForSelect("dialog_account2", _cacheBill.accountList, "account_id", "account_name");
		account1.val(_cacheBill.accountDefault);
		account2.val(_cacheBill.accountDefault);
		$("#account1Div label").text("转出账户");
		$("#account2Div label").text("转入账户");
		break;

	default:
		break;
	}

	if (op == "add") {
		dialogTitle.text("新增" + flagStr);
		$("#submitAgainButton").removeClass("disabled");
		billTime.val(getToday());
		$('#dialog_billTime').datepicker('update', billTime.val());

		// 还欠款，收欠款/报销
		if (billTypeFlag == 4 || billTypeFlag == 7) {
			getBillRelatedList((billTypeFlag == 4) ? 5 : 6);
		}

	} else if (op == "edit") {
		dialogTitle.text("修改" + flagStr);
		$("#submitAgainButton").addClass("disabled");
		getBill($(obj).attr("data-id"), billTypeFlag);

	}

	$("#dialog_billTypeFlag").val(billTypeFlag);
	$("#op").val(op);
	$("#billDialog").modal("show");
}

/**
 * 提交表单
 */
function submitBill(isContinue) {
	cleanBillErrorTip();
	var op = $("#op");
	var billId = $("#billId");
	var billTypeFlag = $("#dialog_billTypeFlag");
	var billTime = $("#dialog_billTime");
	var billType = $("#dialog_billType");
	var account1 = $("#dialog_account1");
	var account2 = $("#dialog_account2");
	var billSum = $("#dialog_billSum");
	var billDesc = $("#dialog_billDesc");
	var repayArray = "";
	var flag = billTypeFlag.val();

	var isVaild = true;
	if (isEmpty(billTime.val())) {
		showTip("mustInputBillTime");
		isVaild = false;
	}
	if (flag == "2" && account1.val() == account2.val()) { // 转账账户校验
		showTip("mustAccount2Different");
		isVaild = false;
	}
	if ((flag == "4" || flag == "7") && op.val() == "add") { // 还欠款 或 收欠款/报销
		var repayList = $("#relatedList");
		var repaySums = repayList.find("tr[data-template!='templateRow'] .repaySum"); // 获取应付输入框组
		var emptyCount = 0;
		var isValLegal = true;
		var isValLegal2 = true;

		repaySums.each(function() {
			var val = this.value;
			if (isEmpty(val)) { // 校验为空
				emptyCount++;
			} else if (!checkFloat(val) || parseFloat(val) == 0.0) { // 校验数字合法性
				isValLegal = false;
			} else { // 校验数值是否小于等于应付数
				var billSum = parseFloat($(this).parent().siblings("[data-key='bill_related_sum']").text());
				if (parseFloat(val) > billSum) {
					isValLegal2 = false;
				} else { // 合法数据，拼装json
					if (repayArray != "")
						repayArray += ",";
					repayArray += "{\"billId\":" + $(this).attr("data-id") + ", \"repaySum\":" + val + "}";
				}
			}
		});
		repayArray = "[" + repayArray + "]";

		if (emptyCount == repaySums.length) {
			showTip("mustInputRelatedSum");
			isVaild = false;
		}
		if (!isValLegal) {
			showTip("isNanRelatedSum");
			isVaild = false;
		}
		if (!isValLegal2) {
			showTip("mustRelatedSumBelowRepaySum");
			isVaild = false;
		}
	} else {
		if (isEmpty(billSum.val())) {
			showTip("mustInputBillSum");
			isVaild = false;
		} else if (!checkFloat(billSum.val()) || parseFloat(billSum.val()) == 0.0) {
			showTip("isNanBillSum");
			isVaild = false;
		}
	}

	if ((flag == "4" || flag == "7") && op.val() == "edit") {
		var lastRepaySum = $(".maxSum").text();
		if (billSum.val() > parseInt(lastRepaySum)) {
			showTip("mustBillSumBelowRepaySum");
			isVaild = false;
		}
	}

	if (!isVaild)
		return false;

	$.ajax({
		url : domainUrl + "pbs/action/bill_action.php",
		//type : "POST",
		async : false,
		data : {
			"method" : "updateBill",
			"op" : op.val(),
			"billId" : billId.val(),
			"billTypeFlag" : billTypeFlag.val(),
			"billTime" : billTime.val(),
			"billTypeId" : billType.val(),
			"accountId" : account1.val(),
			"account2Id" : account2.val(),
			"billSum" : billSum.val(),
			"billDesc" : billDesc.val(),
			"repayArray" : escape(repayArray)
		},
		dataType : "json",
		success : function(data) {
			if (data.retCode == "0") {
				getBillList();
				if(isContinue) {
					billSum.val("");
					billDesc.val("");
				} else {
					$("#billDialog").modal('hide');
				}

				try {
					refreshAccountQuickList();
				} catch (e) {
				}
			} else {
				showTip(data.retCode);
			}
		}
	});
}
/**
 * 获取账目详情
 * 
 * @param billId
 */
function getBill(billId, billTypeFlag) {
	$.ajax({
		url : domainUrl + "pbs/action/bill_action.php",
		data : {
			"method" : "getBill",
			"billId" : billId,
			"billTypeFlag" : billTypeFlag
		},
		dataType : "json",
		success : function(data) {
			if (data.retCode == "0") {
				bindDataForOne("billDialog", data.bill, "bill_id");
				$("#dialog_billTime").datepicker('update', data.bill.bill_time);
				switch (billTypeFlag) {
				case 2:
				case 3:
					// 绑定转账对应记录
					$("#dialog_account2").val(data.transferAccountId);
					$("#dialog_billTypeFlag").val("2");
					break;
				}
			}
		}
	});
}

/**
 * 获取关联账目列表
 */
function getBillRelatedList(billTypeFlag) {
	$.ajax({
		url : domainUrl + "pbs/action/bill_action.php",
		type : "POST",
		data : {
			"method" : "getBillRelatedList",
			"billTypeFlag" : billTypeFlag
		},
		dataType : "json",
		success : function(data) {
			if (data.retCode == "0") {
				bindData("relatedList", data.billRelatedList, "bill_id", billRelatedListBindedEvent);
			}
		}
	});
}

/**
 * 关联账目列表绑定事件
 * 
 * @param jRow
 * @param data
 * @returns
 */
function billRelatedListBindedEvent(jRow, data) {
	jRow.find("[data-event='bill_id'] input").attr("data-id", data.bill_id);
	return jRow;
}

/**
 * 确认删除
 * 
 * @param billTypeFlag
 * @param obj
 */
function confirmDeletBill(billTypeFlag, obj) {
	switch (billTypeFlag) {
	case 0: // 支出
	case 1: // 收入
	case 2: // 转出
	case 3: // 转入
	case 4: // 还款
	case 7: // 收款
		showConfirm("确认删除吗？", deleteBill, new Array($(obj).attr("data-id"), billTypeFlag));
		break;
	case 5: // 借入/预收
	case 6: // 借出/垫付
		var actionName = (billTypeFlag == 5) ? "借入/预收" : "借出/垫付";
		var actionName2 = (billTypeFlag == 5) ? "还欠款" : "收欠款/报销";
		showConfirm("删除“" + actionName + "”记录同时也会删除相应的“" + actionName2 + "”记录。确认删除吗？", deleteBill, new Array($(obj).attr("data-id"), billTypeFlag));
		break;
	}
}

/**
 * 删除记账条目
 * 
 * @param params
 */
function deleteBill(params) {
	var billId = params[0];
	var billTypeFlag = params[1];

	$.ajax({
		url : domainUrl + "pbs/action/bill_action.php",
		type : "POST",
		async : false,
		data : {
			"method" : "deleteBill",
			"billId" : billId,
			"billTypeFlag" : billTypeFlag
		},
		dataType : "json",
		success : function(data) {
			if (data.retCode == "0") {
				getBillList();
				try {
					refreshAccountQuickList();
				} catch (e) {
				}
			}
		}
	});
}

function cleanBillErrorTip() {
	closeTips([ "mustInputBillTime", "mustRepayTimeAfterDebtTime", "mustInputBillSum", "isNanBillSum", "mustBillSumBelowRepaySum", "mustAccount2Different", "existBillRelated", "mustInputRelatedSum", "isNanRelatedSum", "mustRelatedSumBelowRepaySum", "mustRefreshRepayList" ]);
}