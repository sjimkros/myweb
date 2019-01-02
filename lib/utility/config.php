<?php  //配置信息操作函数
include_once '_approot.php';

/**
 * 获取指定用户配置
 * @param unknown $config_name
 * @return NULL|string
 */
function get_user_config($config_name){
	$user_config = $_SESSION['userDetail']['user_config'];
	$start_pos = strpos($user_config, $config_name);
	if($start_pos === false){
		return null;
	}
	$start_pos = $start_pos + strlen($config_name) + 1;
	$end_pos = strpos($user_config, ';', $start_pos);
	return substr($user_config, $start_pos, $end_pos - $start_pos);
}

/**
 * 设置指定用户配置
 * @param unknown $config_name
 * @param unknown $value
 */
function set_user_config($config_name, $value){
	$user_config = $_SESSION['userDetail']['user_config'];
	$start_pos = strpos($user_config, $config_name);
	if($start_pos === false){  //配置不存在
		$user_config .= $config_name . ':' . $value . ';';
	}else{
		$start_pos = strpos($user_config, $config_name) + strlen($config_name) + 1;
		$end_pos = strpos($user_config, ';', $start_pos);
		$user_config = substr_replace($user_config, $value, $start_pos, $end_pos - $start_pos);
	}
	$_SESSION['userDetail']['user_config'] = $user_config;
}

/**
 * 获取每页条目数
 */
function get_page_size() {
	$size = get_user_config('pageSize');
	return ($size == null) ? 10 : (int)$size;
}
?>