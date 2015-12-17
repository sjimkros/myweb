<?php
include_once '_approot.php';
include_once APPROOT . '/lib/header.php';

class SimpleTemplate{
	public $filename;
	public $content;
	
	public function getContent(){
		return $this->content;
	}
	
	//构造函数
	public function __construct($filename){
		$this->filename = $filename;
		$this->content = file_get_contents($filename);
		if(empty($this->content)){
			echo "load file: While loading $handle, $filename does not exist or is empty.";
		}
	}
	
	//替换标志位内容
	public function assign($key, $value){
		$this->content = str_replace('${' . $key . '}', $value, $this->content);
	}
	
	//替换标志块内容，二维数组替换
	public function assignBlock2($block_name, $values){
		while(strpos($this->content, '${' . $block_name . '}') !== false){
			$block_start_pos = strpos($this->content, '{' . $block_name . '}');
	   		$block_start_length = strlen('${' . $block_name . '}');
	   		$block_sub_start_pos = $block_start_pos + $block_start_length;
	   		
	   		$block_end_pos = strpos($this->content, '${/' . $block_name . '}', $block_sub_start_pos);
	   		$block_end_length = $block_start_length + 1;
	   		$block_sub_length = $block_end_pos - $block_sub_start_pos;
	   		
	   		$block_replace_length = $block_end_pos + $block_end_length - $block_start_pos;
			if(is_array($values)){
	   			//若有输出行，删除该行
	   			$arr_last = count($values) - 1;
	   			$last = $values[$arr_last];
	   			if(strpos(key($last), '@') === false){}
	   			else{
	   				unset($values[$arr_last]);
	   			}
	
				//获取标志块之间的内容
	   			$str_block = substr($this->content, $block_sub_start_pos, $block_sub_length);
	   			    		
	    		$sub_replace = '';
	    		$block_replace = '';
	    		foreach($values as $row){  //每行
	    			$sub_replace = $str_block;
	    			while(list($key, $value) = each($row)){
	    			    //替换每行中内容
	    				//替换普通标记符
	    				$sub_replace = str_replace('${'.$key.'}', $value, $sub_replace);
	    				
	    				//替换条件标记符
	    				if(strpos($sub_replace, '${if_') !== false){
		    				$if_start_pos = strpos($sub_replace, '${if_' . $key . '}');
	    					$else_start_pos = strpos($sub_replace, '${else_' . $key . '}');
	    					
	    					if($if_start_pos === false || $else_start_pos == false){}
	    					else{
	    						$if_start_length = strlen('${if_' . $key . '}');
	    						$if_sub_start_pos = $if_start_pos + $if_start_length;
	   			
					   			$if_end_pos = strpos($sub_replace, '${/if_' . $key . '}', $if_start_pos);
					   			$if_end_length = $if_start_length + 1;
					   			$if_sub_length = $if_end_pos - $if_sub_start_pos;
					   			
					   			$if_replace_length = $if_end_pos + $if_end_length - $if_start_pos;
					   			
			    				$else_start_length = strlen('${else_' . $key . '}');
	    						$else_sub_start_pos = $else_start_pos + $else_start_length;
	   			
					   			$else_end_pos = strpos($sub_replace, '${/else_' . $key . '}', $else_start_pos);
					   			$else_end_length = $else_start_length + 1;
					   			$else_sub_length = $else_end_pos - $else_sub_start_pos;
					   			
					   			$ifelse_replace_length = $else_end_pos + $else_end_length - $if_start_pos;
	    						
	    						if($value > 0){  //保留if段
			    					$ifstr_block = substr($sub_replace, $if_sub_start_pos, $if_sub_length);
			    					$sub_replace = substr_replace($sub_replace, $ifstr_block, $if_start_pos, $ifelse_replace_length);
			    				}else{  //保留else段
			    					$elsestr_block = substr($sub_replace, $else_sub_start_pos, $else_sub_length);
			    					$sub_replace = substr_replace($sub_replace, $elsestr_block, $if_start_pos, $ifelse_replace_length);
			    				}
	    					}
	    				}
	    			}
	    			$block_replace .= $sub_replace;
	    		}
	    		$this->content = substr_replace($this->content, $block_replace, $block_start_pos, $block_replace_length);
	   		}else{
				$this->content = substr_replace($this->content, ' ', $block_start_pos, $block_replace_length);
			}
		}
	}
	
	//替换标志块内容，二维数组替换
	public static function assignBlock2S(&$content, $block_name, $values){
		$pattern = '${' . $block_name . '}.*${/' . $block_name . '}';
		if(is_array($values)){
   		//若有输出行，删除该行
   			$arr_last = count($values) - 1;
   			$last = $values[$arr_last];
   			if(strpos(key($last), '@') === false){}
   			else{
   				unset($values[$arr_last]);
   			}
   			
   			ereg($pattern, $content, $regs);
			//获取标志块之间的内容
   			$str_block = substr($regs[0], 2 + strlen($block_name), - (strlen($block_name) + 3));
   			    		
    		$sub_replace = '';
    		$block_replace = '';
    		foreach($values as $row){  //每行
    			$sub_replace = $str_block;
    			while(list($key, $value) = each($row)){
    				//替换每行中内容
    				//替换普通标记符
    				$sub_replace = str_replace('${'.$key.'}', $value, $sub_replace);
    				
    				//替换条件标记符
    				if(strpos($sub_replace, '${if_') !== false){
	    				$ifpattern = '${if_' . $key . '}.*${/if_' . $key . '}';
	    				$elsepattern = '${else_' . $key . '}.*${/else_' . $key . '}';
	    				if(ereg($ifpattern, $sub_replace, $ifregs) != false
	    					&& ereg($elsepattern, $sub_replace, $elseregs) != false){  //找到条件标记符
		    				if($value > 0){  //保留if段
		    					$ifstr_block = substr($ifregs[0], 5 + strlen($key), - (strlen($key) + 6));
		    					$sub_replace = ereg_replace($ifpattern, $ifstr_block, $sub_replace);
		    					$sub_replace = ereg_replace($elsepattern, '', $sub_replace);
		    				}else{  //保留else段
		    					$elsestr_block = substr($elseregs[0], 7 + strlen($key), - (strlen($key) + 8));
		    					$sub_replace = ereg_replace($ifpattern, '', $sub_replace);
		    					$sub_replace = ereg_replace($elsepattern, $elsestr_block, $sub_replace);				
		    				}
	    				}
    				}
    			}
    			$block_replace .= $sub_replace;
    		}
    		$content = ereg_replace($pattern, $block_replace, $content);
   		}else{
			$content = ereg_replace($pattern, '', $content);
		}
	}
	
	//输出结果
	public function show(){
		echo $this->content;
	}
}

?>