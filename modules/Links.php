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


class WidgetLinks {
	
	public $result = array();
	
	/**
	 * Initialization of the class.
	 *
	 * @return -
	 * @author Mauro Mandracchia <info@ideabile.com>
	 */
	public function WidgetLinks( ){
		$arr = array();
		include_once (MAIN . '/libs/Links.php');
		$links = new Links();
		
		$arr = $links -> Get();
		$new_arr = array();
		$last_day = '';
		$i = -1;
		
		foreach($arr as $k => $v){
			$day = $v['day'];
			if($day != $last_day){
				$i++;
				$last_day = $day;
				$new_arr[$i] = array('day' => $day, 'elems' => array());
			}
			
			$new_arr[$i]['elems'][] = $v;
			
		}
		
		$this->result = array("days" => $new_arr);
	}
  
}