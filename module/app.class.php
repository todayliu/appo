<?php
include "/../libs/module.class.php";
class App extends Module{
	
	function adlist(){
		// $this->check();
		// $this->useCache();
		$id = $this->getArgs("id",ArgumentType::$NUMBER,100);
		$this->setResult("id",$id);
		$this->setResult("response",200);
		// $key = $this->getCache('some_key');
		// if($key == false){
		// 	$this->setCache("some_key","this is from memcache",0,30);
		// }
		return $this->result;
	}

	function index(){
		$a = $this->getArgs("a");
		$this->setResult("id",6);
		$this->setResult("response",200);
		echo '请看module/app.class.php' . '<br />';
		return $this->result;
	}

	// 析构函数
	function __destruct(){
	    // ...
	}
}