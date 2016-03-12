"use strict";
define(['appAjax','encrypt','live'], function(appAjax,encrypt,live) {	
	appAjax({
		url:"http://appo.com/app/adlists",
		type:"POST",
		debugLog:true,
		timeout:3000,
		data:{a:1,b:2},
		//数据接收后，用什么方法对数据加密。比如：JSONP/json/XML
		decodeFunc:encrypt.decode("628"),
		//数据提交前，用什么方法对数据加密，比如：把对象转成key=value&key1=value1;
		encodeFunc:encrypt.encode("0",'628'),
	}).done(function(data){
		console.log(data);
		return true;
	}).done(function(data){
		//这里的data是上一个done的return的值
		console.log(data);
		return true;
	}).fail(function(data){
		console.log(data);
		return false;
	}).always(function(bool){
		//这里的data是上一个最后一个done/fail的return的值
		console.log(bool);
	})
	
	return live.extend({
		init: function(args) {
			this.supr(args);
		},
		destroy: function() {
			this.supr();
		}

	})


})