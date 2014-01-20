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


require_once(MAIN."/models/base.php");
class Users extends Base {
	
	public $db = "main";
	public $table = "users";
	
	public function __construct () {
		parent::__construct();
	}
	
	
	/**
	 * Get all the element from the table.
	 *
	 * @return BOOLEAN
	 * @see Main (DB)
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function GetAll ( ){
		$r = R::getAll( "SELECT id AS user_id, email, first_name, last_name, md5, status, provider, uid, create_at FROM $this->table" );
		return $r;
	}
	
	/**
	 * This function check if the user already exist in the DB.
	 *
	 * @return BOOLEAN
	 * @see Main (DB)
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function Get ( $id = '' ){
		$users = R::find($this->table,
        ' id = :id', 
            array( 
                ':id' => $id
            )
        );
		
		return $users;
	}
	
	/**
	 * This function check if the user already exist in the DB.
	 *
	 * @return BOOLEAN
	 * @see Main (DB)
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function Update ( $id = '', $first_name = '', $last_name = '' ){
		if( key_exists('id', $_POST)){
			$id = $_POST['id'];
			$query = $this->db->Main("SELECT login, name, last_name FROM users WHERE id = $1", array($id));
			
			if($query) {
				$result = $query->fetch(PDO::FETCH_ASSOC);
				if( $result ) {
					return $result;
				}else{
					return array();
				}
			} else {
				return array();
			}
			
		}else{
			return FALSE;
		}
	}
	
	/**
	 * This function return all errors saved in array, and empty the same array.
	 *
	 * @return -
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function GetErrors (){
		$errors = $this->errors;
		$this->errors = array();
		return array('errors' => $errors);
	}
	
	
	/**
	 * Add error to the array errors.
	 *
	 * @return -
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function AddError ( $error ){
		$this->errors[] = $error;
	}
	
  
}
