
var dnode = require('dnode'),
	requirejs = require("requirejs");
	// require('amdefine/intercept');
	requirejs.config({
		baseUrl: "./../../js/",
		nodeRequire: require
	});
	
	
if (typeof define !== 'function') { var define = require('amdefine')(module) }
	
var server = dnode({
    zing: function (c, d, cb) {
    	// cb(n * 100);
    	console.log(c);
    	console.log(d);
    	console.log(typeof d);
		
    	if( typeof c != "undefined" && c !== null && typeof d != "undefined" && d != null ){
    		var main = "./../../js/",
    			component = c,
    			data = d,
    			React = require("react");
    			
    			
    		var comp = requirejs("views/"+component);
    		var Backbone = require("backbone");
			var Model = Backbone.Model.extend({
				map: function( cb ){
				        
			        var self = this, result = {};
					for( var k in this.attributes ){
						// console.log(this.attributes[k]);
						cb(this.attributes[k]);
			            result[k] = cb(this.attributes[k]);
					}
					return result;
				}
			});
			var Collection = Backbone.Collection.extend({ model: Model});
    			
    		var data = React.renderComponentToString(comp({ data: new Collection(data)}), cb);
		  // json_encode($data)
    			
    	}else{
    		cb("Sorry something go wrong!");
    	}
    	// // $react[] = "/** @jsx React.DOM */";
		// $react[] = "Object.defineProperty(Object.prototype, 'map', {
				    // value: function(f, ctx) {
				        // ctx = ctx || this;
				        // var self = this, result = {};
				        // Object.keys(self).forEach(function(v) {
				            // result[v] = f.call(ctx, self[v], v, self); 
				        // });
				        // return result;
				    // }
				// });";
		// $react[] = "var console = {warn: function(){}, error: print}";
		// $react[] = "var global = {}";
		// // $react[] = "window = this;window.location={href:\"file://\",port:\"\"};document={getElementsByTagName:function(){return []}};";
		// // $react[] = file_get_contents(MAIN.'/js/domnode.js');
		// // $react[] = file_get_contents(MAIN.'/js/htmlelts.js');
		// // $react[] = file_get_contents(MAIN.'/js/domcore.js');
		// // $react[] = file_get_contents(MAIN.'/js/dom.js');
		// // $react[] = file_get_contents(MAIN.'/js/require-server.js');
		// // $react[] = "require.config({
	        // // baseUrl: \"http://".URL."/js/\"
	    // // });";
		// $react[] = file_get_contents(MAIN.'/js/vendor/react.js');
		// $react[] = file_get_contents(MAIN.'/js/vendor/jsx-trasformer.js');
		// $react[] = "var React = global.React";
		// // my custom components
		// $react[] = file_get_contents(MAIN.'/js/views/'.$component.'.js');
// 		
		// $react[] = sprintf(
		  // "React.renderComponentToString(".$component."({data: %s}), print)",
		  // json_encode($data));
    }
});
server.listen(7070);