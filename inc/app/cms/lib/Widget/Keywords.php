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
// Keywords widget.  Displays an HTML <input type="text" /> form field.
//

/**
	 * Text widget.  Displays an HTML <input type="text" /> form field.
	 * 
	 * New in 1.2:
	 * - Made sure to call parent::display() in the display() method.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_keywords ('name');
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
	 * @version	1.2, 2002-05-03, $Id: Keywords.php,v 1.5 2008/02/20 12:10:58 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_keywords extends MF_Widget {
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
	var $type = 'keywords';

	var $rows = 2;
	var $cols = 40;

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
		$attrstr = $this->getAttrs ();

		$len = ($this->length > 0) ? 'maxlength="' . $this->length . '" ' : '';

		if ($generate_html) {
			return "\t" . '<tr>' . "\n\t\t" . '<td class="label" valign="top"><label for="' . $this->name . '" id="' . $this->name . '-label"' . $this->invalid () . '>' . $simple->fill ($this->label_template, $this, '', true) . '</label></td>' . "\n\t\t" .
				'<td class="field"><textarea rows="' . $this->rows . '" cols="' . $this->cols . '" ' . $attrstr .
				' ' . $len . $this->extra . '>' . htmlentities_compat ($this->data_value, ENT_COMPAT, $intl->charset) . '</textarea><br />' .
				'<a href="#" onclick="this.blur (); window.open (\'' . site_prefix () . '/index/cms-keywords-action?el=' . $this->name . '&sel=\' + document.forms[0].elements.' . $this->name . '.value, \'KeywordsWindow\', \'top=100,left=100,width=350,height=500,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,fullscreen=no\'); return false">' . intl_get ('Global Keyword List') . '</a>' .
				'</td>' . "\n\t" . '</tr>' . "\n";
		} else {
			return '<input type="text" ' . $attrstr . ' value="' . htmlentities_compat ($this->data_value, ENT_COMPAT, $intl->charset) . '" ' . $this->extra . ' />';
		}
	}
}



?>