var currentBillTypeFlag;
var billTypeFlagIncome = "1";
var billTypeFlagPayment = "0";

/**
 * 页面加载后执行函数
 */
$(function() {
	// 标签切换处理函数
	$('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
		var tabLink = e.target // newly activated tab
		if (tabLink.hash == "#billTypeInTab") {
			currentBillTypeFlag = billTypeFlagIncome;
			getBillTypeList(currentBillTypeFlag);

		} else if (tabLink.hash == "#billTypeOutTab") {
			currentBillTypeFlag = billTypeFlagPayment;
			getBillTypeList(currentBillTypeFlag);

		}
	});

	// 默认先显示收入类别
	currentBillTypeFlag = billTypeFlagIncome;
	getBillTypeList(currentBillTypeFlag);
});
/**
 * 获取收支类别列表
 * 
 * @param billTypeFlag
 *            收支类别
 */
function getBillTypeList(billTypeFlag) {
	var tableId = "";
	if (billTypeFlag == billTypeFlagIncome)
		tableId = "billTypeIncomeTbl";
	else if (billTypeFlag == billTypeFlagPayment)
		tableId = "billTypePaymentTbl";

	$.ajax({
		url : domainUrl + "pbs/action/bill_type_action.php",
		type : "POST",
		data : {
			"method" : "getBillTypeList",
			"billTypeFlag" : billTypeFlag
		},
		dataType : "json",
		success : function(data) {
			if(data.retCode == "0") {
				bindData(tableId, data.billTypeList, "bill_type_id", billTypeListBindedEvent);
			}
		},
		error : function(XMLHttpRequest, textStatus, errorThrown) {
		},
		complete : function(XMLHttpRequest, textStatus) {
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
function billTypeListBindedEvent(jRow, data) {
	if (data.system_flag == 1) {
		jRow.find("[data-event='operate']").html("");
	} else {
		jRow.find("[data-event='system_flag']").html("");
		jRow.find("[data-event='operate'] a").attr("data-id", data.bill_type_id);
	}
	return jRow;
}
/**
 * 获取收支类别
 * @param billTypeId
 */
function getBillType(billTypeId) {
	$.ajax({
		url : domainUrl + "pbs/action/bill_type_action.php",
		type : "POST",
		async : false,
		data : {
			"method" : "getBillType",
			"billTypeId" : billTypeId
		},
		dataType : "json",
		success : function(data) {
			if (data.retCode == "0") {
				bindDataForOne("billTypeDialog", data.billType, "bill_type_id");
			}
		},
		error : function(XMLHttpRequest, textStatus, errorThrown) {

		},
		complete : function(XMLHttpRequest, textStatus) {

		}
	});
}
/**
 * 打开收支类别编辑对话框
 * 
 * @param op
 *            操作标识 add-新增 edit-修改
 * @param obj
 *            操作链接对象
 */
function showBillTypeDialog(op, obj) {
	closeTips([ "mustInputBillTypeName", "existBillTypeName" ]);
	emptyInputs([ "billTypeId", "op", "billTypeName", "billTypeDesc", "billTypeFlagIncome", "billTypeFlagPayment" ]);
	if (currentBillTypeFlag == billTypeFlagIncome) {
		setInputChecked("billTypeFlagIncome", true);
	} else if (currentBillTypeFlag == billTypeFlagPayment) {
		setInputChecked("billTypeFlagPayment", true);
	}
	setInputDisabled("billTypeFlagIncome", false);
	setInputDisabled("billTypeFlagPayment", false);
	
	if (op == "add") { // 新增
		$(".modal-title").text("新增类别")
	} else if (op == "edit") { // 修改
		$(".modal-title").text("修改类别")
		getBillType($(obj).attr("data-id"));
		setInputDisabled("billTypeFlagIncome", true);
		setInputDisabled("billTypeFlagPayment", true);
	}
	$("#op").val(op);
	$("#billTypeDialog").modal("show");
}
/**
 * 提交数据
 */
function submitBillType() {
	closeTips([ "mustInputBillTypeName", "existBillTypeName" ]);

	var op = $("#op");
	var billTypeId = $("#billTypeId");
	var billTypeName = $("#billTypeName");
	var billTypeDesc = $("#billTypeDesc");
	var billTypeFlag = $("input[name='billTypeFlag']:checked");

	var isVaild = true;
	if (isEmpty(billTypeName.val())) {
		showTip("mustInputBillTypeName");
		isVaild = false;
	}
	if (!isVaild)
		return false;

	$.ajax({
		url : domainUrl + "pbs/action/bill_type_action.php",
		type : "POST",
		async : false,
		data : {
			"method" : "updateBillType",
			"op" : op.val(),
			"billTypeId" : billTypeId.val(),
			"billTypeName" : billTypeName.val(),
			"billTypeDesc" : billTypeDesc.val(),
			"billTypeFlag" : billTypeFlag.val()
		},
		dataType : "json",
		success : function(data) {
			if (data.retCode == "0") {
				$("#billTypeDialog").modal("hide");
				getBillTypeList(currentBillTypeFlag);

			} else {
				showTip(data.retCode);
			}
		},
		error : function(XMLHttpRequest, textStatus, errorThrown) {
		},
		complete : function(XMLHttpRequest, textStatus) {
		}
	});
}
/**
 * 删除确认
 * 
 * @param obj
 */
function confirmBillType(obj) {
	showConfirm("确认删除吗？", deleteBillType, $(obj).attr("data-id"));
}
/**
 * 删除收支类别
 * 
 * @param accountId
 */
function deleteBillType(billTypeId) {
	$.ajax({
		url : domainUrl + "pbs/action/bill_type_action.php",
		type : "POST",
		async : false,
		data : {
			"method" : "deleteBillType",
			"billTypeId" : billTypeId
		},
		dataType : "json",
		success : function(data) {
			if (data.retCode == "0") {
				getBillTypeList(currentBillTypeFlag);
			} else if (data.retCode == "existBillInBillType") {
				showAlert("该类别下已有记账记录，不能删除");
			}
		},
		error : function(XMLHttpRequest, textStatus, errorThrown) {
		},
		complete : function(XMLHttpRequest, textStatus) {
		}
	});
}