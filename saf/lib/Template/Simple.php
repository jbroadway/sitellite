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
// SimpleTemplate is a tag-substitution engine with malleable tag formats,
// fast parsing based on a single regular expression, and a simple,
// object-based approach to tag naming.
//


define ('SIMPLE_TEMPLATE_DELIM_POUND', 0);


define ('SIMPLE_TEMPLATE_DELIM_CURLY', 1);


define ('SIMPLE_TEMPLATE_DELIM_ROUND', 2);


define ('SIMPLE_TEMPLATE_DELIM_DOTS', 3);


define ('SIMPLE_TEMPLATE_DELIM_COMMENTS', 4);


define ('SIMPLE_TEMPLATE_DELIM_TAGS', 5);


define ('SIMPLE_TEMPLATE_DELIM_ASP', 6);

$GLOBALS['simple_template_register'] = array ();

/**
	 * SimpleTemplate is a tag-substitution engine with malleable tag formats,
	 * fast parsing based on a single regular expression, and a simple,
	 * object-based approach to tag naming.  SimpleTemplate is useful
	 * when XT is considered overkill, mostly in short, non-strict templates
	 * that do not represent a complete document or page.
	 * 
	 * The tag format is determined
	 * during the creation of the SimpleTemplate object, by passing one
	 * of the constants defined in this package as the second parameter
	 * to the constructor method.  This defines the $use_delim property
	 * which points to the specified delimiters in the $delim property
	 * array.  New delimiters may be added by adding them to the $delim
	 * array, then calling the setDelim() method.
	 * 
	 * Templates can be passed as either a file name or a string.  Files
	 * that are parsed more than once will be stored in the internal
	 * $cache array, so they will only be read from the filesystem
	 * the first time.  As a general rule, SimpleTemplate template files
	 * are given a .spt extension (if you're geeky enough -- like me --
	 * you can pronounce it "SimPlaTe").
	 * 
	 * Tags take the form:
	 * 
	 * opening delimiter + object name + separator + property or method + closing delimiter
	 * 
	 * The separator can be either a dot (.), a colon (:), or a forward-
	 * slash (/).  The forward-slash is the easiest to type, I find. :)
	 * 
	 * If an object is passed to the fill() method, then that object
	 * is considered the 'default', and you can omit the object name
	 * and separator when referring to its properties and methods.  In
	 * this case, the tag syntax becomes:
	 * 
	 * opening delimiter + property or method + closing delimiter
	 * 
	 * Inline PHP code is also allowed in templates, but it is discouraged
	 * in practice.  It can be useful however, when you want to make
	 * formatting transformations similar to what is possible with the
	 * XT transform tag.  In this case, a short block of PHP code at
	 * the top of the file is sufficient.  Also note that the PHP
	 * code is evaluated in the namespace of the SimpleTemplate fill()
	 * method.  To refer to the default object, use $obj, to refer to
	 * properties of the SimpleTemplate object itself, use $this.
	 * To refer to global objects, use the PHP global command first.
	 * 
	 * See the example for an example template.
	 * 
	 * Note: Methods called in templates DO NOT receive any parameters.
	 * Please be aware of this when using this functionality.
	 * 
	 * Also Note: Some SAF packages rely on a global $simple object
	 * being available to them.  As a result, this is considered a
	 * "core" SAF package.
	 * 
	 * New in 1.2:
	 * - Added SIMPLE_TEMPLATE_DELIM_COMMENTS constant and delimiters,
	 *   which make use of HTML comment tags of the form
	 *   <!-- put: tagname --> to denote tag substitutions.
	 * - Added the ability to specify an alternate delimiter on a
	 *   per-template basis using a comment at the very beginning of
	 *   the template file containing the name of the constant that
	 *   points to the alternate delimiter.  For example, you could
	 *   use the new HTML comment delimiters by starting off your
	 *   template document with
	 *   <!-- SIMPLE_TEMPLATE_DELIM_COMMENTS -->.
	 * 
	 * New in 1.4:
	 * - Added the ability to include basic looping and conditional
	 *   logic in the templates via new {loop obj.list} {end loop}
	 * - Added I18n capabilities via an {intl} tag.
	 * - Added {php} tag.  Takes a PHPShorthand expresssion and
	 *   returns its output.
	 * - Added an {alt} tag.  This creates an saf.Misc.Alt object
	 *   that can be referred to via {alt/next} and {alt/current}.
	 * - See example for usage of the new tags.
	 * 
	 * New in 1.6:
	 * - Added {if condition} {end if} tags, and the ability to
	 *   nest loops and ifs within other loops and ifs.  Note
	 *   that there is no {elseif} or {else} at this point.
	 * - Added the {exec} tag, which is the equivalent to the
	 *   {php} tag, but does not return any output.
	 * - Added {filter function_name} {end filter} tags that
	 *   change the function that other tags are passed through
	 *   when added to the output.  The default is
	 *   {filter htmlentities_compat} which does not need to be
	 *   called at the start, since it is automatic.  Another
	 *   useful filter is {filter urlencode}.  If you do not
	 *   want filtering of a section of your template, use
	 *   {filter none}.
	 * - Added {inc} and {spt} tags.  {inc} includes a file
	 *   for its data, while {spt} includes a file but runs
	 *   fill() on its contents before adding them to the output.
	 *   Both tags also take ordinary variables and aliases,
	 *   such as {inc obj/some_var} or
	 *   {spt CONSTANT_THAT_CONTAINS_SOME_TEMPLATE}.
	 * - Removed the loop() method and the simple_template_loop()
	 *   function.
	 * - Added {form} and {box} tags, to call loader_form() and
	 *   loader_box(), respectively.
	 * 
	 * New in 1.8:
	 * - Fixed a bug that caused the 'end filter' token to be passed
	 *   to the filter function.
	 * - Added comment tags, which do not get passed to the client.
	 *   The syntax is two dashes and a space at the start, then
	 *   a space and two dashes at the end, for example:
	 *   {-- this is a comment --}.
	 *
	 * New in 2.0:
	 * - Added the ability to specify an object to pass in lieu of
	 *   the default $obj when called {spt}.  The syntax is
	 *   {spt template.spt param/name} where "param/name" represents
	 *   the path to a value or object within the register.
	 * - Added the ability to resolve paths such as 'obj' or 'loop'
	 *   to their respective objects within the register.
	 * - Added the ability to pass key names from the register to
	 *   the {loop} tag.  For example: {loop obj} or {loop loop}
	 *   (AKA loop through the items in the current loop item).
	 * - Added the ability to call on values from an outer loop
	 *   from within a nested loop, using {parent/value} as the
	 *   new path name.
	 *
	 * New in 2.2:
	 * - Fixed a bug looping through 2D arrays, where it would start
	 *   to loop through _properties, _index, _total, _key, etc.
	 *
	 * New in 2.4:
	 * - You can now pass parameters to box calls, via the following
	 *   syntax: {box path/to/box?param1=value&param2=value2}
	 * - You can also include dynamic values in your box calls via
	 *   the following syntax: {box path/to/box?param=[expression]}
	 *   The []'s denote an inline expressions which are passed to
	 *   determine() and substituted for the results.  They can only
	 *   be used in place of parameter values, not anywhere else in
	 *   the box calling syntax.
	 * - Added an else clause to if statements, in the form of an
	 *   {if else} tag, which operates in the same way as an ordinary
	 *   {if statement} tag does, except that it should aways come
	 *   directly after the ordinary if statement (if you need it).
	 * 
	 * New in 2.6:
	 * - Specify filters like this: {varname|filter1|filter2}
	 *
	 * New in 2.8:
	 * - Refer to parent loops via {loop0/_value}, {loop1/_value} etc.
	 *   loop0 is the topmost loop's current element, and so on.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $simple = new SimpleTemplate ('inc/html', SIMPLE_TEMPLATE_DELIM_CURLY);
	 * 
	 * // create some fake objects
	 * $person1 = new StdClass;
	 * $person1->firstname = 'Joe';
	 * $person1->lastname = 'Smith';
	 * $person1->age = '29';
	 * 
	 * $person2 = new StdClass;
	 * $person2->firstname = 'Sandy';
	 * $person2->lastname = 'MacDonald';
	 * $person2->age = '25';
	 * 
	 * // load them into an array
	 * $people = array ($person1, $person2);
	 * 
	 * // create a global object to refer to as well
	 * $loader->import ('saf.Misc.Alt');
	 * $colours = new Alt ('#eeeeee', '#ffffff');
	 * 
	 * foreach ($people as $person) {
	 * 	echo $simple->fill ('
	 * 		',
	 * 		$person
	 * 	);
	 * }
	 * 
	 * // example with conditions and looping
	 * // for more info about if and loop syntax, see the package saf.Misc.Shorthand
	 * // note that you can put a single loop inside a condition and vice versa,
	 * // but you cannot embed a loop within a loop, nor a condition within another.
	 * echo $simple->fill ('
	 * 	{if count (obj[people]) gt 0}
	 * 		
	 * 
	 * 		
	 * 	{end if}',
	 * 	array (
	 * 		'listTitle' => 'Employees',
	 * 		'people' => array (
	 * 			array (
	 * 				'firstname' => 'Joe',
	 * 				'lastname' => 'Smith',
	 * 			),
	 * 			array (
	 * 				'firstname' => 'Sandy',
	 * 				'lastname' => 'Miller',
	 * 			),
	 * 		),
	 * 	)
	 * );
	 * 
	 * $data = array (
	 * 	'title' => 'Matrix',
	 * 	array (
	 * 		array ('one', 'two'),
	 * 		array ('three', 'four'),
	 * 		array ('five', 'six'),
	 * 		array ('seven', 'eight'),
	 * 		array ('nine', 'ten'),
	 * 		array ('eleven', 'twelve'),
	 * 	),
	 * );
	 * 
	 * echo template_simple ('
	 * 	{alt #ffffff #cccccc}
	 * 	
	 * 	',
	 * 	$data
	 * );
	 *
	 *
	 * // Example of if/else usage:
	 *
	 * $foo = mt_rand (0, 1);
	 * echo template_simple ('
	 *
	 *   {if obj[foo]}
	 *     YES ({foo})
	 *   {end if}
	 *
	 *   {if else}
	 *     NO ({foo})
	 *   {end if}
	 *
	 * ', array ('foo' => $foo));
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	Template
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	2.6, 2008-04-21, $Id: Simple.php,v 1.10 2008/04/22 04:23:28 lux Exp $
	 * @access	public
	 * 
	 */

