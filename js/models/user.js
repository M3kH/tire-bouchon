/*global define*/
define([
	'underscore',
	'backbone'
], function (_, Backbone) {
	'use strict';

	var User = Backbone.Model.extend({
		
		url: 'api/users/',
		
		// Default attributes for the todo
		// and ensure that each todo created has `title` and `completed` keys.
		defaults: {
			user_id: undefined,
			first_name: '',
			last_name: '',
			email: '',
			status: 0,
			create_at: "0000-00-00 00:00:00"
		},

		// Toggle the `completed` state of this todo item.
		toggle: function () {
			this.save({
				completed: !this.get('completed')
			});
		}
	});

	return User;
});