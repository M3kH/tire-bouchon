/*!
 * Ideabile - Login
 * Author: @cowboy
 * Further changes: @addyosmani
 * Licensed under the MIT license
 */


;(function ( $, window, document, undefined ) {

    $.fn.iLogin = function ( options ) {

		console.log("Go inside function, plugin loaded!");
        options = $.extend( true, $.fn.iLogin.options, options );

        return this.each(function () {

            var elem = $(this),
            	loginButton = elem.find(".e-login"),
            	sigupButton = elem.find(".e-sigup");
            	
            	$('.e-login-modal').on('click', function(){
            		console.log("Enter");
            		$.fn.iLogin.options.cleanAll( options );
					elem.find('.registration').hide();
        			$("#loginModal").modal("show");
            	});
            	
            	options.elem = elem;
            	options.buttons.login = loginButton;
            	options.buttons.sigup = sigupButton;
            	options.buttons.facebook = elem.find(".e-facebook");
            	options.buttons.gplus = elem.find(".e-gplus");
            	options.buttons.twitter = elem.find(".e-twitter");
            	
            	options.forms.login = elem.find("#form-login");
            	options.forms.login.on("submit", function(){
            		return false;
            	});
            	
            	options.inputs.login = elem.find("#login");
            	options.inputs.password = elem.find("#password");
            	options.inputs.firstName = elem.find("#first_name");
            	options.inputs.lastName = elem.find("#last_name");

            	options.inputs.firstName.closest('.control-group').hide();
            	options.inputs.lastName.closest('.control-group').hide();
            	
            	options.buttons.login.on("click", function(){
            		console.log("login click!");
            		$.fn.iLogin.options.trylogin( options );
            	});
            	
            	elem.on("register", function(){
            		$.fn.iLogin.options.register( options );
            	});
            	
            	elem.on("login", function(){
            		$.fn.iLogin.options.login( options );
            	});
            	
            	options.buttons.sigup.on("click", function(){
            		console.log("sigup click!");
            		$.fn.iLogin.options.sigup( options );
            	});
            	options.buttons.facebook.on("click", elem.trigger("e-facebook"));
            	elem.on("e-facebook", function(){
            		console.log("facebook click!");
            		$.fn.iLogin.options.login(options);
            	});
            	
            	options.buttons.gplus.on("click", elem.trigger("e-gplus"));
            	elem.on("e-gplus", function(){
            		$.fn.iLogin.options.login(options);
            	});
            	
            	options.buttons.twitter.on("click", elem.trigger("e-twitter"));
            	elem.on("e-twitter", function(){
            		$.fn.iLogin.options.login(options);
            	});
        });
    };

    $.fn.iLogin.options = {
    	
		elem: '',
		registration: false,
		buttons: {},
		forms: {},
		inputs: {},
		ajax: {
			register: 'api/user/new/',
			login: 'api/user/login/'
		},
        key: "value",
        
        trylogin: function ( params ) {
            $.fn.iLogin.options.removeInputError( params.inputs.login );
    		$.fn.iLogin.options.removeInputError( params.inputs.password );
    		
    		var login, validEmail, _pass = false, _login = false;
    		
    		login = params.inputs.login.val();
    		password = params.inputs.password.val();
    		
    		validEmail = $.fn.iLogin.options.validating.email( login );
    		passwordStr = $.fn.iLogin.options.validating.password( password );
    		
    		if( login.length > 0 && validEmail){
        		_login = true;
        	}else{
        		$.fn.iLogin.options.addInputError( params.inputs.login, params.labels.user.badUser );
        	}
        	
        	if( password.length > 0 && passwordStr > 50){
        		_pass = true;
        	}else{
        		$.fn.iLogin.options.addInputError( params.inputs.password, params.labels.password.badPassword );
        	}
        	
        	if( _pass && _login ){
        		
				params.elem.trigger("login");
        	}
        	
        	return params;


        },
        
        login: function( params ){
    		_debug = true;
        	$.ajax({
				url: params.ajax.login,
				data: params.forms.login.serialize(),
				dataType: 'json',
				type: 'POST',
				beforeSend: function ( ) {
					// Start Loading
	    			$.fn.iLogin.options.addAlert(params, '<img src="img/loader.gif" alt="loading" />');
				},
				success: function ( msg ){
					// Stop Loading
					
					// DEBUG - START 
					// if(_debug){ var _timeout = 3000;}else{var _timeout = 0};
					// setTimeout(function(){
					// DEBUG - END
    				$.fn.iLogin.options.cleanAllAlerts( params );
					if( typeof msg.errors == 'undefined'){
		    			$.fn.iLogin.options.addAlert(params, params.login.success);
		    			document.location.reload(true);
					}else{
						for( var alert in msg.errors ){
		    				$.fn.iLogin.options.addAlert(params, msg.errors[alert]);
						}
					}
		    			
					// DEBUG - START 
					//}, _timeout);
					// DEBUG - END
				},
				error: function( msg ){
					// Stop Loading
					// if(_debug){ var _timeout = 3000;}else{var _timeout = 0};
					// setTimeout(function(){
	    				$.fn.iLogin.options.cleanAllAlerts( params );
		    			$.fn.iLogin.options.addError(params, params.labels.registration.error);
					// }, _timeout);
				}
			});
        },
        
        sigup: function( params ){
    		$.fn.iLogin.options.removeInputError( params.inputs.login );
    		$.fn.iLogin.options.removeInputError( params.inputs.password );
    		$.fn.iLogin.options.removeInputError( params.inputs.firstName );
    		$.fn.iLogin.options.removeInputError( params.inputs.lastName );
    		
    		$.fn.iLogin.options.cleanAllAlerts( params );
    		
    	var login, validEmail, _pass = false, _login = false, _firstName = false, _lastName = false;
    		
    		login = params.inputs.login.val();
    		password = params.inputs.password.val();
    		firstName = params.inputs.firstName.val();
    		lastName = params.inputs.lastName.val();
    		
    		validEmail = $.fn.iLogin.options.validating.email( login );
    		passwordStr = $.fn.iLogin.options.validating.password( password );
    		
        	if( login.length > 0 && validEmail){
        		_login = true;
        	}else{
        		$.fn.iLogin.options.addInputError( params.inputs.login, params.labels.user.badUser );
        	}
        	
        	if( password.length > 0 && passwordStr > 50){
        		_pass = true;
        		if( passwordStr < 80 ){
	    			$.fn.iLogin.options.addAlert(params, params.labels.password.notStrong);
        		}
        	}else{
        		$.fn.iLogin.options.addInputError( params.inputs.password, params.labels.password.badPassword );
        	}
        	
        	if( _pass && _login ){
        		
	    		if(params.registration == false){
	    			$.fn.iLogin.options.askDetails( params );
	    			params.registration = true;
	    			
	    		}else{
    				
		        	if( firstName.length > 3){
		        		_firstName = true;
		        	}else{
        				$.fn.iLogin.options.addInputError( params.inputs.firstName, params.labels.registration.firstNameRequired );
		        	}
        	
		        	if( lastName.length > 3){
		        		_lastName = true;
		        	}else{
        				$.fn.iLogin.options.addInputError( params.inputs.lastName, params.labels.registration.lastNameRequired );
		        	}
		        	
		        	if(_firstName && _lastName){
	    				params.elem.trigger("register");
		        	}
	    		}
        	}
        	
        	return params;
        },
        
        askDetails: function( params ){
			$.fn.iLogin.options.addAlert( params, params.labels.registration.addDetails );
			params.elem.find('.registration').show();
        },
        
        register: function( params ){
    		_debug = true;
        	$.ajax({
				url: params.ajax.register,
				data: params.forms.login.serialize(),
				dataType: 'json',
				type: 'POST',
				beforeSend: function ( ) {
					// Start Loading
	    			$.fn.iLogin.options.addAlert(params, '<img src="img/loader.gif" alt="loading" />');
				},
				success: function ( msg ){
					
					// Stop Loading
					
					// DEBUG - START 
					// if(_debug){ var _timeout = 3000;}else{var _timeout = 0};
					// setTimeout(function(){
					// DEBUG - END 
					
    				$.fn.iLogin.options.cleanAllAlerts( params );
						
					if( typeof msg.errors == 'undefined'){
		    			$.fn.iLogin.options.addAlert(params, params.labels.registration.success);
					}else{
						for( var alert in msg.errors ){
		    				$.fn.iLogin.options.addAlert(params, msg.errors[alert]);
						}
					}
		    			
					// DEBUG - START 
					// }, _timeout);
					// DEBUG - END 
				},
				error: function( msg ){
					// Stop Loading
					if(_debug){ var _timeout = 3000;}else{var _timeout = 0};
					setTimeout(function(){
	    				$.fn.iLogin.options.cleanAllAlerts( params );
		    			$.fn.iLogin.options.addError(params, params.labels.registration.error);
					}, _timeout);
				}
			});
        },
        
        cleanAll: function( params ){
			$.fn.iLogin.options.cleanAllAlerts( params );
			$.fn.iLogin.options.removeAllInputErrors( params );
			
        	params.inputs.firstName.closest('.control-group').hide();
        	params.inputs.lastName.closest('.control-group').hide();
        	
			params.elem.find('input').val('');
        },
        
        addError: function( params, description ){
        	var div = params.elem.find('.modal-body');
        		
        		if( typeof description == 'string' && description.length > 0 ){
    				div.prepend('<div class="alert alert-error">' + description + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
        		}
        },
        
        addAlert: function( params, description ){
        	var div = params.elem.find('.modal-body');
        		
        		if( typeof description == 'string' && description.length > 0 ){
    				div.prepend('<div class="alert">' + description + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
        		}
        },
        
        cleanAllAlerts: function( params ){
        	var div = params.elem.find('.modal-body');
        		div.find('.alert').remove();
        },
        
        cleanErrors: function( params ){
        	var div = params.elem.find('.modal-body');
        		div.find('.alert').remove();
        },
        
        addInputError: function( input, description ){
        	var input = $(input),
        		div = input.closest('.control-group');
        		
        		div.addClass('error');
        		
        		if( typeof description == 'string' && description.length > 0 ){
    				input.after('<span class="help-inline">' + description + '</span>');
        		}
        },
        
        removeAllInputErrors: function( params ){
        	var elem = params.elem,
        		input = elem.find('input'),
        		div = input.closest('.control-group'),
        		label = div.find('label'),
        		desc = div.find('.help-inline');
        		
        		div.removeClass('error');
        		desc.remove();
        },
        
        removeInputError: function( input ){
        	var input = $(input),
        		div = input.closest('.control-group'),
        		desc = div.find('.help-inline');

        		div.removeClass('error');
        		desc.fadeOut(500, function(){ $(this).remove(); });
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
