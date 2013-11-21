<?php

/**
 * Login and Registration system,
 * and social integration with HybridAuth.
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

// @TODO Add record secret code to the database.
// @TODO Add sending email when the registration is completed.
// @TODO Add password field in random generation for security reason.
// @TODO Limit at 20 times fallied login for each session. And add the Ip and the Web Agent to the yellow list.
// @TODO If the Ip and the Web Agent try again with other 60 fallied login. Add the Ip to unauthorized client login for 1 hour.


class Mail {
	
	/**
	 * Initialization of the class.
	 *
	 * @return -
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function Mail( ){
		require_once(MAIN.'/core/db.php');
		require_once(MAIN.'/ext/Mustache/Autoloader.php' );
		require_once(MAIN.'/ext/PHPMailer/class.phpmailer.php' );
		
		Mustache_Autoloader::register(MAIN.'/ext/');
		$this->mustache = new Mustache_Engine;
		
		$this->email = new PHPMailer();
		$this->email->SetFrom('info@ideabile.com', 'Maps for You');
		$this->email->AddReplyTo("info@ideabile.com","Maps for You");
		
		
		$this->db = new DB();
		$this->errors = array();
	}
	
	/**
	 * This function create a new user.
	 *
	 * @return ['result'], ['type']
	 * @see CheckRegistration, AddErrors, GetErrors
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function Templating ( $txt, $arr = array() ){
		
		return $this->mustache->render( $txt, $arr );
		
	}
	
	/**
	 * This function create a new user.
	 *
	 * @return ['result'], ['type']
	 * @see CheckRegistration, AddErrors, GetErrors
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function Send ( $address, $name, $subject, $text, $html ){
		$this->email->AddAddress($address, $name);
		$this->email->Subject    = $subject;
		$this->email->AltBody    = $text; // optional, comment out and test
		$this->email->MsgHTML($html);
		$success = $this->email->Send();

		// if(!$success) {
		  // echo "Mailer Error: " . $this->email->ErrorInfo;
		// } else {
		  // echo "Message sent!";
		// }
		
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