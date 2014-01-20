/** @jsx React.DOM */
define(function(require) {
	
	// var deps = require("something");
	var ReactView = React.createClass({
	  render: function () {
	    return (
	      <table><tbody>
	        {this.props.data.map(function(row) {
	          return (
	            <tr>
	              {row.map(function(cell) {
	                return <td>{cell}</td>;
	              })}
	            </tr>);
	        })}
	      </tbody></table>
	    );
	  }
	});
	
	return ReactView;
});

// React.renderComponent(<MessageBox name="Rogers"/>, mountNode);