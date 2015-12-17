<?php
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

/**
 * 数据访问控制类
 *
 * @author sjimkros
 *        
 */
class DaoBase {
	private $dbControl;
	private $isDbOpen;
	private $lastInsertId;
	private $totalCount;
	
	/**
	 * 数据访问控制类构造函数
	 */
	public function __construct() {
		$this->dbControl = new MySqlControl();
		$this->isDbOpen = false;
	}
	
	/**
	 * 获取数查询执行错误信息
	 */
	public function getError() {
		return $this->dbControl->getError();
	}
	
	/**
	 * 执行select查询
	 *
	 * @param unknown $sqlText        	
	 * @param unknown $params        	
	 */
	public function select($sqlText, $params) {
		$sqlText = $this->getFullQueryText($sqlText, $params);
		//echo $sqlText;
		$this->open();
		$this->dbControl->query($sqlText);
		
		if ($this->dbControl->getRowsCount() > 0) {
			return $this->dbControl->getRows();
		} else {
			return null;
		}
	}
	
	/**
	 * 执行分页select查询
	 *
	 * @param unknown $sqlText        	
	 * @param unknown $params        	
	 * @param unknown $curPageIndex        	
	 * @param unknown $pageSize        	
	 */
	public function selectForPage($sqlText, $params, $curPageIndex, $pageSize) {
		$params['startPos'] = ($curPageIndex - 1) * $pageSize;
		$params['pageSize'] = $pageSize;
		
		$sqlText = $this->getFullQueryText($sqlText, $params);
		//echo $sqlText;
		$this->open();
		$this->dbControl->query($sqlText);
		
		if ($this->dbControl->getRowsCount() > 0) {
			$firstRow = $this->dbControl->shiftRow();
			$this->totalCount = $firstRow['total_count'];
			
			$plus = 0;
			if (($this->totalCount % $pageSize) > 0) {
				$plus = 1;
			}
			
			$pageCount = floor($this->totalCount / $pageSize) + $plus;
			
			$page = array (
					'totalCount' => $this->totalCount,
					'pageCount' => $pageCount,
					'page' => $this->dbControl->getRows() 
			);
			
			return $page;
		} else {
			return null;
		}
	}
	
	/**
	 * 获取查询的总记录数（用于分页查询获取总数使用）
	 */
	public function getTotalCount() {
		return $this->totalCount;
	}
	
	/**
	 * 执行isnert查询
	 *
	 * @param unknown $sqlText        	
	 * @param unknown $params        	
	 */
	public function insert($sqlText, $params) {
		$sqlText = $this->getFullQueryText($sqlText, $params);
		
		$this->open();
		$this->dbControl->beginTransaction();
		$this->dbControl->query($sqlText);
		
		if (! $this->dbControl->getErrno()) {
			$this->dbControl->commit();
		} else {
			$this->dbControl->rollback();
			return - 1;
		}
		
		return $this->dbControl->getAffectedCount();
	}
	
	/**
	 * 执行update查询
	 *
	 * @param unknown $sqlText        	
	 * @param unknown $params        	
	 */
	public function update($sqlText, $params) {
		$sqlText = $this->getFullQueryText($sqlText, $params);
		
		$this->open();
		$this->dbControl->beginTransaction();
		$this->dbControl->query($sqlText);
		
		if (! $this->dbControl->getErrno()) {
			$this->dbControl->commit();
		} else {
			$this->dbControl->rollback();
			return - 1;
		}
		
		return $this->dbControl->getAffectedCount();
	}
	
	/**
	 * 执行delete查询
	 *
	 * @param unknown $sqlText        	
	 * @param unknown $params        	
	 */
	public function delete($sqlText, $params) {
		$sqlText = $this->getFullQueryText($sqlText, $params);
		
		$this->open();
		$this->dbControl->beginTransaction();
		$this->dbControl->query($sqlText);
		
		if (! $this->dbControl->getErrno()) {
			$this->dbControl->commit();
		} else {
			$this->dbControl->rollback();
			return - 1;
		}
		
		return $this->dbControl->getAffectedCount();
	}
	
	/**
	 * 执行事务
	 *
	 * @param unknown $sqlTextArray        	
	 * @param unknown $params        	
	 */
	public function proceedTransaction($sqlTextVariable, $params) {
		$this->open();
		$this->dbControl->beginTransaction();
		
		$errorCount = 0;
		
		if (is_array($sqlTextVariable) == true) {
			foreach ($sqlTextVariable as $sqlText) {
				$sqlText = $this->getFullQueryText($sqlText, $params);
				$this->dbControl->query($sqlText);
				
				if ($this->dbControl->getErrno() && $this->dbControl->getErrno() != 0) {
					$errorCount ++;
				}
			}
		} else {
			$sqlTextVariable = $this->getFullQueryText($sqlTextVariable, $params);
			//echo $sqlTextVariable;
			//return false;
			
			$this->dbControl->query($sqlTextVariable);
			
			//即使没有select也要执行，否则事务不同步无法提交
			$rowsCount = $this->dbControl->getRowsCount();
			
			if ($this->dbControl->getErrno() && $this->dbControl->getErrno() != 0) {
				$errorCount ++;
			}
		}
		
		if ($errorCount == 0) {
			$this->dbControl->commit();
		} else {
			$this->dbControl->rollback();
		}
		
		return ($errorCount == 0);
	}
	
