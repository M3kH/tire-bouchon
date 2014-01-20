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
			var views = [];
			for( var k in global.widgets ){
				var elem = global.widgets[k],
					vl = views.length,
					name = elem["name"],
					file = elem["file"],
					instance = capitalize(file)+'View',
					view = require(widgets[k]);
				
				
				if(name === file){
					views[vl] = new view({});
					views[vl].initialize();
				}else{
					views[vl] = new view({el:name});
					console.log(views[vl]);
					views[vl].initialize();
				}
			}
		});
});