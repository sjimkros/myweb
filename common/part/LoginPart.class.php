<?php 
// 登陆部件
class LoginPart extends PartBase {
	
	
	public $user_name;
	
	public function doAssign() {
		$this->template->assign('user_name', $this->user_name);
	}
	
	public function doDefault() {
		
	}
	
	public function getTemplateFile(){
		return '/common/part/LoginPart.html';
	}
}

?>