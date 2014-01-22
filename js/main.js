require(['global', './common'], function(global, common) {
		common.baseUrl = global.baseUrl; 
		var widgets = [];
		/*
		 * Here I loop trough the global widget and I prepare a requireJs loading module.
		 */
		for(var k in global.widgets){
			// widgets[widgets.length] = "async!/widget/"+global.widgets[k].file+".js";
			widgets[widgets.length] = common.baseUrl+"views/"+global.widgets[k].file+".js";
			 // define( widget, function () { return ko; });
			
		}
		function capitalize(s){
		    return s[0].toUpperCase() + s.slice(1);
		}
		
		/*
		 * This is the loop function after the widgets are loaded
		 */
		
		require(common, widgets, function(){
	
		var $ = require("jquery");
		var React = require(["vendor/react"]);
		// var ReactMount = require(['vendor/ReactMount']);
		
			var views = [];
			for( var k in global.widgets ){
				var elem = global.widgets[k],
					vl = views.length,
					name = elem["name"],
					file = elem["file"],
					instance = capitalize(file)+'View';
					
				var scheme = $(document).find(name).data("json");
					
					require(common, [widgets[k]], function( Component ){
						React.renderComponent( Component(scheme), document.getElementById(name));
					});
					// view = require(widgets[k]);
// 					
				// // var ReactView = require(widgets[k]);
// 				
// 				
				// if(name === file){
					// views[vl] = new view({});
					// views[vl].initialize();
				// }else if( typeof view != "undefined" ){
					// console.log(view instanceof React);
					// // console.log(view instanceof Backbone);
					// var scheme = $(document).find(name).data("json");
					// views[vl] = new view(scheme);
					// console.log(typeof views[vl].initialize);
					// if(typeof views[vl].initialize == "undefined"){
						// // var comp = views[vl];
						// // console.log(views);
						// // var ReactView = require([widgets[k]]);
						// // ReactView = ReactView(null);
						// // new view();
						// // console.log(view( ));
						// console.log(scheme);
						// React.renderComponent( views[vl], document.getElementById(name));
					// }else{
						// views[vl].initialize();
					// }
				// }
			}
		});
});