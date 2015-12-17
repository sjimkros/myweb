<?php
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

$method = $_REQUEST['method'];

switch ($method) {
	case 'doLogin' :
		do_login();
		break;
	case 'updateProfile' :
		update_profile();
		break;
}

/**
 * 登录处理
 */
function do_login() {
	$userName = $_REQUEST['userName'];
	$password = md5($_REQUEST['password']);
	$rememberMe = $_REQUEST['rememberMe'];
	
	$userService = new UserService();
	$userId = $userService->checkPassword($userName, $password);
	
	// 检查cookie中是否设置了“记住我”
	$isCookieLogin = false;
	if (isset($_COOKIE['rememberMe'])) {
		$userId = (int) substr($_COOKIE['rememberMe'], 0, 1);
		$isCookieLogin = true;
	}
	
	if ($userId == - 1) { // 用户名或密码不存在
		$output = array (
				'result' => 'wrongUserNamePassword' 
		);
		
		echo get_json($output);
	} else { // 登录处理
		
		$result = $userService->getUser($userId);
		$code = '0';
		$redirect = '';
		
		if ($result != null) {
			$_SESSION['userId'] = $result['user_id'];
			$_SESSION['userDetail'] = $result; // 用户数据
			                                   
			// 将用户名保存至cookie
			setcookie('userName', $result['user_name'], time() + 60 * 60 * 24 * 30, '/');
			
			// “记住我”流程补充
			if ($rememberMe == 1) {
				$saveVal = $_SESSION['userId'] . md5($_SESSION['userId']);
				setcookie('rememberMe', $saveVal, time() + 60 * 60 * 24 * 30, '/');
			} else {
				setcookie('rememberMe', '', time() - 3600, '/');
			}
			
			$code = '0';
			if (isset($_SESSION['lastUrl'])) {
				$redirect = $_SESSION['lastUrl'];
			} else {
				$redirect = DOMAIN_NAME . '/' . APP_NAME . '/index.php';
			}
		} else {
			$code = 'wrongUserNamePassword';
		}
		
		$output = array (
				'retCode' => $code,
				'redirect' => $redirect 
		);
		
		echo get_json($output);
		
		if ($isCookieLogin == true) {
			header('location: ' . $redirect);
			exit();
		}
	}
}

/**
 * 更新用户信息
 */
function update_profile() {
	$userId = $_SESSION['userId'];
	
	$oldPassword = is_empty($_REQUEST['oldPassword']) ? null : $_REQUEST['oldPassword'];
	$password = is_empty($_REQUEST['password']) ? null : $_REQUEST['password'];
	$password2 = is_empty($_REQUEST['password2']) ? null : $_REQUEST['password2'];
	
	$code = '0';
	
	if($_SESSION['userDetail']['user_password'] != md5($oldPassword)) {
		$code = 'wrongOldPassword';
	} else {
		$userService = new UserService();
		$userService->updatePassword($userId, md5($password));
	}
	
	$output = array (
			'retCode' => $code
	);
	
	echo get_json($output);
}
?>