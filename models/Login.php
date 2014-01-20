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


require_once(MAIN."/models/base.php");
require_once(MAIN.'/core/mail.php');
class Login extends Base {
	
	public $db = "main";
	public $table = "users";
	
	public function SocialLogin( ){
		if(isset($_GET['sociaLogin'])){
			
			$provider = $_GET['sociaLogin'];
			$hybridauth_config = MAIN.'/ext/hybridauth/config.php';
			
			require_once(MAIN.'/ext/hybridauth/Hybrid/Auth.php');
			
	
			try{
			// create an instance for Hybridauth with the configuration file path as parameter
				$hybridauth = new Hybrid_Auth( $hybridauth_config );
	
			// try to authenticate the selected $provider
				$adapter = $hybridauth->authenticate( $provider );
	
			// grab the user profile
				$user_profile = $adapter->getUserProfile();
	
			// load user and authentication models, we will need them...
				// $authentication = $this->loadModel( "authentication" );
				// $user = $this->loadModel( "user" );
	
			# 1 - check if user already have authenticated using this provider before
				$authentication_info = $this->CheckAuth( $provider, $user_profile->identifier );
	
			# 2 - if authentication exists in the database, then we set the user as connected and redirect him to his profile page
			// var_dump($authentication_info);
				if( $authentication_info ){
					// 2.2 - redirect to user/profile
					$this->redirect( "userHome" );
				}
	
			# 3 - else, here lets check if the user email we got from the provider already exists in our database ( for this example the email is UNIQUE for each user )
				// if authentication does not exist, but the email address returned  by the provider does exist in database, 
				// then we tell the user that the email  is already in use 
				// but, its up to you if you want to associate the authentication with the user having the adresse email in the database
				if( $user_profile->email ){
					$user_info = $this->CheckAuthExist( $user_profile->email );
					if( $user_info == 1 ) {
						die( 'User blocked' );
					}else if( $user_info == 2 ){
						die( '<br /><b style="color:red">Well! the email returned by the provider ('. $user_profile->email .') already exist in our database, so in this case you might use the <a href="index.php?route=users/login">Sign-in</a> to login using your email and password.</b>' );
					}else{
						
					}
				}
	
			# 4 - if authentication does not exist and email is not in use, then we create a new user 
				$provider_uid  = $user_profile->identifier;
				$email         = $user_profile->email;
				$first_name    = $user_profile->firstName;
				$last_name     = $user_profile->lastName;
				$display_name  = $user_profile->displayName;
				$website_url   = $user_profile->webSiteURL;
				$profile_url   = $user_profile->profileURL;
				$password      = rand( ) ; # for the password we generate something random
	
				// 4.1 - create new user
				// $new_user_id = $user->create( $email, $password, $first_name, $last_name ); 
	
				// 4.2 - creat a new authentication for him
				$create = $this->CreateUser( $provider, $provider_uid, $email, $password, $first_name, $last_name );
				// $authentication->create( $new_user_id, $provider, $provider_uid, $email, $display_name, $first_name, $last_name, $profile_url, $website_url );
	 
				// 4.3 - store the new user_id in session
				// $_SESSION["user"] = $new_user_id;
				if($create['type'] == "success"){
					$this->CheckAuth( $provider, $provider_uid );
					$this->redirect( "firstLogin" );
				}
	
				// 4.4 - redirect to user/profile
				
  				return array();
			}
			catch( Exception $e ){
				// Display the recived error
				switch( $e->getCode() ){ 
					case 0 : $error = "Unspecified error."; break;
					case 1 : $error = "Hybriauth configuration error."; break;
					case 2 : $error = "Provider not properly configured."; break;
					case 3 : $error = "Unknown or disabled provider."; break;
					case 4 : $error = "Missing provider application credentials."; break;
					case 5 : $error = "Authentication failed. The user has canceled the authentication or the provider refused the connection."; break;
					case 6 : $error = "User profile request failed. Most likely the user is not connected to the provider and he should to authenticate again."; 
						     // $adapter->logout(); 
						     break;
					case 7 : $error = "User not connected to the provider."; 
						     // $adapter->logout(); 
						     break;
				} 
	
				// well, basically your should not display this to the end user, just give him a hint and move on..
				$error .= "<br /><br /><b>Original error message:</b> " . $e->getMessage(); 
				$error .= "<hr /><pre>Trace:<br />" . $e->getTraceAsString() . "</pre>"; 
	
				// load error view
				$data = array( "error" => $error ); 
				return $data;
			}
		}else{
			return false;
		}
	}

	private function redirect( $uri )
	{
		$url = URL;
		header( "Location: $url?m=$uri" );

		die();
	}
	
	
	
