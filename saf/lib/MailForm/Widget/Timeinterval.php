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
// Timeinterval widget.  Displays 3 select boxes which represent the hour
// (1 through 12), the minutes (:00, :15, :30, and :45), and AM/PM.
//

/**
	 * Timeinterval widget.  Displays 3 select boxes which represent the hour
	 * (1 through 12), the minutes (:00, :15, :30, and :45), and AM/PM.
	 * 
	 * New in 1.2:
	 * - Added a setDefault(value) method.
	 * 
	 * New in 1.4:
	 * - Changed parseTime() to use the Date package's roundTime() method.
	 * 
	 * New in 1.6:
	 * - Added automatic I18n (if $intl object exists) calls in display() to allow
	 *   for translations of nullable names.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_timeinterval ('name');
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
	 * @version	1.6, 2002-08-25, $Id: Timeinterval.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_timeinterval extends MF_Widget {
	/**
	 * These properties are deprecated.  All properties except $extra
	 * that are not inherited from MF_Widget are private.
	 * 
	 * @access	private
	 * 
	 */
	var $hour;

	/**
	 * These properties are deprecated.  All properties except $extra
	 * that are not inherited from MF_Widget are private.
	 * 
	 * @access	private
	 * 
	 */
	var $minute;

	/**
	 * These properties are deprecated.  All properties except $extra
	 * that are not inherited from MF_Widget are private.
	 * 
	 * @access	private
	 * 
	 */
	var $second;

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
	var $type = 'timeinterval';

	/**
	 * Constructor Method.
	 * 
	 * @access	public
	 * @param	string	$name
	 * 
	 */
	function MF_Widget_timeinterval ($name) {
		// initialize core Widget settings
		parent::MF_Widget ($name);

		// load Hidden and Select widgets, on which this widget depends
		$GLOBALS['loader']->import ('saf.MailForm.Widget.Hidden');
		$GLOBALS['loader']->import ('saf.MailForm.Widget.Select');

		// initialize custom widget settings
		/*
		list (
			$this->data_value_HOUR,
			$this->data_value_MINUTE,
			$this->data_value_AMPM
		) = $this->parseTime (date ('H:i:s'));*/
	}

	/**
	 * Sets the actual value for this widget.  An optional second
	 * parameter can be passed, which is unused here, but can be used in
	 * complex widget types to assign parts of a value and piece it together
	 * from multiple physical form fields.
	 * 
	 * @access	public
	 * @param	string	$value
	 * @param	string	$inner_component
	 * 
	 */
	function setValue ($value = '', $inner_component = '') {
		// accept values as HH:MM:SS and round to nearest :15
		if (! empty ($inner_component)) {
			$this->{'data_value_' . $inner_component} = $value;
		} elseif (! empty ($value)) {
			list (
				$this->data_value_HOUR,
				$this->data_value_MINUTE,
				$this->data_value_AMPM
			) = $this->parseTime ($value);
		} else {
			$this->data_value_HOUR = '';
			$this->data_value_MINUTE = '';
			$this->data_value_AMPM = '';
		}
	}

	/**
	 * Parses a time string (format: HH:MM:SS) and returns the
	 * hour, minute and ampm values for internal use with this widget.
	 * 
	 * @access	public
	 * @param	string	$time
	 * @return	array
	 * 
	 */
	function parseTime ($time) {
		global $loader;
		$loader->import ('saf.Date');
		list ($hour, $min, $sec) = split (':', Date::roundTime ($time, 15));
		$MINUTE = ':' . $min;
		if ($hour >= 12) {
			$HOUR = $hour - 12;
			$AMPM = 'pm';
		} else {
			$HOUR = $hour;
			$AMPM = 'am';
		}
		return array ($HOUR, $MINUTE, $AMPM);
	}

	/**
	 * Takes your hour, minute and ampm values (used
	 * internally), and returns a time string of the format
	 * HH:MM:SS.
	 * 
	 * @access	public
	 * @param	string	$hour
	 * @param	string	$minute
	 * @param	string	$ampm
	 * @return	string
	 * 
	 */
	function makeTime ($hour, $minute, $ampm) {
		if (empty ($hour) && empty ($minute) && empty ($ampm)) {
			return '';
		}
		if ($ampm == 'am' && $hour == '12') {
			return '00' . $minute . ':00';
		} elseif ($ampm == 'pm' && $hour == '12') {
			return '12' . $minute . ':00';
		} elseif ($ampm == 'pm') {
			$hour += 12;
		}
		if (strlen ($hour) == 1) {
			$hour = '0' . $hour;
		}
		return $hour . $minute . ':00';
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
		if (! is_object ($cgi)) {
			return $this->makeTime ($this->data_value_HOUR, $this->data_value_MINUTE, $this->data_value_AMPM);
		} else {
			return $this->makeTime ($cgi->{'MF_' . $this->name . '_HOUR'}, $cgi->{'MF_' . $this->name . '_MINUTE'}, $cgi->{'MF_' . $this->name . '_AMPM'});
		}
	}

	/**
	 * Gives this widget a default value.  Accepts a time string
	 * of the format 'HH:MM:SS'.
	 * 
	 * @access	public
	 * @param	string	$value
	 * 
	 */
	function setDefault ($value) {
		list ($this->data_value_HOUR, $this->data_value_MINUTE, $this->data_value_AMPM) = $this->parseTime ($value);
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
		$_hour = new MF_Widget_select ('MF_' . $this->name . '_HOUR');
		$_hour->nullable = $this->nullable;
		$_hour->setValues (array (
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5',
			'6' => '6',
			'7' => '7',
			'8' => '8',
			'9' => '9',
			'10' => '10',
			'11' => '11',
			'12' => '12',
		));
		$_hour->data_value = $this->data_value_HOUR;

		$_minute = new MF_Widget_select ('MF_' . $this->name . '_MINUTE');
		$_minute->nullable = $this->nullable;
		$_minute->setValues (array (
			':00' => ':00',
			':15' => ':15',
			':30' => ':30',
			':45' => ':45',
		));
		$_minute->data_value = $this->data_value_MINUTE;

		$_ampm = new MF_Widget_select ('MF_' . $this->name . '_AMPM');
		$_ampm->nullable = $this->nullable;
		$_ampm->setValues (array (
			'am' => 'am',
			'pm' => 'pm',
		));
		$_ampm->data_value = $this->data_value_AMPM;

		if ($this->nullable || $this->addblank) {
			global $intl;
			if (is_object ($intl)) {
				$_ampm->value[''] = $intl->get ('AM/PM');
				$_minute->value[''] = $intl->get ('Minute');
				$_hour->value[''] = $intl->get ('Hour');
			} else {
				$_ampm->value[''] = 'AM/PM';
				$_minute->value[''] = 'Minute';
				$_hour->value[''] = 'Hour';
			}
		}

		$_hour->extra = $this->extra;
		$_minute->extra = $this->extra;
		$_ampm->extra = $this->extra;

		$_time = new MF_Widget_hidden ($this->name);
		if ($generate_html) {
			$data = $_time->display (0) . "\n";
			$data .= "\t<tr>\n\t\t<td class=\"label\"><label for=\"" . $this->name . '"' . $this->invalid () . '>' . $this->display_value . "</label></td>\n\t\t<td class=\"field\">" . 
				$_hour->display (0) . '&nbsp;' . $_minute->display (0) . '&nbsp;' . $_ampm->display (0) .
				"</td>\n\t</tr>\n";
		} else {
			$data = $_time->display (0);
			$data .= $_hour->display (0) . '&nbsp;' . $_minute->display (0) . '&nbsp;' . $_ampm->display (0);
		}
		return $data;
	}
}



?>