	/**
	 * 执行统计，剔除结果集中的@变量输出
	 *
	 * @param unknown $sqlTextArray
	 * @param unknown $params
	 */
	public function proceedStatistic($sqlText, $params) {
		$sqlText = $this->getFullQueryText($sqlText, $params);
		
		$this->open();
		$this->dbControl->query($sqlText);
		
		if ($this->dbControl->getRowsCount() > 0) {
			$rows = $this->dbControl->getRows();
			$count = 0;
			
			foreach($rows as $key => $val) {
				if(is_array($val)) {
					foreach($val as $innerKey => $innerVal) {
						if(strpos($innerKey, '@') > -1) {
							unset($rows[$key]);
							break;
						}
					}
				}
			}
			return array_values($rows);
		} else {
			return null;
		}
	}
	
	/**
	 * 获取查询结果行数
	 */
	public function getSelectCount() {
		return $this->dbControl->getRowsCount();
	}
	
	/**
	 * 获取最后一条插入生成的主键id
	 */
	public function getLastInsertId() {
		return $this->dbControl->getLastInsertId();
	}
	
	/**
	 * 析构函数，自动关闭数据库，垃圾回收机制
	 */
	public function __destruct() {
		$this->dbControl->close();
	}
	
	/**
	 * 打开数据库
	 */
	private function open() {
		if ($isDbOpen == false) {
			$this->dbControl->open();
			$isDbOpen = true;
		}
	}
	
	/**
	 * 获取合并查询条件值之后的sql语句
	 *
	 * @param unknown $sqlText        	
	 * @param unknown $params        	
	 */
	public function getFullQueryText($sqlText, $params) {
		if (! empty($params)) {
			foreach ($params as $key => $value) {
				$sqlValue;
				if (is_bool($value)) {
					$sqlValue = ($value == true) ? 1 : 0;
				} else if (is_int($value) || is_long($value) || is_double($value)) {
					$sqlValue = $value;
				} else if (is_string($value)) {
					if (strlen($value) == 0) {
						$sqlValue = 'NULL';
					} else {
						$sqlValue = '\'' . $value . '\'';
					}
				} else if (is_null($value)) {
					$sqlValue = 'NULL';
				} else {
					$sqlValue = $value;
				}
				$sqlText = str_replace('${' . $key . '}', $sqlValue, $sqlText);
			}
			
			// 处理逻辑表达式
			$expStart = '<IF_';
			$expEnd = '</IF>';
			$offset = 0;
			$expOffset = 0;
			while (strpos($sqlText, $expStart, $expOffset) !== false) { // 查找下一个
				$expBeginBlockStartPos = strpos($sqlText, $expStart, $offset); // 逻辑表达式标签块起始位置
				$expFieldStartPos = $expBeginBlockStartPos + strlen($expStart); // 逻辑表达式中字段名起始位置
				$offset = $expFieldStartPos;
				$expOffset = $expBeginBlockStartPos;
				
				$expFieldEndPos = strpos($sqlText, '_', $offset);
				$expField = substr($sqlText, $expFieldStartPos, $expFieldEndPos - $expFieldStartPos);
				
				$expOpStartPos = $expFieldEndPos + 1;
				$offset = $expOpStartPos;
				$expOpEndPos = strpos($sqlText, '_', $offset);
				$expOp = substr($sqlText, $expOpStartPos, $expOpEndPos - $expOpStartPos);
				
				$expValStartPos = $expOpEndPos + 1;
				$offset = $expValStartPos;
				$expValEndPos = strpos($sqlText, '>', $offset);
				$expVal = substr($sqlText, $expValStartPos, $expValEndPos - $expValStartPos);
				
				// echo '[' . $expField . ']';
				// echo '[' . $expOp . ']';
				// echo '[' . $expVal . ']';
				
				$isMeet = false;
				
				// 表达式判断处理
				switch ($expOp) {
					case 'EQUALS' :
						if ($expVal == strval($params[$expField])) {
							$isMeet = true;
						}
						break;
					case 'IS' :
						if ($expVal == 'NULL' && is_null($params[$expField])) {
							$isMeet = true;
						} else if ($expVal == 'NOTNULL' && ! is_null($params[$expField])) {
							$isMeet = true;
						}
						break;
				}
				
				$expEndBlockStartPos = strpos($sqlText, $expEnd, $offset);
				$expEndBlockEndPos = $expEndBlockStartPos + strlen($expEnd);
				
				// 满足表达式条件，删除表达式标签，保留表达式内的语句
				if ($isMeet == true) {
					// 先删除尾部标签
					$sqlText = substr_replace($sqlText, '', $expEndBlockStartPos, $expEndBlockEndPos - $expEndBlockStartPos);
					// 再删除头部标签
					$sqlText = substr_replace($sqlText, '', $expBeginBlockStartPos, $expValEndPos + 1 - $expBeginBlockStartPos);
				} else { // 不满足，连同表达式标签与标签内的语句一同删除
					$sqlText = substr_replace($sqlText, '', $expBeginBlockStartPos, $expEndBlockEndPos - $expBeginBlockStartPos);
				}
				
				$offset = $expFieldStartPos;
				// break;
			}
			// echo '[' . $sqlText . ']';
		}
		
		return $this->handleSuffix($sqlText);
	}
	
	/**
	 * 处理sql语句结尾的逗号
	 *
	 * @param unknown $sqlText        	
	 */
	private function handleSuffix($sqlText) {
		// 处理insert语句中逗号结尾的情况
		$sqlText = preg_replace('/,\s*\)/', ')', $sqlText);
		// 处理update语句中逗号结尾的情况
		$sqlText = preg_replace('/,\s*WHERE/i', ' WHERE', $sqlText);
		return $sqlText;
	}
}

?>