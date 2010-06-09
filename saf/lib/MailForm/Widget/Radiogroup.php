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
// Radiogroup widget.  Displays a group of related radio widgets.
//

$GLOBALS['loader']->import ('saf.MailForm.Widget.Radio');

/**
	 * Radiogroup widget.  Displays a group of related radio widgets.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_radiogroup ('name');
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	MailForm
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.0, 2003-08-16, $Id: Radiogroup.php,v 1.5 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_radiogroup extends MF_Widget {
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
	var $type = 'radiogroup';

	/**
	 * A list of "inner" radio buttons, which are MF_Widget_radio
	 * objects.
	 * 
	 * @access	public
	 * 
	 */
	var $buttons = array ();

	/**
	 * A list of headers, which are displayed above the radio buttons.
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
	function MF_Widget_radiogroup ($name = 'radio_group', $value = '') {
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
	function &addButton ($name = 'radio_button', $value = '') {
		$this->buttons[] = new MF_Widget_radio ($name);
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
			$res[$button->name] = $button->getValue ($cgi);
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
		global $intl, $cgi;
		$res = array ();
		foreach ($this->buttons as $key => $button) {
			$this->buttons[$key]->setValue ($cgi->{$button->name});
			$res[] = $this->buttons[$key]->display (true);
		}

		$headers = '';
		if (count ($this->headers) > 0) {
			$headers = '<tr>';
			foreach ($this->headers as $key => $header) {
				$width = '';
				if ($key == 0) {
					$width = ' width="35%"';
				}
				$headers .= '<td' . $width . '>' . $header . '</td>';
			}
			$headers .= '</tr>';
		}

		if ($generate_html) {
			return "\t" . '<tr>' . "\n\t\t" . '<td colspan="2"><fieldset><legend>' . $this->display_value . '</legend><table width="100%">' . NEWLINEx2 . $headers . NEWLINEx2 . join (NEWLINEx2, $res) . NEWLINE . '</table></fieldset></td>' . "\n\t" . '</tr>' . "\n";
		} else {
			return join (' <br /> ', $res);
		}
	}
}



?>