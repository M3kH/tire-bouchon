<?php
class Loader {

	/**
	 * Initialization of the class.
	 *
	 * @return -
	 * @author Mauro Mandracchia <info@ideabile.com>
	 * @todo The loader need to controll the requirement of the template, after he need to
	 * satisfy them checking inside the libs/ dir and cheching for the function called "template"
	 *
	 * The function "template" it's called for default, but could be overwrited from the module configuration.
	 * just passing the attribute "function" inside the template.
	 *
	 * Note: Inside Api we need to create a function check trought the classes, and listing the public punctions.
	 * and hand an url and pass the parameters in a correct way.
	 *
	 */
	public $loaded = array();

	public function Loader($arr = array(), $opts = array()) {
		// var_dump($arr);
		$this -> loaded = $arr;

		if (is_array($arr) && count($arr) > 0) {
			foreach ($arr as $key => $value) {
				if ($key != "template" && $key != "require" && $key != "bb" && $key != "backbone" ) {
					if ($value != "placeholder") {
						// echo "\n\n\n LOADED: \n";
						// var_dump($key);
						// echo "\n\n\n";
						if($key == "url"){
							$this -> loaded[$key] = $arr[$key] = URL;
							// var_dump(URL);
						}else{
							$this -> loaded[$key] = $arr[$key] = $this -> IsModule($key, $value);
						}
					} else {
				
						$this -> loaded[$key] = "placeholder";
					}
				}
			}
			// echo "\n\n\n LOADED: \n";
			// var_dump($this -> loaded);
			// echo "\n\n\n";
		}
	}

	private function IsModule($key = '', $value = '') {
		// var_dump($key);
		// echo "\n\n";
		
		
		if(file_exists(MAIN.'/views/'.$key.".php")){
			$class = "View".$key;
			include_once(MAIN.'/views/'.$key.".php");
			$instance = new $class();
			return $instance->result;
		}else{
			return $value;
		}
		
		
	}

}
