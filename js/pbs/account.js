var accountDefault;

/**
 * 页面加载后执行函数
 */
$(function() {
	getAccountManageData();
});
/**
 * 获取账户管理数据
 */
function getAccountManageData() {
	$.ajax({
		url : domainUrl + "pbs/action/account_action.php",
		type : "POST",
		data : {
			"method" : "getAccountManageData"
		},
		dataType : "json",
		success : function(data) {
			if (data.retCode == "-1")
				data = null;
			accountDefault = parseInt(data.accountDefault);
			bindData("accountListTbl", data.accountList, "account_id", accountListBindedEvent);
			bindDataForSelect("accountType", data.accountTypeList, "account_type_id", "account_type_name");
			try {
				refreshAccountQuickList();
			} catch (e) {}
		}
	});
}
/**
 * 获取账户列表
 */
function getAccountList() {
	$.ajax({
		url : domainUrl + "pbs/action/account_action.php",
		type : "POST",
		data : {
			"method" : "getAccountList"
		},
		dataType : "json",
		success : function(data) {
			if (data.retCode == "-1")
				data = null;
			accountDefault = parseInt(data.accountDefault);
			bindData("accountListTbl", data.accountList, "account_id", accountListBindedEvent);
			try {
				refreshAccountQuickList();
			} catch (e) {}
		}
	});
}

/**
 * 获取账户
 */
function getAccount(accountId) {
	$.ajax({
		url : domainUrl + "pbs/action/account_action.php",
		type : "POST",
		async : false,
		data : {
			"method" : "getAccount",
			"accountId" : accountId
		},
		dataType : "json",
		success : function(data) {
			if (data.retCode == "0") {
				bindDataForOne("accountDialog", data.account, "account_id");
			}
		}
	});
}

/**
 * 每行绑定时的事件处理函数
 * 
 * @param jTr
 *            行jquery对象
 * @param data
 *            json数据
 */
function accountListBindedEvent(jRow, data) {
	if (data.account_id != accountDefault) {
		jRow.find("[data-event='account_default']").html("");
	}
	jRow.find("[data-event='account_id'] input").val(data.account_id);
	jRow.find("[data-event='operate'] a").attr("data-id", data.account_id);
	jRow.find("[data-event='operate'] a").attr("data-flag", data.account_type_flag);
	return jRow;
}
/**
 * 设置默认账户
 */
function setAccountDefault() {
	closeTips([ "mustCheckAccount" ]);
	var newAccountDefault = $("#accountListTbl :radio:checked").val();
	if (newAccountDefault == undefined) {
		showTip("mustCheckAccount");
		return false;
	}
	if (parseInt(newAccountDefault) == accountDefault)
		return false;

	$.ajax({
		url : domainUrl + "pbs/action/account_action.php",
		async : false,
		data : {
			"method" : "setAccountDefault",
			"accountId" : newAccountDefault
		},
		dataType : "json",
		success : function(data) {
			if (data.retCode == "0") {
				setAccountDefaultDisplay(newAccountDefault);
			}
		}
	});
}
/**
 * 设置默认账户显示
 * 
 * @param newAccountDefault
 */
function setAccountDefaultDisplay(newAccountDefault) {
	var defaultSpan = $("#accountListTbl tr[row-id='row_" + accountDefault + "'] .glyphicon-ok");
	var oldParent = defaultSpan.parent();

	var col = $("#accountListTbl tr[row-id='row_" + newAccountDefault + "'] td[event-field='account_default']");
	col.append(defaultSpan.clone());
	oldParent.html("");
	accountDefault = newAccountDefault;
}
/**
 * 打开账户编辑对话框
 * 
 * @param op
 */
function showManageDialog(op, obj) {
	closeTips([ "mustInputAccountName", "existAccountName", "mustInputAccountSum", "IsNanAccountSum" ]);
	emptyInputs([ "accountId", "op", "accountName", "accountSum", "accountDesc", "accountType" ]);
	setInputChecked("accountFlag0", true);
	setInputDisabled("accountType", false);

	if (op == "add") { // 新增
		$(".modal-title").text("新增账户");
	} else if (op == "edit") { // 修改
		$(".modal-title").text("修改账户");
		getAccount($(obj).attr("data-id"));
		setInputDisabled("accountType", true);
	}
	$("#op").val(op);
	$("#accountDialog").modal("show");
}
/**
 * 提交表单
 */
function submitAccount() {
	closeTips([ "mustInputAccountName", "existAccountName", "mustInputAccountSum", "IsNanAccountSum" ]);

	var op = $("#op");
	var accountId = $("#accountId");
	var accountName = $("#accountName");
	var accountDesc = $("#accountDesc");
	var accountSum = $("#accountSum");
	var accountType = $("#accountType");
	var accountFlag = $("input[name='accountFlag']:checked");
	
	var isVaild = true;
	if (isEmpty(accountName.val())) {
		showTip("mustInputAccountName");
		isVaild = false;
	}
	if (isEmpty(accountSum.val())) {
		showTip("mustInputAccountSum");
		isVaild = false;
	} else if (!checkFloat(accountSum.val())) {
		showTip("isNanAccountSum");
		isVaild = false;
	}

	if (!isVaild)
		return false;

	$.ajax({
		url : domainUrl + "pbs/action/account_action.php",
		type : "POST",
		async : false,
		data : {
			"method" : "updateAccount",
			"op" : op.val(),
			"accountId" : accountId.val(),
			"accountName" : accountName.val(),
			"accountDesc" : accountDesc.val(),
			"accountSum" : accountSum.val(),
			"accountType" : accountType.val(),
			"accountFlag" : accountFlag.val(),
		},
		dataType : "json",
		success : function(data) {
			if (data.retCode == "0") {
				getAccountList();
				$("#accountDialog").modal('hide')
			} else {
				showTip(data.retCode);
			}
		}
	});
}
/**
 * 删除账户确认
 * 
 * @param obj
 */
function confirmDeleteAccount(obj) {
	var params = [$(obj).attr("data-id"), $(obj).attr("data-flag")];
	showConfirm("确认删除吗？", deleteAccount, params);
}
/**
 * 删除账户
 * 
 * @param accountId
 */
function deleteAccount(params) {
	$.ajax({
		url : domainUrl + "pbs/action/account_action.php",
		type : "POST",
		async : false,
		data : {
			"method" : "deleteAccount",
			"accountId" : params[0],
			"accountTypeFlag" : params[1]
		},
		dataType : "json",
		success : function(data) {
			if (data.retCode == "0") {
				getAccountList();
			} else if (data.retCode == "mustHaveAccount") {
				showAlert("必须至少保留一组个人、债权或债务账户");
			} else if (data.retCode == "existBillInAccount") {
				showAlert("该账户下已有记账记录，不能删除");
			}
		}
	});
}