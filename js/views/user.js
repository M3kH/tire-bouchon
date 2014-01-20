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
	
	var UserView = Backbone.View.extend({
		
		// This is the all element where the view is working
		// el: "tr.user-model",
		
		tagName: "tr",
		
		className: "user-model",
		
		// This is for the template
		template: _.template($("#user-model-template").html()),
		
		events: {
			'change input': 'change',
		},
		
		initialize: function(){
			
			/*
			 * Create the main dom reference
			 */
			var _this = this;
			this.$buttons = {};
			this.$inputs = {};
			this.$forms = {};
			
			
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);
            // this.listenTo(this.model, 'visible', this.toggleVisible);
			
			/*
			 * Forms
			 */
			// this.$forms.login = this.$("#form-login");
			
			
			/*
			 * Buttons
			 */
			this.$buttons.add = this.$(".e-add");
			
			/*
			 * Inputs
			 */
        	this.$inputs.id = this.$('input[data-prop="id"]');
        	this.$inputs.first_name = this.$('input[data-prop="first_name"]');
        	this.$inputs.last_name = this.$('input[data-prop="last_name"]');
        	this.$inputs.email = this.$('input[data-prop="email"]');
        	this.$inputs.status = this.$('input[data-prop="status"]');
        	this.$inputs.create_at = this.$('input[data-prop="create_at"]');
            	
		},
		
		change: function(){
			this.model.save({
				user_id: this.$inputs.id.val().trim(),
				email: this.$inputs.email.val().trim(),
				first_name: this.$inputs.first_name.val().trim(),
				last_name: this.$inputs.last_name.val().trim(),
				status: this.$inputs.status.val().trim(),
				create_at: this.$inputs.create_at.val().trim(),
			});
		},
		
		render: function(){
	        if (this.model.changed.id !== undefined) {
	                return;
	        }
	        
	      var $el = $(this.el);
		      $el.data('userId', this.model.get('id'));
		      $el.html(this.template(this.model.toJSON()));
		      return this;
		    // this.$el.html(this.template(this.model.toJSON()));
		    // return this;
		},
		
		parse: function(){
			
		}
		
	});
	
	return UserView;

});