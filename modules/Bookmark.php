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


class WidgetBookmark {
	
	public $result = array();
	
	/**
	 * Initialization of the class.
	 *
	 * @return -
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function WidgetBookmark( ){
		
		if(isset($_SESSION['id_user'])){
			$this->result['user'] = true;
		}else{
			$this->result['user'] = false;
		}
		
		include_once (MAIN . '/ext/JSMin.php');
		$js = file_get_contents(MAIN. '/js/linkmanager.js');
		$minifiedCode = JSMin::minify($js);
		// var_dump($minifiedCode);
		// $minifiedCode = addslashes($minifiedCode);
		$link = "javascript:".rawurlencode($minifiedCode)."void(0);";
		$this->result["link"] = $link;
		// $this->result["script"] = $minifiedCode;
	}
  
}