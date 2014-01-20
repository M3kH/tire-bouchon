/*global define*/
define([
	'jquery',
	'underscore',
	'backbone'
], function ($, _, Backbone ) {
	'use strict';
	
	var BasicCollectionView = Backbone.View.extend({
		
		renderCollection: function() {
			var els = _.map(this._viewPointers, function(view){
				return view.el;
			});
			return $(els);
		},
		
		rendersCollectively: function() {
			var self = this;
			this._viewPointers = {}; // make sure we're starting over
			this.collection.each(function(model){
				self.addOne(model);
			});
			this.collection.on('add', function(model) {
				this.addOne(model);
				this.render();
			}, this);
			this.collection.on('remove', this.removeOne, this);
		},
		
		addOne: function(model) {
			view = new this.collectionView({ model: model });
			this._viewPointers[model.cid] = view;
		},
		
		removeOne: function(model) {
			this._viewPointers[model.cid].remove();
			delete this._viewPointers[model.cid];
		},
		
		_viewPointers: {}
		
	});
	
	return BasicCollectionView;

});