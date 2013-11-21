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


class WidgetNewLink {
	
	public $result = array();
	
	/**
	 * Initialization of the class.
	 *
	 * @return -
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function WidgetNewLink( ){
		
		if(isset($_SESSION['id_user'])){
			$this->result['user'] = true;
		}else{
			$this->result['user'] = false;
		}
		if(isset($_GET['url'])){
			$this->result["url"] = $_GET['url'];
		}
		if(isset($_GET['title'])){
			$this->result["title"] = $_GET['title'];
		}
		if(isset($_GET['description'])){
			$this->result["description"] = $_GET['description'];
		}
		if(isset($_GET['tags'])){
			$this->result["tags"] = $_GET['tags'];
		}else{
			$this->result["tags"] = "";
		}
		// $this->result["script"] = $minifiedCode;
	}
  
}