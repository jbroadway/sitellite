<?php
//
// +----------------------------------------------------------------------+
// | Sitellite - Content Management System                                |
// +----------------------------------------------------------------------+
// | Copyright (c) 2007 Simian Systems                                    |
// +----------------------------------------------------------------------+
// | This software is released under the GNU General Public License (GPL) |
// | Please see the accompanying file docs/LICENSE for licensing details. |
// |                                                                      |
// | You should have received a copy of the GPL Software License along    |
// | with this program; if not, write to Simian Systems, 242 Lindsay,     |
// | Winnipeg, MB, R3N 1H1, CANADA.  The License is also available at     |
// | the following web site address:                                      |
// | <http://www.sitellite.org/index/license>                             |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <lux@simian.ca>                                |
// +----------------------------------------------------------------------+
//
// Handles validation of form fields for the MailForm package.
//

/**
	 * Handles validation of form fields for the MailForm package.
	 * 
	 * Rule Format:
	 * 
	 * type "value"
	 * 
	 * Validation Rules:
	 * - is "value"
	 * - contains "some value"
	 * - regex "some regex"
	 * - equals "anotherfield"
	 * - empty
	 * - length "6+" (eg: 6, 6+, 6-12, 12-)
	 * - gt "value"
	 * - ge "value"
	 * - lt "value"
	 * - le "value"
	 * - func "func_name" (or function "func_name")
	 * - unique "dbtablename.columnname"
	 * - exists "path/to/directory"
	 * - numeric
	 * - email
	 * - header
	 * 
	 * Note: Any rule may be negated by preceeding it with a 'not', for example:
	 * - not empty
	 * - not contains "some value"
	 * 
	 * New in 1.2:
	 * - Added a 'unique' rule, which compares the value against a specified field
	 *   in a database table.
	 * - Fixed a bug in the 'length' rule evaluation.
	 * 
	 * New in 1.4:
	 * - Added 'exists' and 'not exists' rules, which checks if the value given
	 *   exists (or doesn't) as a file name in the path provided by the rule.
	 * 
	 * New in 1.6:
	 * - Abstracted 'not empty' and 'not exists' so that 'not' now negates any
	 *   rule, and 'empty' and 'exists' are ordinary rules now.  This required
	 *   the addition of two new methods, _validate(), and _validateNegated(),
	 *   and a new $negated property.
	 *
	 * New in 1.8:
	 * - Added warning notices when rules fail the syntax parser.
	 * - Added new rule type "numeric", which checks the data type of the value
	 *   to see whether it is a valid number or not.
	 * 
	 * New in 2.0:
	 * - New rules: 'email' and 'header' which help prevent form abuse by spammers.
	 *   'email' checks that it is a valid email, and 'header' checks that there
	 *   are no newlines in the field so that it can't pass extra headers to your
	 *   mail() function.
	 *
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget ('name');
	 * $widget->addRule ('is "foo"', 'You must enter "foo" to pass!');
	 * 
	 * // note: MailFormRule is never accessed directly.
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	MailForm
	 * @author	John Luxford <lux@simian.ca>
	 * @copyright	Copyright (C) 2001-2003, Simian Systems Inc.
	 * @license	http://www.sitellite.org/index/license	Simian Open Software License
	 * @version	2.0, 2002-10-12, $Id: Rule.php,v 1.5 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MailFormRule {
	/**
	 * The original unmodified rule definition.
	 * 
	 * @access	public
	 * 
	 */
	var $rule;

	/**
	 * The name of the widget.
	 * 
	 * @access	public
	 * 
	 */
	var $name;

	/**
	 * The error message for this rule.
	 * 
	 * @access	public
	 * 
	 */
	var $msg;

	/**
	 * The rule type.  Can be 'is', 'contains', 'regex', 'not empty',
	 * 'equals', 'length', 'gt', 'ge', 'lt', 'le', or 'func'.
	 * 
	 * @access	public
	 * 
	 */
	var $type;

	/**
	 * The rule value.  This corresponds to the part of the rule
	 * in double quotes (ie. type "value").
	 * 
	 * @access	public
	 * 
	 */
	var $value;

	/**
	 * If a 'not' is present at the start of the rule, this will be
	 * set to true, otherwise false.
	 * 
	 * @access	public
	 * 
	 */
	var $negated = false;

	/**
	 * Constructor Method.
	 * 
	 * @access	public
	 * @param	string	$rule
	 * @param	string	$name
	 * @param	string	$msg
	 * 
	 */
	function MailFormRule ($rule, $name, $msg = '') {
		$this->rule = $rule;
		$this->name = $name;
		if (! empty ($msg)) {
			$this->msg = $msg;
		} else {
			$this->msg = 'Oops!  The following field was not filled in correctly: ' . $this->name . '.  Please fix this to continue.';
		}
		$this->parseRuleStatement ($rule);
	}

	/**
	 * Parses the original rule into the $type and $value properties.
	 * 
	 * @access	public
	 * @param	string	$rule
	 * 
	 */
	function parseRuleStatement ($rule) {
		if (preg_match ('/^(not )?(empty|length|unique|contains|func|function|regex|equals|is|gt|lt|ge|le|exists|numeric|email|header)( ("|\')(.*)\4)?$/', $rule, $regs)) {
			if ($regs[2] == 'function') {
				$this->type = 'func';
			} else {
				$this->type = $regs[2];
			}
			if ($regs[1] == 'not ') {
				$this->negated = true;
			}
			$this->value = $regs[5];
		} else {
			trigger_error ('Invalid MailForm rule (' . $this->name . '): ' . $rule, E_USER_WARNING);
		}
	}

	/**
	 * Validates the value given against itself.  Returns false on
	 * failure and true on success.
	 * 
	 * @access	public
	 * @param	string	$value
	 * @param	object	$form
	 * @param	object	$cgi
	 * @return	boolean
	 * 
	 */
	function validate ($value, $form, $cgi) {
		if ($this->negated) {
			return $this->_validateNegated ($value, $form, $cgi);
		} else {
			return $this->_validate ($value, $form, $cgi);
		}
	}

	/**
	 * Validates the value given against itself.  Returns false on
	 * failure and true on success.
	 * 
	 * @access	public
	 * @param	string	$value
	 * @param	object	$form
	 * @param	object	$cgi
	 * @return	boolean
	 * 
	 */
	function _validate ($value, $form, $cgi) {
		if ($this->type == 'empty') {
			if (! empty ($value)) {
				return false;
			}
		} elseif ($this->type == 'length') {
			if (preg_match ('/^([0-9]+)([+-]?)([0-9]*)$/', $this->value, $regs)) {
				if (! empty ($regs[3])) {
					if (strlen ($value) < $regs[1] || strlen ($value) > $regs[3]) {
						return false;
					}
				} elseif ($regs[2] == '+' && strlen ($value) < $regs[1]) {
					return false;
				} elseif ($regs[2] == '-' && strlen ($value) > $regs[1]) {
					return false;
				} elseif (empty ($regs[2]) && strlen ($value) != $regs[1]) {
					return false;
				}
			}
		} elseif ($this->type == 'unique') {
			list ($table, $column) = preg_split ('/[\.:\/]/', $this->value);
			global $db;
			$res = $db->fetch ('select ' . $column . ' from ' . $table . ' where ' . $column . ' = ??', $value);
			if ($db->rows > 0) {
				// it's not unique
				return false;
			} elseif (! $res && $db->error) {
				return false;
			}
		} elseif ($this->type == 'exists') {
			if (! empty ($value)) {
				if (! @file_exists ($this->value . '/' . $value)) {
					// file name does not exist
					return false;
				}
			}
		} elseif ($this->type == 'contains') {
			if (! stristr ($value, $this->value)) {
				return false;
			}
		} elseif ($this->type == 'func') {
			$GLOBALS['mailform_current_form'] =& $form;
			$func = $this->value;
			if (! $func ($form->getValues ($cgi))) {
				return false;
			}
		} elseif ($this->type == 'regex') {
			if (! ereg ($this->value, $value)) {
				return false;
			}
		} elseif ($this->type == 'equals') {
			if ($value != $form->widgets[$this->value]->getValue ($cgi)) {
				return false;
			}
		} elseif ($this->type == 'is') {
			if ($value != $this->value) {
				return false;
			}
		} elseif ($this->type == 'gt') {
			if ($value <= $this->value) {
				return false;
			}
		} elseif ($this->type == 'lt') {
			if ($value >= $this->value) {
				return false;
			}
		} elseif ($this->type == 'ge') {
			if ($value < $this->value) {
				return false;
			}
		} elseif ($this->type == 'le') {
			if ($value > $this->value) {
				return false;
			}
		} elseif ($this->type == 'numeric') {
			if (! is_numeric ($value)) {
				return false;
			}
		} elseif ($this->type == 'email') {
			if (! preg_match ("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/" , $value)) {
				return false;
			}
		} elseif ($this->type == 'header') {
			if (preg_match ("/[\r\n]/s" , $value)) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Validates the value given against itself.  Returns false on
	 * failure and true on success.
	 * 
	 * @access	public
	 * @param	string	$value
	 * @param	object	$form
	 * @param	object	$cgi
	 * @return	boolean
	 * 
	 */
	function _validateNegated ($value, $form, $cgi) {
		if ($this->type == 'empty') {
			if (empty ($value)) {
				return false;
			}
		} elseif ($this->type == 'length') {
			if (preg_match ('/^([0-9]+)([+-]?)([0-9]*)$/', $this->value, $regs)) {
				if (! empty ($regs[3])) {
					if (strlen ($value) >= $regs[1] || strlen ($value) <= $regs[3]) {
						return false;
					}
				} elseif ($regs[2] == '+' && strlen ($value) >= $regs[1]) {
					return false;
				} elseif ($regs[2] == '-' && strlen ($value) <= $regs[1]) {
					return false;
				} elseif (empty ($regs[2]) && strlen ($value) == $regs[1]) {
					return false;
				}
			}
		} elseif ($this->type == 'unique') {
			list ($table, $column) = preg_split ('/[\.:\/]/', $this->value);
			global $db;
			$res = $db->fetch ('select ' . $column . ' from ' . $table . ' where ' . $column . ' = ??', $value);
			if ($db->rows <= 0) {
				// it's not unique
				return false;
			} elseif (! $res && $db->error) {
				return false;
			}
		} elseif ($this->type == 'exists') {
			if (! empty ($value)) {
				if (@file_exists ($this->value . '/' . $value)) {
					// file name does not exist
					return false;
				}
			}
		} elseif ($this->type == 'contains') {
			if (stristr ($value, $this->value)) {
				return false;
			}
		} elseif ($this->type == 'func') {
			$func = $this->value;
			if ($func ($form->getValues ($cgi))) {
				return false;
			}
		} elseif ($this->type == 'regex') {
			if (ereg ($this->value, $value)) {
				return false;
			}
		} elseif ($this->type == 'equals') {
			if ($value == $form->widgets[$this->value]->getValue ($cgi)) {
				return false;
			}
		} elseif ($this->type == 'is') {
			if ($value == $this->value) {
				return false;
			}
		} elseif ($this->type == 'gt') {
			if ($value > $this->value) {
				return false;
			}
		} elseif ($this->type == 'lt') {
			if ($value < $this->value) {
				return false;
			}
		} elseif ($this->type == 'ge') {
			if ($value >= $this->value) {
				return false;
			}
		} elseif ($this->type == 'le') {
			if ($value <= $this->value) {
				return false;
			}
		} elseif ($this->type == 'numeric') {
			if (is_numeric ($value)) {
				return false;
			}
		} elseif ($this->type == 'email') {
			if (preg_match ("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/" , $value)) {
				return false;
			}
		} elseif ($this->type == 'header') {
			if (! preg_match ("/[\r\n]/s" , $value)) {
				return false;
			}
		}
		return true;
	}
}



?>