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
// Datetime widget.  Displays 3 select boxes representing the year, month,
// and day, as well as three text boxes representing the hour, minute, and
// second.
//

/**
	 * Datetimeinterval widget.  Displays 3 select boxes representing the year, month,
	 * and day, as well as three text boxes representing the hour, minute in the form
	 * of :00, :15, :30, and :45, and am/pm.
	 * 
	 * Please note: MySQL handles timestamp and datetime column types differently, and while
	 * both are supported by this widget, it is important to be aware of these differences
	 * when designing your database tables.
	 * 
	 * New in 1.2:
	 * - Added a setDefault(value) method.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_datetimeinterval ('name');
	 * $widget->validation ('is "foo"');
	 * $widget->setValue ('foo');
	 * $widget->error_message = 'Oops!  This widget is being unruly!';
	 * 
	 * ?>
	 * </code>
	 * 
	 * @package	MailForm
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.2, 2002-03-25, $Id: Datetimeinterval.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_datetimeinterval extends MF_Widget {
	/**
	 * Unused and deprecated.
	 * 
	 * @access	private
	 * 
	 */
	var $date;

	/**
	 * Unused and deprecated.
	 * 
	 * @access	private
	 * 
	 */
	var $time;

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
	var $type = 'datetimeinterval';

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
	function MF_Widget_datetimeinterval ($name) {
		// initialize core Widget settings
		parent::MF_Widget ($name);

		// load Hidden and Select widgets, on which this widget depends
		$GLOBALS['loader']->import ('saf.MailForm.Widget.Hidden');
		$GLOBALS['loader']->import ('saf.MailForm.Widget.Date');
		$GLOBALS['loader']->import ('saf.MailForm.Widget.Timeinterval');

		/*
		// initialize custom widget settings
		$this->data_value_DATE = date ('Y-m-d');
		list (
			$this->data_value_DATE_YEAR,
			$this->data_value_DATE_MONTH,
			$this->data_value_DATE_DAY
		) = split ('-', $this->data_value_DATE);
		list (
			$this->data_value_TIME_HOUR,
			$this->data_value_TIME_MINUTE,
			$this->data_value_TIME_AMPM
		) = MF_Widget_timeinterval::parseTime (date ('H:i:s'));*/
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
		if (! empty ($inner_component)) {
			$this->{'data_value_' . $inner_component} = $value;
		} elseif (! strstr ($value, ' ')) {
			if (preg_match ('/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/', $value, $regs)) {
				$this->data_value_DATE_YEAR = $regs[1];
				$this->data_value_DATE_MONTH = $regs[2];
				$this->data_value_DATE_DAY = $regs[3];
				list (
					$this->data_value_TIME_HOUR,
					$this->data_value_TIME_MINUTE,
					$this->data_value_TIME_AMPM
				) = MF_Widget_timeinterval::parseTime ($regs[4] . ':' . $regs[5] . ':' . $regs[6]);
			}
		} elseif (strstr ($value, ' ')) {
			list (
				$this->data_value_DATE,
				$this->data_value_TIME
			) = split (' ', $value);
			list (
				$this->data_value_DATE_YEAR,
				$this->data_value_DATE_MONTH,
				$this->data_value_DATE_DAY
			) = split ('-', $this->data_value_DATE);
			list (
				$this->data_value_TIME_HOUR,
				$this->data_value_TIME_MINUTE,
				$this->data_value_TIME_AMPM
			) = MF_Widget_timeinterval::parseTime ($this->data_value_TIME);
		} elseif (empty ($value)) {
			$this->data_value_DATE = '';
			$this->data_value_DATE_YEAR = '';
			$this->data_value_DATE_MONTH = '';
			$this->data_value_DATE_DAY = '';
			$this->data_value_TIME_HOUR = '';
			$this->data_value_TIME_MINUTE = '';
			$this->data_value_TIME_AMPM = '';
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
		if (! is_object ($cgi)) {
			if (empty ($this->data_value_DATE_YEAR) && empty ($this->data_value_DATE_MONTH) && empty ($this->data_value_DATE_DAY)) {
				return MF_Widget_timeinterval::makeTime (
					$this->data_value_TIME_HOUR,
					$this->data_value_TIME_MINUTE,
					$this->data_value_TIME_AMPM
				);
			}
			return
				$this->data_value_DATE_YEAR . '-' .
				$this->data_value_DATE_MONTH . '-' .
				$this->data_value_DATE_DAY . ' ' .
				MF_Widget_timeinterval::makeTime (
					$this->data_value_TIME_HOUR,
					$this->data_value_TIME_MINUTE,
					$this->data_value_TIME_AMPM
				);
		} else {
			if (empty ($cgi->{'MF_MF_' . $this->name . '_DATE_YEAR'}) && empty ($cgi->{'MF_MF_' . $this->name . '_DATE_MONTH'}) && empty ($cgi->{'MF_MF_' . $this->name . '_DATE_DAY'})) {
				return MF_Widget_timeinterval::makeTime (
					$cgi->{'MF_MF_' . $this->name . '_TIME_HOUR'},
					$cgi->{'MF_MF_' . $this->name . '_TIME_MINUTE'},
					$cgi->{'MF_MF_' . $this->name . '_TIME_AMPM'}
				);
			}
			return
				$cgi->{'MF_MF_' . $this->name . '_DATE_YEAR'} . '-' .
				$cgi->{'MF_MF_' . $this->name . '_DATE_MONTH'} . '-' .
				$cgi->{'MF_MF_' . $this->name . '_DATE_DAY'} . ' ' .
				MF_Widget_timeinterval::makeTime (
					$cgi->{'MF_MF_' . $this->name . '_TIME_HOUR'},
					$cgi->{'MF_MF_' . $this->name . '_TIME_MINUTE'},
					$cgi->{'MF_MF_' . $this->name . '_TIME_AMPM'}
				);
		}
	}

	/**
	 * Gives this widget a default value.  Accepts a date string
	 * of the format 'YYYY-MM-DD HH:MM:SS' or 'YYYYMMDDHHMMSS'.
	 * 
	 * @access	public
	 * @param	string	$value
	 * 
	 */
	function setDefault ($value) {
		if (strstr ($value, ' ')) {
			list ($d, $t) = split (' ', $value);
			list ($this->data_value_DATE_YEAR, $this->data_value_DATE_MONTH, $this->data_value_DATE_DAY) = split ('-', $d);
			list ($this->data_value_TIME_HOUR, $this->data_value_TIME_MINUTE, $this->data_value_TIME_AMPM) = MF_Widget_timeinterval::parseTime ($t);
		} else {
			// handle YYYYMMDDHHMMSS format
			if (preg_match ('/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/', $value, $regs)) {
				$this->data_value_DATE_YEAR = $regs[1];
				$this->data_value_DATE_MONTH = $regs[2];
				$this->data_value_DATE_DAY = $regs[3];
				list (
					$this->data_value_TIME_HOUR,
					$this->data_value_TIME_MINUTE,
					$this->data_value_TIME_AMPM
				) = MF_Widget_timeinterval::parseTime ($regs[4] . ':' . $regs[5] . ':' . $regs[6]);
			}
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
		global $simple;
		$_date = new MF_Widget_date ('MF_' . $this->name . '_DATE');
		$_date->nullable = $this->nullable;
		$_date->addblank = $this->addblank;
		$_date->setValue (
			$this->data_value_DATE_YEAR . '-' .
			$this->data_value_DATE_MONTH . '-' .
			$this->data_value_DATE_DAY
		);

		$_time = new MF_Widget_timeinterval ('MF_' . $this->name . '_TIME');
		$_time->nullable = $this->nullable;
		$_time->addblank = $this->addblank;
		$_time->setValue (
			$_time->makeTime (
				$this->data_value_TIME_HOUR,
				$this->data_value_TIME_MINUTE,
				$this->data_value_TIME_AMPM
			)
		);

		$_date->extra = $this->extra;
		$_time->extra = $this->extra;

//		echo '<pre>'; print_r ($this); print_r ($_date); echo "\n"; print_r ($_time); echo '</pre>';

		$_datetime = new MF_Widget_hidden ($this->name);
		if ($generate_html) {
			$data = $_datetime->display (0) . "\n";
			$data .= "\t<tr>\n\t\t<td valign=\"top\" class=\"label\">" . '<label for="' . $this->name . '"' . $this->invalid () . '>' . $simple->fill ($this->label_template, $this, '', true) . "</label></td>\n\t\t<td class=\"field\">" . 
				$_date->display (0) . '<br />' . $_time->display (0) .
				"</td>\n\t</tr>\n";
		} else {
			$data = $_datetime->display (0);
			$data .= $_date->display (0) . '<br />' . $_time->display (0);
		}
		return $data;
	}
}



?>