class SimpleTemplate {
	

	/**
	 * The path to the template directory.
	 * 
	 * @access	public
	 * 
	 */
	var $path;

	/**
	 * A cache for templates read from files, so if they are
	 * called a second or third time, we don't have to read them from
	 * the file system again.
	 * 
	 * @access	private
	 * 
	 */
	var $cache = array ();

	/**
	 * List of predefined delimiters.  A delimiter is a
	 * 2-element array with an opening and a closing delimiter
	 * string (must be valid as a regular expression, no escaping
	 * is done for you).  Predefined delimiters are: double-pound
	 * (ie. ##tag##), curly braces (ie. {tag}), round braces
	 * (ie. (tag)), and triple-dots (ie. ...tag...).
	 * 
	 * @access	public
	 * 
	 */
	var $delim = array (
		array ('##', '##'),
		array ('\\{', '\\}'),
		array ('\\(', '\\)'),
		array ('\\.\\.\\.', '\\.\\.\\.'),
		array ('<\\!-- put: ', ' -->'),
		array ('<spt>', '<\\/spt>'),
		array ('<% ', ' %>'),
	);

	/**
	 * List of predefined delimiters, represented minus
	 * the slashes in place for insertion into regular expressions,
	 * as the $delim list is.
	 * 
	 * @access	public
	 * 
	 */
	var $delim_literal = array (
		array ('##', '##'),
		array ('{', '}'),
		array ('(', ')'),
		array ('...', '...'),
		array ('<!-- put: ', ' -->'),
		array ('<spt>', '</spt>'),
		array ('<% ', ' %>'),
	);

