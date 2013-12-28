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

require(MAIN.'/ext/lightncandy.ext.inc');

class Template {
	
	/**
	 * Initialization of the class.
	 *
	 * @return -
	 * @author Mauro Mandracchia <info@ideabile.com>
	 * 
	 * @TODO bisogna cambiare questo sistema, adesso accetta tre parametri, md5, module alters.
	 * 		 bisogna rimuovere md5 e mettere varaibles a posto di alerts, gli alerts sono dichiarati all'interno di un'array associativo.
	 */
	public function Template( $module = 'index', $view = 'web' ){
		
		if( key_exists( 'lang', $_SESSION ) ){
			$this->lang = $_SESSION['lang'];
		}else{
			$this->lang = 'it_IT';
		}
		
		$this->module = $module;
		$this->view = $view;
		
		$this->template_variables = array();
		if( !key_exists("tmplt", $_SESSION) ){
			$_SESSION['tmplt'] = array("opt"=>array());
		}
		
		$this->_LightnCandy = LightnCandyExt::compile($this->Get($module, $view));
		$this->_context = LightnCandyExt::getContext();
		// var_dump($this->_context);
		$this->_opts = LightnCandyExt::get_opts();
		// var_dump($this->_context);
		// echo "\n\n\n\n";
	}
	
	public function Render($arr = ''){
		if( $arr == ''){
			$cnf = $this->GetConfiguration();
			// $_SESSION['tmplt']['opt'] = array_merge_recursive($_SESSION['tmplt']['opt'], $cnf["options"]);
			$arr = $this->RequireConfig($cnf);
		}

		// $arr["md5"] = "madsdas";
		// $arr["title"] = "madsdas";
		// $arr["alerts"]["error"]["msg"] = "madsdas";
		// $arr["alerts"]["success"]["msg"] = "madsdas";
		
		$php = $this->_LightnCandy;
		$renderer = LightnCandyExt::prepare($php);
		// echo "PHPScript: \n\n $php \n\n\n";
		return $renderer($arr);
	}
	
	// @todo GetConfiguration change in #DECIDERE#
	public function GetConfiguration(){
		$lnc = $this->_context;
		$arr = $lnc["jsonSchema"]["properties"];
		
		if(key_exists("config", $lnc["jsonSchema"])){
			// echo "\n\nLNC:\n";
			// var_dump($lnc);
			// array_push($var)
			// $arr = array_merge($arr, $lnc["jsonSchema"]["config"]); 
			$arr["options"] = $lnc["jsonSchema"]["config"];
			// $_SESSION['tmplt']['opt'] = array_merge($_SESSION['tmplt']['opt'], $arr["options"]);
			// $_SESSION['tmplt']['opt'] = array_merge($_SESSION['tmplt']['opt'], $arr["options"]);
		}
		// var_dump($arr);
		return $arr;
	}
                                        
