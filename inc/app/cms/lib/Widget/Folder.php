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
// Folder widget.    Displays a multiple-select box with a Javascript popup
// to add/remove items from the list.
//

/**
	 * Folder widget.  Displays a multiple-select box with a Javascript popup
	 * to add/remove items from the list.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_folder ('name');
	 * echo $widget->display ();
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	MailForm
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.0, 2003-08-16, $Id: Folder.php,v 1.2 2007/02/05 00:11:42 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_folder extends MF_Widget {
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
	var $type = 'folder';

	/**
	 * This is the database table to get the list of items from.
	 *
	 * @access public
	 *
	 */
	var $basedir = 'inc/data';

	/**
	 * Name of the box that adds the new item or items to the database
	 * table underlying the selector.
	 *
	 * @access public
	 *
	 */
	var $addAction = 'cms/folder/add';

	/**
	 * Name of the box that removes the specified item or items from the
	 * database table underlying the selector.
	 *
	 * @access public
	 *
	 */
	var $removeAction = 'cms/folder/remove';

	/**
	 * Constructor Method.
	 * 
	 * @access	public
	 * @param	string	$name
	 * 
	 */
	function MF_Widget_folder ($name) {
		// initialize core Widget settings
		parent::MF_Widget ($name);
		$this->passover_isset = true;
	}

	/**
	 * Retrieve the list of items.
	 *
	 * @return array
	 *
	 */
	function getList ($basedir = false) {
		if (! $basedir) {
			$basedir = $this->basedir;
		}
		loader_import ('saf.File.Directory');
		$res = assocify (Dir::getStruct ($basedir));
		$list = array ('' => ucfirst ('default'));
		foreach ($res as $file) {
			if (preg_match ('|/CVS$|', $file)) {
				continue;
			}
			$file = str_replace ($basedir . '/', '', $file);
			$list[$file] = $file;
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

		$this->size = false;
		$this->multiple = false;

		if ($this->size) {
			$multiple = ' size="' . $this->size . '"';
			$braces = '';
			if ($this->multiple) {
				$multiple = ' multiple="multiple"' . $multiple;
				$braces = '[]';
			}
		} else {
			$multiple = '';
			$braces = '';
		}

		loader_import ('saf.GUI.Prompt');
		page_add_script ('
			var cms_' . $this->name . '_form = false;
			var cms_' . $this->name . '_elem = false;
			var cms_' . $this->name . '_action = false;
			var cms_' . $this->name . '_oldhandler;

			//var rpc_handler = new Function ("if (arguments[0] == true) { if (cms_' . $this->name . '_action == \'add\') { cms_' . $this->name . '_add_item (); } else { cms_' . $this->name . '_remove_item (); } } else { if (cms_' . $this->name . '_action == \'add\') { cms_' . $this->name . '_add_alert (); } else { cms_' . $this->name . '_remove_alert (); } }");

			function cms_' . $this->name . '_add_item (res) {
				if (res == false) {
					cms_' . $this->name . '_add_alert ();
					return;
				}

				f = cms_' . $this->name . '_form;
				words = cms_' . $this->name . '_elem;

				// 2. add the selected keywords to the list
				for (i = 0; i < words.length; i++) {
					if (document.all) {
						f.elements[\'' . $this->name . $braces . '\'].options[f.elements[\'' . $this->name . $braces . '\'].options.length + 1] = new Option ("/" + words[i], words[i], false, true);
					} else {
						o = document.createElement (\'option\');
						o.text = words[i];
						o.value = words[i];
						f.elements[\'' . $this->name . $braces . '\'].add (o, null);
					}
				}

				rpc_handler = null;
				rpc_handler = cms_' . $this->name . '_oldhandler;
			}

			function cms_' . $this->name . '_remove_item (res) {
				if (res == false) {
					cms_' . $this->name . '_remove_alert ();
					return;
				}

				f = cms_' . $this->name . '_form;

				// 2. remove the selected keywords from the list
				for (i = f.elements[\'' . $this->name . $braces . '\'].options.length - 1; i >= 0; i--) {
					if (f.elements[\'' . $this->name . $braces . '\'].options[i].selected) {
						// remove
						if (document.all) {
							f.elements[\'' . $this->name . $braces . '\'].options.remove (i);
						} else {
							f.elements[\'' . $this->name . $braces . '\'].options[i] = null;
						}
						break;
					}
				}

				rpc_handler = null;
				rpc_handler = cms_' . $this->name . '_oldhandler;
			}

			function cms_' . $this->name . '_add_alert () {
				alert (\'Failed to add folder: Permission denied.  Please check your server permissions and try again.\');
			}

			function cms_' . $this->name . '_remove_alert () {
				alert (\'Failed to remove folder: Permission denied.  Please check that the selected folder is empty, and that your server permissions are correct, and try again.\');
			}

			function cms_' . $this->name . '_add (f) {
				cms_' . $this->name . '_form = f;

				// 0. collect our new items(s) from the user
				prompt (
					\'New folder(s) -- separate multiple with commas (one, two, three)\',
					\'\',
					function (word) {
						if (word == null || word.length == 0) {
							return false;
						}
						words = word.split (/, ?/);

						cms_' . $this->name . '_oldhandler = rpc_handler;
						rpc_handler = null;
						rpc_handler = cms_' . $this->name . '_add_item;

						// 1. call {site/prefix}/index/' . str_replace ('/', '-', $this->addAction) . '-action in a popup
						cms_' . $this->name . '_form = f;
						cms_' . $this->name . '_elem = words;
						cms_' . $this->name . '_action = "add";

						rpc_handler = new Function ("if (arguments[0] == true) { if (cms_' . $this->name . '_action == \'add\') { cms_' . $this->name . '_add_item (); } else { cms_' . $this->name . '_remove_item (); } } else { if (cms_' . $this->name . '_action == \'add\') { cms_' . $this->name . '_add_alert (); } else { cms_' . $this->name . '_remove_alert (); } }");

						rpc_call (\'' . site_prefix () . '/index/' . str_replace ('/', '-', $this->addAction) . '-action?path=' . $this->basedir . '&items=\' + word);
					}
				);

				// 3. cancel the click
				return false;
			}

			function cms_' . $this->name . '_remove (f) {
				// 0. collect the selected items from the "items" field
				word = \'\';
				sep = \'\';
				for (i = 0; i < f.elements[\'' . $this->name . $braces . '\'].options.length; i++) {
					if (f.elements[\'' . $this->name . $braces . '\'].options[i].selected) {
						word = word + sep + f.elements[\'' . $this->name . $braces . '\'].options[i].value;
						sep = \',\';
					}
				}

				// 0.1. confirm that they want to delete the selected list
				c = confirm (\'' . intl_get ('Are you sure you want to remove this folder?') . '  \' + word);
				if (! c) {
					return false;
				}

				cms_' . $this->name . '_oldhandler = rpc_handler;
				rpc_handler = null;
				rpc_handler = cms_' . $this->name . '_remove_item;

				// 1. call {site/prefix}/index/' . str_replace ('/', '-', $this->addAction) . '-action in a popup
				cms_' . $this->name . '_form = f;
				cms_' . $this->name . '_action = "remove";
				rpc_call (\'' . site_prefix () . '/index/' . str_replace ('/', '-', $this->removeAction) . '-action?path=' . $this->basedir . '&items=\' + word);

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
					<table border="0" cellpadding="3" cellspacing="0">
						<tr>
							<td valign="top">
					<select name="' . $this->name . $braces . '" ' . $multiple . $attrstr . ' ' . $this->extra . '>' . NEWLINE;
			foreach ($this->getList () as $k => $v) {
				$data .= TABx2 . TABx2 . TABx2 . '<option value="' . $k . '"';
				if (in_array ($k, $selected)) {
					$data .= ' selected="selected"';
				}
				$data .= '>' . $v . '</option>' . NEWLINE;
			}
			$data .= '</select>
							</td>
							<td valign="top" width="100%">
					<input type="submit" value="' . intl_get ('Add') . '" onclick="return cms_' . $this->name . '_add (this.form)" /><br />
					<input type="submit" value="' . intl_get ('Remove') . '" onclick="return cms_' . $this->name . '_remove (this.form)" />
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