	/**
	 * Points to which delimiters to use (from the $delim
	 * array).
	 * 
	 * @access	public
	 * 
	 */
	var $use_delim;

	/**
	 * Default filter for inserted vars is htmlentities_compat.
	 */
	var $filter = 'htmlentities_compat';

	/**
	 * Whether to ignore output until an end loop is found.
	 */
	var $_ignoreUntilEndLoop = false;

	/**
	 * Whether to buffer output until an end loop is found.
	 */
	var $_bufferUntilEndLoop = false;

	/**
	 * Whether to ignore output until an end if is found.
	 */
	var $_ignoreUntilEndIf = false;

	/**
	 * List of loops.
	 */
	var $_loopList = array ();

	/**
	 * Loop buffer.
	 */
	var $_loopBuffer = '';

	/**
	 * Count of structs (ifs and loops).
	 */
	var $_structCount = 0;

	/**
	 * Constructor method.
	 * 
	 * @access	public
	 * @param	string	$path
	 * @param	integer	$use_delim
	 * 
	 */
	function SimpleTemplate ($path = 'inc/html', $use_delim = SIMPLE_TEMPLATE_DELIM_CURLY) {
		$this->path = $path;
		$this->use_delim = $use_delim;
	}

	/**
	 * Sets $use_delim to the specified value.
	 * 
	 * @access	public
	 * @param	integer	$use_delim
	 * 
	 */
	function setDelim ($use_delim = SIMPLE_TEMPLATE_DELIM_CURLY) {
		$this->use_delim = $use_delim;
	}

