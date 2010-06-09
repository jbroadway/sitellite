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
// Imagechooser widget.  Displays an HTML <input type="file" />-like form
// field that uses the imagechooser app.
//

/**
	 * Imagechooser widget.  Displays an HTML <input type="file" />-like form
	 * field that uses the imagechooser app.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_imagechooser ('name');
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
	 * @version	1.0, 2003-07-31, $Id: Imagechooser.php,v 1.5 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_imagechooser extends MF_Widget {
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
	var $type = 'imagechooser';

	/**
	 * Base directory path under which all images must be kept.  If $setPath is
	 * set to true and this is given a value, it will set a session variable
	 * called "imagechooser_path" which will be used by the imagechooser app.
	 * If no value is given for this, the default path used by the imagechooser
	 * app is "/pix".
	 * 
	 * @access	public
	 * 
	 */
	var $path = false;

	/**
	 * Tells imagechooser whether or not to set the session variable
	 * "imagechooser_path" upon display() of this widget.
	 * 
	 * @access	public
	 * 
	 */
	var $setPath = true;

	/**
	 * Constructor Method.  Also sets the $passover_isset property
	 * to false.
	 * 
	 * @access	public
	 * @param	string	$name
	 * 
	 */
	function MF_Widget_imagechooser ($name) {
		parent::MF_Widget ($name);
		$this->passover_isset = true;
	}

	function _link () {
		// display a button that pops up the imagechooser app
		static $included = false;
		if (! $included) {
			page_add_script (site_prefix () . '/js/dialog.js');
			page_add_script (loader_box ('imagechooser/js', $this));
			//$included = true;
		}
		return template_simple ('
			<script language="javascript" type="text/javascript">

				function imagechooser_{name}_handler () {
					if (typeof dialogWin.returnedValue == \'object\') {
						url = \'{site/url}\' + dialogWin.returnedValue[\'src\'];
					} else {
						url = \'{site/url}\' + dialogWin.returnedValue;
					}
					imagechooser_{name}_form.elements[imagechooser_{name}_element].value = url;
				}

				function imagechooser_{name}_view_current (src) {
					if (src.length > 0) {
						imagechooser_{name}_view (src);
					}
					return false;
				}

			</script>

			<input type="submit" onclick="return imagechooser_{name}_view_current (this.form.elements[\'{name}\'].value)" value="{intl View Current}" />
			<input type="submit" onclick="imagechooser_{name}_get_image (this.form, \'{name}\'); return false" value="{intl Choose}" />
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

		if ($this->setPath && $this->path) {
			session_set ('imagechooser_path', $this->path);
		}

		// initialize modal dialog event handlers
		static $included = false;

		if (! $included) {
			page_onclick ('checkModal ()');
			page_onfocus ('return checkModal ()');
			$included = true;
		}

		$attrstr = $this->getAttrs ();
		if ($generate_html) {
			return "\t" . '<tr>' . "\n\t\t" . '<td class="label"><label for="' . $this->name . '"' . $this->invalid () . '>' . $simple->fill ($this->label_template, $this, '', true) . '</label></td>' . "\n\t\t" .
				'<td class="field"><input type="text" ' . $attrstr . ' value="' . htmlentities_compat ($this->data_value) . '" ' . $this->extra . ' />&nbsp;' . $this->_link () . '</td>' . "\n\t" . '</tr>' . "\n";
		} else {
			return '<input type="file" ' . $attrstr . ' value="" ' . $this->extra . ' />';
		}
	}
}



?>