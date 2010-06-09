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
// Allowedbox widget.  Displays a group of related checkbox widgets.
//

$GLOBALS['loader']->import ('saf.MailForm.Widget.Checkbox');

/**
	 * Allowedbox widget.  Displays a group of related checkbox widgets.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_allowedbox ('name');
	 * 
	 * ?>
	 * </code>
	 * 
	 * @package	MailForm
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.0, 2003-08-16, $Id: Allowedbox.php,v 1.1.1.1 2005/04/29 04:44:33 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_allowedbox extends MF_Widget {
	/**
	 * A way to pass extra parameters to the HTML form tag, for
	 * example 'enctype="multipart/formdata"'.
	 * 
	 * @access	public
	 * 
	 */
	var $extra = '';

	/**
	 * This is the short name for this widget.  The short name is
	 * the class name minus the 'MF_Widget_' prefix.
	 * 
	 * @access	public
	 * 
	 */
	var $type = 'allowedbox';

	/**
	 * A list of "inner" buttons, which are MF_Widget_checkbox
	 * objects.
	 * 
	 * @access	public
	 * 
	 */
	var $buttons = array ();

	/**
	 * A list of headers, which are displayed next to the buttons.
	 * 
	 * @access	public
	 * 
	 */
	var $headers = array ();

	/**
	 * Constructor Method.  This creates the first submit widget in
	 * the list.  To retrieve a reference to this widget (to set it's value,
	 * etc.), use the getButton() method before adding any additional buttons
	 * with the addButton() method.  You can also set the value of the first
	 * button immediately by passing a second parameter to the constructor.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	string	$value
	 * 
	 */
	function MF_Widget_allowedbox ($name = 'allowed_box', $value = '') {
		parent::MF_Widget ($name);
		$this->passover_isset = true;
	}

	/**
	 * Adds a new submit button to the $buttons list.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	string	$value
	 * @return	object reference
	 * 
	 */
	function &addButton ($name = 'allowed_button', $value = '') {
		$this->buttons[] = new MF_Widget_checkbox ($this->name . '_' . $this->type . '_' . $name);
		$this->buttons[count ($this->buttons) - 1]->align = 'horizontal';
		$this->buttons[count ($this->buttons) - 1]->setValues ($value);
		return $this->buttons[count ($this->buttons) - 1];
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
		foreach ($this->buttons as $button) {
			if (! $button->validate ($value, $form, $cgi)) {
				$this->error_message = $button->error_message;
				return false;
			}
		}
		return true;
	}

	function setValue ($value = '', $inner_component = '') {
		global $cgi;
		foreach ($this->buttons as $k => $v) {
			if (isset ($cgi->{$v->name})) {
				$this->buttons[$k]->setValue ($cgi->{$v->name});
			}
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
		$res = array ();
		foreach ($this->buttons as $button) {
			$res[str_replace ($this->name . '_' . $this->type . '_', '', $button->name)] = $button->getValue ($cgi);
		}
		return $res;
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
		global $intl;
		$res = array ();
		foreach ($this->buttons as $key => $button) {
			$res[] = $this->buttons[$key]->display (true);
		}

		$headers = '';
		if (count ($this->headers) > 0) {
			$headers = '<tr>';
			foreach ($this->headers as $key => $header) {
				$width = '';
				if ($key == 0) {
					$width = ' width="60%"';
				}
				$headers .= '<td' . $width . ' align="center">' . $header . '</td>';
			}
			$headers .= '</tr>';
		}

		if ($generate_html) {
			return "\t" . '<tr>' . "\n\t\t" . '<td colspan="2" class="field"><fieldset><legend>' . $this->display_value . '</legend><table width="100%">' . NEWLINEx2 . $headers . NEWLINEx2 . join (NEWLINEx2, $res) . NEWLINE . '</table></fieldset></td>' . "\n\t" . '</tr>' . "\n";
		} else {
			return join (' <br /> ', $res);
		}
	}
}



?>