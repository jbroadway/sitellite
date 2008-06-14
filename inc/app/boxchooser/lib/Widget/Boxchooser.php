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
// Imagechooser widget.  Displays an HTML <input type="file" />-like form
// field that uses the boxchooser app.
//

/**
	 * Imagechooser widget.  Displays an HTML <input type="file" />-like form
	 * field that uses the boxchooser app.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_boxchooser ('name');
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
	 * @version	1.0, 2003-07-31, $Id: Boxchooser.php,v 1.1 2008/02/20 10:24:47 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_boxchooser extends MF_Widget {
	/**
	 * A way to pass extra parameters to the HTML form tag, for
	 * example 'enctype="multipart/form-data"'.
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
	var $type = 'boxchooser';

	/**
	 * Constructor Method.  Also sets the $passover_isset property
	 * to false.
	 * 
	 * @access	public
	 * @param	string	$name
	 * 
	 */
	function MF_Widget_boxchooser ($name) {
		parent::MF_Widget ($name);
		$this->passover_isset = true;
	}

	function _link () {
		// display a button that pops up the boxchooser app
		static $included = false;
		if (! $included) {
			page_add_script (site_prefix () . '/js/dialog.js');
			page_add_script (loader_box ('boxchooser/js', $this));
			$included = true;
		}
		return template_simple ('
			<script language="javascript" type="text/javascript">

				function boxchooser_{name}_handler () {
					if (typeof dialogWin.returnedValue == \'object\') {
						url = dialogWin.returnedValue[\'src\'];
					} else {
						url = dialogWin.returnedValue;
					}
					boxchooser_{name}_form.elements[boxchooser_{name}_element].value = unescape (url);
				}

			</script>

			<input type="submit" onclick="boxchooser_{name}_get_file (this.form, \'{name}\'); return false" value="{intl Choose}" />
		', $this);
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

		// initialize modal dialog event handlers
		static $included = false;

		if (! $included) {
			page_onclick ('checkModal ()');
			page_onfocus ('return checkModal ()');
			$included = true;
		}

		$attrstr = $this->getAttrs ();
		if ($generate_html) {
			return "\t" . '<tr>' . "\n\t\t" . '<td class="label"><label for="' . $this->name . '" id="' . $this->name . '-label" ' . $this->invalid () . '>' . $simple->fill ($this->label_template, $this, '', true) . '</label></td>' . "\n\t\t" .
				'<td class="field"><input type="text" ' . $attrstr . ' value="' . htmlentities_compat ($this->data_value) . '" ' . $this->extra . ' />&nbsp;' . $this->_link () . '</td>' . "\n\t" . '</tr>' . "\n";
		} else {
			return '<input type="file" ' . $attrstr . ' value="" ' . $this->extra . ' />';
		}
	}
	
}

?>