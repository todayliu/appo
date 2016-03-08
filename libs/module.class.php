<?php

class Module {
	// 构造函数
	protected $data  	= 1;
	protected $cache 	= 2;
	protected $argments = array();
	protected $result 	= array("response"=>500,"responseText"=>'nil');
	function __construct($args){
	    /* Class initialization code */
	    $this->args = $args;
	    $this->config();
	    $this->useSql();
	}

	protected function useSql(){
		$config = require("/../libs/config.class.php");
		require_once("/../libs/mysql.class.php");
		$this->data = new Mysql($config["dbhost"], $config["dbuser"],$config["dbpass"],$config["dbname"],$config["prefix"], $config["charset"]);
	}
	protected function config(){

	}
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
	function check(){

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