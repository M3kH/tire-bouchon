require(['global', './common'], function(global, common) {
	// console.log( 'views/' + global.view + '/' + global.module);
	// if(typeof global.init == "function"){
		// global.init();
		// console.log(global);
		// alert("Something!");
		common.baseUrl = global.baseUrl; 
		var widgets = Array();
		console.log(global);
		for(var k in global.widgets){
			// widgets[widgets.length] = "async!/widget/"+global.widgets[k].file+".js";
			widgets[widgets.length] = common.baseUrl+"widget/"+global.widgets[k].file+".js";
			 // define( widget, function () { return ko; });
			
		}
		require(common, widgets, function(){});
	// }
	
	
	require(common, ['jquery', 'views/' + global.view + '/' + global.module, 'vendor/mustache',], function($, Module) {
		// console.log(Module);
		$(function() {

			$(document).ready(function() {
    			// console.log("Document is ready");

				if ( typeof Module != 'undefined') {

        			// console.log("Module is not undefined.");
					if ( typeof Module.start == 'function') {

						Module.start();
            			// console.log("Module have init function.");

					}

				}

			});

		});

	});

});