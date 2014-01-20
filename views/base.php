<?php
/**
 * Users managements
 *
 * @category   Login / Registration
 * @package    Ideabile Framework
 * @author     Mauro Mandracchia <info@ideabile.com>
 * @copyright  2013 - 2014 Ideabile
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    Release: 0.1a
 * @link       http://www.ideabile.com
 * @see        -
 * @since      -
 * @deprecated -
 */

include_once(MAIN."/views/interface.php");
class BaseView implements View {
	
	public $result = array();
	
	/**
	 * Initialization of the class.
	 *
	 * @return -
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function __construct( ){
		
		if( isset( $_SESSION['id_user'] ) ){
			$this->result["global"]["is_user"] = TRUE;
			$this->result["global"]["user"]["id"] = $_SESSION['id_user'];
			$this->result["global"]["user"]["email"] = $_SESSION['email'];
			$this->result["global"]["user"]["first_name"] = $_SESSION['first_name'];
			$this->result["global"]["user"]["last_name"] = $_SESSION['last_name'];			
		}else{
			$this->result["global"]["not_user"] = TRUE;
			$this->result["global"]['md5'] = (string) $_SESSION['md5_login'];
		}
		
	}
	
	public function renderComponetNode( $component, $data ){
		// Connect to DNode server running in port 7070 and call 
		// Zing with argument 33
		require MAIN.'/core/ext/autoload.php';
		$result = "something";
		
	 	ob_start();
		$loop = new React\EventLoop\StreamSelectLoop();
		
		// Connect to DNode server running in port 7070 and call Zing with argument 33
		$dnode = new DNode\DNode($loop);
		$dnode->connect( 7070, function( $remote, $connection ) use ( $component, $data ) {
		    $remote->zing( $component, $data, function($r) use ($connection) {
		        $result = $r;
		        echo "{$r}";
		        $connection->end();
		    });
		});
		
		$loop->run();
		$markup = ob_get_clean();
		
		return $markup;
		
	}
	
	public function renderComponent( $component, $data ){
		// stubs, react
		$v8 = new V8Js();
		
		// $react[] = "/** @jsx React.DOM */";
		$react[] = "Object.defineProperty(Object.prototype, 'map', {
				    value: function(f, ctx) {
				        ctx = ctx || this;
				        var self = this, result = {};
				        Object.keys(self).forEach(function(v) {
				            result[v] = f.call(ctx, self[v], v, self); 
				        });
				        return result;
				    }
				});";
		$react[] = "var console = {warn: function(){}, error: print}";
		$react[] = "var global = {}";
		// $react[] = "window = this;window.location={href:\"file://\",port:\"\"};document={getElementsByTagName:function(){return []}};";
		// $react[] = file_get_contents(MAIN.'/js/domnode.js');
		// $react[] = file_get_contents(MAIN.'/js/htmlelts.js');
		// $react[] = file_get_contents(MAIN.'/js/domcore.js');
		// $react[] = file_get_contents(MAIN.'/js/dom.js');
		// $react[] = file_get_contents(MAIN.'/js/require-server.js');
		// $react[] = "require.config({
	        // baseUrl: \"http://".URL."/js/\"
	    // });";
		$react[] = file_get_contents(MAIN.'/js/vendor/react.js');
		$react[] = file_get_contents(MAIN.'/js/vendor/jsx-trasformer.js');
		$react[] = "var React = global.React";
		// my custom components
		$react[] = file_get_contents(MAIN.'/js/views/'.$component.'.js');
		
		$react[] = sprintf(
		  "React.renderComponentToString(".$component."({data: %s}), print)",
		  json_encode($data));
		  // concat all JS
		 $react = implode(";\n", $react);
		 try {
		 	ob_start();
  			$v8->executeString($react);
  			$markup = ob_get_clean();
			return $markup;
		} catch (V8JsException $e) {
		  // blow up spectacularly
		  // echo "<pre>"; 
		  var_dump($react);
		  return $e;
		}
	}
	
	public function getResult( ){
		return $this->result;
	}
	
	public function getJsonModel( $arr= array() ){
		foreach ($arr as $key => $value) {
			$arr[$key]["json_model"] = json_encode($arr[$key]);
		}
		return $arr;
	}
  
}