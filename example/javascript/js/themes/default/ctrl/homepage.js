"use strict";
define(['appAjax','encrypt','live'], function(appAjax,encrypt,live) {	
	appAjax({
		url:"http://appo.com/app/adlist",
		type:"POST",
		debugLog:true,
		timeout:3000,
		data:{a:123,b:567},
		decodeFunc:encrypt.decode("12345a"),
	}).done(function(data){
		console.log(data);
		return true;
	}).fail(function(data){
		console.log(data);
		return false;
	}).always(function(bool){
		console.log(bool);
	})
	
	return live.extend({
		init: function(args) {
			this.supr(args);		},
		destroy: function() {
			this.supr();
		}

	})


})