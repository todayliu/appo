define(['config', 'md5', "mongo", "sha1"], function(config, md5, mongo, sha1) {
	'use strict';
	function get_shop_name(data) {
		return jsonpReq('addpshop', data, false, 5 * 1000);
	}
	
	function generateVerfyCode(obj,appid,token){
		if(!token)token='';
		obj = obj || {};
		obj.appid = appid;
		obj.timestamp = parseInt(new Date().getTime()/1000);
		var keys=[];
		for (var k in obj) {
			if (obj.hasOwnProperty(k)) {
				keys.push(k);
			}
		}
		
		keys = keys.sort();
		var len = keys.length;
		var str = ""
		var key = "";
		for(var i = 0;i<len;i++){
			key  = keys[i];
			str+=key+""+$t[key];
		};
		str += "" . token;
		var code = sha1(str);
		obj.code = code;
		return obj;	
	}
//		return $.ajax({
//			type: "POST",
//			url: url,
//			data: openOption,
//			dataType: "json",
//			timeout: 20000
//		});
//		var t = {
//			access_token: "OezXcEiiBSKSxW0eoylIeFq3ZiaZaAwqyhzMY2MiszfhDH8a2lJxTPUmTo-i9aw_tTIsxLMkyY4d8VELiHP6Bs2Q5e-u2kBWpsd_NSHuwoHRc-io6FycMfYCew8XYf67i2-ruqL31xQrY2AyR97pYQ",
//			expires_in: 7200,
//			refresh_token: "OezXcEiiBSKSxW0eoylIeFq3ZiaZaAwqyhzMY2MiszfhDH8a2lJxTPUmTo-i9aw_NoTslog4zudpdNShjEwkELkYkrKkVsPzrABddKD_6da9LCJgnRweCKKzOlF0iteluAEOgoWlTiBGry3068mizQ",
//			openid: "onkawv7qXad3_HVLuDV9TLVxYZPE",
//			scope: "snsapi_userinfo",
//			unionid: "oaeyBs1xXku9DC3JBBcGEvcdUlJM"
//		}

	//jsonp请求 mod:请求的模块 action:请求的接口 submitdata:请求的参数 cache:是否缓存 默认不缓存 expiretime：缓存时间 微秒 默认5分钟 callback 回调函数

	function jsonpReq(act, submitdata, cache, expiretime, storefunc) {
		cache = cache || false;
		expiretime = expiretime || 5 * 60000;
		storefunc = storefunc || function() {};
		submitdata = config.GetHeader(submitdata);
		var url = "http://192.168.1.109/app/i.php/product/"+act;
//		console.log(url);
		var md = md5.Hash(JSON.stringify(submitdata));
		if (cache) {
			var data = mongo.Get(act.toLocaleLowerCase() + md);
			if (data) {
				var dtd = $.Deferred();
				dtd.resolveWith({}, [data]);
				return dtd.promise();
			}
		}

		return $.ajax({
			type: "POST",
			url: url,
			data: submitdata,
			cache: cache,
			dataType: "jsonp",
			timeout: 20000,
			success: function(data) {
				//执行缓存信息
				if (cache) {
					mongo.Set(act.toLocaleLowerCase() + md, data, expiretime);
				}
				if (data.response === 304) {
					config.islogin = false;
					if (config.store) config.store.Delete("token");
					config.ClearHeader();
					return;
				}

			},
			error: function(xhr, status) {
				if ("error" === status) {

				}
			}
		});
	}

	return {
		generateVerfyCode:generateVerfyCode,
		GetShopname:get_shop_name
	}
});