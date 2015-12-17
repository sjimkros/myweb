<?php
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

/**
 * 用户服务类
 * 
 * @author sjimkros
 *        
 */
class UserService extends ServiceBase {
	
	/**
	 * 校验登录用户名密码
	 * 
	 * @param unknown $userName        	
	 * @param unknown $password        	
	 */
	public function checkPassword($userName, $password) {
		$params = array (
				'userName' => $userName,
				'password' => $password 
		);
		
		$rows = $this->getDAO()->select(MywebUserSQL::$SQL_CHECK_PASSWORD, $params);
		
		if ($this->getDAO()->getSelectCount() > 0) {
			return $rows[0]['user_id'];
		} else {
			return -1;
		}
	}
	
	/**
	 * 获取用户信息
	 * @param unknown $userId
	 */
	public function getUser($userId) {
		$params = array(
				'userId' => $userId
		);
		
		$rows = $this->getDAO()->select(MywebUserSQL::$SQL_SELECT_BY_USERID, $params);
		
		if($this->getDAO()->getSelectCount() > 0) {
			$row = $rows[0];
			return $row;
			
		} else {
			return null;
		}
	}
	
	/**
	 * 更新用户配置到数据库
	 * @param unknown $userId
	 * @param unknown $config
	 */
	public function updateConfig($userId, $config) {
		$params = array(
				'userId' => $userId,
				'userConfig' =>$config
		);
		
		$count = $this->getDAO()->update(MywebUserSQL::$SQL_UPDATE_CONFIG_BY_USERID, $params);
		return $count;
	}
	
	/**
	 * 更新密码
	 * @param unknown $userId
	 * @param unknown $password
	 */
	public function updatePassword($userId, $password) {
		$params = array(
				'userId' => $userId,
				'password' =>$password
		);
		
		$count = $this->getDAO()->update(MywebUserSQL::$SQL_UPDATE_PASSWORD, $params);
		return $count;
	}
}

?>