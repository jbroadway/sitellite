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
// Date widget.  Displays 3 select boxes representing the year, month, and
// day.
//

/**
	 * Date widget.  Displays 3 select boxes representing the year, month, and
	 * day.
	 * 
	 * New in 1.2:
	 * - Added a setDefault(value) method.
	 * 
	 * New in 1.4:
	 * - Added automatic I18n (if $intl object exists) calls in display() to allow
	 *   for translations of month names.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_date ('name');
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
	 * @version	1.4, 2002-08-25, $Id: Date.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_date extends MF_Widget {
	/**
	 * Unused and deprecated.
	 * 
	 * @access	private
	 * 
	 */
	var $year;

	/**
	 * Unused and deprecated.
	 * 
	 * @access	private
	 * 
	 */
	var $month;

	/**
	 * Unused and deprecated.
	 * 
	 * @access	private
	 * 
	 */
	var $day;

	/**
	 * The lowest year to be displayed in the year select box.  Set to a
	 * default value of 25 years ago by the constructor method.
	 * 
	 * @access	public
	 * 
	 */
	var $lowest_year;

	/**
	 * The highest year to be displayed in the year select box.  Set to a
	 * default value of one year into the future by the constructor method.
	 * 
	 * @access	public
	 * 
	 */
	var $highest_year;

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
	var $type = 'date';

	/**
	 * Constructor Method.
	 * 
	 * @access	public
	 * @param	string	$name
	 * 
	 */
	function MF_Widget_date ($name) {
		// initialize core Widget settings
		parent::MF_Widget ($name);

		// load Hidden and Select widgets, on which this widget depends
		$GLOBALS['loader']->import ('saf.MailForm.Widget.Hidden');
		$GLOBALS['loader']->import ('saf.MailForm.Widget.Select');

		// initialize custom widget settings
		$this->lowest_year = date ('Y') - 25;
		$this->highest_year = date ('Y') + 1;
		/*
		$this->data_value_YEAR = date ('Y');
		$this->data_value_MONTH = date ('m');
		$this->data_value_DAY = date ('d');*/
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
		} else {
			list (
				$this->data_value_YEAR,
				$this->data_value_MONTH,
				$this->data_value_DAY
			) = split ('-', $value);
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
			if (empty ($this->data_value_YEAR) && empty ($this->data_value_MONTH) && empty ($this->data_value_DAY)) {
				return '';
			}
			return $this->data_value_YEAR . '-' . $this->data_value_MONTH . '-' . $this->data_value_DAY;
		} else {
			if (empty ($cgi->{'MF_' . $this->name . '_YEAR'}) && empty ($cgi->{'MF_' . $this->name . '_MONTH'}) && empty ($cgi->{'MF_' . $this->name . '_DAY'})) {
				return '';
			}
			return $cgi->{'MF_' . $this->name . '_YEAR'} . '-' . $cgi->{'MF_' . $this->name . '_MONTH'} . '-' . $cgi->{'MF_' . $this->name . '_DAY'};
		}
	}

	/**
	 * Gives this widget a default value.  Accepts a date string
	 * of the format 'YYYY-MM-DD'.
	 * 
	 * @access	public
	 * @param	string	$value
	 * 
	 */
	function setDefault ($value) {
		list ($this->data_value_YEAR, $this->data_value_MONTH, $this->data_value_DAY) = split ('-', $value);
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
		$_year = new MF_Widget_select ('MF_' . $this->name . '_YEAR');
		$_year->nullable = $this->nullable;
		$year_array = array ();
		for ($i = $this->highest_year; $i >= $this->lowest_year; $i--) {
			$year_array[$i] = $i;
		}
		$_year->setValues ($year_array);
		$_year->data_value = $this->data_value_YEAR;

		$_month = new MF_Widget_select ('MF_' . $this->name . '_MONTH');
		$_month->nullable = $this->nullable;
		global $intl, $simple;
		if (is_object ($intl)) {
			$_month->setValues (array (
				'01' => $intl->get ('January'),
				'02' => $intl->get ('February'),
				'03' => $intl->get ('March'),
				'04' => $intl->get ('April'),
				'05' => $intl->get ('May'),
				'06' => $intl->get ('June'),
				'07' => $intl->get ('July'),
				'08' => $intl->get ('August'),
				'09' => $intl->get ('September'),
				'10' => $intl->get ('October'),
				'11' => $intl->get ('November'),
				'12' => $intl->get ('December'),
			));
		} else {
			$_month->setValues (array (
				'01' => 'January',
				'02' => 'February',
				'03' => 'March',
				'04' => 'April',
				'05' => 'May',
				'06' => 'June',
				'07' => 'July',
				'08' => 'August',
				'09' => 'September',
				'10' => 'October',
				'11' => 'November',
				'12' => 'December',
			));
		}
		$_month->data_value = $this->data_value_MONTH;

		$_day = new MF_Widget_select ('MF_' . $this->name . '_DAY');
		$_day->nullable = $this->nullable;
		$_day->setValues (array (
			'01' => '1',
			'02' => '2',
			'03' => '3',
			'04' => '4',
			'05' => '5',
			'06' => '6',
			'07' => '7',
			'08' => '8',
			'09' => '9',
			'10' => '10',
			'11' => '11',
			'12' => '12',
			'13' => '13',
			'14' => '14',
			'15' => '15',
			'16' => '16',
			'17' => '17',
			'18' => '18',
			'19' => '19',
			'20' => '20',
			'21' => '21',
			'22' => '22',
			'23' => '23',
			'24' => '24',
			'25' => '25',
			'26' => '26',
			'27' => '27',
			'28' => '28',
			'29' => '29',
			'30' => '30',
			'31' => '31',
		));
		$_day->data_value = $this->data_value_DAY;

		if ($this->nullable || $this->addblank) {
			global $intl;
			if (is_object ($intl)) {
				$_day->value[''] = $intl->get ('Day');
				$_month->value[''] = $intl->get ('Month');
				$_year->value[''] = $intl->get ('Year');
			} else {
				$_day->value[''] = 'Day';
				$_month->value[''] = 'Month';
				$_year->value[''] = 'Year';
			}
		}

		$_year->extra = $this->extra;
		$_month->extra = $this->extra;
		$_day->extra = $this->extra;

		$_date = new MF_Widget_hidden ($this->name);
		if ($generate_html) {
			$data = $_date->display (0) . "\n";
			$data .= "\t<tr>\n\t\t<td class=\"label\"><label for=\"" . $this->name . '"' . $this->invalid () . '>' . $simple->fill ($this->label_template, $this, '', true) . "</label></td>\n\t\t<td class=\"field\">" . 
				$_month->display (0) . '&nbsp;' . $_day->display (0) . '&nbsp;' . $_year->display (0) .
				"</td>\n\t</tr>\n";
		} else {
			$data = $_date->display (0);
			$data .= $_month->display (0) . '&nbsp;' . $_day->display (0) . '&nbsp;' . $_year->display (0);
		}
		return $data;
	}
}



?>