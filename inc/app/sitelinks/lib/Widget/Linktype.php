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
// Linktype widget.  Displays an HTML <select> form field containing a
// list of types from inc/app/sitelinks/html/types.
//

/**
	 * Linktype widget.  Displays an HTML <select> form field containing a
	 * list of types from inc/app/sitelinks/html/types.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_linktype ('name');
	 * $widget->validation ('is "foo"');
	 * $widget->setValue ('foo');
	 * $widget->error_message = 'Oops!  This widget is being unruly!';
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	SiteLinks
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.0, 2001-11-28, $Id: Linktype.php,v 1.1.1.1 2004/06/14 17:44:22 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_linktype extends MF_Widget {
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
	var $type = 'linktype';

	function getTypes ($path = false) {
		if (! $path) {
			$path = site_docroot () . '/inc/app/sitelinks/html/full';
		}

		$types = array ('default' => 'Default');

		loader_import ('saf.File.Directory');
		$dir = new Dir;
		if (! $dir->open ($path)) {
			return $types;
		}

		foreach ($dir->read_all () as $file) {
			if (strpos ($file, '.') === 0 || @is_dir ($path . '/' . $file)) {
				continue;
			}
			if (preg_match ('/^([^\.]+)\.spt$/', $file, $regs)) {
				$types[$regs[1]] = ucfirst ($regs[1]);
			}
		}

		return $types;
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
		if (! isset ($this->data_value)) {
			$this->data_value = $this->default_value;
		}

		$this->value = $this->getTypes ();

		$attrstr = $this->getAttrs ();
		if ($generate_html) {
			$data = "\t" . '<tr>' . "\n\t\t" . '<td class="label"><label for="' . $this->name . '" id="' . $this->name . '-label"' . $this->invalid () . '>' . $simple->fill ($this->label_template, $this, '', true) . '</label></td>' . "\n\t\t" .
				'<td class="field"><select ' . $attrstr . ' ' . $this->extra . ' >' . "\n";
			foreach ($this->value as $value => $display) {
				if ($value == 'admin') {
					continue;
				}
				$display = str_replace ('_', ' ', ucwords ($display));
				if ($value == $this->data_value) {
					$selected = ' selected="selected"';
				} else {
					$selected = '';
				}
				$data .= "\t" . '<option value="' . $value . '"' . $selected . '>' . $display . '</option>' . "\n";
			}
			$data .= '</select>';
			$data .= '</td>' . "\n\t" . '</tr>' . "\n";
			return $data;

		} else {
			$data = '<select ' . $attrstr . ' ' . $this->extra . ' >' . "\n";
			foreach ($this->value as $value => $display) {
				$display = str_replace ('_', ' ', ucwords ($display));
				if ($value == $this->data_value) {
					$selected = ' selected="selected"';
				} else {
					$selected = '';
				}
				$data .= "\t" . '<option value="' . htmlentities_compat ($value, ENT_COMPAT, $intl->charset) . '"' . $selected . '>' . $display . '</option>' . "\n";
			}
			return $data . '</select>';
		}
	}
}

?>