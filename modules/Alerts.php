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


class WidgetAlerts {
	
	public $result = array();
	
	/**
	 * Initialization of the class.
	 *
	 * @return -
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function WidgetAlerts( ){
		
		$alerts = array();
		if (isset($_GET['activate'])) {
			require_once (MAIN . '/libs/Login.php');
			$login = new Login();
			$act = $login -> ActiveUser($_GET['activate']);
			// var_dump($act);
			if ($act) {
				$alerts['success']['msg'] = _("<strong>Complimenti!</strong> L'utente è stato attivato.");
			} else {
				$alerts['error']['msg'] = _("<strong>Attenzione!</strong> Sei sicuro dell'url che hai consultato?");
			}
		}
		
		$this->result = $alerts;
	}
  
}