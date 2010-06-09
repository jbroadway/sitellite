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
// Generic parser class from which new and complex parsers can be derived.
//


/**
	 * Generic parser class from which new and complex parsers can be derived.
	 * Provides basic lexical analysis via regular expressions, which are assigned
	 * to callback functions.  These functions are used to iterate over the resulting
	 * token list.  Where you extend this class is in the syntax analysis and code
	 * generation/execution stages.  Parser can also be used as a Finite State
	 * Machine (FSM), in which case saf.Parser.Buffer is handy for implementing
	 * the data structure creation.
	 * 
	 * <code>
	 * <?php
	 * 
	 * // this example creates a comma-separated values (CSV) parser,
	 * // which can be accomplished in PHP much easier than this, but
	 * // this does serve as an example of what Parser can do, and hopefully
	 * // you'll see more complex and more interesting uses for it.
	 * 
	 * class CSVParser extends Parser {
	 * 
	 *   function CSVParser () {
	 *     // define our tokens
	 *     $this->addInternal ('_comma', ',');
	 *     $this->addInternal ('_newline', "\n");
	 *     $this->addInternal ('_escape', '\\');
	 * 
	 *     // define our internal variables.
	 *     // we define $list as an array
	 *     // since we're parsing CSV files to create
	 *     // 2D arrays.  note: in this case we're
	 *     // not using $output, but we don't want to
	 *     // override $output or the internal variables.
	 *     // in this case, we'll consider $output and
	 *     // $struct and $tokens and $regex reserved
	 *     // words.
	 *     $this->list = array ();
	 *     $this->skip = false;
	 *     $this->row = 0;
	 *     $this->column = 0;
	 *     $this->list[$this->row] = array ();
	 *   }
	 * 
	 *   function _default ($token, $name) {
	 *   	$this->list[$this->row][$this->column] .= $token;
	 *   }
	 * 
	 *   function _comma ($token, $name) {
	 *     // commas are the separators
	 *     if ($this->skip) {
	 *       $this->list[$this->row][$this->column] .= ',';
	 *       $this->skip = false;
	 *     } else {
	 *       $this->column++;
	 *     }
	 *   }
	 * 
	 *   function _newline ($token, $name) {
	 *     // increment
	 *     $this->row++;
	 *     $this->column = 0;
	 *     $this->list[$this->row] = array ();
	 *   }
	 * 
	 *   function _escape ($token, $name) {
	 *     if ($this->skip) {
	 *       $this->list[$this->row][$this->column] .= '\\';
	 *       $this->skip = false;
	 *     } else {
	 *       $this->skip = true;
	 *     }
	 *   }
	 * }
	 * 
	 * $data = 'Joe,Smith,joe@yoursite.com
	 * Phil,Johnson,phil@yoursite.com
	 * Bert,Morris,bert@yoursite.com';
	 * 
	 * $csv = new CSVParser ();
	 * $csv->parse ($data);
	 * 
	 * echo '<pre>';
	 * print_r ($csv->list);
	 * echo '</pre>';
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	Parser
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.0, 2003-01-18, $Id: Parser.php,v 1.4 2008/03/09 18:46:06 lux Exp $
	 * @access	public
	 * 
	 */

class Parser {
	

	/**
	 * Contains the original data sent to parse().
	 * 
	 * @access	public
	 * 
	 */
	var $original;

	/**
	 * Contains the array of parsed elements, aka tokens.
	 * 
	 * @access	public
	 * 
	 */
	var $struct;

	/**
	 * Contains all registered tokens as hashes containing 'name',
	 * 'token', 'callback', and 'object' keys.
	 * 
	 * @access	public
	 * 
	 */
	var $tokens;

	/**
	 * Contains the output of parse().
	 * 
	 * @access	public
	 * 
	 */
	var $output;

	/**
	 * Contains the output of makeRegex() on the current token list.
	 * 
	 * @access	public
	 * 
	 */
	var $regex;

	/**
	 * Contains a list of switches to the preg_split() and
	 * preg_match() regular expression evaluations  Default is 's',
	 * for dot-all mode.  Note that these are PCRE (Perl-Compatible
	 * Regular Expression) expressions, not ereg() calls.  For more
	 * info about switches, check out the PHP documentation at
	 * http://www.php.net/manual/en/pcre.pattern.modifiers.php
	 * 
	 * @access	public
	 * 
	 */
	var $switches = 's';

	

	/**
	 * Constructor method.
	 * 
	 * @access	public
	 * 
	 */
	function Parser () {
		$this->tokens = array ();
	}

	/**
	 * Alias of addToken().
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	string	$token
	 * @param	boolean	$quote
	 * 
	 */
	function addInternal ($name, $token, $quote = true) {
		$this->addToken ($name, $token, $quote);
	}

	/**
	 * Defines a token whose callback function has the same name as
	 * $name, and is a method defined in the subclass of Parser (the class
	 * you create when you create a custom parser).  Tokens are literal
	 * strings, unless $quote is set to false, in which case their values
	 * become active pieces of the token parsing regular expression.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	string	$token
	 * @param	boolean	$quote
	 * 
	 */
	function addToken ($name, $token, $quote = true) {
		if ($quote) {
			$tok = preg_quote ($token, '/');
		} else {
			$tok = $token;
		}
		$this->tokens[$token] = array (
			'name' => $name,
			'token' => $tok,
			'callback' => $name,
			'object' => &$this,
			'quoted' => $quote,
		);
	}

	/**
	 * Turns the $tokens list into a regular expression.
	 * 
	 * @access	public
	 * @return	string
	 * 
	 */
	function makeRegex () {
		$regex = '/(';
		$tokens = array ();
		foreach ($this->tokens as $token) {
			$tokens[] = $token['token'];
		}
		$regex .= join ('|', $tokens);
		$regex .= ')/' . $this->switches;
		return $regex;
	}

	/**
	 * This is the mainloop of the parser.
	 * 
	 * @access	public
	 * @param	string	$data
	 * @return	string
	 * 
	 */
	function parse ($data) {
		$this->original = $data;
		$this->regex = $this->makeRegex ();
		$this->struct = preg_split ($this->regex, $data, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		$this->output = '';
		foreach ($this->struct as $token) {
			if (isset ($this->tokens[$token]) && is_array ($this->tokens[$token])) {
				$this->output .= call_user_func (array (&$this, $this->tokens[$token]['callback']), $token, $this->tokens[$token]['name']);
			} else {
				$p = false;
				foreach ($this->tokens as $key => $tok) {
					if (! $tok['quoted'] && preg_match ('/' . $tok['token'] . '/' . $this->switches, $token, $regs)) {
						$this->output .= call_user_func (array (&$this, $tok['callback']), $token, $tok['name'], $regs);
						$p = true;
						break;
					}
				}
				if (! $p) {
					$this->output .= call_user_func (array (&$this, '_default'), $token, 'DEFAULT');
				}
			}
		}
		return $this->output;
	}

	/**
	 * This is the default token handler.  It merely returns
	 * the token sent to it, which will be added to the output string
	 * in the parse() method, thereby recreating the original source
	 * data.  This method is usually overridden when this class is
	 * extended.
	 * 
	 * @access	public
	 * @param	string	$token
	 * @param	string	$name
	 * @return	string
	 * 
	 */
	function _default ($token, $name) {
		return $token;
	}
	
}



?>