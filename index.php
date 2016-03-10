<?php
header("Content-type: text/html; charset=utf-8");
define('MODULE_DIR', './module/');
// $config = require("./libs/config.class.php");
require("./libs/argument.class.php");
require("./libs/check.class.php");

$pathinfo=$_SERVER["PATH_INFO"];   //计算出index.php后面的字段 index.php/c/methon/id/3
$pathinfo=trim($pathinfo,'/');
//这里需要对$pathinfo进行过滤处理。
$ctrl=explode('/',$pathinfo);
$ctrl_num=count($ctrl);
//没有任何参数的情况下
if($ctrl_num == 0 || ($ctrl_num==1 && $ctrl[0]=='')){
    $ctrl = array("index","index");
    $ctrl_num=2;
}
//当有奇数个参数时，系统断定第一个为模块名称，省略第二个参数index
if($ctrl_num%2==1){
    $ctrl = array_merge(array(array_shift($ctrl),"index"),$ctrl);
    $ctrl_num++;
}
//index.php/控制/方法/参数1key/参数1value/参数2key/参数2value/
$module_name=$ctrl[0];
$method_name=$ctrl[1];
$args = array();
for($i=2;$i < $ctrl_num;$i=$i+2){
    $args=array_merge($args,array(strtolower($ctrl[$i])=>$ctrl[$i+1]));
}
$module_file=MODULE_DIR.$module_name.'.class.php';
$module_name = ucfirst($module_name);

if(file_exists($module_file)){
    include($module_file);
    $obj_module=new $module_name($args);    //实例化模块m
    if(is_callable(array($obj_module, $method_name))){    //该方法是否能被调用
        $res=$obj_module->$method_name();    //执行a方法,并把key-value参数的数组传过去''
        // if($config["result_type"] == "json"){
           echo check::data_encode(json_encode($res),'12345');
        // }
    }else{
        echo json_encode(array("response"=>500,"responseText"=>'该方法不能被调用'));
    	exit;
    }
}else{
	echo json_encode(array("response"=>500,"responseText"=>'模块文件不存在'));
    exit;
}

?>
