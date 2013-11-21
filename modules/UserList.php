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


class WidgetUserList {
	
	public $result = array();
	
	/**
	 * Initialization of the class.
	 *
	 * @return -
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function WidgetUserList( ){
		
		if (!isset($this -> mm_users)) {
			include_once (MAIN . '/libs/Users.php');
			$this -> mm_users = new Users();
		}
		$arr = array();
		$arr["rows"] = $this -> mm_users -> GetAll();
		
		$this->result = $arr;
	}
  
}