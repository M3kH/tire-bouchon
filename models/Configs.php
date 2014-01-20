<?php
class Configs {

	/**
	 * Initialization of the class.
	 *
	 * @return -
	 * @author Massimiliano Scifo
	 <massimilianoscifo@gmail.com>
	 */
	public function Configs() {
		require_once (MAIN.'/core/db.php');

		$this -> db = new DB();
		$this -> errors = array();
	}

	public function InsertConfig($key = '', $value = '', $type = '', $status = 1, $overwrite = 0) {
			
		if (count($_POST)) {

			foreach ($_POST as $k => $v) {
				$keyArray = (string)$k;
				switch($keyArray) {
					case 'key' :
						$key = $v;
						break;
					case 'value' :
						$value = $v;
						break;
					case 'type' :
						$type = $v;
						break;
					case 'status' :
						 $status = $v;
						 break;
					 case 'overwrite' :
						 $overwrite = $v;
						 break;
				}
			}
			if (!empty($key) && !empty($value) && !empty($type)) {

				$query = $this -> db -> Main("INSERT INTO mglobal_config(key, value, type, status, overwrite) VALUES ($1,$2,$3,$4,$5) RETURNING id", array($key, $value, $type, $status, $overwrite));
				if ($query) {
					$result = $query->fetch(PDO::FETCH_ASSOC);
					if ($result) {
						return $result;
					} else {
						return array();
					}
				} else {
					return array();
				}
				echo "test";
			}

		}
	}
	
	public function GetAll()
	{
		$query = $this->db->Main("SELECT id, key, value, type, status, overwrite FROM mglobal_config");
		
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
	}

}
