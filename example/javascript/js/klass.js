"use strict";
define(function(){
	var klass = function(option) {
			return klass.extend.call(function() {}, option)
		}
		/**
		 * 如果是方法函数，给函数加一个关执行父类的方法
		 * 感谢klass的作者 klass: a classical JS OOP façade 
		 * https://github.com/ded/klass
		 */
	klass.__wrap = function(k, fn, supr) {
		return function() {
			var tmp = this.supr
			this.supr = supr["prototype"][k]
			var undef = {}.fabricatedUndefined
			var ret = undef
			try {
				ret = fn.apply(this, arguments)
			} finally {
				this.supr = tmp
			}
			return ret
		}
	}
	klass.__isfunc=function(fn){
		return typeof fn === "function";
	}
	klass.extend = function(option) {
		;var supr = this
		,	 tsupr = Object.create(this.prototype)
		,    fn = function() {}
		,    option = option || {};

		;var sup = {
			destroy:function(){}
		   ,abstract:true
		   ,init:function(){}
		}
		/**
		 * 子类有父类方法的关键。
		 */
		;for (var index in tsupr) {
			var charcode = index.charCodeAt(0);
			//只有第一个字母大字的才可以被继承
			if (charcode >= 65 && charcode <= 90) {
				;sup[index] = tsupr[index]
			}
		};
		//析构函数
		if(typeof tsupr["destroy"] !== "undefined"){
			sup["destroy"] 	= tsupr["destroy"];
		}
		if(typeof tsupr["init"] !== "undefined"){
			sup["init"] 	= tsupr["init"];
		}
		if(typeof tsupr["abstract"] !== "undefined"){
			sup["abstract"] = tsupr["abstract"];
		}
		;for (var index in option) {
			var fo = option[index];
			if (klass.__isfunc(fo)) {
				/**
				 * 如果是方法函数，给函数加一个关执行父类的方法
				 * 感谢klass的作者 klass: a classical JS OOP façade 
				 * https://github.com/ded/klass
				 */
				if(typeof sup[index] != "undefined"){
					sup[index] = klass.__wrap(index, fo, supr)
					continue;
				}
			} 
			sup[index] = fo;
			
		}
		//模拟构造函数，如果有声明init函数，TA就是了。
		;if (typeof sup.init === "function") {
			fn = sup.init;
		}
		;fn.extend = klass.extend;
		;fn.test = function(implement) {
			/**
			 * 
			 */
			for (var key in implement) {
				;var charcode = key.charCodeAt(0)
				//只有第一个字母大写才是正宗的接口
				;if (charcode >= 65 && charcode <= 90) {
					if (typeof implement[key] === typeof this['prototype'][key]) {
						continue;
					} else {
						throw "亲，你需要" + key + '，类型得还得是：' + typeof implement[key]
					}

				}
			}
			return fn
		}
		;fn.prototype = sup
		return fn
	}
	return klass;
})
