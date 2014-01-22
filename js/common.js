/**
 * This example make use of requireJS to provide a clean and simple way to split JavaScript class definitions
 * into separate files and avoid global namespace pollution.  http://requirejs.org/
 *
 * We start by defining the definition within the require block inside a function; this means that any
 * new variables / methods will not be added to the global namespace; requireJS simply requires us to return
 * a single value (function / Object) which represents this definition.  In our case, we will be returning
 * the Class' function.
 */
define(['require'], function (require) {
    // Forces the JavaScript engine into strict mode: http://tinyurl.com/2dondlh
    "use strict";
 
    var Config = {
	    baseUrl: '/js/',
	    
	    paths : {
	    	backbone: 'vendor/backbone',
	    	underscore: 'vendor/underscore',
	    	react: 'vendor/react',
	    	ReactMount: 'vendor/ReactMount',
        	JSXTransformer: 'vendor/jsx-traformer',
	    	
	        //create alias to plugins (not needed if plugins are on the baseUrl)
	        i18n: 'plugins/requirejs/i18n',
	        async: 'plugins/requirejs/async',
	        font: 'plugins/requirejs/font',
	        goog: 'plugins/requirejs/goog',
	        image: 'plugins/requirejs/image',
	        json: 'plugins/requirejs/json',
	        noext: 'plugins/requirejs/noext',
	        mdown: 'plugins/requirejs/mdown',
	        jsx: "plugins/requirejs/jsx",
	        propertyParser : 'plugins/requirejs/propertyParser',
	        markdownConverter : 'plugins/requirejs/Markdown.Converter'
	    },
	    // Remove to remove in production
	    urlArgs: "v=" +  (new Date()).getTime(),
	    shim: {
	    	
	    	'views/test':{
	    		deps: ["backbone"]
	    	},
	    	
	    	underscore: {
                    exports: '_'
            },
            
            backbone: {
                    deps: [
                            'underscore',
                            'jquery'
                    ],
                    exports: 'Backbone'
            },
	    	
	    	'plugins/bootstrap/bootstrap-affix':{
		          deps: ["jquery"]
	    	},
	    	'plugins/bootstrap/bootstrap-alert':{
		          deps: ["jquery"]
	    	},
	    	'plugins/bootstrap/bootstrap-button':{
		          deps: ["jquery"]
	    	},
	    	'plugins/bootstrap/bootstrap-carousel':{
		          deps: ["jquery"]
	    	},
	    	'plugins/bootstrap/bootstrap-collapse':{
		          deps: ["jquery"]
	    	},
	    	'plugins/bootstrap/bootstrap-dropdown':{
		          deps: ["jquery"]
	    	},
	    	'plugins/bootstrap/modal':{
		          deps: ["jquery"]
	    	},
	    	'plugins/bootstrap/bootstrap-popover':{
		          deps: ["jquery"]
	    	},
	    	'plugins/bootstrap/bootstrap-scrollspy':{
		          deps: ["jquery"]
	    	},
	    	'plugins/bootstrap/bootstrap-tab':{
		          deps: ["jquery"]
	    	},
	    	'plugins/bootstrap/bootstrap-tooltip':{
		          deps: ["jquery"]
	    	},
	    	'plugins/bootstrap/bootstrap-transition':{
		          deps: ["jquery"]
	    	},
	    	'plugins/bootstrap/bootstrap-typeahead':{
		          deps: ["jquery"]
	    	},
	    	'plugins/jquery/jquery.login':{
		          deps: ["jquery"]
	    	},
	    	
	    	'plugins/jquery/jquery.fullPage':{
		          deps: ["jquery", "plugins/jquery/jquery.slimscroll.min", "vendor/jquery.ui.core_effects.min"]
	    	},
	    	'plugins/jquery/jquery.slimscroll.min':{
		          deps: ["jquery"]
	    	},
	    	'plugins/jquery/jquery.scrollto':{
		          deps: ["jquery"]
	    	},
	    	
	    	
	    	'plugins/jquery/selectize.min':{
		          deps: ["jquery"]
	    	}
	    	
	    	
	    }
	};
	
	
	// requirejs.config(Config);
	var _jqueryUrl = Config.baseUrl+'vendor/jquery.js';
	define(
	    'jquery'
	    , [_jqueryUrl]
	    , function() {
	        // we just pick up global jQuery here. 
	        // If you want more than one version of jQuery in dom, read a more complicated solution discussed in
	        // "Registering jQuery As An Async-compatible Module" chapter of
	        // http://addyosmani.com/writing-modular-js/
	        return window.jQuery 
	    }
	)
 
    // As mentioned up top, requireJS needs us to return a value - in this files case, we will return
    // a reference to the constructor function.
    return Config;
});


