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
// Time widget.  Displays 3 select boxes which represent HH : MM : SS.
//

/**
	 * Time widget.  Displays 3 select boxes which represent HH : MM : SS.
	 * 
	 * New in 1.2:
	 * - Added a setDefault(value) method.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_time ('name');
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
	 * @version	1.2, 2002-03-25, $Id: Time.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_time extends MF_Widget {
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
	var $type = 'time';

	/**
	 * Constructor Method.
	 * 
	 * @access	public
	 * @param	string	$name
	 * 
	 */
	function MF_Widget_time ($name) {
		// initialize core Widget settings
		parent::MF_Widget ($name);

		// load Hidden and Select widgets, on which this widget depends
		$GLOBALS['loader']->import ('saf.MailForm.Widget.Hidden');
		$GLOBALS['loader']->import ('saf.MailForm.Widget.Text');

		/*
		// initialize custom widget settings
		$this->data_value_HOUR = date ('H');
		$this->data_value_MINUTE = date ('i');
		$this->data_value_SECOND = date ('s');*/
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
				$this->data_value_HOUR,
				$this->data_value_MINUTE,
				$this->data_value_SECOND
			) = split (':', $value);
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
		list ($this->data_value_HOUR, $this->data_value_MINUTE, $this->data_value_SECOND) = split (':', $value);
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
			if (empty ($this->data_value_HOUR) && empty ($this->data_value_MINUTE) && empty ($this->data_value_SECOND)) {
				return '';
			}
			return $this->data_value_HOUR . ':' . $this->data_value_MINUTE . ':' . $this->data_value_SECOND;
		} else {
			if (empty ($cgi->{'MF_' . $this->name . '_HOUR'}) && empty ($cgi->{'MF_' . $this->name . '_MINUTE'}) && empty ($cgi->{'MF_' . $this->name . '_SECOND'})) {
				return '';
			}
			return $cgi->{'MF_' . $this->name . '_HOUR'} . ':' . $cgi->{'MF_' . $this->name . '_MINUTE'} . ':' . $cgi->{'MF_' . $this->name . '_SECOND'};
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
		$_hour = new MF_Widget_text ('MF_' . $this->name . '_HOUR');
		$_hour->nullable = $this->nullable;
		$_hour->data_value = $this->data_value_HOUR;
		$_hour->extra = 'size="2"';

		$_minute = new MF_Widget_text ('MF_' . $this->name . '_MINUTE');
		$_minute->nullable = $this->nullable;
		$_minute->data_value = $this->data_value_MINUTE;
		$_minute->extra = 'size="2"';

		$_second = new MF_Widget_text ('MF_' . $this->name . '_SECOND');
		$_second->nullable = $this->nullable;
		$_second->data_value = $this->data_value_SECOND;
		$_second->extra = 'size="2"';

		//$_hour->extra = $this->extra;
		//$_minute->extra = $this->extra;
		//$_second->extra = $this->extra;

		$_time = new MF_Widget_hidden ($this->name);
		if ($generate_html) {
			$data = $_time->display (0) . "\n";
			$data .= "\t<tr>\n\t\t<td class=\"label\">" . '<label for="' . $this->name . '"' . $this->invalid () . '>' . $this->display_value . "</label></td>\n\t\t<td class=\"field\">" . 
				$_hour->display (0) . '&nbsp;:&nbsp;' . $_minute->display (0) . '&nbsp;:&nbsp;' . $_second->display (0) .
				"</td>\n\t</tr>\n";
		} else {
			$data = $_time->display (0);
			$data .= $_hour->display (0) . '&nbsp;:&nbsp;' . $_minute->display (0) . '&nbsp;:&nbsp;' . $_second->display (0);
		}
		return $data;
	}
}



?>