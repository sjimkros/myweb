<?php  //部件接口

/**
 * 部件定义接口
 * @author sjimkros
 *
 */
interface PartInterface{
	
	/**
	 * 替换内容
	 */
	public function doAssign();
	
	/**
	 * 默认动作
	 */
	public function doDefault();
	
	/**
	 * 获取页面内容
	 */
	public function getContent();
	
	public function getTemplateFile();
}

?>