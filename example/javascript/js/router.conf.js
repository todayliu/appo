define([], function() {
	'use strict';
	return {
		config: function(path) {
			path = path || "";
			return {
				// change:{
				// 	path: '/change',
				// 	moduleId: path + 'change'
				// },
				notFound: {
					path: '*',
					moduleId: path + 'homepage'
				}
				
			}
		}
	}
});