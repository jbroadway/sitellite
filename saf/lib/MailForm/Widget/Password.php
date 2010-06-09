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
// Password widget.  Displays an HTML <input type="password" /> form field.
//

/**
	 * Password widget.  Displays an HTML <input type="password" /> form field.
	 * 
	 * New in 1.2:
	 * - Added encrypt() and verify() methods.
	 * 
	 * New in 1.4:
	 * - Added a makeStrong() method that automatically defines a series of recommended
	 *   rules for all passwords, such as minimum length and required characters to
	 *   reduce the odds of guessability.
	 * - Added a generate() method that generates strong passwords for you.
	 * - Added a $ignoreEmpty property.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_password ('name');
	 * $widget->validation ('is "foo"');
	 * $widget->setValue ('foo');
	 * $widget->error_message = 'Oops!  This widget is being unruly!';
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	MailForm
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.2, 2002-08-27, $Id: Password.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_password extends MF_Widget {
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
	var $type = 'password';

	/**
	 * Determines whether or not this password field should be ignored
	 * if left blank.  This is useful for situations where a password change
	 * field may be present in a form but is not required to be filled out to
	 * change it since the current value cannot or should not be sent
	 * back to the browser.  Defaults to true.
	 * 
	 * @access	public
	 * 
	 */
	var $ignoreEmpty = true;

	/**
	 * Encrypts the value given with the optional salt.  If the
	 * value is also missing, uses the $data_value property.  Returns the
	 * encrypted string.
	 * 
	 * @access	public
	 * @param	string	$value
	 * @param	string	$salt
	 * @return	string
	 * 
	 */
	function encrypt ($value = '', $salt = '') {
		return better_crypt ($value, $salt);
	}

	/**
	 * Verifies input agains an encrypted value to see if it
	 * matches.
	 * 
	 * @access	public
	 * @param	string	$input
	 * @param	string	$encrypted
	 * @return	boolean
	 * 
	 */
	function verify ($input, $encrypted) {
		return better_crypt_compare ($input, $encrypted);
	}

	/**
	 * Creates a series of rules for the current widget that state the
	 * minimum length of a valid password (8), and that it must contain at least
	 * one of each of the following: a lowercase letter, an uppercase letter,
	 * a number, a symbol.
	 * 
	 * @access	public
	 * 
	 */
	function makeStrong () {
		global $intl;
		$this->addRule ('length "8+"', $intl->get ('Your password must be at least eight characters in length.'));
		$this->addRule ('regex "[a-z]"', $intl->get ('Your password must contain at least one lowercase letter.'));
		$this->addRule ('regex "[A-Z]"', $intl->get ('Your password must contain at least one uppercase letter.'));
		$this->addRule ('regex "[0-9]"', $intl->get ('Your password must contain at least one number.'));
		$this->addRule ('regex "[^a-zA-Z0-9]"', $intl->get ('Your password must contain at least one symbol.'));
	}

	/**
	 * Creates a "secure" password of the specified $length using a random
	 * combination of lowercase and uppercase letters, numbers, and symbols
	 * (containing one of each for every four characters of the password, not
	 * necessarily in any order).  This aims to help site administrators enforce
	 * more secure password policies, in conjunction with the makeStrong() method
	 * for verifying that user-created passwords meet certain security criteria.
	 * 
	 * @access	public
	 * @param	integer	$length
	 * @return	string
	 * 
	 */
	function generate ($length = 8) {
		$clist = array ();
		$clist[] = 'abcdefghijklmnopqrstuvwxyz';
		$clist[] = strtoupper ($clist[0]);
		$clist[] = '1234567890';
		$clist[] = '~`!@#$%^&*()+=.,/?\\|{}[]<>\'"_-';
		$orders = array (
			'1234', '1243', '1324', '1342', '1423', '1432',
			'2134', '2143', '2314', '2341', '2413', '2431',
			'3124', '3142', '3214', '3241', '3412', '3421',
			'4123', '4132', '4213', '4231', '4312', '4321',
		);
		$plist = array ();
		$pass = '';

		$ord = $orders[mt_rand (0, count ($orders) - 1)];

		while (count ($plist) < $length) {
			foreach ($clist as $key => $value) {
				$plist[] = substr ($value, mt_rand (0, strlen ($value) - 1), 1);
				if (count ($plist) >= $length) {
					break;
				}
			}
		}

		$orders = array_reverse ($orders, true);
		$ord2 = $orders[mt_rand (0, count ($orders) - 1)];

		for ($j = 0; $j < 2; $j++) {
			for ($i = 0; $i < 4; $i++) {
				$one = $ord[$i] - 1;
				$two = $ord[$i] - 1 + 4;
				$pass .= $plist[$one];
				$pass .= $plist[$two];
			}
		}
		return substr ($pass, 0, $length);
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
		if ($this->ignoreEmpty && (empty ($cgi->{$this->name}) || ! isset ($cgi->{$this->name}))) {
			return true;
		} else {
			return parent::validate ($value, $form, $cgi);
		}
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
		$attrstr = $this->getAttrs ();
		if ($generate_html) {
			return "\t" . '<tr' . $this->getClasses () . '>' . "\n\t\t" . '<td class="label"><label for="' . $this->name . '" id="' . $this->name . '-label"' . $this->invalid () . '>' . $this->display_value . '</label></td>' . "\n\t\t" .
				'<td class="field"><input type="password" ' . $attrstr . ' value="' . htmlentities_compat ($this->data_value, ENT_COMPAT, $intl->charset) .
				'" ' . $this->extra . ' /></td>' . "\n\t" . '</tr>' . "\n";
		} else {
			return '<input type="password" ' . $attrstr . ' value="' . htmlentities_compat ($this->data_value, ENT_COMPAT, $intl->charset) . '" ' . $this->extra . ' />';
		}
	}
}



?>
