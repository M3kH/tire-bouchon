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


include_once(MAIN."/views/base.php");
class ViewLogin extends BaseView{
	
	public $result = array();
	
	/**
	 * Initialization of the class.
	 *
	 * @return -
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function __construct( ){
		
		parent::__construct();
		
		if(isset($_SESSION['id_user'])){
			$this->result['is_user'] = TRUE;
			$this->result['user_id'] = $_SESSION['id_user'];
			$this->result['email'] = $_SESSION['email'];
			$this->result['name'] = $_SESSION['name'];
			$this->result['last_name'] = $_SESSION['last_name'];
		}else{
			$this->result['not_user'] = TRUE;
			$this->result['md5'] = (string) $_SESSION['md5_login'];
		}
		// var_dump($this->result);
	}
  
}