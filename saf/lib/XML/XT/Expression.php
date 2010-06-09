<?php
//
// +----------------------------------------------------------------------+
// | Sitellite Content Management System                                  |
// +----------------------------------------------------------------------+
// | Copyright (c) 2010 Sitellite.org Community                           |
// +----------------------------------------------------------------------+
// | This software is released under the GNU GPL License.                 |
// | Please see the accompanying file docs/LICENSE for licensing details. |
// |                                                                      |
// | You should have received a copy of the GNU GPL License               |
// | along with this program; if not, visit www.sitellite.org.            |
// | The license text is also available at the following web site         |
// | address: <http://www.sitellite.org/index/license                     |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <john.luxford@gmail.com>                       |
// +----------------------------------------------------------------------+
//
// XTExpression (XTE) is the expression syntax to the XT XML-based
// template engine.
//

loader_import ('saf.Parser');

/**
	 * XTExpression (XTE) is the expression syntax to the XT XML-based
	 * template engine.
	 * 
	 * @package	XML
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.0, 2003-01-15, $Id: Expression.php,v 1.6 2008/03/09 18:46:06 lux Exp $
	 * @access	public
	 * 
	 */
class XTExpression extends Parser {
	

	/**
	 * The types of expressions available in XTE.  Currently
	 * these are 'path', 'string', and 'php', where a path is a
	 * filesystem-like reference to objects and their properties and
	 * methods, a string is a literal string (with the ability to
	 * embed sub-expressions within them via a ${sub-expr} syntax),
	 * and php expressions are PHP code with a few modifications
	 * for brevity and for avoiding the > character.  These
	 * differences are documented elsewhere in this document.
	 * 
	 * @access	private
	 * 
	 */
	var $types = array (
		'path',
		'string',
		'php',
	);

	/**
	 * This is the internal variable register.
	 * 
	 * @access	private
	 * 
	 */
	var $register = array (
		'nothing' => false,
		'default' => false, // not used

		'loop' => false,
		'repeat' => false, // alias of loop

		'attrs' => false, // depends on the node sent to evaluate()

		'session' => false,
		'user' => false, // alias of session

		'cgi' => false,
		'request' => false, // alias of cgi

		'object' => false,
		'here' => false, // alias of object

		'result' => false, // from <xt:sql>
		'block' => false, // from <xt:block>/<xt:show>
	);

	//var $register = array ();
	var $mode = 'normal';
	var $out = '';
	var $action = 'none'; // can be 'none', 'buildingPath', 'buildingString', 'buildingPHP'
	var $buffer = array ();
	var $outputMode = 'path';
	var $defaultMode = 'path';
	var $previousMode = 'string';

	

	/**
	 * Constructor method.  $object is used to set a default
	 * object within the register that takes precedence (as does its
	 * properties and methods, should they share a name with another
	 * register object) over other objects in the register.
	 * 
	 * @access	public
	 * @param	object
	 * 
	 */
	function XTExpression ($object) {
		global $cgi, $session;

		$this->register['object'] =& $object;
		$this->register['here'] =& $this->register['object'];

		$this->register['cgi'] =& $cgi;
		$this->register['request'] =& $this->register['cgi'];

		$this->register['session'] =& $session;
		$this->register['user'] =& $this->register['session'];

		$this->register['repeat'] =& $this->register['loop'];

		$this->addInternal ('_pathOpen', 'path:');
		$this->addInternal ('_stringOpen', 'string:');
		$this->addInternal ('_phpOpen', 'php:');
		//$this->addInternal ('_space', ' ');

		$this->addInternal ('_innerPath', '${');
		$this->addInternal ('_endInnerPath', '}');

		$this->addInternal ('_escape', '\\');
		$this->addInternal ('_else', '|');
		//$this->addInternal ('_separator', ';');
		//$this->addInternal ('_slash', '/');
	}

	/**
	 * Sets the specified register entry to the provided object.
	 *
	 * @param object
	 * @param string
	 *
	 */
	function setObject (&$obj, $name = 'object') {
		$this->register[$name] =& $obj;
	}

	/**
	 * Unsets the specified register entry.
	 *
	 * @param string
	 *
	 */
	function unsetObject ($name = 'object') {
		$this->register[$name] = false;
	}

	/**
	 * Registers the specified name into the register, using a global object of the
	 * same name as the register value.
	 *
	 * @param string
	 *
	 */
	function register ($name) {
		if (! isset ($this->register[$name]) && isset ($GLOBALS[$name])) {
			$this->register[$name] =& $GLOBALS[$name];
		}
	}

