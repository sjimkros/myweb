<?php
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

/**
 * MySQL数据库控制类
 *
 * @author sjimkros
 *        
 */
class MySqlControl {
	private $hostname;
	private $hostport;
	private $username;
	private $password;
	private $dbname;
	private $dbencoding;
	private $connection;
	private $affectedCount;
	private $rows;
	private $rowsCount;
	private $result;
	
	/**
	 * 构造函数
	 */
	public function __construct() {
		$this->hostname = HOST_NAME;
		$this->hostport = DB_PORT;
		$this->username = DB_USERNAME;
		$this->password = DB_PASSWORD;
		$this->dbname = DB_NAME;
		$this->dbencoding = DB_ENCODING;
		
		$this->affectedCount = 0;
		$this->rowsCount = 0;
		$this->rows = null;
	}
	
	/**
	 * 析构函数，自动关闭数据库，垃圾回收机制
	 */
	public function __destruct() {
		// $this->close();
	}
	
	// 连接MySQL数据库
	public function open() {
		$this->connection = new mysqli($this->hostname, $this->username, $this->password, $this->dbname, $this->hostport);
		if (mysqli_connect_errno()) { // 连接失败
			echo $this->connection->connect_error;
			die("Connect failed: " . mysqli_error($this->connection));
			return false;
		}
		mysqli_query($this->connection, 'SET NAMES ' . DB_ENCODING);
		return true;
	}
	
	/**
	 * 关闭连接，释放资源
	 */
	public function close() {
		$this->connection->close();
	}
	
	/**
	 * 获取结果集
	 */
	public function getRows() {
		$this->getArrayFromResult();
		return $this->rows;
	}
	
	/**
	 * 获取单行结果
	 *
	 * @param unknown $i        	
	 */
	public function getRow($i) {
		$this->getArrayFromResult();
		return $this->rows[$i];
	}
	
	/**
	 * 获取结果集行数
	 *
	 * @return number
	 */
	public function getRowsCount() {
		$this->getArrayFromResult();
		return $this->rowsCount;
	}
	
	/**
	 * 获取影响的行数
	 *
	 * @return number
	 */
	public function getAffectedCount() {
		return $this->connection->affected_rows;
	}
	
	/**
	 * 执行sql语句
	 *
	 * @param unknown $sqlText        	
	 */
	public function query($sqlText) {
		unset($this->rows);
		$this->rows = null;
		$this->rowsCount = 0;
		$this->affectedCount = 0;
		
		$this->connection->multi_query($sqlText);
	}
	
	/**
	 * 获取上一次insert生成的自增长ID
	 *
	 * @return number
	 */
	public function getLastInsertId() {
		return mysql_insert_id($this->connection);
	}
	
	/**
	 * 开始事务处理
	 */
	public function beginTransaction() {
		$this->connection->autocommit(false);
	}
	
	/**
	 * 获取事务执行错误状态
	 */
	public function getErrno() {
		return $this->connection->errno;
	}
	
	/**
	 * 获取事务执行错误信息
	 */
	public function getError() {
		return $this->connection->error;
	}
	
	/**
	 * 提交事务
	 */
	public function commit() {
		$this->connection->commit();
		$this->connection->autocommit(true);
		//print_r('[' . $this->connection->error . ']');
	}
	
	/**
	 * 回退事务
	 */
	public function rollback() {
		$this->connection->rollback();
		$this->connection->autocommit(true);
	}
	
	/**
	 * 释放结果集
	 */
	public function free() {
		mysqli_free_result($this->rows);
	}
	
	/**
	 * 获取首行并从结果集中剔除，结果集数量-1
	 */
	public function shiftRow() {
		$firstRow = array_shift($this->rows);
		
		$this->rowsCount--;
		return $firstRow;
	}
	
	/**
	 * 将查询结果集转为二维数组
	 */
	private function getArrayFromResult() {
		if ($this->rows == null) {
			do {
				if ($out = $this->connection->use_result()) {
					while ($current_row = $out->fetch_assoc()) {
						$this->rows[$this->rowsCount ++] = $current_row;
					}
					$out->close();
				}
			} while ($this->connection->next_result());
			
			$this->affected_rows = $this->connection->affected_rows;
		}
	}
}

?>