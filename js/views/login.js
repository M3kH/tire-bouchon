/*global define*/
define([
	'jquery',
	'underscore',
	'backbone',
	'models/user',
	'common',
	'i18n!js/nls/login.js',
	'plugins/bootstrap/modal'
], function ($, _, Backbone, User, Common, Labels) {
	'use strict';
	
	var LoginView = Backbone.View.extend({
		
		// This is the all element where the view is working
		el: "#login",
		
		// This is for the template
		// template: _.template(loginTemplate),
		
		events: {
			'click .e-login': 'tryLogin',
			'click .e-sigup': 'sigup'
		},
		
		initialize: function(){
			
			/*
			 * Create the main dom reference
			 */
			var _this = this;
			this.$buttons = {};
			this.$inputs = {};
			this.$forms = {};
			
			/*
			 * Forms
			 */
			this.$forms.login = this.$("#form-login");
			
			
			/*
			 * Buttons
			 */
			this.$buttons.login = this.$(".e-login");
			this.$buttons.sigup = this.$(".e-sigup");
			this.$buttons.facebook = this.$(".e-facebook");
			this.$buttons.twitter = this.$(".e-twitter");
			this.$buttons.gplus = this.$(".e-gplus");
			
			/*
			 * Inputs
			 */
        	this.$inputs.login = this.$("#login");
        	this.$inputs.password = this.$("#password");
        	this.$inputs.firstName = this.$("#first_name");
        	this.$inputs.lastName = this.$("#last_name");
            	
        	$('.e-login-modal').on('click', function(){
        		_this.cleanAll( );
				_this.$('.registration').hide();
    			$("#loginModal").modal("show");
        	});
        	
        	this.$forms.login.on("submit", function(){
        		return false;
        	});
        	
        	this.$inputs.firstName.closest('.control-group').hide();
            this.$inputs.lastName.closest('.control-group').hide();
            	
		},
		
		ajax: {
			register: 'api/user/new/',
			login: 'api/user/login/'
		},
		
		tryLogin: function(){
			
			console.log("Try to login!");
			
           	this.removeInputError( this.$inputs.login );
    		this.removeInputError( this.$inputs.password );
    		
    		var login, password, passwordStr, validEmail, _pass = false, _login = false;
    		
    		login = this.$inputs.login.val().trim();
    		password = this.$inputs.password.val().trim();
    		
    		validEmail = this.validating.email( login );
    		passwordStr = this.validating.password( password );
    		
    		if( login.length > 0 && validEmail){
        		_login = true;
        	}else{
        		this.addInputError( this.$inputs.login, Labels.user.badUser );
        	}
        	
        	if( password.length > 0 ){
        		_pass = true;
        	}else{
        		this.addInputError( this.$inputs.password, Labels.password.badPassword );
        	}
        	
        	if( _pass && _login ){
				this.login();
        	}
        	
        	return false;

		},
        
        login: function( ){
			var _this = this;
    		// @todo th
        	$.ajax({
				url: _this.ajax.login,
				data: _this.$forms.login.serialize(),
				dataType: 'json',
				type: 'POST',
				beforeSend: function ( ) {
					// Start Loading
	    			_this.addAlert('<img src="img/loader.gif" alt="loading" />');
				},
				success: function ( msg ){
					// Stop Loading
					
					// DEBUG - START 
					// if(_debug){ var _timeout = 3000;}else{var _timeout = 0};
					// setTimeout(function(){
					// DEBUG - END
    				_this.cleanAllAlerts( );
					if( typeof msg.errors == 'undefined'){
		    			_this.addAlert(Labels.login.success);
		    			document.location.reload(true);
					}else{
						for( var alert in msg.errors ){
		    				_this.addAlert(msg.errors[alert]);
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
	    				_this.cleanAllAlerts( params );
		    			_this.addError(Labels.registration.error);
					// }, _timeout);
				}
			});
        },
        
        sigup: function( params ){
    		this.removeInputError( this.$inputs.login );
    		this.removeInputError( this.$inputs.password );
    		this.removeInputError( this.$inputs.firstName );
    		this.removeInputError( this.$inputs.lastName );
    		
    		this.cleanAllAlerts( params );
    		
    	var login, password, passwordStr, firstName, lastName, validEmail, _pass = false, _login = false, _firstName = false, _lastName = false;
    		
    		login = this.$inputs.login.val().trim();
    		password = this.$inputs.password.val().trim();
    		firstName = this.$inputs.firstName.val().trim();
    		lastName = this.$inputs.lastName.val().trim();
    		
    		validEmail = this.validating.email( login );
    		passwordStr = this.validating.password( password );
    		
        	if( login.length > 0 && validEmail){
        		_login = true;
        	}else{
        		// @TODO Here is a Label reference should be replace somewhere else
        		this.addInputError( this.$inputs.login, Labels.user.badUser );
        	}
        	
        	if( password.length > 0 ){
        		_pass = true;
        		if( passwordStr < 80 ){
	        		// @TODO Here is a Label reference should be replace somewhere else
	    			this.addAlert( Labels.password.notStrong );
        		}
        	}else{
	        		// @TODO Here is a Label reference should be replace somewhere else
        		this.addInputError( this.$inputs.password, Labels.password.badPassword );
        	}
        	
        	if( _pass && _login ){
        		
    			this.askDetails( );
	        	if( firstName.length > 3){
	        		_firstName = true;
	        	}else{
	        		// @TODO Here is a Label reference should be replace somewhere else
    				this.addInputError( this.$inputs.firstName, Labels.registration.firstNameRequired );
	        	}
    	
	        	if( lastName.length > 3){
	        		_lastName = true;
	        	}else{
	        		// @TODO Here is a Label reference should be replace somewhere else
    				this.addInputError( this.$inputs.lastName, Labels.registration.lastNameRequired );
	        	}
	        	
	        	if(_firstName && _lastName){
					this.register();
	        	}
        	}
        	return false;
        },
        
        askDetails: function( ){
    		// @TODO Here is a Label reference should be replace somewhere else
			this.addAlert( Labels.registration.addDetails );
			this.$('.registration').show();
        },
        
        register: function( ){
			var _this = this;
			
        	$.ajax({
				url: _this.ajax.register,
				data: _this.$forms.login.serialize(),
				dataType: 'json',
				type: 'POST',
				beforeSend: function ( ) {
					// Start Loading
	    			_this.addAlert('<img src="img/loader.gif" alt="loading" />');
				},
				success: function ( msg ){
					
					// Stop Loading
					
					// DEBUG - START 
					// if(_debug){ var _timeout = 3000;}else{var _timeout = 0};
					// setTimeout(function(){
					// DEBUG - END 
					
    				_this.cleanAllAlerts( );
						
					if( typeof msg.errors == 'undefined'){
		        		// @TODO Here is a Label reference should be replace somewhere else
		    			this.addAlert(Labels.registration.success);
					}else{
						for( var alert in msg.errors ){
		    				this.addAlert(msg.errors[alert]);
						}
					}
		    			
					// DEBUG - START 
					// }, _timeout);
					// DEBUG - END 
				},
				error: function( msg ){
					// Stop Loading
    				_this.cleanAllAlerts( );
	    			_this.addError(Labels.registration.error);
				}
			});
        },
		
		registration: function(){
			
		},
        
        cleanAll: function( params ){
			this.cleanAllAlerts( params );
			this.removeAllInputErrors( params );
			
        	this.$inputs.firstName.closest('.form-group').hide();
        	this.$inputs.lastName.closest('.form-group').hide();
        	
			this.$('input').val('');
        },
        
        addError: function( params, description ){
        	var div = this.$('.modal-body');
        		
        		if( typeof description == 'string' && description.length > 0 ){
    				div.prepend('<div class="alert alert-error">' + description + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
        		}
        },
        
        addAlert: function( description ){
        	var div = this.$('.modal-body');
        		
        		if( typeof description == 'string' && description.length > 0 ){
    				div.prepend('<div class="alert alert-warning">' + description + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
        		}
        },
        
        cleanAllAlerts: function( ){
        	var div = this.$('.modal-body');
        		div.find('.alert-warning').remove();
        },
        
        cleanErrors: function( ){
        	var div = this.$('.modal-body');
        		div.find('.alert-error').remove();
        },
        
        addInputError: function( input, description ){
        	var input = $(input),
        		div = input.closest('.form-group');
        		
        		div.addClass('has-error');
        		
        		if( typeof description == 'string' && description.length > 0 ){
    				input.after('<span class="help-block">' + description + '</span>');
        		}
        },
        
        removeAllInputErrors: function( params ){
        	var elem = this,
        		input = elem.$('input'),
        		div = input.closest('.form-group'),
        		label = div.find('label'),
        		desc = div.find('.help-block');
        		
        		div.removeClass('error');
        		desc.remove();
        },
        
        removeInputError: function( input ){
        	var input = $(input),
        		div = input.closest('.form-group'),
        		desc = div.find('.help-block');

        		div.removeClass('has-error');
        		desc.fadeOut(500, function(){ $(this).remove(); });
        },
		
		validating: {
        	email: function(email){
        		return  /^([a-zA-Z0-9_.-])+@([a-zA-Z0-9_.-])+.([a-zA-Z])+([a-zA-Z])+/.test(email);
        	},
        	password: function(pass){
        		var score, variationCount = 0;
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
        }		
		
	});
	
	return LoginView;

});