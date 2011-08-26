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
// Templates widget.  Displays an HTML <select> form field with a
// "Preview Template" button next to it.
//

/**
	 * Templates widget.  Displays an HTML <select> form field with a
	 * "Preview Template" button next to it.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_templates ('name');
	 * $widget->validation ('is "foo"');
	 * $widget->setValue ('foo');
	 * $widget->error_message = 'Oops!  This widget is being unruly!';
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	CMS
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.0, 2001-11-28, $Id: Templates.php,v 1.3 2007/06/01 23:20:13 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_templates extends MF_Widget {
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
	var $type = 'template';

	function getTemplates ($path = false) {
		if (! $path) {
			$path = 'inc/html/' . conf ('Server', 'default_template_set');
		}

		$templates = array ('' => 'Inherit', 'default' => 'Default');

		loader_import ('saf.File.Directory');
		$dir = new Dir;
		if (! $dir->open ($path)) {
			return $templates;
		}

		foreach ($dir->read_all () as $file) {
			if (strpos ($file, '.') === 0 || @is_dir ($path . '/' . $file)) {
				continue;
			}
			if (preg_match ('/^html.([^\.]+)\.tpl$/', $file, $regs)) {
				if ($regs[1] == 'default') {
					continue;
				}
				$templates[$regs[1]] = ucfirst ($regs[1]);
			}
		}

		asort ($templates);

		return $templates;
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

		$this->value = $this->getTemplates ();

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
			$data .= '</select> &nbsp; ';

			$data .= '<script language="javascript" type="text/javascript">
				function preview_template (f) {
					o = f.elements.' . $this->name . '.options[f.elements.' . $this->name . '.selectedIndex].value;
					if (o.length == 0) {
						w = window.open (\'' . site_prefix () . '/index/cms-templatepreview-action?tpl=' . conf ('Server', 'default_template') . '\', \'TemplatePreview\', \'top=50,left=50,height=500,width=780,resizable=yes,scrollbars=yes\');
					} else {
						w = window.open (\'' . site_prefix () . '/index/cms-templatepreview-action?tpl=\' + o, \'TemplatePreview\', \'top=50,left=50,height=500,width=780,resizable=yes,scrollbars=yes\');
					}
					return false;
				}
			</script><input type="submit" value="' . intl_get ('Preview Template') . '" onclick="return preview_template (this.form)" />';

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