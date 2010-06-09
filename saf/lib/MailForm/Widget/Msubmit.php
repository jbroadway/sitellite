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
// Msubmit widget (as in Multiple Submit).  Displays a list of submit
// buttons all in a horizontal row.
//

$GLOBALS['loader']->import ('saf.MailForm.Widget.Submit');

/**
	 * Msubmit widget (as in Multiple Submit).  Displays a list of submit
	 * buttons all in a horizontal row.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_msubmit ('name');
	 * 
	 * $inner_one =& $widget->getButton ();
	 * $inner_one->addRule ('is "foo"', 'You must select "foo", even if you want to choose "bar".');
	 * $inner_one->setValues ('foo');
	 * 
	 * $inner_two =& $widget->addButton ('name', 'bar');
	 * 
	 * echo $widget->display (false);
	 * 
	 * ?>
	 * </code>
	 * 
	 * @package	MailForm
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.0, 2002-09-21, $Id: Msubmit.php,v 1.4 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_msubmit extends MF_Widget {
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
	var $type = 'submit';

	/**
	 * A list of "inner" submit buttons, which are MF_Widget_submit
	 * objects.
	 * 
	 * @access	public
	 * 
	 */
	var $buttons = array ();

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
	function MF_Widget_msubmit ($name = 'submit_button', $value = '') {
		$this->addButton ($name);
		if (! empty ($value)) {
			$this->buttons[0]->setValues ($value);
		}
		parent::MF_Widget ($name);
	}

	/**
	 * Returns a reference to the first submit button.
	 * 
	 * @access	public
	 * @return	object reference
	 * 
	 */
	function &getButton () {
		return $this->buttons[0];
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
	function &addButton ($name = 'submit_button', $value = '') {
		$this->buttons[] = new MF_Widget_submit ($name);
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
		foreach ($this->buttons as $button) {
			$res = $button->getValue ($cgi);
			if ($res) {
				return $res;
			}
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
		global $intl;
		$res = array ();
		foreach ($this->buttons as $button) {
			$res[] = $button->display (0);
		}

		if ($this->reference !== false) {
			$colspan = 3;
		} else {
			$colspan = 2;
		}

		if ($generate_html) {
			return "\t" . '<tr>' . "\n\t\t" . '<td colspan="' . $colspan . '" align="center" class="submit">' . join (' &nbsp; ', $res) . '</td>' . "\n\t" . '</tr>' . "\n";
		} else {
			return join (' &nbsp; ', $res);
		}
	}
}



?>