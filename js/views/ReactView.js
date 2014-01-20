if (typeof define !== 'function') { var define = require('amdefine')(module) }
define(function(require) {
	
    // var dep = require('dependency');
//  
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

    var React = require("react");
    var ReactView = React.createClass({displayName: 'ReactView',
    	mixins: [BackboneMixin],
	  render: function () {
      	console.log(this.props);
	    return (
	      React.DOM.table(null, React.DOM.tbody(null, 
	        this.props.data.models.map(function(row) {
	        	console.log(row.attributes);
	          return (
	            React.DOM.tr(null, 
	              row.map(function(cell) {
	              	console.log(cell);
	                return React.DOM.td(null, cell);
	              })
	            ));
	        })
	      ))
	    );
	  }
	});

    //The value returned from the function is
    //used as the module export visible to Node.
	return ReactView;
    // return function () {};
});
// React.renderComponent(<MessageBox name="Rogers"/>, mountNode);