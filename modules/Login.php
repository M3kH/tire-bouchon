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


class WidgetLogin {
	
	public $result = array();
	
	/**
	 * Initialization of the class.
	 *
	 * @return -
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function WidgetLogin( ){
		
		if(isset($_SESSION['id_user'])){
			$this->result['user'] = true;
			$this->result['user_id'] = $_SESSION['id_user'];
			$this->result['email'] = $_SESSION['email'];
			$this->result['name'] = $_SESSION['name'];
			$this->result['last_name'] = $_SESSION['last_name'];
		}else{
			$this->result['user'] = false;
			$this->result['md5'] = (string) $_SESSION['md5_login'];
		}
	}
  
}