<?php
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

//模版缓存类
class template_cache{
	//模版路径数组
	private static $template_path = array(
		'/common/part/',
		'/pbs/part/',
		'/template/'
	);
	//缓存
	private $cache;
	private $ver = 0;
	
	//构造函数
	public function __construct(){
		$this->reload_cache();
		echo $this->ver++;
	}
	
	//重载模版缓存，加载模版内容到内存
	public static function reload_cache(){
		$this->cache = array();
		
		foreach($this->template_path as $path){
			$handle = opendir(APPROOT . $path);
			while(false !== ($file = readdir($handle))){
				if ($file != "." && $file != ".." && strrpos($file, '.html') > 0){  //查找所有模版文件
					$filename = APPROOT . $path . $file;
					//echo $filename.'<br/>';
					$this->cache[$filename] = file_get_contents($filename);
					
				}
			}
			closedir($handle);
		}
	}
	
	//获取模版缓存内容
	public static function get_cache($filename){
		if(array_key_exists($filename, $this->cache)){  //存在于缓存中
			return $this->cache[$filename];
		}else{  //不在缓存中
			if(file_exists($filename)){
				$this->cache[$filename] = file_get_contents($filename);
				return $this->cache[$filename];
			}else{
				return "load file: While loading $handle, $filename does not exist or is empty.";
			}
		}
	}
}

?>