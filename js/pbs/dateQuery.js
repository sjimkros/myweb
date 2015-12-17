// 查询日期选择事件
function setDateInputOnChange(select, start, end) {
	var now = new Date();
	switch (select.value) {
	case "":
		start.val("");
		end.val("");
		break;
	case "null":
		start.val("");
		end.val("");
		break;
	case "1": // 今日
		start.val(getDateString(now.getFullYear(), (now.getMonth() + 1), now.getDate()));
		end.val(getDateString(now.getFullYear(), (now.getMonth() + 1), now.getDate()));
		break;
	case "2": // 本周
		var nowDayOfWeek = now.getDay();
		var nowDay = now.getDate();
		var nowMonth = now.getMonth();
		var nowYear = now.getFullYear();
		var weekStartDate = new Date(nowYear, nowMonth, nowDay - nowDayOfWeek + 1);
		var weekEndDate = new Date(nowYear, nowMonth, nowDay + (6 - nowDayOfWeek) + 1);
		start.val(getDateString(weekStartDate.getFullYear(), (weekStartDate.getMonth() + 1), weekStartDate.getDate()));
		end.val(getDateString(weekEndDate.getFullYear(), (weekEndDate.getMonth() + 1), weekEndDate.getDate()));
		break;
	case "3": // 本月
		var nowMonth = now.getMonth();
		var nowYear = now.getFullYear();
		var weekStartDate = new Date(nowYear, nowMonth, 1);
		var weekEndDate = new Date(nowYear, nowMonth + 1, 0);
		start.val(getDateString(weekStartDate.getFullYear(), (weekStartDate.getMonth() + 1), weekStartDate.getDate()));
		end.val(getDateString(weekEndDate.getFullYear(), (weekEndDate.getMonth() + 1), weekEndDate.getDate()));
		break;
	case "4": // 今年
		start.val(now.getFullYear() + "-01-01");
		end.val(now.getFullYear() + "-12-31");
		break;
	case "5": // 上周
		var nowDayOfWeek = now.getDay();
		var nowDay = now.getDate();
		var nowMonth = now.getMonth();
		var nowYear = now.getFullYear();
		var weekStartDate = new Date(nowYear, nowMonth, nowDay - nowDayOfWeek - 6);
		var weekEndDate = new Date(nowYear, nowMonth, nowDay - nowDayOfWeek);
		start.val(getDateString(weekStartDate.getFullYear(), (weekStartDate.getMonth() + 1), weekStartDate.getDate()));
		end.val(getDateString(weekEndDate.getFullYear(), (weekEndDate.getMonth() + 1), weekEndDate.getDate()));
		break;
	case "6": // 上月
		var nowMonth = now.getMonth();
		var nowYear = now.getFullYear();
		var weekStartDate = new Date(nowYear, nowMonth - 1, 1);
		var weekEndDate = new Date(nowYear, nowMonth, 0);
		start.val(getDateString(weekStartDate.getFullYear(), (weekStartDate.getMonth() + 1), weekStartDate.getDate()));
		end.val(getDateString(weekEndDate.getFullYear(), (weekEndDate.getMonth() + 1), weekEndDate.getDate()));
		break;
	case "7": // 去年
		start.val((now.getFullYear() - 1) + "-01-01");
		end.val((now.getFullYear() - 1) + "-12-31");
		break;
	default:
		break;
	}
	start.datepicker('update', start.val());
	end.datepicker('update', end.val());
}
// 获取日期
function getDateString(year, month, day) {
	if (month.toString().length < 2)
		month = "0" + month;
	if (day.toString().length < 2)
		day = "0" + day;
	return year + "-" + month + "-" + day;
}
/**
 * 获取今日的日期
 * @returns {String}
 */
function getToday() {
	var now = new Date();
	return getDateString(now.getFullYear(), (now.getMonth() + 1), now.getDate());
}
