/**
 * 获取url中的参数值
 * 
 * @param name
 * @returns {String}
 */
function getUrlParam(name) {
	var url = location.href;
	var paramStrings = url.substring(url.indexOf("?") + 1, url.length).split("&");
	var val = "";
	for (var i = 0; i < paramStrings.length; i++) {
		var param = paramStrings[i];
		if (param.substring(0, param.indexOf("=")).toLowerCase() == name.toLowerCase()) {
			val = param.substring(param.indexOf("=") + 1, param.length);
			break;
		}
	}
	return val;
}
/**
 * 获取删除url参数的路径
 * 
 * @returns
 */
function getUrlWithoutParams() {
	var url = location.href.split("?");
	return url[0];
}
/**
 * 显示错误提示
 * 
 * @param id
 *            html控件id
 */
function showTip(id) {
	$("#" + id).toggleClass("hidden", false);
}
/**
 * 关闭错误提示
 * 
 * @param ids
 *            html控件id数组
 */
function closeTips(ids) {
	for (var i = 0; i < ids.length; i++) {
		$("#" + ids[i]).toggleClass("hidden", true);
	}
}

/**
 * 控制控件显示
 * 
 * @param jqueryObj
 * @param isDisplay
 */
function setDisplay(obj, isDisplay) {
	if (obj instanceof jQuery) {
		obj.toggleClass("hidden", !isDisplay);
	} else {
		$("#" + obj).toggleClass("hidden", !isDisplay);
	}
}
/**
 * 设置输入控件选择
 * 
 * @param id
 *            html控件id
 */
function setInputChecked(id, isChecked) {
	$("#" + id).prop("checked", isChecked);
}
/**
 * 设置输入控件禁用或启用
 * 
 * @param id
 *            html控件id
 */
function setInputDisabled(id, isDisabled) {
	$("#" + id).prop("disabled", isDisabled);
}
/**
 * 将json字符串转换为object对象
 * 
 * @param json
 *            json字符串
 * @returns 转换后的object对象
 */
function getObjectFromJSON(json) {
	return eval("(" + json + ")");
}
/**
 * 绑定json数组的数据到集合对象上
 * data-template为每行数据绑定时使用的模板，包含data-key属性的html容器内文本将被替换为对应列数据值；
 * 每行的顶级容器将添加row-id属性，并赋值为“row_主键值”；含有.lastFixed css属性的行将在绑定后保留在列表结尾；
 * 
 * @param listId
 *            集合对象id
 * @param dataArray
 *            json数组
 * @param keyId
 *            主键名
 * @param onRowBindedEvent
 *            每行绑定时的事件处理函数
 */
function bindData(listId, dataArray, keyId, onRowBindedEvent) {
	emptyBind(listId);
	var list = $("#" + listId);
	var templateRow = list.find("[data-template='templateRow']");

	if (dataArray == null || dataArray.length == 0) {
		list.find(".noDataTr").toggleClass("hidden", false);
	} else {
		var html = "";
		$(dataArray).each(function() {
			var row = templateRow.clone();
			row.removeAttr("data-template");
			row.removeClass("sr-only");
			for ( var pName in this) {
				if (pName != undefined && typeof (this[pName] != "function")) {
					if (pName == keyId) { // 主键处理
						row.attr("row-id", "row_" + this[pName]); // 将当前行的data-field值改为“row主键值”
					}
					var col = row.find("[data-key='" + pName + "']"); // 将数据值插入在有data-key属性的子元素内
					if (col.length > 0) {
						var pValue = this[pName];
						if (pValue != null)
							col.text(pValue);
					}
				}
			}
			if (onRowBindedEvent != undefined) {
				row = onRowBindedEvent(row, this);
			}
			html += row.get(0).outerHTML;
		});
		list.append(html);
	}

	var lastFixed = list.find(".lastFixed").detach();
	if (lastFixed.length > 0) {
		list.append(lastFixed);
	}
}
/**
 * 绑定json数组的数据到select下拉框上。每个选项的value值赋值为keyId对应的列值，选项显示内容赋值为valueId对应的列值
 * 
 * @param listId
 * @param dataArray
 * @param keyId
 * @param onRowBindedEvent
 */
function bindDataForSelect(listId, dataArray, keyId, valueId) {
	var list = $("#" + listId);
	var options = list.find(".option");
	if (options.length > 0) {
		options.remove();
	}

	if (dataArray != null && dataArray.length >= 0) {
		var html = "";
		$(dataArray).each(function() {
			html += "<option value=\"" + this[keyId] + "\" class=\"option\">" + this[valueId] + "</option>";
		});
		list.append(html);
	}
}
/**
 * 绑定一组数据到指定html容器内。包含data-key属性的html容器内文本将被替换为对应列数据值
 * 
 * @param containerId
 * @param data
 * @param keyId
 */
