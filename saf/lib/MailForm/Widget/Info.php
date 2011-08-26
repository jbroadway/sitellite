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
// Info widget.  Displays text and a hidden widget.
//

/**
	 * Info widget.  Displays text and a hidden widget.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_disabled ('name');
	 * $widget->setValue ('foo');
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	MailForm
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.0, 2003-07-23, $Id: Info.php,v 1.8 2007/10/11 07:06:19 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_info extends MF_Widget {
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
	var $type = 'info';

	/**
	 * A filter function to apply to the value before displaying it.  Note that
	 * htmlentities_compat() is still called afterwards.
	 *
	 * @access	public
	 *
	 */
	var $filter = false;

	/**
	 * A library to import which contains the filter function.
	 *
	 * @access	public
	 *
	 */
	var $filter_import = false;

	/**
	 * Set this to false to skip calling htmlentities_compat() on the displayed
	 * data.
	 *
	 * @access	public
	 *
	 */
	var $htmlentities = true;

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

		if ($this->filter) {
			if ($this->filter_import) {
				loader_import ($this->filter_import);
			}
			$display = call_user_func ($this->filter, $this->data_value);
		} else {
			$display = $this->data_value;
		}

		$disp = $display;
		if ($this->htmlentities) {
			$disp = htmlentities_compat ($disp, ENT_COMPAT, $intl->charset);
		}

		if ($generate_html) {
			return "\t" . '<tr>' . "\n\t\t" . '<td class="label" valign="top"><label for="' . $this->name . '"' . $this->invalid () . '>' . $simple->fill ($this->label_template, $this, '', true) . '</label></td>' . "\n\t\t" .
				'<td class="field" valign="top"><strong>' . $disp . '</strong><input type="hidden" name="' . $this->name . '" value="' . htmlentities_compat ($this->data_value, ENT_COMPAT, $intl->charset) . '" ' . $this->extra . ' /></td>' . "\n\t" . '</tr>' . "\n";
		} else {
			return '<input type="text" ' . $attrstr . ' value="' . htmlentities_compat ($this->data_value, ENT_COMPAT, $intl->charset) . '" ' . $this->extra . ' />';
		}
	}
}



?>