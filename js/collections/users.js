/*global define*/
define([
	'underscore',
	'backbone',
	'models/user'
], function (_, Backbone, User) {
	'use strict';

	var UsersCollection = Backbone.Collection.extend({
		// Reference to this collection's model.
		model: User,
		
		url: 'api/users/',
		
		// Save all of the todo items under the `"todos"` namespace.
		// localStorage: new Store('todos-backbone'),

		// Filter down the list of all todo items that are finished.
		status: function () {
			return this.filter(function (user) {
				return user.get('status');
			});
		},

		// Filter down the list to only todo items that are still not finished.
		remaining: function () {
			return this.without.apply(this, this.status());
		},

		// We keep the Todos in sequential order, despite being saved by unordered
		// GUID in the database. This generates the next order number for new items.
		nextOrder: function () {
			if (!this.length) {
				return 1;
			}
			return this.last().get('status') + 1;
		},

		// Todos are sorted by their original insertion order.
		comparator: function (todo) {
			return todo.get('status');
		}
	});

	return new UsersCollection();
});