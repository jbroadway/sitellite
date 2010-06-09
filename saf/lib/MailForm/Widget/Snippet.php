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
// Snippet widget.  Displays an HTML <textarea> form field but instead of
// returning its value it stores it and returns its ID for later retrieval.
//

/**
	 * Snippet widget.  Displays an HTML <textarea> form field but instead of
	 * returning its value it stores it and returns its ID for later retrieval.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_snippet ('name');
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
	 * @version	1.0, 2001-11-28, $Id: Snippet.php,v 1.2 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_snippet extends MF_Widget {
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
	var $type = 'snippet';

	/**
	 * This is the position of the label for the textarea.  It may
	 * be set to 'top' (the default), or 'left' to be pushed to the side.
	 * 
	 * @access	public
	 * 
	 */
	var $labelPosition = 'top';

	/**
     * If set to true, the form contains an imput to choose the name of the snippet
     *
     * @access public
     *
     */
     var $selectName = false;

	/**
     * The property key for this snippet. Unsed internally.
	 *
     * @access private
     *
     */
     var $_key = null;

	/**
	 * Sets the actual value for this widget.
	 * 
	 * @access	public
	 * @param	string	$value
	 * 
	 */
	function setValue ($value = '') {
		loader_import ('saf.Database.PropertySet');
		$ps = new PropertySet ('mailform', 'snippet');
		$val = $ps->get ($value);
		if ($val) {
			$this->_key = $value;
			$this->data_value = $val;
		} else {
			$this->data_value = $value;
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
		$oldkey = null;
		if (is_object ($cgi)) {
			if (! isset ($cgi->{$this->name})) {
				$value = '';
			} elseif (is_array ($cgi->{$this->name})) {
				$value = join (',', $cgi->{$this->name});
			} else {
				$value = $cgi->{$this->name};
			}
			if (isset ($cgi->{$this->name.'_key'})) {
				$this->_key = $cgi->{$this->name.'_key'};
			}
			if (isset ($cgi->{$this->name.'_oldkey'})) {
				$oldkey = $cgi->{$this->name.'_oldkey'};
			}
		} else {
			$value = $this->data_value;
		}

		loader_import ('saf.Database.PropertySet');
		$ps = new PropertySet ('mailform', 'snippet');
		if (! $this->_key) {
			$this->_key = md5 ($value);

			// Don't overwrite an existing value
			$i = 0;
			while ($v = $ps->get ($this->_key)) {
				$this->_key = md5 ($value . $i++);
			} 
		}
		if ($oldkey && ($oldkey != $this->_key)) {
			$ps->delete ($oldkey);
		}
		$ps->set ($this->_key, $value);
		return $this->_key;
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
		parent::display ($generate_html);
		global $intl, $simple;

		if ($this->cols == '') {
			$this->cols = 40;
		}

		if ($this->rows == '') {
			$this->rows = 8;
		}

		$attrstr = $this->getAttrs ();
		if ($this->selectName) {
			$keyfield = intl_get ('Snippet name') . ': <input type="hidden" name="' . $this->name . '_oldkey" value="' . $this->_key . '" />'
				.'<input type="text" name="' . $this->name . '_key" value="' . $this->_key . '" /><br/>';
		}
		else {
			$keyfield = '<input type="hidden" name="' . $this->name . '_key" value="' . $this->_key . '" />';
		}
		if ($generate_html) {
			if ($this->labelPosition == 'left') {
				return "\t" . '<tr>' . "\n\t\t" . '<td class="label" valign="top"><label for="' . $this->name . '" id="' . $this->name . '-label"' . $this->invalid () . '>' . $simple->fill ($this->label_template, $this, '', true) . '</label></td>'
					. "\n\t\t" . '<td class="field">' . $keyfield . '<textarea ' . $attrstr . ' rows="' . $this->rows . '" cols="' . $this->cols . '" ' . $this->extra . ' >' .
					htmlentities_compat ($this->data_value, ENT_COMPAT, $intl->charset) . '</textarea></td>' . "\n\t" . '</tr>' . "\n";
			} else {
				if (empty ($this->alt)) {
					return "\t" . '<tr>' . "\n\t\t" . '<td colspan="2" class="field">' . $keyfield . '<textarea ' . $attrstr . ' rows="' . $this->rows . '" cols="' . $this->cols . '" ' . $this->extra . ' >' .
						htmlentities_compat ($this->data_value, ENT_COMPAT, $intl->charset) . '</textarea></td>' . "\n\t" . '</tr>' . "\n";
				} else {
					return "\t" . '<tr>' . "\n\t\t" . '<td colspan="2" class="label"><label for="' . $this->name . '" id="' . $this->name . '-label"' . $this->invalid () . '>' . $simple->fill ($this->label_template, $this, '', true) . '</label></td>' . "\n\t" .
						'</tr>' . "\n\t" . '<tr>' . "\n\t\t" . '<td colspan="2" class="field">' . $keyfield . '<textarea ' . $attrstr . ' rows="' . $this->rows . '" cols="' . $this->cols . '" ' . $this->extra . ' >' .
						htmlentities_compat ($this->data_value, ENT_COMPAT, $intl->charset) . '</textarea></td>' . "\n\t" . '</tr>' . "\n";
				}
			}
		} else {
			return $keyfield . '<textarea ' . $attrstr . ' rows="' . $this->rows . '" cols="' . $this->cols . '" ' . $this->extra . ' >' . htmlentities_compat ($this->data_value, ENT_COMPAT, $intl->charset) . '</textarea>';
		}
	}
}



?>