	/**
	 * This function create a new user.
	 *
	 * @return ['result'], ['type']
	 * @see CheckRegistration, AddErrors, GetErrors
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function CreateUser ( $provider = '', $uid = '', $email = '', $password = '', $first_name = '', $last_name = ''){
			
		$type = "";
		$text = "";
		$html = "";
		
		if(!isset($_SESSION['md5_login'])){
			
			$result = array("type" => "error");
			$this->AddError( "Mhh... Something go wrong, we are going to check, sorry." );
  			return $this->GetErrors();
  			
  		}else if( count( $_POST ) ){
			$md5 = (string) $_SESSION['md5_login'];
			$type = "form";
			
			foreach($_POST as $k => $v){
				$key = (string) $k;
				switch($key){
					case 'login':
						$email = $v;
					break;
					case 'first_name':
						$first_name = $v;
					break;
					case 'last_name':
						$last_name = $v;
					break;
					case $md5:
						$password = $v;
					break;
				}
				if( $key == $md5 ){
					$password = $v;
				}
			}
			
		}else if($provider == '' && $uid == ''){
			$result = array("type" => "error");
			$this->AddError( "Field required." );
  			return $this->GetErrors();
  		}else{
			$type = "social";
  		}
		
		$check = $this->CheckRegistration( $email, $password, $first_name, $last_name );
		
		
		// var_dump($_POST);
		// var_dump($md5);
		// var_dump($password);
		// var_dump($first_name);
		// var_dump($last_name);
		// var_dump($md5);
		$password = md5($password);
		$md5 = md5(uniqid());
		
		//This declare the bean based on the table;
		$this->bean = R::dispense($this->table);
		
		if( $type == "form" && $check ){
			// $text = $this->email->Templating( file_get_contents( '../templates/emails/registration.txt' ), array('md5' => $md5 ) );
			// $html = $this->email->Templating( file_get_contents( '../templates/emails/registration.html' ), array('md5' => $md5 ) );
			
			// $query = $this->db->Main(
			// "INSERT INTO users ( email, password, name, last_name, created_at, email_validation )
			// VALUES ( ?, ?, ?, ?, NOW(), ? )",
			// array( $email, $password, $first_name, $last_name, $md5 ) );
			$this->bean->email = $email;
			$this->bean->password = $password;
			$this->bean->first_name = $first_name;
			$this->bean->last_name = $last_name;
			$this->bean->status = 1;
			$this->bean->provider = "";
			$this->bean->uid = "";
			$this->bean->md5 = $md5;
			$this->bean->create_at = date("Y-m-d H:i:s");
			$id = R::store($this->bean);
			
		}else if( $type == "social" && $check ){
			// $text = $this->email->Templating( file_get_contents( '../templates/emails/registration.txt' ), array('md5' => $md5 ) );
			// $html = $this->email->Templating( file_get_contents( '../templates/emails/registration.html' ), array('md5' => $md5 ) );
			
			// $query = $this->db->Main(
			// "INSERT INTO users ( email, password, name, last_name, hybridauth_provider_name, hybridauth_provider_uid, created_at, status )
			// VALUES ( ?, ?, ?, ?, ?, ?, NOW(), 1 )",
			// array( $email, $password, $first_name, $last_name, $provider, $uid ) );
			
			$this->bean->email = $email;
			$this->bean->password = $password;
			$this->bean->first_name = $first_name;
			$this->bean->last_name = $last_name;
			$this->bean->provider = $provider;
			$this->bean->status = 1;
			$this->bean->create_at = date("Y-m-d H:i:s");
			$this->bean->uid = $uid;
			$id = R::store($this->bean);
			 
		}else{
			return $this->GetErrors();
		}
		
		if($id) {
			// if( $text != "" && $html != ""){
				// $this->email->Send( $email, $first_name, _( "Welcome to the LinkSharingComunity!" ), $text, $html );
			// }
			
			$result['result'] = 1;
			 // if( DEBUG ){
				// print_r($result['result']);
			 // }
			$result['type'] = "success";
		} else {
			$result = array("type" => "error");
		}
  
		return $result;
		// return $this->GetErrors();
		
	}

	/**
	 * This function create a new user.
	 *
	 * @return ['result'], ['type']
	 * @see CheckRegistration, AddErrors, GetErrors
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function Authorization ( $email = '', $password = '' ){
		$md5 = (string) $_SESSION['md5_login'];
		if( count( $_POST ) ){
			
			foreach($_POST as $k => $v){
				$key = (string) $k;
				switch($key){
					case 'login':
						$email = $v;
					break;
				}
				if( $key == $md5 ){
					$password = $v;
				}
			}
			
		}else if( $email != '' && $password != '' ){
			$result = array("type" => "error");
			$this->AddError( "Field required." );
  			return $this->GetErrors();
  		}
  		
		$check = $this->CheckLogin( $email, $password );
			
		if( $check ){
 
			$password = md5($password);
						
			// $query = $this->db->Main(
			// "SELECT id, email, name, last_name FROM users
	  		// WHERE email = ? AND password = ?",
	  		// array( $email, $password ) );
			
			$user = R::findOne($this->table,
        	' email = ? AND password = ? ',array($email, $password));
			
			if($user) {
				
				$_SESSION['id_user'] = $user["id"];
				$_SESSION['email'] = $user['email'];
				$_SESSION['name'] = $user['name'];
				$_SESSION['last_name'] = $user['last_name'];
				$result['result'] = 1;
				 if( DEBUG ){
					// print_r($result['result']);
				 }
				$result['type'] = "success";
				return $result;
					
			} else {
				$this->AddError( _( "Sei sicuro d'aver inserito i dati corretti?" ) );
  				return $this->GetErrors();
			}
	  
  		}else{
			$this->AddError( _( "Sei sicuro d'aver inserito i dati corretti?" ) );
  			return $this->GetErrors();
  		}
		
	}
	
	
	/**
	 * This function check if the email is write in the correct format.
	 *
	 * @return BOOLEAN
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function SendConfirmationEmail( $first_name, $secure_code, $email ){
    	return filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match('/@.+\./', $email);
	}

	
	/**
	 * This function create a new user.
	 *
	 * @return BOOLEAN
	 * @see AddError, CheckEmailFormat, CheckUserExist
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function CheckRegistration ( $email = '', $password = '', $first_name = '', $last_name = ''){
		if( $email == '' || $password == '' || $first_name == '' || $last_name == '' ){ $this->AddError( _( "Tutti i campi sono richiesti." ) ); return false; }
		if( !$this->CheckEmailFormat( $email ) ){  $this->AddError( _( "L'email non è nel formato giusto." ) ); return false; }
		
		$user_exist = $this->CheckUserExist( $email );
		// echo $user_exist."\n\n";
		switch($user_exist){
			case 1:
				$this->AddError( _( "L'utente inserito è già stato registrato" ) ); return false;
			break;
			case 2:
				$this->AddError( _( "L'utente inserito è già stato registrato, ma non è ancora stato attivato. Hai ricevuto l'email?" ) ); return false;
			break;
		}
		
		if( strlen( $password ) < 6 ){ $this->AddError( "La password è troppo corta." ); return false; }
		return true;
	}
	
	/**
	 * This function create a new user.
	 *
	 * @return BOOLEAN
	 * @see AddError, CheckEmailFormat, CheckUserExist
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function CheckAuth ( $provider = '', $uid = ''){
		if( $provider == '' || $uid == '' ){ $this->AddError( _( "Tutti i campi sono richiesti." ) ); return false; }
		
		// $query = $this->db->Main("SELECT id, email, name, last_name, status FROM users WHERE hybridauth_provider_name = ? AND hybridauth_provider_uid = ?", array($provider, $uid));
		
			
		$user = R::findOne($this->table,
    	' hybridauth_provider_name = ? AND hybridauth_provider_uid = ? ',array($provider, $uid));
		
		if($user) {
			$_SESSION['id_user'] = $user['id'];
			$_SESSION['email'] = $user['email'];
			$_SESSION['name'] = $user['name'];
			$_SESSION['last_name'] = $user['last_name'];
			return true;
		}
		
		return false;
		
	}

	
	/**
	 * This function create a new user.
	 *
	 * @return BOOLEAN
	 * @see AddError, CheckEmailFormat, CheckUserExist
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function CheckLogin ( $email = '', $password = '' ){
		if( $email == '' || $password == '' ){ $this->AddError( _( "Tutti i campi sono richiesti." ) ); return false; }
		if( !$this->CheckEmailFormat( $email ) ){  $this->AddError( _( "L'email non è nel formato giusto." ) ); return false; }
		return true;
	}
	
	
	/**
	 * This function check if the user already exist in the DB.
	 *
	 * @return BOOLEAN
	 * @see Main (DB)
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function ActiveUser ( $md5 = '' ){
		if( $md5 != ''){
  			// $query = $this->db->Main("UPDATE users SET status = '1' WHERE status = '0' AND email_validation = ?", array($md5));
			
			
			$query = R::exec(" UPDATE $this->table SET status = '1' WHERE status = '0' AND email_validation = :md5 ",array(":md5" => $md5));
		
			
			// var_dump($result);
			
			if($query) {
				$result = TRUE;
			} else {
				$result = FALSE;
			}
			
		}else{
			$result = FALSE;
		}
		  
		return $result;
	}
	


	
	/**
	 * This function check if the user already exist in the DB.
	 *
	 * @return BOOLEAN
	 * @see Main (DB)
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function CheckAuthExist ( $email = '' ){
		if( $email != ''){
  			// $query = $this->db->Main("SELECT status FROM users WHERE email = ?", array($email));
			
			$user = R::findOne($this->table,
	    	' email = ? ',array($email));
			
			if($user) {
				if($user['status'] === 0){
					return 1;
				}else{
					return 2;
				}
			} else {
				return 0;
			}
		}else{
			return false;
		}
	}
	
	/**
	 * This function check if the user already exist in the DB.
	 *
	 * @return BOOLEAN
	 * @see Main (DB)
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function CheckUserExist ( $email = '' ){
		if( $email != ''){
  			// $query = $this->db->Main("SELECT status FROM users WHERE email = ?", array($email));
			
			$user = R::findOne($this->table,
	    	' email = ? ',array($email));
			
			if($user) {
				if($user['status'] === 0){
					return 1;
				}else{
					return 2;
				}
			} else {
				return 0;
			}
		}else{
			return false;
		}
	}
	
	
	/**
	 * This function check if the email is write in the correct format.
	 *
	 * @return BOOLEAN
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function CheckEmailFormat( $email ){
    	return filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match('/@.+\./', $email);
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