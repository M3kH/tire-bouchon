<?php
/* 

Copyrights for code authored by Yahoo! Inc. is licensed under the following terms:
MIT License
Copyright (c) 2013 Yahoo! Inc. All Rights Reserved.
Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

origin: https://github.com/zordius/lightncandy 
*/

ini_set('allow_url_include', 1);

class LightnCandy {
    const FLAG_ERROR_LOG = 1;
    const FLAG_ERROR_EXCEPTION = 2;
    const FLAG_STANDALONE = 4;
    const FLAG_JSTRUE = 8;
    const FLAG_JSOBJECT = 16;
    const FLAG_THIS = 32;

    const FLAG_BESTPERFORMANCE = 0;
    const FLAG_HANDLEBARSJS = 56; // FLAG_JSTRUE + FLAG_JSOBJECT + FLAG_THIS 

    const TOKEN_SEARCH = '/(\\{{2,3})(.+?)(\\}{2,3})/s';

    private static $lastContext;

    public static function compile($template, $flags = self::FLAG_BESTPERFORMANCE) {
        $context = Array(
            'flags' => Array(
                'errorlog' => $flags & self::FLAG_ERROR_LOG,
                'exception' => $flags & self::FLAG_ERROR_EXCEPTION,
                'standalong' => $flags & self::FLAG_STANDALONE,
                'jstrue' => $flags & self::FLAG_JSTRUE,
                'jsobj' => $flags & self::FLAG_JSOBJECT,
                'this' => $flags & self::FLAG_THIS
            ),
            'level' => 0,
            'stack' => Array(),
            'error' => false,
            'useVar' => false,
            'vars' => Array(),
            'obj' => ($flags & self::FLAG_STANDALONE) ? '$' : 'LightnCandy::',
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
                'dot' => 0,
                'comment' => 0
            )
        );

        if (preg_match_all(self::TOKEN_SEARCH, $template, $tokens, PREG_SET_ORDER) > 0) {
        	// var_dump($tokens);
			// echo "\n\n\n";
            foreach ($tokens as $token) {
                self::scan($token, $context);
            }
        }

        if (self::_error($context)) {
            return false;
        }

        if (!$context['flags']['jsobj'] && (($context['usedFeature']['sec'] < 1) || !$context['flags']['jsobj'])) {
            $context['useVar'] = Array('$in');
        }

        $code = preg_replace_callback(self::TOKEN_SEARCH, function ($matches) use (&$context) {
            return '\'' . LightnCandy::tokens($matches, $context) . '\'';
        }, addcslashes($template, "'"));

        if (self::_error($context)) {
            return false;
        }

        $flagJStrue = self::_on($context['flags']['jstrue']);
        $flagJSObj = self::_on($context['flags']['jsobj']);

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

    protected static function _error($context) {
        self::$lastContext = $context;
        if ($context['error']) {
            if ($context['flags']['errorlog']) {
                error_log($context['error']);
            }
            if ($context['flags']['exception']) {
                throw new Exception($context['error']);
            }
            return true;
        }
        return false;
    }

    public static function getContext() {
        return self::$lastContext;
    }

    public static function getJsonSchema() {
        return self::$lastContext['jsonSchema'];
    }

    public static function getJsonSchemaString($indent = '  ') {
        $level = 0;
        return preg_replace_callback('/\\{|\\[|,|\\]|\\}|:/', function ($matches) use (&$level) {
            switch ($matches[0]) {
                case '}':
                case ']':
                    $level--;
                    $is = str_repeat('  ', $level);
                    return "\n$is{$matches[0]}";
                case ':':
                    return ': ';
            }
            $br = '';
            switch ($matches[0]) {
                case '{':
                case '[':
                    $level++;
                case ',':
                    $br = "\n";
            }
            $is = str_repeat('  ', $level);
            return "{$matches[0]}$br$is";
        }, json_encode(self::getJsonSchema()));
    }

    protected static function _on($v) {
        return ($v > 0) ? 'true' : 'false';
    }

    protected static function _true($v) {
        return ($v === true) ? 'true' : $v;
    }

    protected static function _scope($scopes) {
        return count($scopes) ? '[' . implode('][', $scopes) . ']' : '';
    }

    protected static function _qscope($list) {
        return self::_scope(array_map(function ($v) {return "'$v'";}, $list));
    }

    protected static function _v(&$v, $context) {
        $v = trim($v);
        if (($v == 'this') || $v == '.') {
            if ($context['flags']['this']) {
                $v = null;
            }
        }
    }

    protected static function _vn($vn) {
        return $vn ? self::_qscope(explode('.', $vn)) : '';
    }

    protected static function _vs($v) {
        if ($v == '.') {
            return Array('.');
        }
        return $v ? explode('.', $v) : null;
    }

    protected static function &_jsp(&$context) {
        $target = &$context['jsonSchema'];
        foreach ($context['vars'] as $var) {
            if ($var) {
                foreach ($var as $v) {
                    $target = &self::_jst($target, $v);
                }
            }
            $target = &self::_jst($target);
        }
        return $target;
    }

