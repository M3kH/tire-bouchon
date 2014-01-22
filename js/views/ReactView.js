/** @jsx React.DOM */
if (typeof define !== 'function') { var define = require('amdefine')(module) }
define(function(require) {

	// var $ = require("../vendor/jquery.js");
	var React = require("../vendor/react.js");
    // var dep = require('dependency');
	var BackboneMixin = {
	  componentDidMount: function() {
	    // Whenever there may be a change in the Backbone data, trigger a reconcile.
	    this.getBackboneModels().forEach(function(model) {
	      model.on('add change remove', this.forceUpdate.bind(this, null), this);
	    }, this);
	  },
	
	  componentWillUnmount: function() {
	    // Ensure that we clean up any dangling references when the component is
	    // destroyed.
	    this.getBackboneModels().forEach(function(model) {
	      model.off(null, null, this);
	    }, this);
	  }
	};
	
	// var deps = require("something");
	var ReactView = React.createClass({displayName: 'ReactView',
			mixins: [BackboneMixin],
			
			changeInput: function(e){
				console.log("Something change");
		    	// this.setState({text: e.target.value});
			},
			
			editCell : function(e){
				console.log(e);
				// var val = $(e.target).html();
				// $(e.target).html( return (
					// <input type="text" value="{val}" onChange="{this.changeInput}" />
				// ) );
		    	// this.setState({text: e.target.value});
			},
		  render: function () {
		    return (
		      React.DOM.table( {id:"#reactview"}, React.DOM.tbody(null, 
		        this.props.data.models.map(function(row) {
		          return (
		            React.DOM.tr(null, 
		              row.map(function(cell) {
		                return React.DOM.td( {ondblclick:"{this.editCell}"}, cell);
		          })
		        ));
		    })
		  ))
		    );
		  }
		});
	
	return ReactView;
});

// React.renderComponent(<MessageBox name="Rogers"/>, mountNode);