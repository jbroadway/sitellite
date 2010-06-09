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
// Xarea widget.  Displays an HTML <textarea> form field, with a button to
// pop open a Xed editor in a new window that will return its results to
// the textarea widget.
//

/**
	 * Xarea widget.  Displays an HTML <textarea> form field, with a button to
	 * pop open a Xed editor in a new window that will return its results to
	 * the textarea widget.
	 *
	 * Please Note: This widget *requires* that you set the "name" attribute
	 * of the form object.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_xarea ('name');
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
	 * @version	1.0, 2001-11-28, $Id: Xarea.php,v 1.1.1.1 2005/04/29 04:44:33 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_xarea extends MF_Widget {
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
	var $type = 'textarea';

	/**
	 * This is the position of the label for the textarea.  It may
	 * be set to 'top' (the default), or 'left' to be pushed to the side.
	 * 
	 * @access	public
	 * 
	 */
	var $labelPosition = 'top';

	/**
	 * Specify an alternate template to display the editor popup with.
	 * Default is 'admin'.
	 */
	var $template = '';

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

		$attrstr = $this->getAttrs ();
		if ($generate_html) {
			static $script = false;
			if (! $script) {
				$out = '<script language="javascript" type="text/javascript">
					function xed_wysiwyg_popup (f, field_name) {
						form_name = "' . $this->form->name . '";
						window.open ("' . site_prefix () . '/index/xed-editor-action?template=' . $this->template . '&form_name=" + form_name + "&field_name=" + field_name + "&body=" + f.elements[field_name].value, "wysiwygEditor", "top=50,left=50,height=550,width=700,resizable=yes");
						return false;
					}
				</script>';
				$script = true;
			} else {
				$out = '';
			}
			if ($this->labelPosition == 'left') {
				return $out . "\n\t" . '<tr>' . "\n\t\t" . '<td class="label" valign="top"><label for="' . $this->name . '" id="' . $this->name . '-label"' . $this->invalid () . '>' . $simple->fill ($this->label_template, $this, '', true) . '</label></td>'
					. "\n\t\t" . '<td class="field"><textarea ' . $attrstr . ' rows="' . $this->rows . '" cols="' . $this->cols . '" ' . $this->extra . ' >' .
					htmlentities_compat ($this->data_value, ENT_COMPAT, $intl->charset) . '</textarea><br />' .
					'<input type="submit" value="' . intl_get ('WYSIWYG Editor') . '" onclick="return xed_wysiwyg_popup (this.form, \'' . $this->name . '\')" /></td>' . "\n\t" . '</tr>' . "\n";
			} else {
				return $out . "\n\t" . '<tr>' . "\n\t\t" . '<td colspan="2" class="label"><label for="' . $this->name . '" id="' . $this->name . '-label"' . $this->invalid () . '>' . $simple->fill ($this->label_template, $this, '', true) . '</label></td>' . "\n\t" .
					'</tr>' . "\n\t" . '<tr>' . "\n\t\t" . '<td colspan="2" class="field"><textarea ' . $attrstr . ' rows="' . $this->rows . '" cols="' . $this->cols . '" ' . $this->extra . ' >' .
					htmlentities_compat ($this->data_value, ENT_COMPAT, $intl->charset) . '</textarea><br />' .
					'<input type="submit" value="' . intl_get ('WYSIWYG Editor') . '" onclick="return xed_wysiwyg_popup (this.form, \'' . $this->name . '\')" /></td>' . "\n\t" . '</tr>' . "\n";

			}
		} else {
			return '<textarea ' . $attrstr . ' rows="' . $this->rows . '" cols="' . $this->cols . '" ' . $this->extra . ' >' . htmlentities_compat ($this->data_value, ENT_COMPAT, $intl->charset) . '</textarea>';
		}
	}
}



?>