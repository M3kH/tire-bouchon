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


class WidgetDocs {
	
	public $result = array();
	
	/**
	 * Initialization of the class.
	 *
	 * @return -
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function WidgetDocs( ){
		
		if (!isset($this -> ide)) {
			include_once (MAIN . '/libs/Ide.php');
			$this -> ide = new Ide();
		}
		$arr = array();
		
		if(isset($_GET['file'])){
			$file = $_GET['file'];
		}else{
			$file = "index.php";
		}
		
		$renderDir =  $this->RenderDir($this -> ide -> GetDir($dir));
		$arr["dir"] = $renderDir;
		$arr["file"] = $this -> ide -> GetFile($file);
		
		$this->result = $arr;
		// var_dump($this->result);
	}
	
	private function RenderDir($arr = array(), $dir = ""){
		// var_dump($dir);
		$html = "<ul>";
		foreach ($arr as $k => $v) {
			if(is_array($v)){
				if( $dir == ""){
					$d == "/";
				}else{
					$d = $dir;
				}
				
				$html .= "<li><a href=\"#\"><i class=\"fa fa-folder\"></i> {$k}</a>";
				$html .= $this->RenderDir($v, $d.$k."/");
				$html .= "</li>";
			}else{
				$html .= "<li><a href=\"?file={$dir}{$v}\" class=\"file\"><i class=\"fa fa-file\"></i> {$v}</a></li>";
			}
		}
		$html .= "</ul>";
		return $html;
	}
  
}