    protected static function _jsv(&$context, $var) {
        $target = &self::_jsp($context);
        foreach (self::_vs($var) as $v) {
            $target = &self::_jst($target, $v);
        }
        $target['type'] = Array('string', 'number');
        $target['required'] = true;
    }

    protected static function &_jst(&$target, $key = false) {
        if ($key) {
            if (!isset($target['properties'])) {
                $target['type'] = 'object';
                $target['properties'] = Array();
            }
            if (!isset($target['properties'][$key])) {
                $target['properties'][$key] = Array();
            }
            return $target['properties'][$key];
        } else {
            if (!isset($target['items'])) {
                $target['type'] = 'array';
                $target['items'] = Array();
            }
            return $target['items'];
        }
    }


    protected static function _jsv_details(&$context, $var, $value) {
        $target = &self::_jsp($context);
        // foreach (self::_vs($var) as $v) {
            // $target = &self::_jst_details($target, $v, $value);
        // }
    }

	protected static function &_jst_details(&$target, $key = false, $value = false) {
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
				
	            if (!isset($target['config'][$key])) {
	                $target['config'][$key] = $value;
	            }
				
        		return $target['config'][$key];
			}else{
				
	            if (!isset($target['config'][0])) {
	                $target['config'][0] = $value;
	            }
				
        		return $target['config'][0];
			}
        }else {
            if (!isset($target['items'])) {
                $target['type'] = 'array';
                $target['items'] = Array();
            }
            return $target['items'];
        }
    }

    protected static function scan($token, &$context) {
        $head = substr($token[2], 0, 1);
        $act = substr($token[2], 1);
        $raw = ($token[1] === '{{{');

        if (count($token[1]) !== count($token[3])) {
            $context['error'] = "Bad token {$token[1]}{$token[2]}{$token[3]} ! Do you mean {{}} or {{{}}}?";
            return;
        }

        if ($raw) {
            if (preg_match('/\\^|\\/|#/', $head)) {
                $context['error'] = "Bad token {$token[1]}{$token[2]}{$token[3]} ! Do you mean \{\{{$token[2]}\}\}?";
                return;
            }
        }
        switch ($head) {
        case '^':
            return $context['usedFeature']['isec'] ++;
        case '/':
            $context['level']--;
            return;
        case '#':
            $acts = explode(' ', $act);
            switch ($acts[0]) {
            case 'if':
                return $context['usedFeature']['if'] ++;
            case 'unless':
                return $context['usedFeature']['unless'] ++;
            case 'each':
                $context['level']++;
                return $context['usedFeature']['each'] ++;
            default:
                $context['level']++;
                return $context['usedFeature']['sec'] ++;
            }
        case '!':
            return $context['usedFeature']['comment'] ++;
        default:
            $fn = $raw ? 'raw' : 'enc';
            $context['usedFeature'][$fn] ++;
            $token[2] = trim($token[2]);
            switch ($token[2]) {
                case 'else':
                    return $context['usedFeature']['else'] ++;
                case 'this':
                    if ($context['level'] == 0) {
                        $context['usedFeature']['rootthis'] ++;
                    }
                    if (!$context['flags']['this']) {
                        $context['error'] = 'do not support {{this}}, you should do compile with LightnCandy::FLAG_THIS flag';
                    }
                    return $context['usedFeature']['this'] ++;
                case '.':
                    if ($context['level'] == 0) {
                        $context['usedFeature']['rootthis'] ++;
                    }
                    if (!$context['flags']['this']) {
                        $context['error'] = 'do not support {{.}}, you should do compile with LightnCandy::FLAG_THIS flag';
                    }
                    return $context['usedFeature']['dot'] ++;
            }
            if ($context['level'] == 0) {
                $context['usedFeature']['rootvar'] ++;
            }
        }
    }

    public static function tokens($token, &$context) {
        $head = substr($token[2], 0, 1);
        $act = substr($token[2], 1);
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
                    return ";}).";
                default:
                    $context['error'] = "Unexpect token: {$token[2]} !";
                    return;
                }
            }
        case '#':
            $each = 'false';
            $acts = explode(' ', $act);
            switch ($acts[0]) {
            case 'if':
                $context['stack'][] = 'if';
                self::_v($acts[1], $context);
                return ".({$context['obj']}ifvar('{$acts[1]}', \$in) ? (";
            case 'unless':
                $context['stack'][] = 'unless';
                self::_v($acts[1], $context);
                return ".(!{$context['obj']}ifvar('{$acts[1]}', \$in) ? (";
            case 'each':
                $each = 'true';
                $act = $acts[1];
            default:
                self::_v($act, $context);
                $context['level']++;
                $context['vars'][] = self::_vs($act);
                self::_jsp($context);
                $context['stack'][] = $act;
                $context['stack'][] = '#';
                return ".{$context['obj']}sec('$act', \$cx, \$in, $each, function(\$cx, \$in) {return ";
            }
        case '$':
            $acts = explode(' ', $act);
			// echo "\n\n\n\n\nACTS:\n";
			// var_dump($acts);
			// echo "\n\n\n\n\n\n";
            self::_v($token[2], $context);
			for($k=0; $k < count($acts); $k++ ){
				$_matches = array();
				// echo "\nact:\n";
				// var_dump($acts[$k]);
				// echo "\n\n";
            	$_var = preg_match('/([^"]*)="([^"]*)"/', $acts[$k], $_matches);
				// echo "\n\n\n\n\nMatches:\n";
				// var_dump($_matches);
				// echo "\n\n\n\n\n\n";
				// echo "\n\n\n\n\nACTS:\n";
				// var_dump($acts);
				// echo "\n\n\n\n\n\n";
//             
				for($i=0; $i < count($_matches); $i++ ){
					if($i == 0 || ($i%3 && ($i+2) < count($_matches) ) ){
						$c = $i+1;
						$d = $c+1;
		            	self::_jsv_details($context, "".$_matches[$c]."", "".$_matches[$d]."");
					}
				}
			}
            // self::_jsv_details($context, "".$_matches[1]."", "".$_matches[2]."");
			return '.';
            // }
        case '!':
            return '.';
        default:
            self::_v($token[2], $context);
            if ($token[2] ==='else') {
                $context['stack'][] = ':';
                return ') : (';
            }
			
			// Here we need to apply additional data in case of values inside the tag {{modulName 0="this" 1="other"}}
            self::_jsv($context, $token[2]);
			
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

    public static function ifvar($var, $in) {
        return (!is_null($in[$var]) && ($in[$var] !== false));
    }

    public static function raw($var, $cx, $in) {
        if (preg_match('/(.+?)\\.(.+)/', $var, $matched)) {
            if (array_key_exists($matched[1], $in)) {
                return self::raw($matched[2], $cx, $in[$matched[1]]);
            } else {
                return '';
            }
        }

        $v = ($var === '') ? $in : (is_array($in) ? $in[$var] : '');
        if ($v === true) {
            if ($cx['flags']['jstrue']) {
                return 'true';
            }
        } elseif (is_array($v)) {
            if ($cx['flags']['jsobj']) {
                if (count(array_diff_key($v, array_keys(array_keys($v)))) > 0) {
                    return '[object Object]';
                } else {
                    $ret = Array();
                    foreach ($v as $k => $vv) {
                        $ret[] = self::raw($k, $cx, $v);
                    }
                    return join(',', $ret);
                }
            }
        }
        return $v;
    }

    public static function enc($var, $cx, $in) {
        return htmlentities(self::raw($var, $cx, $in), ENT_QUOTES);
    }

    public static function sec($var, &$cx, $in, $each, $cb) {
        $v = ($var === '') ? $in : $in[$var];
        $loop = $each;
        if (is_array($v)) {
            $loop = (count(array_diff_key($v, array_keys(array_keys($v)))) == 0);
        }
        if ($loop) {
            $ret = Array();
            foreach ($v as $raw) {
                $ret[] = $cb($cx, $raw);
            }
            return join('', $ret);
        }
        if ($each) {
            return '';
        }
        if (is_array($v)) {
            return $cb($cx, $v);
        }
        if ($v === true) {
            return $cb($cx, $in);
        }
        if (is_string($v)) {
            return $cb($cx, Array());
        }
        if (!is_null($v) && ($v !== false)) {
            return $cb($cx, $v);
        }
        return '';
    }

    public static function isec($var, $in) {
        return !self::ifvar($var, $in);
    }

    public static function _prepare($php) {
        return include('data://text/plain,' . urlencode($php));
        // return eval(urlencode($php));
    }

    public static function _render($prepared, $data) {
        $func = include($prepared);
        return $func($data);
    }
    public static function __render($prepared, $data) {
        // $func = include($prepared);
        $in = $data;
        // $php = urldecode($prepared);
        $php = $prepared;
		$func = eval("function($in){".$php.";};");
		var_dump($func);
        return $func($data);
    }

    public static function __prepare($php) {
        // return include('data://text/plain,' . urlencode($php));
		// $php = substr( $php, 5, -2 );
        var_dump($php);
        // return urlencode($php);
        return $php;
    }
	

    public static function prepare($php) {
        // return include('data://text/plain,' . urlencode($php));
		// $php = substr( $php, 5, -2 );
        // var_dump($php);
		$tmpfname = tempnam("/tmp", "tmplt");
		$handle = fopen($tmpfname, "w");
		fwrite($handle, $php);
		fclose($handle);
		$funct = include $tmpfname;
		// fclose($temp);
        return $funct;
    }
	

    // public static function prepare($php) {
		// $file = include 'data://text/plain;base64,' . base64_encode($php);
        // return $file;
    // }
	

    public static function render($prepared, $data) {
    	$funct = include $prepared;
        return $funct($data);
    }
}
?>