	/**
	 * Returns the $path property or the current directory
	 * if the $path is empty.
	 * 
	 * @access	public
	 * @return	string
	 * 
	 */
	function getPath () {
		if (empty ($this->path)) {
			return getcwd ();
		} else {
			return $this->path;
		}
	}

	/**
	 * Determines the value to return based on the
	 * specified $obj and $var.
	 * 
	 * @access	public
	 * @param	string	$var
	 * @param	object	$obj
	 * @return	mixed
	 * 
	 */
	function determine ($var, $obj) {
		global $simple_template_register;

		if (strpos ($var, 'loop ') === 0) {
			// start of loop

			// 1. parse
			$simple_template_register['obj'] = $obj;

			$v = str_replace ('loop ', '', $var);

			if (isset ($simple_template_register[$v])) {
				$res = $simple_template_register[$v];

			} else {
				loader_import ('saf.Misc.Shorthand');
				$sh = new PHPShorthand;
				$sh->replaceGlobals ('simple_template_register');
				$php = $sh->transform ($v);

				$res = eval (CLOSE_TAG . OPEN_TAG . ' return (' . $php . '); ' . CLOSE_TAG);

			}

			if (! $res) {
				//$this->_loopList = array ();
				$this->_ignoreUntilEndLoop = true;
			} elseif (! is_array ($res)) {
				$this->_loopList = array ($res);
			} else {
				$this->_loopList = $res;
			}

			$this->_bufferUntilEndLoop = true;
			$this->_loopBuffer = '';
			$this->_structCount = 0;

		} elseif (strpos ($var, 'if ') === 0) {

			// start of if
			if (str_replace ('if ', '', $var) == 'else') {
				if (! isset ($simple_template_register['else'])) {
					$simple_template_register['else'] = 'true';
				}
				$php = '! (' . $simple_template_register['else'] . ')';
				unset ($simple_template_register['else']);
			} else {
				loader_import ('saf.Misc.Shorthand');
				$sh = new PHPShorthand;
				$sh->replaceGlobals ('simple_template_register');
				$php = $sh->transform (substr ($var, 3));

				$simple_template_register['obj'] = $obj;
				$simple_template_register['else'] = $php;
			}

			$res = eval (CLOSE_TAG . OPEN_TAG . ' return (' . $php . '); ' . CLOSE_TAG);
			if (! $res) {
				$this->_ignoreUntilEndIf = true;
				$this->_structCount = 0;
			}

		} elseif (strpos ($var, 'intl ') === 0) {
			return intl_get (str_replace ('intl ', '', $var));

		} elseif (strpos ($var, 'php ') === 0) {
			loader_import ('saf.Misc.Shorthand');
			$sh = new PHPShorthand;
			$sh->replaceGlobals ('simple_template_register');
			$php = $sh->transform (substr ($var, 4)); //str_replace ('php ', '', $var));

			$simple_template_register['obj'] = $obj;

			return eval (CLOSE_TAG . OPEN_TAG . ' return (' . $php . '); ' . CLOSE_TAG);

		} elseif (strpos ($var, 'exec ') === 0) { // {php} but returns no output
			loader_import ('saf.Misc.Shorthand');
			$sh = new PHPShorthand;
			$sh->replaceGlobals ('simple_template_register');
			$php = $sh->transform (substr ($var, 5)) ; //str_replace ('exec ', '', $var));

			$simple_template_register['obj'] = $obj;

			@eval (CLOSE_TAG . OPEN_TAG . ' return (' . $php . '); ' . CLOSE_TAG);

		} elseif (strpos ($var, 'alt ') === 0) {
			loader_import ('saf.Misc.Alt');
			$simple_template_register['alt'] = new Alt (preg_split ('/ +/', str_replace ('alt ', '', $var)));
			return '';

		} elseif (strpos ($var, 'filter ') === 0) {
			$this->filter = str_replace ('filter ', '', $var);

		} elseif (strpos ($var, '-- ') === 0 && strrpos ($var, ' --') === (strlen ($var) - 3)) {
			return '';

		} // end special cases

		/* order of precedence after special tags:
		 * - check $obj before anything else
		 * - check the register second
		 * - check the global namespace third
		 * - properties before methods
		 * - objects/arrays before variables
		 * - is it a function?
		 * - is it a constant?
		 * - if all else fails, return nothing
		 */

		if (is_object ($obj)) {
			if (isset ($obj->{$var})) {
				return $obj->{$var};
			} elseif (method_exists ($obj, $var)) {
				return $obj->{$var} ();
			}
		} elseif (is_array ($obj) && isset ($obj[$var])) {
			return $obj[$var];
		}

		if ($var == 'obj') {
			return $obj;
		} elseif (count ($simple_template_register) && in_array ($var, array_keys ($simple_template_register))) {
			return $simple_template_register[$var];
		}

		$original = $obj;
		list ($obj, $var) = preg_split ('/(\.|:|\/)/', $var);
		if (! empty ($var)) {
			if (is_object ($simple_template_register[$obj])) {
				if (isset ($simple_template_register[$obj]->{$var})) {
					return $simple_template_register[$obj]->{$var};
				} elseif (method_exists ($simple_template_register[$obj], $var)) {
					return $simple_template_register[$obj]->{$var} ();
				} else {
					return '';
				}

			} elseif (is_array ($simple_template_register[$obj]) && isset ($simple_template_register[$obj][$var])) {
				return $simple_template_register[$obj][$var];

			} elseif (is_object ($GLOBALS[$obj])) {
				if (isset ($GLOBALS[$obj]->{$var})) {
					return $GLOBALS[$obj]->{$var};
				} elseif (method_exists ($GLOBALS[$obj], $var)) {
					return $GLOBALS[$obj]->{$var} ();
				} else {
					return '';
				}

			} elseif (is_array ($GLOBALS[$obj]) && isset ($GLOBALS[$obj][$var])) {
				return $GLOBALS[$obj][$var];

			} elseif (function_exists ($obj)) {
				if (is_object ($original) && isset ($original->{$var})) {
					return $obj ($original->{$var});
				} elseif (is_array ($original) && isset ($original[$var])) {
					return $obj ($original[$var]);
				} else {
					return $obj ($var);
				}
			}
		} elseif (defined ($obj)) {
			return constant ($obj);
		}

		return '';
	}