	// @todo RequireConfig change in #DECIDERE#
	public function RequireConfig($cnf, $arr = array(), $opts = array()){
		// echo "\n\n\n\nOptions:\n";
		// var_dump($cnf);

		// $_SESSION['tmplt']['opt'] = array_merge_recursive($_SESSION['tmplt']['opt'], $opts);
		if(count($opts) == 0 && key_exists("options", $cnf)){
			$opts = $cnf["options"];
			// echo "\n\n\n\nOptions:\n";
			// var_dump($opts);
		}
		
		foreach ( $cnf as $key => $value) {
			$module = $key;
			if(isset($cnf[$key]["type"])){
				
				if(!is_array($cnf[$key]["type"]) && $cnf[$key]["type"] == "array"){
						if($key != "template"){
							// var_dump($this->RequireConfig(array($cnf[$key]["items"])));
							$arr[$key] = $this->RequireConfig(array($cnf[$key]["items"]), $opts);
						}else{
							
						}
						
				}elseif(!is_array($cnf[$key]["type"]) && $cnf[$key]["type"] == "object"){

					if(key_exists("config", $cnf[$key])){
						$arr["config"] = $cnf[$key]["config"];
					}
                    if($module != "template" && is_array($cnf[$key]["properties"])){
						$arr["properties"] = $this->RequireConfig($cnf[$key]["properties"], $opts);
					}elseif($module === "template"){
							// $render_templates = $this->RenderTemplates($this->RequireConfig($cnf[$key]["properties"], $arr, $opts));
							$arr['template'] = $this->ReturnArrayTemplate($cnf[$key], $opts);
					}else{
						if(key_exists("config", $cnf[$key])){
							$arr['properties'] = $this->RequireConfig($cnf[$key]["properties"], $opts);
						}else{
							$arr = $this->RequireConfig($cnf[$key]["properties"], $opts);
						}
					}
					
				}elseif(is_array($cnf[$key]["type"])){
					
					switch ($module) {
						case 'require':
							$arr['require'] = $this->RequireJS($this->module);
							break;
							
						case 'template':
							// $render_templates = $this->RenderTemplates($this->RequireConfig($cnf[$key]["properties"], array(), $opts), $opts);
							$arr['template'] = $this->ReturnArrayTemplate($cnf[$key], $opts);
							break;
						
						default:
							$arr[$module]= " ";
							break;
					}
				}
			}elseif(isset($cnf[$key]["properties"])){
				// echo "Error!\n\n\n\n";
				
				if(is_array($cnf[$key]["config"])){
					$arr[$key]["config"] = $cnf[$key]["config"];
				}
			}else{
				// echo "Error!\n\n\n\n";
			}
		}
				// echo "\n\n\nCNF:\n";
		// var_dump($cnf);
				// echo "\n\n\n\n";
				
			// $_SESSION['tmplt']['opt'] = array_merge_recursive($_SESSION['tmplt']['opt'],$opts);
		return $arr;
	}

	private function ReturnArrayTemplate($arr = array(), $opts = array(), $path="", $new = array(), $cnfg = array()){
		// echo "\n\n\nCNF:\n";
		// var_dump($opts);
		// echo "\n\n\nCNF:\n";
		// $_SESSION['tmplt']['opt'] = array_merge_recursive($_SESSION['tmplt']['opt'], $opts);
		
		if(key_exists("properties", $arr)){
			foreach($arr["properties"] as $key => $value){
				if(key_exists("properties", $value)){
					$new[$key] = $this->ReturnArrayTemplate($value, $opts, $path.$key."/");
				}else{
					// Here is the good moment to renderize the c
					
					// $new[$key] = $path.$key;
					
					$src = $path.$key;
					$src = explode("/", $src);
					
					$module = '';
					$view = '';
					$_keyConfig = '';
					for($c=0; $c<count($src); $c++){
						if(count($src) > 1 && $c == 0){
							$view = $view.$src[$c];
						}else{
							$module = $module.'/'.$src[$c];
						}
						$_keyConfig .= $src[$c].".";
					}
					$_keyConfig = "template.".substr($_keyConfig, 0, -1);
					$tmpl = $this->Get($module, $view);
					// var_dump($_keyConfig);
					$template = LightnCandyExt::compile($tmpl);
					// var_dump($template);
					$renderer = LightnCandyExt::prepare($template);
					$context = LightnCandyExt::getContext();
					$_opts = LightnCandyExt::get_opts();
					$this->_opts = array_merge_recursive($this->_opts, $_opts);
					// echo "\n\n\n THIS IS Loaded internaly \n\n\n";
					// var_dump($this->_opts);
					if(key_exists("properties", $context["jsonSchema"])){
						if(count($opts) == 0 && key_exists("config", $context["jsonSchema"])){
							$cntx = $this->RequireConfig($context["jsonSchema"]["properties"], array(), $context["jsonSchema"]["config"]);
						}else{
							$cntx = $this->RequireConfig($context["jsonSchema"]["properties"], array(), $opts);
						}
						$cnfg = $this->Requirements($cntx, $opts);
						if(key_exists($_keyConfig, $opts)){
							// var_dump($cnfg);
							$cnfg = array_merge_recursive($cnfg, $opts[$_keyConfig]);
							
						}
						// var_dump($cnfg);
						// $_SESSION['tmplt']['opt'] = array_merge($_SESSION['tmplt']['opt'], $opts);
						// var_dump($opts);
						$new[$key] = $renderer($cnfg);
					}else{
						$new[$key] = $tmpl;
					}
					// echo "\n\n\n";
				}
			}
		}else{
			$new = $arr;
		}
		return $new;
	}
	
