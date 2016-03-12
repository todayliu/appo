<?php
include "/../libs/module.class.php";
class App extends Module{
	
	function adlist(){
		$this->check();
		// $this->useCache();
		$id = $this->getArgs("id",ArgumentType::$NUMBER,100);
		$a = $this->getArgs("a");
		$this->setResult("id",$id);
		$this->setResult("a",$a);
		$this->setResult("response",200);
		// $key = $this->getCache('some_key');
		// if($key == false){
		// 	$this->setCache("some_key","this is from memcache",0,30);
		// }
		//见libs/module.class.php
		$this->resolve();
	}

	function index(){
		$a = $this->getArgs("a");
		$this->setResult("id",6);
		$this->setResult("response",200);
		echo '请看module/app.class.php' . '<br />';
		echo '请运行example/javascript/index.html'. '<br />';
		$this->reject(200,'测试');
	}

	// 析构函数
	function __destruct(){
	    // ...
	}
}