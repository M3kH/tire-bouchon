<?php

/**
 * Postgres query management.
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
 
// @TODO Add the possibility when is GET the data to add pagination. (LIMIT 0 100)
// @TODO Add the possibility to GET the data by filters.


class DB {
	public $db;
	
	/**
	 * The costructor check if the configuration file is in the directory for start the sessio.
	 *
	 * @return -
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function DB( $shell = 'demo' ){
		include_once(MAIN."/core/configs/db.$shell.php");
		$shell = new Shell();
		$this->host = $shell->host;
		$this->port = $shell->port;
		$this->dbname = $shell->dbname;
		$this->user = $shell->user;
		$this->password = $shell->password;
	}
	
	/**
	 * This function make a query to the Main db with postgres.
	 *
	 * @return ['result'], ['type']
	 * @see pg_connect, pg_query_params, pgsql_error
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function Main( $query, $params = array() ){
		
		$this->db = new PDO('mysql:host=localhost;dbname=main;charset=utf8', 'root', 'root',
				  array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));		
				
		try {
		    //connect as appropriate as above
		    
			$stmt = $this->db->prepare($query);
			$stmt->execute($params);
			// $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		    return $stmt; //invalid query!
		    
		} catch(PDOException $ex) {
		    echo "An Error occured!"; //user friendly message
		    // some_logging_function($ex->getMessage());
			echo var_dump($query);																		
			echo var_dump($params);																		
		    echo $ex->getMessage();
			return false;
			exit;
		}				
	}
	
	
	/**
	 * This function make a query to the Shell db with postgres.
	 *
	 * @return ['result'], ['type']
	 * @see pg_connect, pg_query_params, pgsql_error
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function Shell( $query, $params = array() ){
		
			
		$host = $this->host;
		$port = $this->port;
		$dbname = $this->dbname;
		$user = $this->user;
		$password = $this->password;
		
		$this->db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password,
				  array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));		
				
		try {
		    //connect as appropriate as above
		    
			$stmt = $this->db->prepare($query);
			$stmt->execute($params);
			// $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		    return $stmt; //invalid query!
		    
		} catch(PDOException $ex) {
		    echo "An Error occured!"; //user friendly message
		    // some_logging_function($ex->getMessage());
			echo var_dump($query);																		
			echo var_dump($params);																		
		    echo $ex->getMessage();
			return false;
			exit;
		}				
	}

}
