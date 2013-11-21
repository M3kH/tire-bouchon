require(["vendor/mustache"], function(mustache) {
	
	/*!
	 * Ideabile - Listfly
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
	
	    $.fn.listfly = function ( options ) {
	
			// console.log("Go inside function, plugin loaded!");
	        options = $.extend( true, $.fn.listfly.options, options );
	
	        return this.each(function () {
	
	            var elem = $(this),
	            	loginButton = elem.find(".e-login"),
	            	sigupButton = elem.find(".e-sigup");
	            	
	            	options.elem = elem;
	            	
	            	$('.e-login-modal').on('click', function(){
	            		console.log("Enter");
	            		$.fn.listfly.options.cleanAll( options );
						elem.find('.registration').hide();
	            	});
	            	
	            	if( typeof options.ctrls != 'undefined' ){
	            		if( typeof options.data.ctrls.row != 'undefined' && options.showCtrlsRow === 1){
	            			options = $.fn.listfly.options.setCtrlsRows(options, options.data.ctrls.rows);
	            		}
	            	}
	            	
	            	if( typeof options.data != 'undefined' ){
	            		if( typeof options.data.thead != 'undefined' && options.showTitles === 1){
	            			options = $.fn.listfly.options.setTitles(options, options.data.titles);
	            		}
	            		if( typeof options.data.rows != 'undefined'){
	            			options = $.fn.listfly.options.setRows(options, options.data.rows);
	            		}
	            	}
	            	
	        });
	    };
	
	    $.fn.listfly.options = {
	    	
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
			
	        validating: {
	        	email: function(email){
	        		return  /^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_.-])+.([a-zA-Z])+([a-zA-Z])+/.test(email);
	        	},
	        	password: function(pass){
	        		var score = 0;
				    if (!pass)
				        return score;
				
				    // award every unique letter until 5 repetitions
				    var letters = new Object();
				    for (var i=0; i<pass.length; i++) {
				        letters[pass[i]] = (letters[pass[i]] || 0) + 1;
				        score += 5.0 / letters[pass[i]];
				    }
				
				    // bonus points for mixing it up
				    var variations = {
				        digits: /\d/.test(pass),
				        lower: /[a-z]/.test(pass),
				        upper: /[A-Z]/.test(pass),
				        nonWords: /\W/.test(pass),
				    }
				
				    variationCount = 0;
				    for (var check in variations) {
				        variationCount += (variations[check] == true) ? 1 : 0;
				    }
				    score += (variationCount - 1) * 10;
				
				    return parseInt(score);
	        	}
	        },
	        
	        labels: {
	        	registration: {
	        		success: 'L\'autenticazione è terminata con successo. La reindirizeremo all\'applicazione in 3 secondi. Altrimenti clicca <a href="index.php">qui</a>.',
	        	},
	        	
	        	registration: {
	        		error: 'Opss... Qualcosae è andato storto.  Si prega di riporvare.',
	        		success: 'Le è stata inviata un\' email nel suo account di posta, la preghiamo di controllare e seguire le istruzioni per concludere la registrazione.',
	        		addDetails: 'Perfavore compila anche nome e cognome.',
	        		firstNameRequired: 'Questo campo è obbligatorio.',
	        		lastNameRequired: 'Questo campo è obbligatorio.'
	        	},
	        	
	        	user:{
	        		badUser: 'Il login deve essere in formato email: <b>utente@provider.ext</b>.'
	        	},
	        	password:{
	        		badPassword: 'Ci dispiace ma la password deve soddisfare i seguenti requisiti: <b>Un numero</b> e <b>un carattere speciale</b>',
	        		notStrong: 'Attenzione, la password non è "sicura", ti consigliamo di cambiarla.'
	        	}
	        }
	    };
	    
	})( jQuery, window, document );
	
});