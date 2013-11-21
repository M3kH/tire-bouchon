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

class Bootstrap {
	
	/**
	 * Initialization of the class.
	 *
	 * @return -
	 * @author Mauro Mandracchia <info@ideabile.com>
	 * 
	 * @TODO bisogna cambiare questo sistema, adesso accetta tre parametri, md5, module alters.
	 * 		 bisogna rimuovere md5 e mettere varaibles a posto di alerts, gli alerts sono dichiarati all'interno di un'array associativo.
	 */
	public function Bootstrap( ){
		
		define('URL', "http://www.ideabile.com/tirebouchon/");
		define('JS', "/tirebouchon/js/");
		
		require_once MAIN . '/core/configs/api.php';
		global $config;
		
		$view = $this->SetView();
		$this->SetLanguage();
		$this->SetSession();
		if($view == "api"){
			$this->_config = $config;
			$this->GetApi();
		}else {
			$m = 'index';
			if(isset($_GET['m'])){
				$m = $_GET['m'];
			}
			$this->Render($m, $view);
		}


	}
	
	public function GetApi($jsonp = false){
		
		if(key_exists("__route__", $_GET)){
			$_route = $_GET["__route__"];
			require_once( MAIN.'/ext/epiphany/src/Epi.php' );
			Epi::setPath('base', MAIN.'/ext/epiphany/src');
			Epi::init('api');
			
			$start = 5;
			if($jsonp){ $start = 7; }
			$route = substr($_route, $start, strlen($_route));
			
			if( key_exists($route, $this->_config["api"]["route"]) ){
				$r = $this->_config["api"]["route"][$route];
				
				if(key_exists("class", $r) && file_exists(MAIN.'/libs/'.$r["class"].".php")){
					$class = $r["class"];
					include(MAIN.'/libs/'.$class.".php");
					$instance = new $class();
					if(key_exists("fnc", $r) AND method_exists($instance, $r["fnc"])){
							$exc = $r["fnc"];
						
					}else{
						$exc = FALSE;
					}
					
				}else{
					
					$exc = FALSE;
				}
				
				if(key_exists("method", $r) && $exc){
					
					
					switch ($r["method"]) {
						case 'get':
							getApi()->get( $_route, array($instance, $exc), EpiApi::external );
							
							break;
						case 'post':
							getApi()->post(	$_route, array($instance, $exc), EpiApi::external );
							
							break;
						
						default:
							
							
							break;
					}
				}elseif(isset($instance)){
					
					
					switch ($r["method"]) {
						case 'get':
							getApi()->get(	$_route, array($instance), EpiApi::external);
							
							break;
						case 'post':
							getApi()->post(	$_route, array($instance), EpiApi::external);
							
							break;
						
						default:
							
							
							break;
					}
				}else{
					return false;
				}
			}
			getRoute()->run();
		}else{
			return false;
		}
	}
	
	public function SetView(){
		
		$v = 'web';
		if(key_exists("__route__", $_GET)){
			$route = $_GET["__route__"];
			$route = explode("/", $route);
			$view = $route[1];
			// @TODO if here you find a double slash /view/ we need to redirect to /view, less the '/api/' case
			switch ($view) {
				case 'api':
					$v = 'api';
					break;
					
				case 'doc':
					$v = 'doc';
					
					break;
					
				case 'app':
					$v = 'app';
					
					break;
				
				default:
					$v = 'web';
					
					break;
			}
		}
		return $v;
	}
	
	public function SetLanguage($lang = "it_IT"){
		
		if(isset($_SESSION)){
			
			if(!key_exists('lang', $_SESSION)){
				$_SESSION['lang'] = 'it_IT';
			}
			$lang = $_SESSION['lang'];
		}else{
			$lang = "it_IT";
		}
		
		setlocale( LC_MESSAGES, $lang);
		bindtextdomain("*", MAIN.'/i18n');
		textdomain("*");
		bind_textdomain_codeset("*", 'UTF-8');
	}
	
	public function SetSession(){
		
		session_start();
		$alerts = array();
		
		if(!key_exists('md5_login', $_SESSION)){
			$_SESSION['md5_login'] = md5( uniqid() );
		}

		if(isset($_GET['logout'])){
			session_destroy();
			header( "Location: ".URL );
			die();
		}
		

		
	}
	
	
	public function Render($m = "index", $view = "web"){
		unset($_SESSION["tmplt"]);
		require_once(MAIN.'/core/template.php' );
		$template = new Template($m, $view);
		$configuration = $template->RequireConfig($template->GetConfiguration());
		// var_dump($view);
		require_once(MAIN.'/core/loader.php');
		$loader = new Loader($configuration);
		$loaded = $loader->loaded;
		// var_dump($_SESSION);
		echo $template->Render($loaded);
	}
	
	
	/**
	 * Add error to the array errors.
	 *
	 * @return -
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function __call($method, $args) {
		return '';
	}
  
}
