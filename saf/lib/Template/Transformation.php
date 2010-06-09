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
// TemplateTransformation is the class that Template uses internally
// to handle the manipulation of values before they are substituted
// into a template.
//

/**
	 * TemplateTransformation is the class that Template uses internally
	 * to handle the manipulation of values before they are substituted
	 * into a template.  It is often valuable to be able to call a function
	 * or a regular expression on a value before it is shown.  For instance,
	 * when you are retrieving a date value straight from your database to
	 * the template (using the [SQL: ...] directive), it is nice to be able
	 * to format it how you like, instead of how the database decides it
	 * should look.
	 * 
	 * The format of a transformation is as follows:
	 * 
	 * Tag name:Transformation type:Transformation rule
	 * 
	 * The tag name corresponds to the latter part of the name if the name
	 * is of the form ##object:property##.
	 * 
	 * There are currently three types of transformations:
	 * 
	 * - func : a function call
	 * - regex: a regular expression search and replace
	 * - alternate : alternates the value of the variable between the two
	 *   provided (good for alternating background colours of table rows)
	 * 
	 * New 1.2:
	 * - Added an optional list of variables to import from the global namespace.
	 * 
	 * <code>
	 * <?php
	 * 
	 * A few basic transformations:
	 * 
	 * [Transformations:
	 * 	bgcolor:alternate:#ffffff:#eeeeee
	 * 	icon:regex:(.+):<img src="sitellite/\1" />
	 * 	file_extension:func:strtoupper ("##file_extension##")
	 * 	date:func:Date::format ("##date##", "F j, Y")
	 * ]
	 * 
	 * Class usage:
	 * 
	 * $transformation = new TemplateTransformation ('func', 'date', 'Date::format ("##date##", "F j, Y")');
	 * 
	 * echo $transformation->transform ('2001-12-24');
	 * 
	 * // can also be used like this:
	 * 
	 * // note: data is the contents of a [Transformations: ...] directive
	 * $transformations =& TemplateTransformation::parse ($data);
	 * 
	 * foreach ($transformations as $key => $object) {
	 * 	// do things with $key and $object here
	 * }
	 * 
	 * ?>
	 * </code>
	 * 
	 * @package	Template
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.2, 2002-03-24, $Id: Transformation.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class TemplateTransformation {
	/**
	 * The type of transformation.  Can be 'regex', 'func', or 'alternate'.
	 * 
	 * @access	public
	 * 
	 */
	var $type;

	/**
	 * The name of the element to which the transformation belongs.
	 * 
	 * @access	public
	 * 
	 */
	var $key;

	/**
	 * The actual transformation rule.  See the examples above for format information.
	 * 
	 * @access	public
	 * 
	 */
	var $rule;

	/**
	 * Constructor method.
	 * 
	 * @access	public
	 * @param	string	$type
	 * @param	string	$key
	 * @param	string	$rule
	 * 
	 */
	function TemplateTransformation ($type, $key, $rule) {
		$this->type = $type;
		$this->key = $key;
		$this->rule = $rule;
	}

	/**
	 * Performs the transformation on a given value.
	 * 
	 * @access	public
	 * @param	string	$value
	 * @return	string
	 * 
	 */
	function transform ($value, $import = '') {
		if ($this->type == 'func') {
			// bring in any requested global variables, so that they may be
			// called in a transformation function
			$import = ltrim (rtrim ($import));
			if (! empty ($import)) {
				foreach (preg_split ('/, ?/', $import) as $g) {
					global ${$g};
				}
			}
			$object->{$this->key} = $value;
			//$tpl = new Template;
			if (! empty ($this->rule)) {
				eval (CLOSE_TAG . OPEN_TAG . ' $value = '
					//. $tpl->fill ($this->rule, $object, '', 1)
					. $this->rule
					. '; ' . CLOSE_TAG);
			}
			return $value;
		} elseif ($this->type == 'regex') {
			if (preg_match ('/^([^:]+):(.*)$/', $this->rule, $regs)) {
				return ereg_replace ($regs[1], $regs[2], $value);
			} else {
				return $value;
			}
		} elseif ($this->type == 'alternate') {
			list ($first, $second) = split (':', $this->rule);
			if (! isset ($this->_previous) || $this->_previous == $second) {
				$this->_previous = $first;
				return $first;
			} else {
				$this->_previous = $second;
				return $second;
			}
		} else {
			return $value;
		}
	}

	/**
	 * Parses a block of transformations into an associative array of TemplateTransformation
	 * objects (usually taken from a [Transformations: ...] directive).
	 * 
	 * @access	public
	 * @param	string	$data
	 * @return	associative array
	 * 
	 */
	function &parse ($data = '') {
		$list = array ();
		$list = split ("[ \t]*[\r\n]+[ \t]*", $data);
		$transformations = array ();
		foreach ($list as $line) {
			if (preg_match ('/^([^:]+):(func|regex|alternate):(.*)$/i', $line, $regs)) {
				$transformations[$regs[1]] = new TemplateTransformation ($regs[2], $regs[1], $regs[3]);
			}
		}
		return $transformations;
	}
}



?>