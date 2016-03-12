'use strict'
define(['js/lib/deferred.class.js'], function(Deferred) {
	function initXMLhttp() {
		var xmlhttp;
		if (window.XMLHttpRequest) {
			//code for IE7,firefox chrome and above
			xmlhttp = new XMLHttpRequest();
		} else {
			//code for Internet Explorer
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
		return xmlhttp;
	}

	function appAjax(config) {
		/*Config Structure
		        url:"reqesting URL"
		        type:"GET or POST"
		        timeout: (OPTIONAL) 20000,
		        encodeFunc:(OPTIONAL)
		        decodeFunc:(OPTION)
		        method: "(OPTIONAL) True for async and False for Non-async | By default its Async"
		        debugLog: "(OPTIONAL)To display Debug Logs | By default it is false"
		        data: "(OPTIONAL) another Nested Object which should contains reqested Properties in form of Object Properties"
		        success: "(OPTIONAL) Callback function to process after response | function(data,status)"
		*/
		var timer = null;
		var defer = new Deferred();
		if (!config.url) {
			if (config.debugLog == true)
				console.log("No Url!");
			return;
		}
		if (!config.type) {
			if (config.debugLog == true)
				console.log("No Default type (GET/POST) given!");
			return;
		}
		if (!config.method) {
			config.method = true;
		}
		var timeout = parseInt(config.timeout); 
		config.timeout = timeout>0 && timeout<60000?timeout:20000;
		if (!config.debugLog) {
			config.debugLog = false;
		}
		var xmlhttp = initXMLhttp();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4) {
				if (xmlhttp.status == 200) {
					if(timer != null){
						clearTimeout(timer);
					}
					var responseText =typeof config.decodeFunc == "function" ? config.decodeFunc(xmlhttp.responseText) : xmlhttp.responseText;
					if (config.success) {
						config.success(responseText);
					}
					defer.resolve(responseText);
					if (config.debugLog == true) {
						console.log("SuccessResponse");
					}

					if (config.debugLog == true) {
						console.log('responseText:' + responseText);
					}
				}else{
					if(timer != null){
						clearTimeout(timer);
					}
					defer.reject(xmlhttp.status);	
				}
			} else {
				if(timer != null){
						clearTimeout(timer);
					}
				if (config.debugLog == true) {
					console.log("FailureResponse --> State:" + xmlhttp.readyState + "Status:" + xmlhttp.status);
				}
			}
		}
		var sendString = [],
			sendData = config.data;
		if (typeof sendData === "string") {
			var tmpArr = String.prototype.split.call(sendData, '&');
			for (var i = 0, j = tmpArr.length; i < j; i++) {
				var datum = tmpArr[i].split('=');
				sendString.push(encodeURIComponent(datum[0]) + "=" + encodeURIComponent(datum[1]));
			}
		} else if (typeof sendData === 'object' && !(sendData instanceof String || (FormData && sendData instanceof FormData))) {
			for (var k in sendData) {
				var datum = sendData[k];
				if (Object.prototype.toString.call(datum) == "[object Array]") {
					for (var i = 0, j = datum.length; i < j; i++) {
						sendString.push(encodeURIComponent(k) + "[]=" + encodeURIComponent(datum[i]));
					}
				} else {
					sendString.push(encodeURIComponent(k) + "=" + encodeURIComponent(datum));
				}
			}
		}
		sendString = sendString.join('&');
		if (config.type == "GET") {
			xmlhttp.open("GET", config.url + "?" + sendString, config.method);
			xmlhttp.send();
			if (config.debugLog == true)
				console.log("GET fired at:" + config.url + "?" + sendString);
		}
		if (config.type == "POST") {
			xmlhttp.open("POST", config.url, config.method);
			xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xmlhttp.send(sendString);
			if (config.debugLog == true)
				console.log("POST fired at:" + config.url + " || Data:" + sendString);
		}
		if(timer == null)timer = setTimeout(function(){
			xmlhttp.abort();
			clearTimeout(timer);
			timer = null;
		},config.timeout);
		return defer;
	}

	return appAjax;
})