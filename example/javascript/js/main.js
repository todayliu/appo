require.config({
	urlArgs: "bust=130",
	paths: {
		"rgl": 'rgl.js?1.0.0',
        "regularjs": 'regular.min.js?0.31.0',
		"sha1":"include/sha1.js?0.1",
		"live":"live.class.js?0.1",
		'appAjax':'lib/appAjax.js?0.1',
		'encrypt':'include/encrypt.class.js?0.1',
	},
	waitSeconds: 30,
})



require(["router", "routerconf"], function(r, c) {
	var module = null;
			r.registerRoutes(c.config("themes/default/ctrl/"))
			 .on('routeload', 
			 	function(module, routeArguments) {
					var md  = new module(routeArguments);
					if(module && typeof(module.destroy) === "function"){
						module.DeadCircle();
					}
					md.LiveCircle();
					module = md;
				})
				.init();
});