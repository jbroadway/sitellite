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
// Hiddenswitch widget.  Displays no form field, but returns a value
// based on another field's value.
//

/**
	 * Hiddenswitch widget.  Displays no form field, but returns a value
	 * based on another field's value.
	 * 
	 * New in 1.2:
	 * - Added a constructor method to set the $passover_isset value to true, which
	 *   is inherited from MF_Widget.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_hiddenswitch ('associations');
	 * $widget->set = array (
	 * 	'foo' => 'bar',
	 * 	'asdf' => 'qwerty',
	 * 	'apples' => 'oranges',
	 * );
	 * $widget->field = 'pick';
	 * 
	 * // now create a Select widget called 'pick' and give it the
	 * // options 'foo', 'asdf', and 'apples'.  when you submit
	 * // your form, you will end up with a field called 'associations'
	 * // containing the appropriate value using the value of
	 * // 'pick' as the key.
	 * 
	 * ?>
	 * </code>
	 * 
	 * @package	MailForm
	 * @author	John Luxford <lux@simian.ca>
	 * @copyright	Copyright (C) 2001-2003, Simian Systems Inc.
	 * @license	http://www.sitellite.org/index/license	Simian Open Software License
	 * @version	1.2, 2002-05-18, $Id: Hiddenswitch.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_hiddenswitch extends MF_Widget {
	/**
	 * The field to use to determine the key in the key/value $set property.
	 * 
	 * @access	public
	 * 
	 */
	var $field = '';

	/**
	 * This is the short name for this widget.  The short name is
	 * the class name minus the 'MF_Widget_' prefix.
	 * 
	 * @access	public
	 * 
	 */
	var $type = 'hiddenswitch';

	/**
	 * Constructor Method.  Also sets the $passover_isset property
	 * to false.
	 * 
	 * @access	public
	 * @param	string	$name
	 * 
	 */
	function MF_Widget_hiddenswitch ($name) {
		parent::MF_Widget ($name);
		$this->passover_isset = true;
	}

	/**
	 * Fetches the actual value for this widget.  Note: This field actual
	 * relies on a global $cgi object.
	 * 
	 * @access	public
	 * @param	object	$cgi
	 * @return	string
	 * 
	 */
	function getValue ($cgi = '') {
		return $this->value[$GLOBALS['cgi']->{$this->field}];
	}
}



?>
