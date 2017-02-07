<?php
/**
	驗證傳入參數
	(default pgsz=20)
 */
class JoblistCondition{

	private $data = array('pgsz' => 20);

	public function getAllCondition(){
		return $this->data;
	}

	public function __set($name, $value)
	{	
		switch ($name) {
	    case 'area':
	    	$arrValue = explode(",", $value);
	        if(is_array($arrValue)){
	        	foreach ($arrValue as $key=>$val){
	        		if (preg_match("/^6/i", $val)) {
	        			if(empty($this->data[$name])){
	        				$this->data[$name] = $val;
	        			}else{
	        				$this->data[$name] = $this->data[$name] .",". $val;
	        			}
	        			
	        		}
	        	}
	        }
	        break;
	    case 'jobcat':
			$arrValue = explode(",", $value);
	        if(is_array($arrValue)){
	        	foreach ($arrValue as $key=>$val){
	        		if (preg_match("/^2/i", $val)) {
	        			// var_dump($val);
	        			if(empty($this->data[$name])){
	        				$this->data[$name] = $val;
	        			}else{
	        				$this->data[$name] = $this->data[$name] .",". $val;
	        			}
	        			
	        		}
	        	}
	        }
	        break;
		case 'indcat':
			$arrValue = explode(",", $value);
	        if(is_array($arrValue)){
	        	foreach ($arrValue as $key=>$val){
	        		if (preg_match("/^1/i", $val)) {
	        			// var_dump($val);
	        			if(empty($this->data[$name])){
	        				$this->data[$name] = $val;
	        			}else{
	        				$this->data[$name] = $this->data[$name] .",". $val;
	        			}
	        			
	        		}
	        	}
	        }
	        break;
	    case 'keyword':	//中文英文皆可 濾除特殊符號
			if (!preg_match("/\'|\"|<|>|\*/", $value)) {
	        	$this->data[$name] = $value;
	        }
	       	break;
    	case 'asc':
    		$arr = array("0","1");
    		if(in_array($value, $arr)){
    			$this->data[$name] = $value;
    		}
    		break;
    	case 'order':
    		$arr = array("3","5");
    		if(in_array($value, $arr)){
    			$this->data[$name] = $value;
    		}
    		break;
    	case 'page':
    		if(is_numeric($value) && $value >= 0){
    			$this->data[$name] = $value;
    		}
    		break;
	    default:
	    	throw new Exception("its a error name of key.");
	    }
	}
	
	public function __get($name)
	{
		if(array_key_exists($name, $this->data)) {
			return $this->data[$name];
		}else{
			return null;
		}
	}
}