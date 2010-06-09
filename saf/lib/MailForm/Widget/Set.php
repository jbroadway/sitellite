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
// Set widget.  Displays a text box with a select box next to it of
// previously entered values.
//

/**
	 * Set widget.  Displays a text box with a select box next to it of
	 * previously entered values.  This creates the equivalent of a select box
	 * that also allows new values to be entered.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_set ('name');
	 * $widget->setValues ('tablename');
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
	 * @version	1.0, 2003-01-09, $Id: Set.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_set extends MF_Widget {
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
	var $type = 'set';

	/**
	 * Constructor Method.
	 * 
	 * @access	public
	 * @param	string	$name
	 * 
	 */
	function MF_Widget_set ($name) {
		// initialize core Widget settings
		parent::MF_Widget ($name);

		// load Hidden and Select widgets, on which this widget depends
		$GLOBALS['loader']->import ('saf.MailForm.Widget.Hidden');
		$GLOBALS['loader']->import ('saf.MailForm.Widget.Text');
		$GLOBALS['loader']->import ('saf.MailForm.Widget.Select');

		// initialize custom widget settings
		//$this->lowest_year = date ('Y') - 25;
		//$this->highest_year = date ('Y') + 1;
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
			$this->data_value_SELECT = $value;
		}
	}

	/**
	 * Sets the *POSSIBLE* values for this widget.  Uses
	 * the specified $table to find out all of the existing values
	 * of this field, which will be displayed in the inner select
	 * box.  Returns true or false, and sets the $error property
	 * if a database error occurs.
	 * 
	 * @access	public
	 * @param	string	$table
	 * @return	boolean
	 * 
	 */
	function setValues ($table) {
		global $db;
		$res = $db->fetch ('select distinct ' . $this->name . ' from ' . $table . ' order by ' . $this->name . ' asc');
		if ($res === false) {
			$this->error = $db->error;
			return false;
		} elseif (is_object ($res)) {
			$res = array ($res);
		}
		foreach ($res as $row) {
			$this->value[$row->{$this->name}] = ucfirst ($row->{$this->name});
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
		if (! is_object ($cgi)) {
			if (! empty ($this->data_value_TEXT)) {
				return $this->data_value_TEXT;
			} else {
				return $this->data_value_SELECT;
			}
		} else {
			if (! empty ($cgi->{'MF_' . $this->name . '_TEXT'})) {
				return $cgi->{'MF_' . $this->name . '_TEXT'};
			} else {
				return $cgi->{'MF_' . $this->name . '_SELECT'};
			}
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
		$this->data_value_SELECT = $value;
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
		global $intl, $simple;

		$_text = new MF_Widget_text ('MF_' . $this->name . '_TEXT');
		$_text->nullable = $this->nullable;
		$_text->data_value = $this->data_value_TEXT;

		$_select = new MF_Widget_select ('MF_' . $this->name . '_SELECT');
		$_select->nullable = $this->nullable;
		$_select->setValues ($this->value);
		$_select->data_value = $this->data_value_SELECT;

		$_text->extra = $this->extra;
		$_select->extra = $this->extra;

		$_set = new MF_Widget_hidden ($this->name);

		if ($generate_html) {
			$data = $_set->display (0) . "\n";
			$data .= "\t<tr>\n\t\t<td class=\"label\"><label for=\"" . $this->name . '"' . $this->invalid () . '>' . $simple->fill ($this->label_template, $this, '', true) . "</label></td>\n\t\t<td class=\"field\">" . 
				$_text->display (0) . '&nbsp;' . $_select->display (0) .
				"</td>\n\t</tr>\n";
		} else {
			$data = $_set->display (0);
			$data .= $_text->display (0) . '&nbsp;' . $_select->display (0);
		}
		return $data;
	}
}



?>