	private function Requirements($cntx, $opts){
		if(!isset($this->loader)){
			require_once(MAIN."/core/loader.php");
		}
		// $_SESSION['tmplt']['opt'] = array_merge_recursive($_SESSION['tmplt']['opt'], $opts);
		$this->loader = new Loader($cntx, $opts);
		return $this->loader->loaded;
	}
	
	// @todo cambiare in FetchHtml
	public function Get( $module, $view ){
		$lang = $this->lang;
		
		if( !isset($module) ){
			$module = $this->module;
		}
		if( !isset($view) ){
			$view = $this->view;
		}
		
		$module_file = MAIN.'/templates/' . $view . '/' . $module . '.' . $lang . '.html';
		
		if ($module != '' && file_exists($module_file) ) {
			$m = file_get_contents($module_file);
			// echo $m;
			return $m;
		}else{
			$module_file = MAIN.'/templates/' . $view . '/index.' . $lang . '.html';
			$m = file_get_contents($module_file);
			// echo $m;
			return $m;
		}
	}
	
	public function Values( ){
		$result = $this->template_variables;
		return $result;
	}

	private function RequireJS($module = '', $action = '') {
	
		$js_module = 'index';
		$js_view = $this->view;
	
		$module_file = MAIN.'/js/views/' . $js_view . '/' . $module . '.js';
		$action_file = MAIN.'/js/views/' . $js_view . '/' . $module . '.' . $action . '.js';
		
		if ($module != '' && $action == '') {
			if (file_exists($module_file)) {
				$js_module = $module;
			}
			
		} else if ($module != '' && $action != '') {
			if (file_exists($action_file)) {
				$js_module = $module . '.' . $action;
			} else if (file_exists($module_file)) {
				$js_module = $module;
			}
		}
		
		$require = "";
		$widgets = array();
		// if(key_exists("opt",$_SESSION["tmplt"]) && key_exists("requirejs",$_SESSION["tmplt"]["opt"])){
		if(isset($this->_opts) && key_exists("requirejs",$this->_opts)){
			// foreach ($_SESSION["tmplt"]["opt"]["requirejs"]["widgets"] as $key => $value) {
				// var_dump($this->_opts["requirejs"]["widgets"] );
			foreach ($this->_opts["requirejs"]["widgets"] as $key => $value) {
				if(is_array($value)){
					$require .= "require(['widget/$key'], function($key){
									if(typeof $key != \"undefined\"){
										console.log($key);
									}
								});
";
					$widgets[] = array("name" => $key, "file" => $key);
				}else{
					
					$require .= "require(['widget/$key'], function($value){
									if(typeof $value != \"undefined\"){
										
									}
								});
";
					$widgets[] = array("name" => $value, "file" => $key);
				}
			}
			
			// require(['text!templates/'+this.template+'.mustache'], function(template){
			// });
		}
		$base = JS;
		$widgets = json_encode($widgets);
		if($this->view != "web"){$path="../";}else{$path="";} 
		$html = "
		<script data-main=\"".$path."js/main\"  src=\"".$path."js/require.js\"></script>
		<script>
		define('global', {
			view: '$js_view',
			module: '$js_module',
			widgets: $widgets,
			baseUrl: '$base',
			init: function(){
			}
		});
		</script>
		";
	
		return $html;
	
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
