<?php

include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

/**
 * 服务基础类
 * @author sjimkros
 *
 */
class ServiceBase {
	
	private $daoBase;
	
	/**
	 * 服务基础类构造函数
	 */
	public function __construct() {
		$this->daoBase = new DaoBase();
	}

	/**
	 * 获取DAO
	 */
	public function getDAO() {
		return $this->daoBase;
	}
}

?>