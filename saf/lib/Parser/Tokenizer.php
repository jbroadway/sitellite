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
// Tokenizer provides an saf.Parser-like wrapper around the PHP tokenizer
// extension that can be extended for token analysis and parser writing.
//


/**
	 * Tokenizer provides an saf.Parser-like wrapper around the PHP tokenizer
	 * extension that can be extended for token analysis and parser writing.
	 * One of its uses in Sitellite is parsing PHP code for I18n text by the
	 * saf.I18n.Builder package.
	 * 
	 * Note: Requires the PHP tokenizer extension, which is built into PHP by
	 * default as of 4.3.0.
	 * 
	 * <code>
	 * <?php
	 * 
	 * class ClassFinder extends Tokenizer {
	 * 	var $classes = array ();
	 * 	var $next = false;
	 * 
	 * 	function ClassFinder () {
	 * 		$this->addCallback ('_class', T_CLASS);
	 * 		$this->addCallback ('_string', T_STRING);
	 * 	}
	 * 
	 * 	function find ($code) {
	 * 		if ($this->parse ($code)) {
	 * 			return $this->classes;
	 * 		} else {
	 * 			return false;
	 * 		}
	 * 	}
	 * 
	 * 	function _class ($token, $data) {
	 * 		$this->next = true;
	 * 	}
	 * 
	 * 	function _string ($token, $data) {
	 * 		if ($this->next) {
	 * 			$this->classes[] = $token;
	 * 			$this->next = false;
	 * 		}
	 * 	}
	 * }
	 * 
	 * $finder = new ClassFinder ();
	 * $res = $finder->find ('<?php
	 * 	class Foo {
	 * 		var $foo = "Hello";
	 * 	}
	 * 	class Bar {
	 * 		var $bar = "World";
	 * 	}
	 * ');
	 * 
	 * if (is_array ($res)) {
	 * 	foreach ($res as $class) {
	 * 		echo $class . "<br />\n";
	 * 	}
	 * }
	 * 
	 * ?>
	 * </code>
	 * 
	 * @package	Parser
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	0.6, 2003-01-20, $Id: Tokenizer.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class Tokenizer {
	

	/**
	 * Stores the code that is passed to the previous call to
	 * parse().
	 * 
	 * @access	public
	 * 
	 */
	var $code;

	/**
	 * Stores the tokens from the previous call to parse()
	 * 
	 * @access	public
	 * 
	 */
	var $tokens;

	/**
	 * Contains the message if an error occurs.
	 * 
	 * @access	public
	 * 
	 */
	var $error;

	

	/**
	 * Fixes some quirks and exceptions to the structure of
	 * the output of the PHP token_get_all() function.
	 * 
	 * @access	public
	 * @param	array	$tokens
	 * @return	array
	 * 
	 */
	function normalize ($tokens) {
		$newtok = array ();
		foreach ($tokens as $token) {
			if (is_array ($token[0])) {
				foreach ($token[0] as $t) {
					$newtok[] = $t;
				}
			} elseif (is_string ($token)) {
				$newtok[] = array (0, $token);
			} else {
				$newtok[] = $token;
			}
		}
		return $newtok;
	}

	/**
	 * Parses the specified $code and calls the callback
	 * method assigned to each token type.
	 * 
	 * @access	public
	 * @param	string	$code
	 * @return	boolean
	 * 
	 */
	function parse ($code) {
		if (! function_exists ('token_get_all')) {
			$this->error = 'Tokenizer PHP extension is not available.';
			return false;
		}

		$this->code = $code;
		$this->tokens = token_get_all ($code);

		$this->tokens = $this->normalize ($this->tokens);

		foreach ($this->tokens as $token) {
			if (isset ($this->callbacks[$token[0]])) {
				if (! $this->{$this->callbacks[$token[0]]} ($token[0], $token[1])) {
					return false;
				}
			} else {
				if (! $this->_default ($token[0], $token[1])) {
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * Adds a callback $function for the specified $token.
	 * The $token is the number of the token, which may be expressed
	 * by passing the appropriate T_* tokenizer constant.
	 * 
	 * @access	public
	 * @param	string	$function
	 * @param	integer	$token
	 * 
	 */
	function addCallback ($function, $token) {
		if (is_array ($token)) {
			foreach ($token as $t) {
				$this->callbacks[$t] = $function;
			}
		} else {
			$this->callbacks[$token] = $function;
		}
	}

	/**
	 * This is the default token handler.  It handles all
	 * tokens that haven't been assigned custom callback functions.
	 * 
	 * @access	public
	 * @param	string	$token
	 * @param	string	$data
	 * @return	boolean
	 * 
	 */
	function _default ($token, $data) {
		return true;
	}

	
}



?>