<?php

class Module {
	// 构造函数
	protected $data  	= 1;
	protected $cache 	= false;
	protected $config   = array();
	protected $argments = array();
	protected $result 	= array("response"=>500,"responseText"=>'nil');
	function __construct($args){
	    /* Class initialization code */
	    $this->config = require("/../libs/config.class.php");
	    $this->args = $args;
	    $this->config();
	    $this->useSql();
	}
	
	protected function useSql(){
		require("/../libs/mysql.class.php");
		$this->data = new Mysql($this->config["dbhost"], $this->config["dbuser"],$this->config["dbpass"],$this->config["dbname"],$this->config["prefix"], $this->config["charset"]);
	}
	protected function config(){

	}
	//###########缓存常用方法############
	/**
	 * 开启缓存
	 */ 
	protected function useCache(){
		if(!$this->cache){
			$cachehost 		= $this->config["cachehost"];
			$cache 			= explode(":",$cachehost);
			$memcachehost 	= $cache[0];
			$memcacheport 	= $cache[1];
			$this->cache 	= new Memcache();
			$this->cache->connect($memcachehost,$memcacheport) or die ("Could not connect");
		}
	}
	protected function getCache($key){
		if($this->cache){
			return $this->cache->get($key);
		}
		return false;
	}
	protected function setCache($key,$value,$comp=0,$expire=3600){
		if($this->cache){
			return $this->cache->set($key,$value,false,30);
		}
		return false;
	}
	protected function replaceCache($key,$value,$comp,$expire){
		if($this->cache){
			return $this->cache->set($key,$value,false,30);
		}
		return false;
	}
	protected function deleteCache($key){
		if($this->cache){
			return $this->cache->delete($key);
		}
		return false;
	}
	protected function closeCache($key){
		if($this->cache){
			return $this->cache->close();
		}
		return false;
	}
	//###########缓存常用方法############
	protected function setResult($key,$value){
		$this->result[trim($key)] = $value;
	}

	protected function getArgs($key,$type=null,$default=null){
		$value = $default;
		if(isset($this->args[$key])) $value = $this->args[$key];
		if(isset($_GET[$key]))  	 $value = $_GET[$key];
		if(isset($_POST[$key])) 	 $value = $_POST[$key];
		if($type === ArgumentType::$NUMBER){
				if(Check::is_number($value)){
					return $value;
				}else{
					return $default;
				}
			}elseif($type == ArgumentType::$CHAR){
				if(Check::is_text($value)){
					return $value;
				}else{
					return $default;
				}
			}elseif($type == ArgumentType::$SQL){
				if(Argument::addslashes_deep($value)){
					return $value;
				}else{
					return $default;
				}
			}else{
				return $value;
			}
	}

	/**
	 * appid:1;
	 * code:333333333;
	 * 去掉code与appid(唯一确定用户的ID)
	 * 
	 */ 
	function check(){
		if(isset($_POST["params"])){
			$verfy  = $_POST["params"];
			$arr 	= json_decode($verfy,true);
			$appid 	= isset($arr["appid"])?$arr["appid"]:"";
			$code  	= isset($arr["code"])?$arr["code"]:"";
			unset($arr["code"]);
			$token = $appid;
			if(!$this->generateVerfyCode($arr,$token,$code)){
				die('{"response":304,"responseText":"您的请求被拒绝"}');	
			}

		}else{
			die('{"response":304,"responseText":"您的请求被拒绝"}');
		}

	}

	function generateVerfyCode($param,$token,$code){  
        $params_data = "";
        ksort($param);
        foreach( $param as $key=>$value ){  
            $params_data=$params_data.$key.$value;  
        }
        $params_data = $params_data.$token;
        return sha1($params_data) == $code;  
}

	// 析构函数
	function __destruct(){
	    // ...
	}
	function __call($a,$b){
		//方法不存在
		$this->setResult("responseText",$a . '方法不存在');
		echo json_encode($this->result);
	}
}