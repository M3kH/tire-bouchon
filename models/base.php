<?php
/**
 * This is the interface for the class modules
 *
 * @category   Interface
 * @package    Tirebouchon Framework
 * @author     Mauro Mandracchia <info@ideabile.com>
 * @copyright  2013 - 2014 Ideabile
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    Release: 0.2a
 * @link       http://www.ideabile.com
 * @see        -
 * @since      -
 * @deprecated -
 */
require_once(MAIN."/models/interface.php");

class Base implements Model {

	public $db = "shell";
	public $table = "table";
	public $bean = null;
	public $errors = array();
	
	/**
	 * This function return all errors saved in array, and empty the same array.
	 *
	 * @return -
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function __construct (){
		R::selectDatabase($this->db);
		$this->bean = R::dispense($this->table);
	}
	
	
	/**
	 * Get all the element from the table.
	 *
	 * @return BOOLEAN
	 * @see Main (DB)
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function GetAll ( ){
		$r = R::getAll( "SELECT * FROM $this->table" );
		return $r;
	}
	
	/**
	 * Validating the data.
	 *
	 * @return BOOLEAN
	 * @see Main (DB)
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function Validate ( $data = array(), $expet = array() ){
		
		$result = TRUE;
		
		foreach( $expet as $k => $v ){
			// $k rapresent the data properties so
			// $k = "email" with values "required, not_empty, valid_email"
			// This call a function passing a value $this->checkRequired( $data['email'] );
			
			if( ( $key = array_search( "required", $data ) ) !== false) {
				if(!key_exists($k, $data)){
					$this->AddError(_("Hai ricordato di settare ")." $k ?");
					$result = FALSE;
				}
			    unset($v[$key]);
			}
			
			if( ( $key = array_search( "not_empty", $data ) ) !== false) {
				if(!is_empty($data[$k])){
					$this->AddError(_("Hai ricordato di compilare ")." $k ?");
					$result = FALSE;
				}
			    unset($v[$key]);
			}
			
			foreach ($v as $check) {
				switch ($check) {
					case 'email':
						if(!$this->CheckEmailFormat($data[$k])){
							$this->AddError(_("L'email inserita non sembra in un formato valido."));
							$result = FALSE;
						}
						break;
						
					case 'date':
						if(!$this->CheckDateFormat($data[$k])){
							$this->AddError(_("La data non è nel formato corretto."));
							$result = FALSE;
						}
						break;
						
					case 'date_time':
						if(!$this->CheckDateTimeFormat($data[$k])){
							$this->AddError(_("La data non è nel formato corretto."));
							$result = FALSE;
						}
						break;
					
					/*
					 * Here would passed to a additional checking in
					 * higer and lower then int
					 * higer and lower then date
					 */
					default:
						$c = explode(" ", $check);
						if( count($c) > 1 ){
							$codition = $c[0];
							$match = $c[1];
							
							if( $match != "NOW()" && !key_exists($match, $data) ){
								$this->AddError(_("Il parametro di confronto è mancate"));
								$result = FALSE;
								break;
							}
								
								
							if($this->CheckDateTimeFormat($data[$k])){
								$a = new DateTime($data[$k]);
								if( $match == "NOW()"){
									$b = new DateTime();
								}elseif( $this->CheckDateTimeFormat($data[$match]) ){
									$b = new DateTime($data[$match]);
								}else{
									$this->AddError(_("Le regole di confronto non sono state soddisfatte"));
									$result = FALSE;
								}
							}else{
								$a = $data[$k];
								$b = $data[$match];
							}
								
							// here I want rapresant just 4 matching
							// higer and lower then int
							// higer and lower then date
							switch( $condition ){
								case '>':
										if (!($a > $b)) { $this->AddError(_("Il valore")." $k "._("non è maggiore a ")." $match "); $result = FALSE; }
									break;
								case '<':
										if (!($a < $b)) { $this->AddError(_("Il valore")." $k "._("non è minore a ")." $match "); $result = FALSE; }
									break;
								case '>=':
										if (!($a >= $b)) { $this->AddError(_("Il valore")." $k "._("non è maggiore o uguale a ")." $match "); $result = FALSE; }
									break;
								case '<=':
										if (!($a >= $b)) { $this->AddError(_("Il valore")." $k "._("non è minore o uguale a ")." $match "); $result = FALSE; }
									break;
								default:
									$this->AddError(_("Condizione non prevista"));
									$result = FALSE;
									break;
							}
						}else{
							$this->AddError(_("Regola di checking non valida, controlla il codice."));
							$result = FALSE;
						}
						break;
				}
			}
		}
		
		return $result;
	}

	/**
	 * This function check if the email is write in the correct format.
	 *
	 * @return BOOLEAN
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function CheckDateTimeFormat( $date ){
		$dt = DateTime::createFromFormat("Y-m-d\TH:i:sO", $date);
		return $dt !== false && !array_sum($dt->getLastErrors());
	}

	/**
	 * This function check if the email is write in the correct format.
	 *
	 * @return BOOLEAN
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function CheckDateFormat( $date ){
		$dt = DateTime::createFromFormat("Y-m-d", $date);
		return $dt !== false && !array_sum($dt->getLastErrors());
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