<?php

class Module {
	// 构造函数
	protected $data  	= 1;
	protected $cache 	= false;
	protected $config   = array();
	protected $argments = array();
	protected $result 	= array("response"=>500,"responseText"=>'nil');
	protected $appid	= 0;
	protected $token    = '628';
	function __construct($args){
	    /* 拿到配制文件 */
	    $this->config = require("./libs/config.class.php");
	    //这里合并的是pathinfo
	    $this->argments = array_merge($this->argments,$args);
	    $this->config();
	    $this->useSql();
	}

	protected function useSql(){
		require("./libs/mysql.class.php");
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
	//接下来都是cache的简便方法
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

	/**
	 * 这里是拿客户端的一切参数
	 * 
	 */
	protected function getArgs($key,$type=null,$default=null){
		$value = $default;
		if(isset($this->argments[$key])) $value = $this->argments[$key];
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
	 * 这里都是对php POST的数据进行处理
	 * 
	 */ 
	protected function check(){
		$data  = file_get_contents("php://input");
		$pos   = strpos($data,',',0);
		$this->appid = intval(substr($data,0,$pos));
		$this->token = $this->getToken($this->appid);
		$data  = substr($data,$pos+1);
		$posts = json_decode(check::data_decode($data,$this->token),true);
		$code  = $posts["code"];
		unset($posts["code"]);
		if(!check::generate_verfy_code($posts,$this->token,$code)){
			$this->reject(304,'您的请求被拒绝');		
		}
		##合并到argment，方法$this->getArgs[""]能拿到参数
		$this->argments = array_merge($this->argments,$posts);
	}
	/**
	 * 失败或主动结束服务用$this->regject()
	 * $code : 数字码
	 * $text : 对应的文字描述
	 */ 
	protected function reject($code,$text){
		$data = array();
		$data["response"] 		= $code;
		$data["responseText"]	= $text;
		$r = check::generate_encode($data,$this->getToken($this->appid));
		echo check::data_encode(json_encode($r),$this->getToken($this->appid));
		die;
	}
	/**
	 * 正常结束返回 
	 */
	protected function resolve(){
		$r = check::generate_encode($this->result,$this->getToken($this->appid));
		echo check::data_encode(json_encode($r),$this->getToken($this->appid));
	}
	/**
	 * 这个方法根据自己项目情况自己修改，每一个用户的TOKEN肯定是不一样。
	 */
	protected function getToken($appid){
		return "628";
	}

	// 析构函数
	function __destruct(){
	    // ...
	}
	function __call($a,$b){
		//方法不存在
		$this->reject(404,'您请求的方法不存在');
	}
}