function bindDataForOne(containerId, data, keyId) {
	var container = $("#" + containerId);
	for ( var pName in data) {
		if (pName != undefined && typeof (this[pName] != "function")) {
			var control = container.find("[data-key='" + pName + "']"); // 找到字段
			if (control.size() > 0) {
				var pValue = data[pName];
				if (pValue != null) {
					setControlVal(control, pValue);
				}
			}
		}
	}
}

/**
 * 绑定一条数据值到指定html容器内。包含data-key属性的html容器内文本将被替换为对应列数据值
 * 
 * @param containerId
 * @param keyName
 * @param val
 */
function bindDataForField(containerId, keyName, val) {
	var container = $("#" + containerId);
	var control = container.find("[data-key='" + keyName + "']"); // 找到字段
	setControlVal(control, val);
}

/**
 * 设置控件值
 * 
 * @param control
 * @param value
 */
function setControlVal(controls, value) {
	if (controls.size() > 0 && controls.attr("type") == "radio") { // 单选框组处理
		controls.filter("[value='" + value + "']").prop("checked", true);
	} else {
		controls.each(function() {
			var tagName = this.tagName.toLowerCase();
			if (tagName == "input" || tagName == "select" || tagName == "textarea") {
				$(this).val(value);
			} else {
				$(this).text(value);
			}
		});
	}
}
/**
 * 清除集合对象的数据绑定
 * 
 * @param listId
 *            集合对象id
 */
function emptyBind(listId) {
	var list = $("#" + listId);
	list.find(".noDataTr").toggleClass("hidden", true);
	var rows = list.find("[row-id*='row']");
	if (rows.size() > 0) {
		rows.remove();
	}
}
/**
 * 清空给定输入域的值
 * 
 * @param ids
 *            控件id数组
 */
function emptyInputs(ids) {
	for (var i = 0; i < ids.length; i++) {
		var obj = $("#" + ids[i]);
		var tagName = obj.get(0).tagName.toLowerCase();
		if (tagName == "input") {
			var type = obj.attr("type");
			if (type == "checkbox" || type == "radio") {
				obj.prop("checkde", false);
			} else {
				obj.val("");
			}
		} else if (tagName == "select") {
			obj.val(obj.children().eq(0).attr("value"));
		} else if (tagName == "textarea") {
			obj.val("");
		}
	}
}
/**
 * 显示警告框
 * 
 * @param content
 */
function showAlert(content) {
	var d = $("#alertDialog");
	d.find(".modal-body").text(content);
	d.modal("show");
}
/**
 * 显示确认框
 * 
 * @param content
 * @param callbackFunc
 */
function showConfirm(content, callbackFunc, callbackParams) {
	var d = $("#confirmDialog");
	d.find(".modal-body").text(content);
	d.find(".btn-primary").one("click", function() {
		d.modal("hide");
		callbackFunc(callbackParams);
	});
	d.modal("show");
}
/**
 * 设置菜单项激活显示
 * 
 * @param menuId
 */
function setMenuActive(menuId) {
	if (menuId != "")
		$("#" + menuId).addClass("active");
}
/**
 * 设置分页控件
 */
function setPaginator(containerId, curPageIndex, pageCount, getTurnPage) {
	if (pageCount < 1)
		return;
	var options = {
		currentPage : curPageIndex,
		numberOfPages : 5,
		totalPages : pageCount,
		bootstrapMajorVersion : 3,
		pageUrl : function(type, page, current) {
			return getTurnPage(type, page, current);
		}
	}

	$("#" + containerId).bootstrapPaginator(options);
}

/**
 * 设置Cookie
 * 
 * @param sName
 * @param sValue
 * @param oExpires
 * @param sPath
 * @param sDomain
 * @param bSecure
 */
function setCookie(sName, sValue, oExpires, sPath, sDomain, bSecure) {
	var sCookie = sName + "=" + encodeURIComponent(sValue);
	if (oExpires)
		sCookie += "; expires=" + oExpires.toGMTString();
	if (sPath)
		sCookie += "; path=" + sPath;
	if (sDomain)
		sCookie += "; domain=" + sDomain;
	if (bSecure)
		sCookie += "; secure";
	document.cookie = sCookie;
}

/**
 * 获取Cookie
 * 
 * @param sName
 * @returns
 */
function getCookie(sName) {
	var sRE = "(?:; )?" + sName + "=([^;]*);?";
	var oRE = new RegExp(sRE);
	if (oRE.test(document.cookie))
		return decodeURIComponent(RegExp["$1"]);
	else
		return null;
}

/**
 * 删除Cookie
 * 
 * @param sName
 * @param sPath
 * @param sDomain
 */
function deleteCookie(sName, sPath, sDomain) {
	setCookie(sName, "", new Date(0), sPath, sDomain);
}