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
// Pseudo-Widget that displays the output of a simple template.
//

/**
	 * Pseudo-Widget that displays the output of a simple template.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_template ('template1');
	 * $widget->setValue ('some_template.spt');
	 * $widget->display ();
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	MailForm
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.0, 2002-08-31, $Id: Template.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_template extends MF_Widget {
	/**
	 * The template to display.
	 * 
	 * @access	public
	 * 
	 */
	var $template;

	/**
	 * The data to pass to the template.
	 *
	 * @access	public
	 *
	 */
	var $data = array ();

	/**
	 * This is the short name for this widget.  The short name is
	 * the class name minus the 'MF_Widget_' prefix.
	 * 
	 * @access	public
	 * 
	 */
	var $type = 'template';

	/**
	 * If you define any submit buttons that are meant to actually
	 * submit the form (ie. not stopped in their tracks via the
	 * onclick handler) within your template, then you need to specify
	 * those here, so that MailForm knows the form was submitted.
	 *
	 * @access	public
	 *
	 */
	var $submitButtons = array ();

	/**
	 * Constructor Method.
	 * 
	 * @access	public
	 * @param	string	$name
	 * 
	 */
	function MF_Widget_template ($name) {
		$this->name = $name;
		$this->passover_isset = true;
		$this->error_message = '';
	}

	/**
	 * Validates the widget against its set of $rules.  Returns false
	 * on failure to pass any rule.
	 * 
	 * @access	public
	 * @param	string	$value
	 * @param	object	$form
	 * @param	object	$cgi
	 * @return	boolean
	 * 
	 */
	function validate ($value, $form, $cgi) {
		return true;
	}

	/**
	 * Sets the data values to pass to the template.  If $value
	 * is given, sets $this->value as a hash, otherwise, as a string.
	 * 
	 * @access	public
	 * @param	string	$key
	 * @param	string	$value
	 * 
	 */
	function setValues ($key, $value = '') {
		if (! empty ($value)) {
			$this->data[$key] = $value;
		} else {
			// assuming $key as a hash or object
			$this->data = $key;
		}
	}

	/**
	 * Sets the section title.  The second parameter is ignored.
	 * 
	 * @access	public
	 * @param	string	$key
	 * @param	string	$value
	 * 
	 */
	function setValue ($key = '', $value = '') {
		if (! empty ($key)) {
			$this->template = $key;
		}
	}

	/**
	 * Fetches the actual value for this widget.
	 * 
	 * @access	public
	 * @param	object	$cgi
	 * @return	string
	 * 
	 */
	function getValue ($cgi = '') {
		return '';
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
		if (count ($this->data) == 0 && is_object ($this->form)) {
			$this->data =& $this->form;
		}
		return template_simple ($this->template, $this->data);
	}
}

?>