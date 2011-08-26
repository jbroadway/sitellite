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
// Selector widget.    Displays a multiple-select box with a Javascript popup
// to add/remove items from the list.
//

/**
	 * Selector widget.  Displays a multiple-select box with a Javascript popup
	 * to add/remove items from the list.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_selector ('name');
	 * echo $widget->display ();
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	MailForm
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.0, 2003-08-16, $Id: Selector.php,v 1.6 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_selector extends MF_Widget {
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
	var $type = 'selector';

	/**
	 * This is the database table to get the list of items from.
	 *
	 * @access public
	 *
	 */
	var $table = 'sitellite_category';

	/**
	 * This is the primary key of the list table.
	 *
	 * @access public
	 *
	 */
	var $key = 'id';

    var $title = false;
    
	/**
	 * Set this property to the number of options to display.
	 *
	 * @access public
	 *
	 */
	var $size = false;

	/**
	 * If you want this to be a multiple-select widget, set this property
	 * to true.
	 *
	 * @access public
	 *
	 */
	var $multiple = false;

	/**
	 * Name of the box that adds the new item or items to the database
	 * table underlying the selector.
	 *
	 * @access public
	 *
	 */
	var $addAction = 'cms/selector/add';

	/**
	 * Name of the box that removes the specified item or items from the
	 * database table underlying the selector.
	 *
	 * @access public
	 *
	 */
	var $removeAction = 'cms/selector/remove';

	/**
	 * Constructor Method.
	 * 
	 * @access	public
	 * @param	string	$name
	 * 
	 */
	function MF_Widget_selector ($name) {
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
	function getList () {
        if ($this->title) {
            $res = db_fetch_array ('select ' . $this->key . ', ' . $this->title . ' from ' . $this->table . ' order by ' . $this->title . ' asc');
        } else {
            $res = db_fetch_array ('select ' . $this->key . ' from ' . $this->table . ' order by ' . $this->key . ' asc');
        }

		return $res;
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

		$mult = 'false';

		if ($this->size) {
			$multiple = ' size="' . $this->size . '"';
			$braces = '';
			if ($this->multiple) {
				$multiple = ' multiple="multiple"' . $multiple;
				$braces = '[]';
				$mult = 'true';
			}
		} else {
			$multiple = '';
			$braces = '';
		}

		if (session_is_resource ($this->table) && ! session_allowed ($this->table, 'rw', 'resource')) {
			$allowed = false;
		} else {
			$allowed = true;
		}

		if ($allowed) {
		loader_import ('saf.GUI.Prompt');

		if ($this->title) {
		page_add_script ('
			var cms_' . $this->name . '_form;

			var cms_' . $this->name . '_oldhandler;

			function cms_' . $this->name . '_add_handler (words) {
				f = cms_' . $this->name . '_form;

				// 2. add the selected keywords to the list
				for (i = 0; i < words.length; i++) {
					if (document.all) {
						f.elements[\'' . $this->name . $braces . '\'].options[f.elements[\'' . $this->name . $braces . '\'].options.length + 1] = new Option (words[i].text, words[i].value, false, true);
					} else {
						o = document.createElement (\'option\');
						o.text = words[i].text;
						o.value = words[i].value;
						f.elements[\'' . $this->name . $braces . '\'].add (o, null);
					}
				}

				rpc_handler = null;
				rpc_handler = cms_' . $this->name . '_oldhandler;
			}

			function cms_' . $this->name . '_add (f) {
				cms_' . $this->name . '_form = f;

				// 0. collect our new items(s) from the user
				prompt (
					\'New items(s) -- separate multiple with commas (one, two, three)\',
					\'\',
					function (word) {
						if (word == null || word.length == 0 || word == false) {
							return false;
						}
						words = word.split (/, ?/);

						cms_' . $this->name . '_oldhandler = rpc_handler;
						rpc_handler = null;
						rpc_handler = cms_' . $this->name . '_add_handler;

						// 1. call {site/prefix}/index/' . str_replace ('/', '-', $this->addAction) . '-action in a popup
						rpc_call (\'' . site_prefix () . '/index/' . str_replace ('/', '-', $this->addAction) . '-action?table=' . $this->table . '&key=' . $this->key . '&title=' . $this->title . '&items=\' + word);
					}
				);

				// 3. cancel the click
				return false;
			}

			function cms_' . $this->name . '_remove (f) {
				// 0. collect the selected items from the "items" field
				word = \'\';
				show = \'\';
				sep = \'\';
				for (i = 0; i < f.elements[\'' . $this->name . $braces . '\'].options.length; i++) {
					if (f.elements[\'' . $this->name . $braces . '\'].options[i].selected) {
						word = word + sep + f.elements[\'' . $this->name . $braces . '\'].options[i].value;
						show = show + sep + f.elements[\'' . $this->name . $braces . '\'].options[i].text;
						sep = \',\';
					}
				}

				// 0.1. confirm that they want to delete the selected list
				c = confirm (\'' . intl_get ('Are you sure you want to remove these items?') . '  \' + show);
				if (! c) {
					return false;
				}

				// 1. call {site/prefix}/index/' . str_replace ('/', '-', $this->addAction) . '-action in a popup
				rpc_call (\'' . site_prefix () . '/index/' . str_replace ('/', '-', $this->removeAction) . '-action?table=' . $this->table . '&key=' . $this->key . '&title=' . $this->title . '&items=\' + word);

				// 2. remove the selected keywords from the list
				multiple = ' . $mult . ';
				for (i = f.elements[\'' . $this->name . $braces . '\'].options.length - 1; i >= 0; i--) {
					if (f.elements[\'' . $this->name . $braces . '\'].options[i].selected) {
						// remove
						if (document.all) {
							f.elements[\'' . $this->name . $braces . '\'].options.remove (i);
						} else {
							f.elements[\'' . $this->name . $braces . '\'].options[i] = null;
						}
						if (! multiple) {
							break;
						}
					}
				}

				// 3. cancel the click
				return false;
			}
		');
		} else {
		page_add_script ('
			function cms_' . $this->name . '_add (f) {
				cms_' . $this->name . '_form = f;

				// 0. collect our new items(s) from the user
				prompt (
					\'New items(s) -- separate multiple with commas (one, two, three)\',
					\'\',
					function (word) {
						if (word == null || word.length == 0 || word == false) {
							return false;
						}
						words = word.split (/, ?/);

						f = cms_' . $this->name . '_form;

						// 1. call {site/prefix}/index/' . str_replace ('/', '-', $this->addAction) . '-action in a popup
						rpc_call (\'' . site_prefix () . '/index/' . str_replace ('/', '-', $this->addAction) . '-action?table=' . $this->table . '&key=' . $this->key . '&items=\' + word);

						// 2. add the selected keywords to the list
						for (i = 0; i < words.length; i++) {
							if (document.all) {
								f.elements[\'' . $this->name . $braces . '\'].options[f.elements[\'' . $this->name . $braces . '\'].options.length + 1] = new Option (words[i], words[i], false, true);
							} else {
								o = document.createElement (\'option\');
								o.text = words[i];
								o.value = words[i];
								f.elements[\'' . $this->name . $braces . '\'].add (o, null);
							}
						}
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
				c = confirm (\'' . intl_get ('Are you sure you want to remove these items?') . '  \' + word);
				if (! c) {
					return false;
				}

				// 1. call {site/prefix}/index/' . str_replace ('/', '-', $this->addAction) . '-action in a popup
				rpc_call (\'' . site_prefix () . '/index/' . str_replace ('/', '-', $this->removeAction) . '-action?table=' . $this->table . '&key=' . $this->key . '&items=\' + word);

				// 2. remove the selected keywords from the list
				multiple = ' . $mult . ';
				for (i = f.elements[\'' . $this->name . $braces . '\'].options.length - 1; i >= 0; i--) {
					if (f.elements[\'' . $this->name . $braces . '\'].options[i].selected) {
						// remove
						if (document.all) {
							f.elements[\'' . $this->name . $braces . '\'].options.remove (i);
						} else {
							f.elements[\'' . $this->name . $braces . '\'].options[i] = null;
						}
						if (! multiple) {
							break;
						}
					}
				}

				// 3. cancel the click
				return false;
			}
		');
		} // end title

		} // end allowed

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
			
             foreach ($this->getList () as $obj) {
                if (! $this->title) {
                    $key = $obj->{$this->key};
                    $keyword = $obj->{$this->key};
                } else {
                    $key = $obj->{$this->key};
                    $keyword = $obj->{$this->title};
                }
				$data .= TABx2 . TABx2 . TABx2 . '<option value="' . $key . '"';
				if (in_array ($key, $selected)) {
					$data .= ' selected="selected"';
				}
				$data .= '>' . $keyword . '</option>' . NEWLINE;
			}
			$data .= '</select>
							</td>' . NEWLINE;

			if ($allowed) {

			$data .= '				<td valign="top" width="100%">
					<input type="submit" value="' . intl_get ('Add') . '" onclick="return cms_' . $this->name . '_add (this.form)" /><br />
					<input type="submit" value="' . intl_get ('Remove') . '" onclick="return cms_' . $this->name . '_remove (this.form)" />
							</td>
						</tr>
					</table>
				</td>' . NEWLINE;

			} else {
				$data .= '</tr></table></td>';
			}

			$data .= '			</tr>' . NEWLINEx2;

		} else {

		}

		return $data;
	}
}

?>