	/**
	 * Erases everything from the register.
	 *
	 */
	function resetRegister () {
		$this->register = array ();
	}

	/**
	 * Retrieves the specified object from the register.
	 *
	 * @param string
	 * @return object
	 *
	 */
	function &getObject ($name = 'object') {
		return $this->register[$name];
	}

	/**
	 * Sets the loop register entry to the provided object.
	 *
	 * @param object
	 * @param string
	 * @param integer
	 * @param integer
	 *
	 */
	function setCurrent (&$obj, $name, $index, $total) {
		//echo '<pre>setCurrent ("' . "\t" . $obj . "\t" . '", "' . "\t" . $name . "\t" . '", "' . "\t" . $index . "\t" . '")</pre>';
		if (is_object ($obj) && count (get_object_vars ($obj)) > 0) {
			$this->register['loop'][$name] =& $obj;
			$this->register['loop'][$name]->index = $index;
			$this->register['loop'][$name]->number = $index + 1;
			$this->register['loop'][$name]->length = $total;
			if ($index == 0 || $index % 2 == 0) {
				$this->register['loop'][$name]->even = true;
				$this->register['loop'][$name]->odd = false;
			} else {
				$this->register['loop'][$name]->even = false;
				$this->register['loop'][$name]->odd = true;
			}
			if ($index == 0) {
				$this->register['loop'][$name]->start = true;
				$this->register['loop'][$name]->end = false;
			} elseif ($index == $total) {
				$this->register['loop'][$name]->start = false;
				$this->register['loop'][$name]->end = true;
			} else {
				$this->register['loop'][$name]->start = false;
				$this->register['loop'][$name]->end = false;
			}
		} elseif (is_array ($obj)) {
			$this->register['loop'][$name] =& $obj;
			$this->register['loop'][$name]['index'] = $index;
			$this->register['loop'][$name]['number'] = $index + 1;
			$this->register['loop'][$name]['length'] = $total;
			if ($index == 0 || $index % 2 == 0) {
				$this->register['loop'][$name]['even'] = true;
				$this->register['loop'][$name]['odd'] = false;
			} else {
				$this->register['loop'][$name]['even'] = false;
				$this->register['loop'][$name]['odd'] = true;
			}
			if ($index == 0) {
				$this->register['loop'][$name]['start'] = true;
				$this->register['loop'][$name]['end'] = false;
			} elseif ($index == $total) {
				$this->register['loop'][$name]['start'] = false;
				$this->register['loop'][$name]['end'] = true;
			} else {
				$this->register['loop'][$name]['start'] = false;
				$this->register['loop'][$name]['end'] = false;
			}
		} else {
			$this->register['loop'][$name] = new StdClass;
			$this->register['loop'][$name]->value = $obj;
			$this->register['loop'][$name]->index = $index;
			$this->register['loop'][$name]->number = $index + 1;
			$this->register['loop'][$name]->length = $total;
			if ($index == 0 || $index % 2 == 0) {
				$this->register['loop'][$name]->even = true;
				$this->register['loop'][$name]->odd = false;
			} else {
				$this->register['loop'][$name]->even = false;
				$this->register['loop'][$name]->odd = true;
			}
			if ($index == 0) {
				$this->register['loop'][$name]->start = true;
				$this->register['loop'][$name]->end = false;
			} elseif ($index == $total) {
				$this->register['loop'][$name]->start = false;
				$this->register['loop'][$name]->end = true;
			} else {
				$this->register['loop'][$name]->start = false;
				$this->register['loop'][$name]->end = false;
			}
		}
	}

	/**
	 * Splits an assignment string by the first space character encountered.
	 *
	 * @param string
	 * @return array
	 *
	 */
	function splitAssignment ($string) {
		if (preg_match ('/^([a-zA-Z0-9_-]+) +/', $string, $regs)) {
			return array ($regs[1], str_replace ($regs[0], '', $string));
		} else {
			return false;
		}
	}

	/**
	 * Splits a statement into multiple expressions by any semi-colons encountered.
	 *
	 * @param string
	 * @return array
	 *
	 */
	function splitStatement ($string) {
		$pieces = preg_split ('/([^\\\]); */', $string, -1, PREG_SPLIT_DELIM_CAPTURE);
		$newpieces = array ();
		foreach ($pieces as $key => $value) {
			if ($key == 0 || $key % 2 == 0) {
				$newpieces[] = $value;
			} else {
				$newpieces[count ($newpieces) - 1] .= $value;
			}
		}
		return $newpieces;
	}

