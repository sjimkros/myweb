<?php
include_once '_approot.php';

session_start();

// 检查session并进行相应的页面跳转
function session_check($lastUrl) { // 参数$goback为设定是否返回之前页面
	if (! isset($_SESSION['userId'])) { // 未登录
		if ($lastUrl != null) {
			$_SESSION['lastUrl'] = $lastUrl; // 设置上次浏览的页面
		}
		if (isset($_COOKIE['rememberMe'])) { // 有cookie，则执行自动登陆
			header('location: ' . DOMAIN_NAME . '/common/action/user_action.php?method=doLogin');
			exit();
		} else { // 没有cookie，转向登陆页面
			header('location: ' . DOMAIN_NAME . '/common/login.php');
			exit();
		}
	} else { // 已登录
		return true;
	}
}

// 检查session并返回是否已登录
function session_check_no_redirect() {
	if (! isset($_SESSION['userId'])) { // 未登录
		if (isset($_COOKIE['rememberMe'])) { // 有cookie，则执行自动登陆
			header('location: ' . DOMAIN_NAME . '/common/action/user_action.php?method=doLogin');
			exit();
		}
		return false;
	} else { // 已登录
		return true;
	}
}
// 获取当前页面URL
function get_current_url() {
	return 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
}
// 获取当前微秒
function get_microtime() {
	list ($usec, $sec) = explode(' ', microtime());
	return ((float) $usec + (float) $sec);
}
// 插入数据库时字符串为空时将值置为NULL
function check_string($str) {
	if ($str == '') {
		return null;
	}
	return $str;
}
// 检查是否为系统管理员admin，并返回结果
function check_admin() {
	if (session_check_no_redirect()) {
	}
}
// 返回给定年月的月天数
function get_month_days($year, $month) {
	switch ($month) {
		case 4 :
		case 6 :
		case 9 :
		case 11 :
			$days = 30;
			break;
		
		case 2 :
			if ($year % 4 == 0) {
				if ($year % 100 == 0) {
					$days = $year % 400 == 0 ? 29 : 28;
				} else {
					$days = 29;
				}
			} else {
				$days = 28;
			}
			break;
		
		default :
			$days = 31;
			break;
	}
	
	return $days;
}
// 根据时间标记返回开始日期
function get_start_time($flag) {
	switch ($flag) {
		case 'null' :
		case '' :
			return null;
		case '1' : // 今日
			return date('Y-m-d');
		case '2' : // 本周
			$day_of_week = (int) date('w');
			return date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - $day_of_week + 1, date('Y')));
		case '3' : // 本月
			return date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y')));
		case "4" : // 今年
			return date('Y-m-d', mktime(0, 0, 0, 1, 1, date('Y')));
		case "5" : // 上周
			$day_of_week = (int) date('w');
			return date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - $day_of_week - 6, date('Y')));
		case "6" : // 上月
			return date('Y-m-d', mktime(0, 0, 0, date('m') - 1, 1, date('Y')));
		case "7" : // 去年
			return date('Y-m-d', mktime(0, 0, 0, 1, 1, date('Y') - 1));
	}
}
// 根据时间标记返回结束日期
function get_end_time($flag) {
	switch ($flag) {
		case 'null' :
		case '' :
			return null;
		case '1' : // 今日
			return date('Y-m-d');
		case '2' : // 本周
			$day_of_week = (int) date('w');
			return date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') + 7 - $day_of_week, date('Y')));
		case '3' : // 本月
			return date('Y-m-d', mktime(0, 0, 0, date('m'), date('t'), date('Y')));
		case "4" : // 今年
			return date('Y-m-d', mktime(0, 0, 0, 12, 31, date('Y')));
		case "5" : // 上周
			$day_of_week = (int) date('w');
			return date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - $day_of_week, date('Y')));
		case "6" : // 上月
			$last_month_days = (int) date('t', mktime(0, 0, 0, date('m') - 1, date('d'), date('Y')));
			return date('Y-m-d', mktime(0, 0, 0, date('m') - 1, $last_month_days, date('Y')));
		case "7" : // 去年
			return date('Y-m-d', mktime(0, 0, 0, 12, 31, date('Y') - 1));
	}
}

/**
 * 获取json格式数据
 *
 * @param unknown $content        	
 * @return string
 */
function get_json($content) {
	return json_encode($content, JSON_UNESCAPED_UNICODE);
}

/**
 * 将json格式数据转为数组
 * 
 * @param unknown $content        	
 */
function json_to_array($content) {
	return json_decode($content, JSON_UNESCAPED_UNICODE);
}

/**
 * 判断变量是否为null或空字符
 * 
 * @param unknown $val        	
 */
function is_empty(&$val) {
	if (isset($val)) {
		if ((string) $val != '') {
			return false;
		}
	}
	return true;
}

?>