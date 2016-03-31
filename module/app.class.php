<?php
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
		// $this->reject(200,'测试');
		$this->data->insert("ecs_table",array('a'=>1,'b'=>2));
		$this->data->insert("ecs_table",'`a`,`b`',"1,1");
		$this->data->insert("ecs_table",'`a`,`b`',array(0=>1,1=>2));
		$this->data->insert("ecs_table",'`a`,`b`',array(0=>array(0=>1,1=>2),1=>array(0=>3,1=>4)));
		var_dump(1);
	}

	// 析构函数
	function __destruct(){
	    // ...
	}
}