	/**
	 * This is the function that interprets path expressions and returns the values
	 * that they refer to.
	 *
	 * @param string
	 * @return mixed
	 *
	 */
	function &getPath ($path) {
		//echo '<h4>' . $path . '</h4>';
		//print_r ($this->register['c']);
		if ($path == 'nothing') {
			return false;
		}
		$elements = explode ('/', $path);
		$first = array_shift ($elements);

		if ($first == 'object') {
			//echo '<pre>registering "' . "\t" . 'object' . "\t" . '" as $current, first element "object" was shifted</pre>';
			$current =& $this->register['object'];
		} elseif (is_object ($this->register['object']) &&
			(isset ($this->register['object']->{$first}) || method_exists ($this->register['object'], $first))) {
			//echo '<pre>registering "' . "\t" . 'object' . "\t" . '" as $current, first element is method "' . $first . '"</pre>';
			array_unshift ($elements, $first);
			$current =& $this->register['object'];
		} elseif (is_array ($this->register['object']) && isset ($this->register['object'][$first])) {
			//echo '<pre>registering "' . "\t" . 'object' . "\t" . '" as $current, first element is "' . $first . '"</pre>';
			array_unshift ($elements, $first);
			$current =& $this->register['object'];
		} elseif (isset ($this->register[$first])) {
			//echo '<pre>registering "' . "\t" . $first . "\t" . '" as $current, found in register</pre>';
			$current =& $this->register[$first];
		} elseif (is_object ($GLOBALS[$first]) || is_array ($GLOBALS[$first])) {
			//echo '<pre>registering "' . "\t" . $first . "\t" . '" as $current, found in global and added to register</pre>';
			$this->register[$first] = $GLOBALS[$first];
			$current =& $this->register[$first];
		} else {
			//echo '<pre>huh? "' . "\t" . $first . "\t" . '"</pre>';
			//echo '<pre>';
			//print_r ($this->register['loop']);
			//print_r ($this->register['repeat']);
			//echo '</pre>';
		}

		foreach ($elements as $k => $e) {
			if (is_object ($current) && isset ($current->{$e})) {
				// set, onto next
				//echo 'setting new current to object "' . $e . '"<br />';
				$foo =& $current;
				unset ($current);
				$current =& $foo->{$e};
			} elseif (is_array ($current) && isset ($current[$e])) {
				// set, onto next
				//echo '<pre>current is array, setting new current to "' . "\t" . $e . "\t" . '"</pre>';
				$foo =& $current;
				unset ($current);
				$current =& $foo[$e];
			} elseif ($k == count ($elements) - 1 && ! is_object ($current) && ! is_array ($current)) {
				//echo 'current is good, returning as is<br />';
				return $current;
			} elseif ($k == count ($elements) - 1 && is_object ($current) && method_exists ($current, $e)) {
				//echo 'current is method, returning output<br />';
				return $current->{$e} ();
			} else {
				//echo '<pre>not found: "' . "\t" . $e . "\t" . '"</pre>';
				return false;
			}
		}
		if (! is_object ($current) && ! is_array ($current) && isset ($current)) {
			return $current;
		}
		//echo 'found nothing<br />';
		return $current;
	}






	function define ($string, $node, $default_type = 'path') {
		foreach ($this->splitStatement ($string) as $expr) {
			$a = $this->splitAssignment ($expr);
			if (is_array ($a)) {
				$this->defineValue ($a[0],
					//$a[1]);
					$this->evaluate ($a[1], $node, $default_type));
			}
		}
		return '';
	}

	function defineObject ($name, $evalstr) {
		eval (CLOSE_TAG . OPEN_TAG . ' $this->register["' . $name . '"] = new ' . $evalstr . '; ' . CLOSE_TAG);
		return '';
	}

	function defineValue ($name, &$value) {
		$this->register['object']->{$name} =& $value;
		return '';
	}






	function repeat ($string, $node, $default_type = 'path') {
		$res = $this->evaluate ($string, $node, $default_type);
		if (! $res) {
			return array ();
		} elseif (! is_array ($res)) {
			return array ($res);
		} else {
			return $res;
		}
	}






