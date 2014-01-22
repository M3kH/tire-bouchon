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
class ViewUsers extends BaseView{
	
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
			include_once(MAIN."/models/Users.php");
			$user = new Users();
			$this->result["users"] = $this->getJsonModel($user->GetAll());
			$this->result["json_model"] = json_encode($user->GetAll());
			$this->result["ReactView"] = $this->renderComponetNode( "ReactView", $user->GetAll());
		}
		// var_dump($this->result);
	}
  
}