	/**
	 * Fills the template and returns it.  This is the mainloop
	 * of the parser.
	 * 
	 * @access	public
	 * @param	string	$tpl
	 * @param	object or array	$obj
	 * @return	string
	 * 
	 */
	function fill ($tpl, $obj = '') {
		$spt = clone ($this);
		return $spt->_fill ($tpl, $obj);
	}

	function _fill ($tpl, $obj) {
		global $simple_template_register, $simple_template_functions;

		if (empty ($tpl)) {
			return '';
		}
		if (isset ($this->cache[$tpl])) {
			$use = $this->cache[$tpl];
		} elseif (@is_file ($this->getPath () . '/' . $tpl)) {
			$this->cache[$tpl] = @join ('', @file ($this->getPath () . '/' . $tpl));
			$use = $this->cache[$tpl];
		} else {
			$use = $tpl;
		}

		// Determine Delimiter
		if (preg_match ('/^<\!-- SIMPLE_TEMPLATE_DELIM_([A-Z0-9_]+) -->/', $use, $regs)) {
			$old_delim = $this->use_delim;
			$this->use_delim = constant ('SIMPLE_TEMPLATE_DELIM_' . $regs[1]);
			$use = str_replace ($regs[0], '', $use);
		}

		// Eval Inline PHP
		ob_start ();
		$use = str_replace ('<?xml', '<!xml', $use);
		eval (CLOSE_TAG . $use);
		$use = str_replace ('<!xml', '<?xml', ob_get_contents ());
		ob_end_clean ();

		//$out = preg_replace ("/" . $this->delim[$this->use_delim][0] . "([a-zA-Z0-9\.:\/_-]+)" . $this->delim[$this->use_delim][1] . "/e", "\$this->determine ('$1', \$obj)", $use);
		//$tokens = preg_split ('/(' . $this->delim[$this->use_delim][0] . '[\[\]\(\)a-zA-Z0-9\.,=<>\?&#$\'":;\!\=\/\| _-]+' . $this->delim[$this->use_delim][1] . ')/s', $use, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		$tokens = preg_split ('/(' . $this->delim[$this->use_delim][0] . '[^\r\n' . $this->delim[$this->use_delim][1] . ']+' . $this->delim[$this->use_delim][1] . ')/s', $use, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

		$out = '';

		foreach ($tokens as $tok) {
			$_tok = substr (
				$tok,
				strlen ($this->delim_literal[$this->use_delim][0]),
				- strlen ($this->delim_literal[$this->use_delim][1])
			);
			if (
					! empty ($_tok) &&
					strpos ($tok, $this->delim_literal[$this->use_delim][0]) === 0 &&
					strrpos ($tok, $this->delim_literal[$this->use_delim][1]) === (strlen ($tok) - strlen ($this->delim_literal[$this->use_delim][1]))
			) {
				$_is_tag = true;
			} else {
				$_is_tag = false;
			}

			// HANDLE END-LOOP

			if ($this->_ignoreUntilEndLoop) {
				if (strpos ($_tok, 'loop ') === 0) {
					$this->_structCount++;
				} elseif ($_tok == 'end loop' && $this->_structCount > 0) {
					$this->_structCount--;
					continue;
				}

				if ($_tok == 'end loop') {
					$this->_ignoreUntilEndLoop = false;
					$this->_bufferUntilEndLoop = false;
				}

			} elseif ($this->_bufferUntilEndLoop) {
				if (strpos ($_tok, 'loop ') === 0) {
					$this->_structCount++;
					$this->_loopBuffer .= $tok;
					continue;
				} elseif ($_tok == 'end loop' && $this->_structCount > 0) {
					$this->_structCount--;
					$this->_loopBuffer .= $tok;
					continue;
				}

				if ($_tok == 'end loop') {
					$this->_bufferUntilEndLoop = false;

					$tmp = (isset ($simple_template_register['loop'])) ? $simple_template_register['loop'] : null;
					$parent = (isset ($simple_template_register['parent'])) ? $simple_template_register['parent'] : null;
					$simple_template_register['parent'] = $tmp;
					$loopcount = 0;
					while (isset ($simple_template_register['loop' . $loopcount])) {
						$loopcount++;
					}
					$simple_template_register['loop' . $loopcount] = $tmp;

					$index = 1;
					$total = count ($this->_loopList);

					foreach ($this->_loopList as $key => $item) {
						if (in_array ($key, array ('_properties', '_index', '_key', '_total'), true)) {
							continue;
						}
						$simple_template_register['loop'] = $item;
						if (is_object ($item)) {
							$simple_template_register['loop']->_properties = get_object_vars ($simple_template_register['loop']);
							$simple_template_register['loop']->_index = $index;
							$simple_template_register['loop']->_key = $key;
							$simple_template_register['loop']->_total = $total;
						} elseif (is_array ($item)) {
							$simple_template_register['loop']['_properties'] = $simple_template_register['loop'];
							$simple_template_register['loop']['_index'] = $index;
							$simple_template_register['loop']['_key'] = $key;
							$simple_template_register['loop']['_total'] = $total;
						} else {
							$simple_template_register['loop'] = (object) array ('_value' => $item, '_index' => $index, '_key' => $key, '_total' => $total);
						}
						$out .= $this->fill ($this->_loopBuffer, $obj);

						$index++;
					}

					$simple_template_register['loop'] = $tmp;
					$simple_template_register['parent'] = $parent;
					unset ($simple_template_register['loop' . $loopcount]);

				} else {
					$this->_loopBuffer .= $tok;
				}

			// HANDLE END-IF

			} elseif ($this->_ignoreUntilEndIf) {
				if (strpos ($_tok, 'if ') === 0) {
					$this->_structCount++;
				} elseif ($_tok == 'end if' && $this->_structCount > 0) {
					$this->_structCount--;
					continue;
				}

				if ($_tok == 'end if') {
					$this->_ignoreUntilEndIf = false;
				}

			// HANDLE INC AND SPT

			} elseif (strpos ($_tok, 'inc ') === 0) { // include
				$file = substr ($_tok, 4);
				if (@ file_exists ($this->getPath () . '/' . $file)) {
					$out .= @join ('', @file ($this->getPath () . '/' . $file));
					continue;
				}
				$val = $this->determine ($file, $obj);
				if (@ file_exists ($this->getPath () . '/' . $val)) {
					$out .= @join ('', @file ($this->getPath () . '/' . $val));
					continue;
				} elseif (! empty ($val)) {
					$out .= $val;
					continue;
				}
				$out .= $file;
				continue;

			} elseif (strpos ($_tok, 'spt ') === 0) { // include another template
				$file = substr ($_tok, 4);

				list ($file, $param) = explode (' ', $file);
				if (! empty ($param)) {
					$param = $this->determine ($param, $obj);
				} else {
					$param = $obj;
				}

				if (@ file_exists ($this->getPath () . '/' . $file)) {
						$out .= $this->fill (@join ('', @file ($this->getPath () . '/' . $file)), $param);
						continue;
				}
				$val = $this->determine ($file, $obj);
				if (@ file_exists ($this->getPath () . '/' . $val)) {
					$out .= $this->fill (@join ('', @file ($this->getPath () . '/' . $val)), $param);
					continue;
				} elseif (! empty ($val)) {
					$out .= $this->fill ($val, $param);
					continue;
				}
				$out .= $this->fill ($file, $param);
				continue;

			} elseif ($_is_tag && strpos ($_tok, 'form ') === 0) { // call loader_form ()
				$form = substr ($_tok, 5);
				$out .= loader_form ($form);
				continue;

			} elseif ($_is_tag && strpos ($_tok, 'box ') === 0) { // call loader_box ()
				$box = substr ($_tok, 4);

				if (strstr ($box, '?')) {
					$u = parse_url ($box);
					$box = $u['path'];
					$param = array ();
					foreach (explode ('&', $u['query']) as $pair) {
						list ($k, $v) = explode ('=', $pair);
						if ($v[0] == '[' && $v[strlen ($v) - 1] == ']') {
							$v = $this->determine (substr ($v, 1, strlen ($v) - 2), $obj);
						}
						$param[$k] = $v;
					}
				} else {
					$param = array ();
				}

				$out .= loader_box ($box, $param);
				continue;

			} elseif (strpos ($_tok, 'info ') === 0) {
				ob_start ();
				info ($this->determine (substr ($_tok, 5), $obj));
				$out .= ob_get_contents ();
				ob_end_clean ();

			} elseif ($_tok == 'end filter') { // return filter to default
				$this->filter = 'htmlentities_compat';

			// STANDARD TAG INTERPRETATION

			} elseif (
					//strpos ($tok, $this->delim_literal[$this->use_delim][0]) === 0 &&
					//strpos ($tok, $this->delim_literal[$this->use_delim][1]) === (strlen ($tok) - strlen ($this->delim_literal[$this->use_delim][1]))
					$_is_tag
			) {
				if (strpos ($_tok, '|') !== false) {
					$_filters = explode ('|', $_tok);
					$_tok = array_shift ($_filters);
					$_prev_filter = $this->filter;
					$this->filter = join ('/', $_filters);
				}
				if ($this->filter == 'none') {
					$out .= $this->determine (
						$_tok,
						$obj
					);
				} else {
					if (isset ($simple_template_register['loop']) && is_object ($simple_template_register['loop'])) {
						$_name = $simple_template_register['loop']->_key;
					} elseif (isset ($simple_template_register['loop']) && is_array ($simple_template_register['loop'])) {
						$_name = $simple_template_register['loop']['_key'];
					} else {
						$_name = false;
					}

					$GLOBALS['simple_template_token_name'] = $_name;
					if (strpos ($this->filter, '/') !== false) {
						$filters = explode ('/', $this->filter);
						$res = $this->determine (
							$_tok,
							$obj
						);
						foreach (array_reverse ($filters) as $filter) {
							$res = @call_user_func ($filter, $res);
						}
						$out .= $res;
					} else {
						$out .= @call_user_func ($this->filter,
							$this->determine (
								$_tok,
								$obj
							)
						);
					}
				}
				if (isset ($_filters)) {
					$this->filter = $_prev_filter;
					unset ($_prev_filter);
					unset ($_filters);
				}

			// IN-BETWEEN TEXT

			} else {
				$out .= $tok;
			}
		}

		// Reset Delimiter
		if (isset ($old_delim)) {
			$this->use_delim = $old_delim;
		}

		return $out;
	}

	function register ($name, $var) {
		global $simple_template_register;
		$simple_template_register[$name] = $var;
	}
}

function template_simple ($tpl, $obj = '') {
	return $GLOBALS['simple']->fill ($tpl, $obj);
}

function template_simple_register ($name, $var) {
	return $GLOBALS['simple']->register ($name, $var);
}

?>
