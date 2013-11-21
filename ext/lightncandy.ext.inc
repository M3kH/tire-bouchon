<?php
/* 

Copyrights for code authored by Yahoo! Inc. is licensed under the following terms:
MIT License
Copyright (c) 2013 Yahoo! Inc. All Rights Reserved.
Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

Origin: https://github.com/zordius/lightncandy 
*/

/**
 * This is abstract engine which defines must-have methods.
 *
 * @package    LightnCandy
 * @subpackage Core
 * @author     Zordius <zordius@yahoo-inc.com>
 */

require(MAIN.'/ext/lightncandy.inc');

/**
 * LightnCandy static core class.
 */
class LightnCandyExt extends LightnCandy {
	
	/**
     * Compile handlebars template into PHP code
     *
     * @param string $template handlebars template string
     * @param integer $flags LightnCandy compile time and run time flags, default is LightnCandy::FLAG_BESTPERFORMANCE
     *
     * @return string Compiled PHP code when successed. If error happened and compile failed, return false.
     */
    // PHP performance flags
    private static $opts;
    
    public static function compile($template, $flags = self::FLAG_BESTPERFORMANCE) {
    	// Declare this opts as array
    	self::$opts = array();
		
        $context = Array(
            'flags' => Array(
                'errorlog' => $flags & self::FLAG_ERROR_LOG,
                'exception' => $flags & self::FLAG_ERROR_EXCEPTION,
                'standalong' => $flags & self::FLAG_STANDALONE,
                'jstrue' => $flags & self::FLAG_JSTRUE,
                'jsobj' => $flags & self::FLAG_JSOBJECT,
                'this' => $flags & self::FLAG_THIS,
                'with' => $flags & self::FLAG_WITH,
            ),
            'level' => 0,
            'stack' => Array(),
            'error' => false,
            'useVar' => false,
            'vars' => Array(),
            'obj' => ($flags & self::FLAG_STANDALONE) ? '$' : 'LCRun::',
            'jsonSchema' => Array(
                '$schema' => 'http://json-schema.org/draft-03/schema',
                'description' => 'Template Json Schema'
            ),
            'usedFeature' => Array(
                'rootvar' => 0,
                'rootthis' => 0,
                'enc' => 0,
                'raw' => 0,
                'sec' => 0,
                'isec' => 0,
                'if' => 0,
                'else' => 0,
                'unless' => 0,
                'each' => 0,
                'this' => 0,
                'with' => 0,
                'dot' => 0,
                'comment' => 0
            )
        );

        // Do first time scan to find out used feature, detect template error.
        if (preg_match_all(self::TOKEN_SEARCH, $template, $tokens, PREG_SET_ORDER) > 0) {
            foreach ($tokens as $token) {
                self::scan($token, $context);
            }
        }

        if (self::_error($context)) {
            return false;
        }

        // Check used features and compile flags. If the template is simple enough,
        // we can generate best performance code with enable 'useVar' internal flag.
        if (!$context['flags']['jsobj'] && (($context['usedFeature']['sec'] < 1) || !$context['flags']['jsobj'])) {
            $context['useVar'] = Array('$in');
        }

        // Do PHP code and json schema generation.
        $code = preg_replace_callback(self::TOKEN_SEARCH, function ($matches) use (&$context) {
            return '\'' . LightnCandyExt::tokens($matches, $context) . '\'';
        }, addcslashes($template, "'"));

        if (self::_error($context)) {
            return false;
        }

        $flagJStrue = self::_on($context['flags']['jstrue']);
        $flagJSObj = self::_on($context['flags']['jsobj']);

        // return generated PHP code string
        return "<?php return function (\$in) {
    \$cx = Array(
        'flags' => Array(
            'jstrue' => $flagJStrue,
            'jsobj' => $flagJSObj
        ),
        'path' => Array(),
        'parents' => Array()
    );
    return '$code';
}
?>";
    }
     
	// This is how to get the options
    public static function get_opts( ) {
		return self::$opts;
	}
	
	protected static function dotResolving($a = array(), $path, $default = null) {
	  $current = $a;
	  $p = strtok($path, '.');
	
	  while ($p !== false) {
	    if (!isset($current[$p])) {
	      return $default;
	    }
	    $current = $current[$p];
	    $p = strtok('.');
	  }
	
	  return $current;
	}
	
	protected static function set_opt(&$array_ptr, $key, $value, $k = FALSE){

	  $keys = explode('.', $key);
	
	  // extract the last key
	  $last_key = array_pop($keys);
	
	  // walk/build the array to the specified key
	  while ($arr_key = array_shift($keys)) {
	    if ($array_ptr && !array_key_exists($arr_key, $array_ptr)) {
	      $array_ptr[$arr_key] = array();
	    }
	    $array_ptr = &$array_ptr[$arr_key];
	  }
	
	  // set the final key
	  if($k != FALSE){
		  $array_ptr[$last_key][$k] = $value;
	  }else{
		  $array_ptr[$last_key] = $value;
	  }
	  
	}
	

    protected static function _jsv_details(&$context, $var, $value, $act) {
        $target = &self::_jsp($context);
        // foreach (self::_vs($var) as $v) {
        self::_jst_details($target, $var, $value, $act);
        // }
    }

	protected static function &_jst_details(&$target, $key = false, $value = false, $act = false) {
        if ($key === false && $value === false) {
            if (!isset($target['properties'])) {
                $target['type'] = 'object';
                $target['properties'] = Array();
            }
            if (!isset($target['properties'][$key])) {
                $target['properties'][$key] = Array();
            }
            return $target['properties'][$key];
        } elseif($value !== false){
            if (!isset($target['config'])) {
                $target['config'] = Array();
            }
			if($key !== false){
				// var_dump($key);
	            // if (!isset($target['config'][$key])) {
	            	if($act != FALSE){
	            		
						
							// var_dump($key);
						if(strpos($key, ".") == FALSE){
							
							// Here is the trick save the action with the value;
							$target['config'][$act][$key] = $value;
							
							// $target['config'][$act] =  array_merge($target['config'][$act], array($key => $value));	
							// $taget['config'][$act] = array_push($taget['config'][$act])
						}else{
		            		$acts = array();
		            		self::set_opt($acts[$act], $key, $value);
							
							// Here is the config overwriting
							$target['config'] = array_replace_recursive($target['config'], $acts);
						}
		                // $target['config'][$key] = $value;
	            	}else{
							// var_dump($key);
						if(strpos($key, ".") == FALSE){
							
							// Here I set the config
							$target['config'][$key] = $value;
							// $target['config'][$act] =  array_merge($target['config'][$act], array($key => $value));	
							// $taget['config'][$act] = array_push($taget['config'][$act])
						}else{
		            		$acts = array();
		            		self::set_opt($acts, $key, $value);
							
							//Here I set the config
							$target['config'] = array_replace_recursive($target['config'], $acts);
						}
					}
					// Here I write it in session
					// Why I use session? Can we change it in a private variable?
					// array_replace_recursive is a simple extension of the array in this cas $target['config']
					// $_SESSION['tmplt']['opt'] = array_replace_recursive($_SESSION['tmplt']['opt'], $target['config']);
					
					self::$opts = array_replace_recursive(self::$opts, $target['config']);
					// var_dump($this->opts);
					$_SESSION['tmplt']['opt'] = array_replace_recursive(self::$opts, $target['config']);
					// $_SESSION['tmplt']['opt'] = array_replace_recursive($_SESSION['tmplt']['opt'], $target['config']);
	            // }
				
				// $lastElem = count($target['config']);
        		// return $target['config'][$lastElem];
			}else{
				
	            if (!isset($target['config'][0])) {
	                $target['config'][0] = $value;
	            }
				
        		// return $target['config'][0];
			}
        }else {
            if (!isset($target['items'])) {
                $target['type'] = 'array';
                $target['items'] = Array();
            }
        }
		$arr = array();
        return $arr;
		
		// var_dump($target);
    }

    /**
     * Internal method used by compile(). Return compiled PHP code partial for a handlebars token.
     *
     * @param string $token detected handlebars {{ }} token
     * @param string $context current scaning context
     */
    public static function tokens($token, &$context) {
        $head = substr($token[2], 0, 1);
        $act = substr($token[2], 1);
		$org_token = false;
		
		if (preg_match('/[^a-z0-9 ]"/i', $act) && $head != "^" && $head != "/" && $head != "#" && $head != "\$" && $head != "!"){

	        $acts = explode(' ', $token[2]);
			switch ($acts[0]) {
				case 'if': case 'unless': case 'each': case 'with': case 'else':
					break;
				
				default:
					$act = $acts[0];
					$org_token = $token[2];
					$token[2] = $act;
					break;
			}
		}
		
        $raw = ($token[1] === '{{{');

        switch ($head) {
        case '^':
            $context['stack'][] = $act;
            $context['stack'][] = '^';
            if ($context['useVar']) {
                $v = end($context['useVar']) . "['{$act}']";
                return ".((is_null($v) && ($v !== false)) ? ("; 
            } else {
                return ".({$context['obj']}isec('$act', \$in) ? (";
            }
        case '/':
            $each = false;
            switch ($act) {
            case 'if':
            case 'unless':
                $pop = array_pop($context['stack']);
                if ($pop == ':') {
                    $pop = array_pop($context['stack']);
                    return ')).';
                }
                return ') : \'\').';
            case 'with':
                $pop = array_pop($context['stack']);
                if ($pop !== 'with') {
                   $context['error'] = 'Unexpect token /with !';
                   return;
                }
                return ';}).';
            case 'each':
                $each = true;
            default:
                $context['level']--;
                array_pop($context['vars']);
                $pop = array_pop($context['stack']);
                switch($pop) {
                case '#':
                case '^':
                    $pop2 = array_pop($context['stack']);
                    if (!$each && ($pop2 !== $act)) {
                        $context['error'] = "Unexpect token {$token[2]} ! Previous token $pop$pop2 is not closed";
                        return;
                    }
                    if ($pop == '^') {
                        return ") : '').";
                    }
                    return ';}).';
                default:
                    $context['error'] = "Unexpect token: {$token[2]} !";
                    return;
                }
            }
        case '#':
            $each = 'false';
            $acts = explode(' ', $act);
            switch ($acts[0]) {
            case 'with':
                $context['stack'][] = 'with';
                self::_vx($acts[1], $context);
                return ".{$context['obj']}wi('{$acts[1]}', \$cx, \$in, function(\$cx, \$in) {return ";
            case 'if':
                $context['stack'][] = 'if';
                self::_vx($acts[1], $context);
                return ".({$context['obj']}ifvar('{$acts[1]}', \$in) ? (";
            case 'unless':
                $context['stack'][] = 'unless';
                self::_vx($acts[1], $context);
                return ".(!{$context['obj']}ifvar('{$acts[1]}', \$in) ? (";
            case 'each':
                $each = 'true';
                $act = $acts[1];
            default:
                self::_vx($act, $context);
                $context['level'] ++;
                $context['vars'][] = self::_vs($act);
                self::_jsp($context);
                $context['stack'][] = $act;
                $context['stack'][] = '#';
                return ".{$context['obj']}sec('$act', \$cx, \$in, $each, function(\$cx, \$in) {return ";
            }
        case '!':
            return '.';
        default:
            self::_vx($token[2], $context);
            if ($token[2] ==='else') {
                $context['stack'][] = ':';
                return ') : (';
            }
            self::_jsv($context, $token[2]);
		
			if( $org_token ){
					
		        // $acts = explode(' ', $org_token);
				// var_dump($org_token);
				// var_dump($acts);
				$_matches = array();
	        	// $_var = preg_match('/([^"]*)="([^"]*)"/', $acts[$k], $_matches);
	        	// test="Title with space"
				// $re =;
				// $pat_attributes = "(\S+)=(\"|')(.*?)(\"|')";
	        	// $_var = preg_match_all("@$pat_attributes@is", $org_token, $_matches);
				// $_var = preg_match('/([^"]*)(\\=")(.+?)(\\")/s', $acts[$k], $_matches);
				$_var = preg_match_all('/([a-zA-Z0-9\.]*)="([^"]*)"/', $org_token, $_matches);
	        	// var_dump($_matches);
	        	// var_dump($acts);
				// for($i=0; $i < count($_matches); $i++ ){
					// if($i == 0 || ($i%3 && ($i+2) < count($_matches[]) ) ){
						// $c = $i+1;
						// $d = $c+1;
						// self::_jsv_details($context, "".$_matches[$c]."", "".$_matches[$d]."", $acts[0]);
					// }
				// }
				for($i=0; $i < count($_matches[0]); $i++ ){
					// if($i == 0 || ($i%3 && ($i+2) < count($_matches[]) ) ){
						// $c = $i+1;
						// $d = $c+1;
						self::_jsv_details($context, $_matches[1][$i], $_matches[2][$i], $acts[0]);
					// }
				}
				// for($k=0; $k < count($acts); $k++ ){
				// }
			}
			
            $fn = $raw ? 'raw' : 'enc';
            if ($context['useVar']) {
                $v = end($context['useVar']) . self::_vn($token[2]);
                if ($context['flags']['jstrue']) {
                    return $raw ? ".(($v === true) ? 'true' : $v)." : ".(($v === true) ? 'true' : htmlentities($v, ENT_QUOTES)).";
                } else {
                    return $raw ? ".$v." : ".htmlentities($v, ENT_QUOTES).";
                }
            } else {
                return ".{$context['obj']}{$fn}('{$token[2]}', \$cx, \$in).";
            }
        }
    }
}


