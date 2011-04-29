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
// Position widget.    Displays a multiple-select box with a Javascript popup
// to add/remove positions from the list.
//

/**
	 * Position widget.  Displays a multiple-select box with a Javascript popup
	 * to add/remove positions from the list.  Positions are stored in the
	 * sitellite_sidebar_position database table.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_position ('name');
	 * echo $widget->display ();
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	CMS
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.0, 2003-08-16, $Id: Position.php,v 1.2 2007/02/05 00:11:42 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_position extends MF_Widget {
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
	var $type = 'position';

	/**
	 * Constructor Method.
	 * 
	 * @access	public
	 * @param	string	$name
	 * 
	 */
	function MF_Widget_position ($name) {
		// initialize core Widget settings
		parent::MF_Widget ($name);
		$this->passover_isset = true;
	}

	function getPositions () {
		$res = db_fetch ('select * from sitellite_sidebar_position order by id asc');

		if (! $res) {
			return array ();
		} elseif (is_object ($res)) {
			return array ($res->id);
		}

		$list = array ();
		foreach ($res as $row) {
			$list[] = $row->id;
		}
		return $list;
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
		if (! is_array ($value)) {
			$this->data_value = $value;
		} else {
			$this->data_value = join (',', $value);
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
		if (is_object ($cgi)) {
			if (! isset ($cgi->{$this->name})) {
				return '';
			} elseif (is_array ($cgi->{$this->name})) {
				return join (',', $cgi->{$this->name});
			} else {
				return $cgi->{$this->name};
			}
		} else {
			return $this->data_value;
		}
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
		$data = '';
		$attrstr = $this->getAttrs ();
		$selected = explode (',', $this->data_value);

		loader_import ('saf.Misc.RPC');

		echo rpc_init ('return false');

		loader_import ('saf.GUI.Prompt');
		page_add_script ('
			var cms_' . $this->name . '_form;
			var cms_' . $this->name . '_oldhandler;

			function cms_position_add (f) {
				cms_' . $this->name . '_form = f;

				// 0. collect our new position(s) from the user
				prompt (
					\'New position(s) -- separate multiple with commas (one, two, three)\',
					\'\',
					function (word) {
						if (word == null || word.length == 0 || !word) {
							return false;
						}
						words = word.split (\',\');

						cms_' . $this->name . '_oldhandler = rpc_handler;
						rpc_handler = null;
						rpc_handler = cms_position_handler;

						// 1. call {site/prefix}/index/cms-sidebar-position-add-action in a popup
						//window.open (\'' . site_prefix () . '/index/cms-sidebar-position-add-action?name=\' + word, \'Position\', \'top=100,left=100,height=50,width=50\');
						rpc_call (\'' . site_prefix () . '/index/cms-sidebar-position-add-action?name=\' + word);

						// 2. add the selected keywords to the list
						for (i = 0; i < words.length; i++) {
							if (document.all) {
								f.elements[\'position\'].options[f.elements[\'position\'].options.length + 1] = new Option (words[i], words[i], false, true);
							} else {
								o = document.createElement (\'option\');
								o.text = words[i];
								o.value = words[i];
								f.elements[\'position\'].add (o, null);
							}
						}
					}
				);

				// 3. cancel the click
				return false;
			}

			function cms_position_handler () {
				rpc_handler = null;
				rpc_handler = cms_' . $this->name . '_oldhandler;
			}

			function cms_position_remove (f) {
				// 0. collect the selected positions from the "position" field
				word = \'\';
				sep = \'\';
				for (i = 0; i < f.elements[\'position\'].options.length; i++) {
					if (f.elements[\'position\'].options[i].selected) {
						word = word + sep + f.elements[\'position\'].options[i].value;
						sep = \',\';
					}
				}

				// 0.1. confirm that they want to delete the selected list
				c = confirm (\'' . intl_get ('Are you sure you want to remove this position?') . '  \' + word);
				if (! c) {
					return false;
				}

				// 1. call {site/prefix}/index/cms-sidebar-position-delete-action in a popup
				//window.open (\'' . site_prefix () . '/index/cms-sidebar-position-delete-action?name=\' + word, \'Position\', \'top=100,left=100,height=50,width=50\');
				rpc_call (\'' . site_prefix () . '/index/cms-sidebar-position-delete-action?name=\' + word);

				// 2. remove the selected keywords from the list
				for (i = f.elements[\'position\'].options.length - 1; i >= 0; i--) {
					if (f.elements[\'position\'].options[i].selected) {
						// remove
						if (document.all) {
							f.elements[\'position\'].options.remove (i);
						} else {
							f.elements[\'position\'].options[i] = null;
						}
						break;
					}
				}

				// 3. cancel the click
				return false;
			}
		');

		if ($generate_html) {
			$data .= '<tr>
				<td class="label"' . $this->invalid () . ' valign="top">
					<label for="' . $this->name . '" id="' . $this->name . '-label">' . template_simple ($this->label_template, $this, '', true) . '</label>
				</td>
				<td class="field">
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td valign="top">
					<select name="' . $this->name . '" ' . $attrstr . ' ' . $this->extra . '>' . NEWLINE;
			foreach ($this->getPositions () as $keyword) {
				$data .= TABx2 . TABx2 . TABx2 . '<option value="' . $keyword . '"';
				if (in_array ($keyword, $selected)) {
					$data .= ' selected="selected"';
				}
				$data .= '>' . $keyword . '</option>' . NEWLINE;
			}
			$data .= '</select>
							</td>
							<td valign="top">
					<input type="submit" value="' . intl_get ('Add') . '" onclick="return cms_position_add (this.form)" /><br />
					<input type="submit" value="' . intl_get ('Remove') . '" onclick="return cms_position_remove (this.form)" />
							</td>
						</tr>
					</table>
				</td>
			</tr>' . NEWLINEx2;

		} else {

		}

		return $data;
	}
}

?>