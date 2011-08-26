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
// Pagebrowser widget.  Displays an HTML <input type="text" />-like form
// field that uses the pagebrowser app.
//

loader_import ('pagebrowser.PageBrowser');

/**
	 * Pagebrowser widget.  Displays an HTML <input type="text" />-like form
	 * field that uses the pagebrowser app.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_pagebrowser ('name');
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
	 * @version	1.0, 2003-07-31, $Id: Pagebrowser.php,v 1.7 2008/02/20 09:52:12 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_pagebrowser extends MF_Widget {
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
	var $type = 'pagebrowser';

	/**
	 * Passes this limit value to the page browser.
	 * 
	 * @access	public
	 * 
	 */
	var $limit = false;

	/**
	 * Constructor Method.  Also sets the $passover_isset property
	 * to false.
	 * 
	 * @access	public
	 * @param	string	$name
	 * 
	 */
	function MF_Widget_pagebrowser ($name) {
		parent::MF_Widget ($name);
		$this->passover_isset = true;
	}

	function displayValue ($id) {
		if ($this->limit) {
			$this->limit_str = 'yes';
		} else {
			$this->limit_str = '';
		}

		$pb = new PageBrowser;
		$trail = $pb->getTrail ($id, $this->limit_str, true);
		$txt = '';
		$sep = '';
		if (count ($trail) == 0 || (count ($trail) == 1 && $trail[0]->id == '')) {
			return 'Root';
		}
		for ($i = 0; $i < count ($trail); $i++) {
			if ($trail[$i]->id == $id) {
				$txt .= $sep . $trail[$i]->title;
				$sep = ' / ';
			} else {
				$txt .= $sep . '<a href="#" onclick="return pagechooser_' . $this->name . '_handler_local (\'' . $trail[$i]->id . '\')">' . $trail[$i]->title . '</a>';
				$sep = ' / ';
			}
		}
		return $txt;
	}

	function _link () {
		// display a button that pops up the pagechooser app
		static $included = false;
		if (! $included) {
			page_add_script (site_prefix () . '/js/dialog.js');
			page_add_script (site_prefix () . '/js/rpc.js');
			//page_add_script (loader_box ('pagebrowser/js', $this));
			//$included = true;
		}

		if ($this->limit) {
			$this->limit_str = 'yes';
		} else {
			$this->limit_str = '';
		}

		return template_simple ('
			<script language="javascript" type="text/javascript">

				var pbrpc = new rpc ();

				var {name}pagebrowser = {
					url: \'{site/prefix}/index/pagebrowser-rpc-action\',
					action: pbrpc.action,

					setTitle: function (id) {
						var pagechooser_{name}_id = id;
						pbrpc.call (
							this.action (\'getTrail\', [id, \'{limit_str}\', true]),
							function (request) {
								o = eval (request.responseText);
								txt = \'\';
								sep = \'\';
								if (o.length == 0 || (o.length == 1 && o[0].id == \'\')) {
									txt = \'Root\';
								} else {
									for (i = 0; i < o.length; i++) {
										if (o[i].id == pagechooser_{name}_id) {
											txt += sep + o[i].title;
											sep = \' / \';
										} else {
											txt += sep + \'<a href="#\' + o[i].id + \'" onclick="return pagechooser_{name}_handler_local (\\\'\' + o[i].id + \'\\\')">\' + o[i].title + \'</a>\';
											sep = \' / \';
										}
									}
								}
								document.getElementById (\'mf-pagechooser-{filter pagebrowser_filter_name}{name}{end filter}-display\').innerHTML = txt;
							}
						);
						return false;
					}
				}

				var pagechooser_{name}_form = false;
				var pagechooser_{name}_element = false;
				var pagechooser_{name}_attrs = false;
				dialogWin.scrollbars = \'yes\';
				dialogWin.resizable = \'yes\';
				
				function pagechooser_{name} () {
					openDGDialog (
						\'{site/prefix}/index/pagebrowser-app?id=\' + pagechooser_{name}_form.elements[pagechooser_{name}_element].value + \'&limit={limit_str}\',
						400,
						300,
						pagechooser_{name}_handler
					);
				}
				
				function pagechooser_{name}_get_page (f, e) {
					pagechooser_{name}_form = f;
					pagechooser_{name}_element = e;
					pagechooser_{name} ();
					return false;
				}

				function pagechooser_{name}_handler () {
					url = dialogWin.returnedValue;
					pagechooser_{name}_form.elements[pagechooser_{name}_element].value = url;
					{name}pagebrowser.setTitle (url);
				}

				function pagechooser_{name}_handler_local (url) {
					if (! pagechooser_{name}_form) {
						pagechooser_{name}_form = document.forms[0];
						pagechooser_{name}_element = \'{name}\';
					}
					pagechooser_{name}_form.elements[pagechooser_{name}_element].value = url;
					return {name}pagebrowser.setTitle (url);
				}

				function pagechooser_{name}_clear (f, e) {
					f.elements[e].value = \'\';
					{name}pagebrowser.setTitle (\'\');
				}

			</script>

			<input type="submit" onclick="pagechooser_{name}_get_page (this.form, \'{name}\'); return false" value="{intl Browse...}" />
			<input type="submit" onclick="pagechooser_{name}_clear (this.form, \'{name}\'); return false" value="{intl Clear}" />
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
			return '<tr>
				<td class="label"' . $this->invalid () . '>
					<label for="' . $this->name . '" id="' . $this->name . '-label">' . $simple->fill ($this->label_template, $this, '', true) . '</label>
				</td>
				<td class="field">
					<input type="hidden" ' . $attrstr . ' value="' . htmlentities_compat ($this->data_value) . '" ' . $this->extra . ' />
					<table border="0" cellpadding="3" cellspacing="0" width="100%">
						<tr>
							<td width="60%">
								<span id="mf-pagechooser-' . pagebrowser_filter_name ($this->name) . '-display" style="font-weight: bold">' . $this->displayValue ($this->data_value) . '</span>
							</td>
							<td width="40%">
								' . $this->_link () . '
							</td>
						</tr>
					</table>
				</td>
			</tr>';
			//return "\t" . '<tr>' . "\n\t\t" . '<td class="label"><label for="' . $this->name . '"' . $this->invalid () . '>' . $simple->fill ($this->label_template, $this, '', true) . '</label></td>' . "\n\t\t" .
			//	'<td class="field"><input type="hidden" ' . $attrstr . ' value="' . htmlentities_compat ($this->data_value) . '" ' . $this->extra . ' />&nbsp;' . $this->_link () . '</td>' . "\n\t" . '</tr>' . "\n";
		} else {
			return '<input type="hidden" ' . $attrstr . ' value="' . htmlentities_compat ($this->data_value) . '" ' . $this->extra . ' />
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="50%">
							<span id="mf-pagechooser-' . pagebrowser_filter_name ($this->name) . '-display" style="font-weight: bold">' . $this->displayValue ($this->data_value) . '</span>
						</td>
						<td width="50%" align="right">
							' . $this->_link () . '
						</td>
					</tr>
				</table>';
		}
	}
}

function pagebrowser_filter_name ($n) {
	return str_replace ('_', '-', $n);
}

?>