/*global define*/
define([
	'jquery',
	'underscore',
	'backbone',
	'collections/users',
	'models/user',
	'views/user',
	'common',
	'i18n!js/nls/login.js',
	'plugins/bootstrap/modal'
], function ($, _, Backbone, UsersCollection, User, UserView, Common, Labels) {
	'use strict';
	
	var DataGridView = Backbone.View.extend({
		
		// This is the all element where the view is working
		el: "#users_list",
				
		collection: UsersCollection,
		
		// This is for the template
		// template: _.template(loginTemplate),
		
		events: {
			'click .e-add': 'createElement'
		},
		
		firstInit: false,
		
		initialize: function(){
			
			/*
			 * Create the main dom reference
			 */
    		this._subViews = [];
    		
			this.$buttons = {};
			this.$inputs = {};
			this.$forms = {};
			


            this.listenTo(this.collection, 'add', this.addOne);
            // this.collection.bind('add', this.addOne);
            this.listenTo(this.collection, 'reset', this.addAll);
            this.listenTo(this.collection, 'change:completed', this.filterOne);
            this.listenTo(this.collection, 'filter', this.filterAll);
            this.listenTo(this.collection, 'all', this.render);

            // Todos.fetch();
			
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
        	this.$inputs.id = this.$('input[data-attribute="id"]');
        	this.$inputs.first_name = this.$('input[data-attribute="first_name"]');
        	this.$inputs.last_name = this.$('input[data-attribute="last_name"]');
        	
        	if(this.firstInit === false){
        		this.firstInit = true;
	            this.parse();
        	}
        	
        	
			var _this = this;
		    this.collection.each(function(user) {
		      _this._subViews.push(new UserView({
		        model : user
		      }));
		    });
		},
		
		render: function(){
		    // Assume our model exposes the items we will
		    // display in our list
		    var _users = this.collection.models,
				_this = this;
		    // console.log(_users);
		    this.$el.find("tbody").empty();
			// console.log(_users);		
		    // Loop through each of our items using the Underscore
		    // _.each iterator
		    
		    // console.log(this._subViews);
		    
		    _(this._subViews).each(function(subView) {
		      _this.$el.find("tbody").append(subView.render().el);
		    });
		    
		    return this;
		},
		
        // Add a single todo item to the list by creating a view for it, and
        // appending its element to the `<ul>`.
        addOne: function (user) {
        	// console.log(this);
            this._subViews.push( new UserView({ model: user }));
        },
        
        // Add all items in the **Todos** collection at once.
        addAll: function () {
                this.$("table tbody").html('');
                this.collection.each(this.addOne, this);
        },

        filterOne: function (todo) {
                todo.trigger('visible');
        },

        filterAll: function () {
                Todos.each(this.filterOne, this);
        },
        
        // Generate the attributes for a new Todo item.
        newAttributes: function () {
                return {
                	'first_name': 'test'
                };
        },
        
        createElement: function(e){
        	e.preventDefault();
        	// console.log(e);
			// new UserView({model: this.newAttributes()});
            this.collection.create(this.newAttributes());
        },
		
		parse: function(){
			/*
			 * In case of user grid view, you can parse the data for populate the collection.
			 */
			var collection = [],
				_this = this;
				
			this.$("[data-model='user']").each(function(){
				var cl = collection.length,
					data = $(this).data('attributes');
					
				collection[cl] = new User(data);
            	_this._subViews.push( new UserView({model: collection[cl], el: $(this)[0]}));
		    });
		    this.collection.reset(collection, {silent: true});
		    // this.collection.add([{'first_name': "Something", "email": "email@email.com", 'last_name': "Else"}]);
		    // this.collection.reset(collection);
		    console.log(collection);
		}
		
	});
	
	return DataGridView;

});