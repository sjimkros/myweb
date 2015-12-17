<?php

/**
 * 页面基础类
 *
 * @author sjimkros
 *        
 */
class PageBase {
	
	/**
	 * 排版布局：3列，比例为3:6:3
	 */
	public static $LAYOUT_COLUMN3 = 'LAYOUT_COLUMN3';
	
	/**
	 * 排版布局 3列，比例为
	 */
	public static $LAYOUT_COLUMN3_NARROWLEFT = 'LAYOUT_COLUMN3_NARROWLEFT';
	
	/**
	 * 排版布局 2列，比例为5：5
	 */
	public static $LAYOUT_COLUMN2_EQUAL = 'LAYOUT_COLUMN2_EQUAL';
	
	/**
	 * 排版布局 2列，比例为3：9
	 */
	public static $LAYOUT_COLUMN2_NARROWLEFT = 'LAYOUT_COLUMN2_NARROWLEFT';
	
	/**
	 * 排版布局 1列
	 */
	public static $LAYOUT_COLUMN1 = 'LAYOUT_COLUMN1';
	
	/**
	 * 排版布局 1列，宽度更窄
	 */
	public static $LAYOUT_COLUMN1_NARROW = 'LAYOUT_COLUMN1_NARROW';
	public static $BODY_LEF = 'BODY_LEFT';
	public static $BODY_CENTER = 'BODY_CENTER';
	public static $BODY_RIGHT = 'BODY_RIGHT';
	public $template;
	public $pageTitle;
	public $pageSubTitle;
	public $navPart;
	public $activeMenuId;
	public $bodyLeft;
	public $bodyCenter;
	public $bodyRight;
	public $bodyLeft_class;
	public $bodyCenterClass;
	public $bodyRightClass;
	public $parts;
	public $css;
	public $script;
	
	/**
	 * 页面基础类构造函数
	 *
	 * @param $pageFilename html页面文件名        	
	 * @param $pageTitle 页面标题        	
	 * @param $bodyLayout 页面排版        	
	 */
	public function __construct($pageFilename, $pageTitle, $bodyLayout) {
		$this->template = new SimpleTemplate($pageFilename);
		$this->pageTitle = $pageTitle;
		
		$this->navPart = '';
		
		$this->css = '';
		$this->script = '';
		
		$this->bodyLeft = '';
		$this->bodyCenter = '';
		$this->bodyRight = '';
		
		$this->setLayout($bodyLayout);
	}
	
	/**
	 * 输出页面内容
	 */
	public function show() {
		$this->doAssign();
		$this->template->show();
	}
	
	/**
	 * 设置页面副标题
	 * 
	 * @param unknown $pageSubTitle        	
	 */
	public function setPgeSubTitle($pageSubTitle) {
		$this->pageSubTitle = $pageSubTitle;
	}
	
	/**
	 * 添加CSS文件
	 */
	public function addCss($targets) {
		foreach ($targets as $value) {
			$this->css .= '<link href="' . DOMAIN_NAME . $value . '" rel="stylesheet" type="text/css">';
		}
	}
	
	/**
	 * 添加Javascript文件
	 */
	public function addScript($targets) {
		foreach ($targets as $value) {
			$this->script .= '<script src="' . DOMAIN_NAME . $value . '" language="javascript"></script>';
		}
	}
	
	/**
	 * 添加部件
	 *
	 * @param unknown $position        	
	 * @param part_interface $part        	
	 */
	public function addPart($position, PartInterface $part) {
		switch ($position) {
			case self::$BODY_LEF :
				$this->bodyLeft .= $part->getContent();
				break;
			case self::$BODY_CENTER :
				$this->bodyCenter .= $part->getContent();
				break;
			case self::$BODY_RIGHT :
				$this->bodyRight .= $part->getContent();
				break;
			default :
				break;
		}
	}
	
	/**
	 * 设置菜单部件
	 */
	public function setNavPart(PartInterface $part) {
		$this->navPart = $part->getContent();
	}
	
	/**
	 * 设置菜单激活
	 */
	public function setActiveMenuId($activeMenuId) {
		$this->activeMenuId = $activeMenuId;
	}
	
	/**
	 * 替换内容
	 */
	public function doAssign() {
		$this->template->assign('bodyCenter', $this->bodyCenter);
		$this->template->assign('bodyLeft', $this->bodyLeft);
		$this->template->assign('bodyRight', $this->bodyRight);
		
		$this->template->assign('pageTitle', $this->pageTitle);
		$this->template->assign('pageSubTitle', $this->pageSubTitle);
		$this->template->assign('navPart', $this->navPart);
		$this->template->assign('activeMenuId', $this->activeMenuId);
		
		$this->template->assign('css', $this->css);
		$this->template->assign('script', $this->script);
		
		$this->template->assign('bodyLeftClass', $this->bodyLeftClass);
		$this->template->assign('bodyCenterClass', $this->bodyCenterClass);
		$this->template->assign('bodyRightClass', $this->bodyRightClass);
		
		// 全局替换
		$this->template->assign('domainUrl', DOMAIN_NAME);
	}
	
	/**
	 * 设置页面排版
	 *
	 * @param unknown $body_layout        	
	 */
	private function setLayout($body_layout) {
		switch ($body_layout) {
			case self::$LAYOUT_COLUMN1 :
				$this->bodyLeftClass = 'col-md-3';
				$this->bodyCenterClass = 'col-md-6';
				$this->bodyRightClass = 'col-md-3';
				break;
			
			case self::$LAYOUT_COLUMN1_NARROW :
				$this->bodyLeftClass = 'col-md-4';
				$this->bodyCenterClass = 'col-md-4';
				$this->bodyRightClass = 'col-md-4';
				break;
			
			case self::$LAYOUT_COLUMN2_NARROWLEFT :
				$this->bodyLeftClass = 'col-md-3';
				$this->bodyCenterClass = 'col-md-9';
				$this->bodyRightClass = 'hidden';
				break;
			
			case self::$LAYOUT_COLUMN3 :
				$this->bodyLeftClass = 'col-md-3';
				$this->bodyCenterClass = 'col-md-6';
				$this->bodyRightClass = 'col-md-3';
				break;
			
			default :
				break;
		}
	}
}

?>