require(["vendor/mustache"], function(Mustache) {
	
	/*!
	 * Ideabile - Tablefy
	 * Author: @cowboy
	 * Further changes: @addyosmani
	 * Licensed under the MIT license
	 * TODO: Prime cose da fare sono, fill the table con json obj, add action to dbclick.
	 * TODO: functions for fill the table
	 * TODO: functions for set the titles
	 * TODO: functions for hide the titles
	 * TODO: functions for pagination
	 * TODO: callback set for get json obj
	 * TODO: function to set the mapping of the json obj to the table
	 * TODO: functions to sort the table
	 * TODO: functions to search live api/table
	 * TODO: functions to show actions in the row (when is over the row hide the content show buttons)
	 * TODO: functions and callback to multiple selection
	 * TODO: enabling multiple selection (checkbox)
	 * TODO: action to double click (callback)
	 */
	
	
	;(function ( $, window, document, undefined ) {
	
	    $.fn.tablefly = function ( options ) {
	
			// console.log("Go inside function, plugin loaded!");
	        options = $.extend( true, $.fn.tablefly.options, options );
	
	        return this.each(function () {
	
	            var elem = $(this),
	            	loginButton = elem.find(".e-login"),
	            	sigupButton = elem.find(".e-sigup");
	            	
	            	options.elem = elem;
	            	
	            	$('.e-login-modal').on('click', function(){
	            		// console.log("Enter");
	            		$.fn.iLogin.options.cleanAll( options );
						elem.find('.registration').hide();
	            	});
	            	
	            	if( typeof options.ctrls != 'undefined' ){
	            		if( typeof options.data.ctrls.row != 'undefined' && options.showCtrlsRow === 1){
	            			options = $.fn.tablefy.options.setCtrlsRows(options, options.data.ctrls.rows);
	            		}
	            	}
	            	
	            	if( typeof options.data != 'undefined' ){
	            		if( typeof options.data.thead != 'undefined' && options.showTitles === 1 ){
	            			options = $.fn.tablefy.options.setTitles(options, options.data.titles);
	            		}
	            		if( typeof options.data.rows != 'undefined' ){
	            			options = $fn.tablefy.options.setRows(options, options.data.rows);
	            		}
	            		options.template = $.fn.tablefly.options.getTemplate(options);
	            		var html =  $.fn.tablefly.options.renderRows(options, options.data);
            			elem.find("tbody").append(html);
	            	}
	            	
	        });
	    };
	
	    $.fn.tablefly.options = {
	    	
			elem: '',
			buttons: {},
			
			showTitles: 1,
			showCtrls: 1,
			showCtrlsRow: 1,
			
			
			setTitles: function(params, titles){
				
			},
			
			setRows: function(params, rows){
				
			},
			
			setCtrlsRows: function(params, ctrls){
				
			},
			
			renderRows: function(params, data){
				// console.log("Render!");
				
				var tmpl = params.template;
					html = '';
					for(var i=0; i < data.length; i++){
						// console.log(tmpl, data[i]);
						html += Mustache.render(tmpl, data[i]);
					}
				return html;
			},
			
			getTemplate: function(params){
				var model = params.elem.find(".model").parent().clone().find(".model").removeClass("hidden").parent();
				params.elem.find(".model").remove();
				return model.html();
			}
	    };
	    
	})( jQuery, window, document );
	
});