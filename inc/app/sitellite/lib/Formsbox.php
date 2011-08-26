<?php
//
// +----------------------------------------------------------------------+
// | Sitellite - Content Management System                                |
// +----------------------------------------------------------------------+
// | Copyright (c) 2001 Simian Systems                                    |
// +----------------------------------------------------------------------+
// | This software is released under the Simian Open Software License.    |
// | Please see the accompanying file OPENLICENSE for licensing details!  |
// |                                                                      |
// | You should have received a copy of the Simian Open Software License  |
// | along with this program; if not, write to Simian Systems,            |
// | 101-314 Broadway, Winnipeg, MB, R3C 0S7, CANADA.  The Simian         |
// | Public License is also available at the following web site           |
// | address: <http://www.simian.ca/license.php>                          |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <lux@simian.ca>                                |
// +----------------------------------------------------------------------+
//
// Formsbox widget.  Displays an HTML <textarea> form field.
//

/**
	 * Formsbox widget.  Displays an HTML <textarea> form field.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_Formsbox ('name');
	 * $widget->validation ('is "foo"');
	 * $widget->setValue ('foo');
	 * $widget->error_message = 'Oops!  This widget is being unruly!';
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	MailForm
	 * @author	John Luxford <lux@simian.ca>
	 * @copyright	Copyright (C) 2001-2003, Simian Systems Inc.
	 * @license	http://www.sitellite.org/index/license	Simian Open Software License
	 * @version	1.0, 2001-11-28, $Id: Formsbox.php,v 1.1 2004/06/04 16:31:53 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_Formsbox extends MF_Widget {
	/**
	 * The height of the textarea widget in rows.
	 * 
	 * @access	public
	 * 
	 */
	var $rows = 8;

	/**
	 * The width of the textarea widget in columns.
	 * 
	 * @access	public
	 * 
	 */
	var $cols = 40;

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
	var $type = 'Formsbox';

	/**
	 * This is the position of the label for the textarea.  It may
	 * be set to 'top' (the default), or 'left' to be pushed to the side.
	 * 
	 * @access	public
	 * 
	 */
	var $labelPosition = 'top';

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
		parent::display ($generate_html);
		global $intl, $simple;

		if ($this->cols == '') {
			$this->cols = 40;
		}

		if ($this->rows == '') {
			$this->rows = 8;
		}

		$this->label_template = '{filter none}{display_value}{end filter}';

		$this->display_value = str_replace (
			array (
				' strong,',
				' em,',
				' a,',
				' blockquote,',
				' code)',
			),
			array (
				' <a href="#" onclick="return siteforum_insert_tag (\'strong\')">strong</a>,',
				' <a href="#" onclick="return siteforum_insert_tag (\'em\')">em</a>,',
				' <a href="#" onclick="return siteforum_insert_tag (\'a\')">a</a>,',
				' <a href="#" onclick="return siteforum_insert_tag (\'blockquote\')">blockquote</a>,',
				' <a href="#" onclick="return siteforum_insert_tag (\'code\')">code</a>)',
			),
			$this->display_value
		);

		$attrstr = $this->getAttrs ();
		if ($generate_html) {
			if ($this->labelPosition == 'left') {
				return "\t" . '<tr>' . "\n\t\t" . '<td class="label" valign="top"><label for="' . $this->name . '" id="' . $this->name . '-label"' . $this->invalid () . '>' . $simple->fill ($this->label_template, $this, '', true) . '</label></td>'
					. "\n\t\t" . '<td class="field"><textarea ' . $attrstr . ' rows="' . $this->rows . '" cols="' . $this->cols . '" ' . $this->extra . ' >' .
					htmlentities_compat ($this->data_value, ENT_COMPAT, $intl->charset) . '</textarea></td>' . "\n\t" . '</tr>' . "\n";
			} else {
				return "\t" . '<tr>' . "\n\t\t" . '<td colspan="2" class="label"><label for="' . $this->name . '" id="' . $this->name . '-label"' . $this->invalid () . '>' . $simple->fill ($this->label_template, $this, '', true) . '</label></td>' . "\n\t" .
					'</tr>' . "\n\t" . '<tr>' . "\n\t\t" . '<td colspan="2" class="field"><textarea ' . $attrstr . ' rows="' . $this->rows . '" cols="' . $this->cols . '" ' . $this->extra . ' >' .
					htmlentities_compat ($this->data_value, ENT_COMPAT, $intl->charset) . '</textarea></td>' . "\n\t" . '</tr>' . "\n";

			}
		} else {
			return '<textarea ' . $attrstr . ' rows="' . $this->rows . '" cols="' . $this->cols . '" ' . $this->extra . ' >' . htmlentities_compat ($this->data_value, ENT_COMPAT, $intl->charset) . '</textarea>';
		}
	}
}

?>