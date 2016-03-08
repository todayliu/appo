<?php
include_once "/../libs/module.class.php";
class Ad extends Module{
	
	function adlist(){
		$this->check();
		$id = $this->getArgs("id",ArgumentType::$NUMBER,100);
		$this->setResult("id",$id);
		$this->setResult("response",200);

// $memcachehost = '192.168.6.191';
// $memcacheport = 11211;
// $memcachelife = 60;
// $memcache = new Memcache;
// $memcache->connect($memcachehost,$memcacheport) or die ("Could not connect");
		
		return $this->result;
	}

	function index(){
		$a = $this->getArgs("a");
		$this->setResult("id",6);
		$this->setResult("response",200);
		return $this->result;
	}

	// 析构函数
	function __destruct(){
	    // ...
	}
}