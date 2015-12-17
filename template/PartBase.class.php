<?php
/**
 * 部件基础类
 * @author sjimkros
 *
 */
class PartBase implements PartInterface {
	public $template;
	
	/**
	 * 部件基础类构造函数
	 */
	public function __construct() {
		$this->template = new SimpleTemplate(APPROOT . $this->getTemplateFile());
		$this->doDefault();
	}
	
	/**
	 * 显示替换内容
	 * 
	 * @see PartInterface::doAssign()
	 */
	public function doAssign() {
	}
	
	/**
	 * 默认动作
	 * 
	 * @see PartInterface::doDefault()
	 */
	public function doDefault() {
	}
	
	/**
	 * 输出页面内容
	 * 
	 * @see PartInterface::getContent()
	 */
	public function getContent() {
		$this->doAssign();
		return $this->template->getContent();
	}
	
	public function getTemplateFile(){
		return '';
	}
	
}

?>