	function evaluate ($string, $node, $default_type = 'path', $carry = true) {
		$this->register['attrs'] = $node['attributes'];
		$this->register['paths'] = (isset ($node['paths'])) ? $node['paths'] : array ();
		if (! $carry) {
			$this->register['loop'] = false;
			$this->register['repeat'] =& $this->register['loop'];
		}
		$this->node = $node;
		$this->defaultMode = $default_type;
		$this->out = '';

		$this->buffer = array (array ());
		$this->bcount = 0;
		$this->ccount = -1;
		$this->escape = false;
		$str = $this->parse ($default_type . ':' . $string);

		$out = '';
		foreach ($this->buffer as $condition) {
			foreach ($condition as $element) {
				// element has [0] => type, [1] => contents
				if ($element[0] == 'string') {
					$out .= $element[1];
				} elseif ($element[0] == 'php') {
					//echo 'PHP: ' . htmlentities ($element[1]) . '<br />';
					//ob_start ();

					global $loader;
					$loader->import ('saf.Misc.Shorthand');
					$sh = new PHPShorthand;
					$sh->replaceGlobals ('this->register');
					$_original = $element[1];
					$element[1] = $sh->transform ($element[1]);

					ob_start ();
					$res = eval (CLOSE_TAG . OPEN_TAG . ' $_return = (' . $element[1] . '); ' . CLOSE_TAG);
					$err = ob_get_contents ();
					ob_end_clean ();
					if ($res === false) {
						echo '<h2>Invalid Shorthand Expression:</h2><pre> ' . htmlentities ($_original) . '</pre><h2>Evaluates To:</h2><pre>' . htmlentities ($element[1]) . '</pre><h2>Error Message</h2>' . $err . '';
						exit;
					}

					if ($_return === false || is_object ($_return) || is_array ($_return)) {
						$out = $_return;
						continue;
					} else {
						$out .= $_return;
					}
				} elseif ($element[0] == 'path') {
					$res =& $this->getPath (rtrim ($element[1]));
					if (is_string ($res) || is_numeric ($res)) {
						$out .= $res;
					} elseif ($res === false) {
						$out = $res;
						continue;
					} else {
						$out =& $res;
						break;
					}
				}
			}
			if ($out !== false) {
				return $out;
			}
		}

		return '';
	}








	function _innerPath ($token, $name) {
		if ($this->escape) {
			$this->escape = false;
			return '${';
		}
		$this->previousMode = $this->outputMode;
		$this->outputMode = $this->defaultMode;
		$this->buffer[$this->bcount][] = array ();
		$this->ccount++;
		return '';
	}

	function _endInnerPath ($token, $name) {
		if ($this->escape) {
			$this->escape = false;
			return '}';
		}
		$this->outputMode = $this->previousMode;

		$inner = array_pop ($this->buffer[$this->bcount]);
		$this->ccount--;
		//$this->ccount--;

		$cp = clone ($this); // deliberate duplication

		$this->buffer[$this->bcount][$this->ccount][1] .= $cp->evaluate ($inner[1], $this->node, $inner[0]);
		$this->buffer[$this->bcount][$this->ccount][0] = $this->outputMode;

		//array_pop ($this->buffer);

		return '';
	}

	function _phpOpen ($token, $name) {
		$this->outputMode = 'php';
		return '';
	}

	function _stringOpen ($token, $name) {
		$this->outputMode = 'string';
		return '';
	}

	function _pathOpen ($token, $name) {
		$this->outputMode = 'path';
		return '';
	}

	function _else ($token, $name) {
		if ($this->escape) {
			$this->escape = false;
			return '|';
		}
		$this->outputMode = $this->defaultMode;
		$this->bcount++;
		$this->buffer[] = array ();
		return '';
	}

	function _escape ($token, $name) {
		if ($this->escape) {
			$this->escape = false;
			return '\\';
		} else {
			$this->escape = true;
		}
		return '';
	}

	function _default ($token, $name) {
		$token = ltrim ($token);
		if (empty ($token)) {
			return '';
		}
		if (isset ($this->buffer[$this->bcount][$this->ccount][0]) && $this->outputMode == $this->buffer[$this->bcount][$this->ccount][0]) {
			// append string
			$this->buffer[$this->bcount][$this->ccount][1] .= $token;
		} else {
			// append array
			$this->ccount++;
			$this->buffer[$this->bcount][] = array (
				$this->outputMode,
				$token,
			);
		}
		return '';
	}	
}

?>