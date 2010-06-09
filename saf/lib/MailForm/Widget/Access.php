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
// Access widget.  Displays a select box with a list of access levels
// the user is allowed to access.
//

$GLOBALS['loader']->import ('saf.MailForm.Widget.Select');

/**
	 * Access widget.  Displays a select box with a list of access levels
	 * the user is allowed to access.
	 * 
	 * New in 1.2:
	 * - Added a setAllowed() method.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_access ('name');
	 * $widget->validation ('is "foo"');
	 * $widget->error_message = 'Oops!  This widget is being unruly!';
	 * $widget->allowed = array ('all');
	 * echo $widget->display ();
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	MailForm
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.2, 2002-08-05, $Id: Access.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_access extends MF_Widget_select {
	/**
	 * The access levels the user is allowed to access.
	 * 
	 * @access	public
	 * 
	 */
	var $allowed = array ();

	/**
	 * This is the short name for this widget.  The short name is
	 * the class name minus the 'MF_Widget_' prefix.
	 * 
	 * @access	public
	 * 
	 */
	var $type = 'access';

	/**
	 * Determines whether this widget can be set to null in the database.
	 * 
	 * @access	public
	 * 
	 */
	var $nullable;

	/**
	 * Constructor Method.
	 * 
	 * @access	public
	 * @param	string	$name
	 * 
	 */
	function MF_Widget_access ($name) {
		// initialize core Widget settings
		parent::MF_Widget_select ($name);
	}

	/**
	 * Sets the list of allowed access levels.
	 * 
	 * @access	public
	 * @param	array	$allowed
	 * @return	boolean
	 * 
	 */
	function setAllowed ($allowed = array ()) {
		if (is_array ($allowed)) {
			$this->allowed = $allowed;
			return true;
		}
		return false;
	}

	/**
	 * Returns the display HTML for this widget.  The optional
	 * parameter determines whether or not to automatically display the widget
	 * nicely, or whether to simply return the widget (for use in a template).
	 * 
	 * @access	public
	 * @param	boolean	$generate_html
	 * @return	string
	 * 
	 */
	function display ($generate_html = 0) {
		foreach (array_keys (parse_ini_file ('inc/conf/auth/access/index.php')) as $access) {
			if (session_allowed ($access, 'w', 'access')) {
				$this->value[$access] = ucfirst ($access);
			}
		}
		if ($this->nullable) {
			$this->value[''] = 'BLANK';
		}

		return parent::display ($generate_html);
	}
}



?>