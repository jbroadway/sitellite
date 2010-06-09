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
// Linker widget.  Displays a select box listing the web site page hierarchy
// followed by a text box to allow external links as well.
//

/**
	 * Linker widget.  Displays a select box listing the web site page hierarchy
	 * followed by a text box to allow external links as well.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_linker ('name');
	 * $widget->validation ('is "foo"');
	 * $widget->setValue ('foo');
	 * $widget->error_message = 'Oops!  This widget is being unruly!';
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	Xed
	 * @access	public
	 * 
	 */

class MF_Widget_linker extends MF_Widget {
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
	var $type = 'linker';

	/**
	 * Constructor Method.
	 * 
	 * @access	public
	 * @param	string	$name
	 * 
	 */
	function MF_Widget_linker ($name) {
		// initialize core Widget settings
		parent::MF_Widget ($name);

		// load Hidden, Text and Ref widgets, on which this widget depends
		$GLOBALS['loader']->import ('saf.MailForm.Widget.Hidden');
		$GLOBALS['loader']->import ('saf.MailForm.Widget.Text');
		//$GLOBALS['loader']->import ('saf.MailForm.Widget.Ref');
		$GLOBALS['loader']->import ('wffolderbrowser.Widget.Pagebrowser');
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
		if (! empty ($inner_component)) {
			$this->{'data_value_' . $inner_component} = $value;
		} else {
			$page = $this->isPage ($value);
			if ($page) {
				$this->data_value_INNER = $page;
			} else {
				$this->data_value_EXTERN = $value;
			}
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
		if (! is_object ($cgi)) {
			if (empty ($this->data_value_INNER) && empty ($this->data_value_EXTERN)) {
				return '';
			} elseif (empty ($this->data_value_INNER)) {
				return $this->data_value_EXTERN;
			}
			//return site_prefix () . '/index/' . $this->data_value_INNER;
			return site_prefix () . $this->data_value_INNER;
		} else {
			if (empty ($cgi->{'MF_' . $this->name . '_INNER'}) && empty ($cgi->{'MF_' . $this->name . '_EXTERN'})) {
				return '';
			} elseif (empty ($cgi->{'MF_' . $this->name . '_INNER'})) {
				return $cgi->{'MF_' . $this->name . '_EXTERN'};
			}
			//return site_prefix () . '/index/' . $cgi->{'MF_' . $this->name . '_INNER'};
			return site_prefix () . $cgi->{'MF_' . $this->name . '_INNER'};
		}
	}

	function isPage ($link) {
		if (strpos ($link, site_prefix () . '/index') === false) {
			return false;
		}
		$parts = parse_url ($link);
		$page = basename ($parts['path']);
		if (empty ($page)) {
			return 'index';
		}
		$strlen = strlen ($page);
		if ($strlen > 7 && better_strrpos ($page, '-action') == ($strlen - 7)) {
			return false;
		} elseif ($strlen > 5 && better_strrpos ($page, '-form') == ($strlen - 5)) {
			return false;
		} elseif ($strlen > 4 && better_strrpos ($page, '-app') == ($strlen - 4)) {
			return false;
		}
		return $page;
	}

	/**
	 * Gives this widget a default value.  Accepts a date string
	 * of the format 'YYYY-MM-DD'.
	 * 
	 * @access	public
	 * @param	string	$value
	 * 
	 */
	function setDefault ($value) {
		$page = $this->isPage ($value);
		if ($page) {
			$this->data_value_INNER = $page;
			$this->data_value_EXTERN = false;
		} else {
			$this->data_value_INNER = false;
			$this->data_value_EXTERN = $value;
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
		$_inner = new MF_Widget_pagebrowser ('MF_' . $this->name . '_INNER');
		//$_inner->nullable = $this->nullable;
		//$_inner->table = 'sitellite_page';
		//$_inner->primary_key = 'id';
		//$_inner->display_column = 'if(nav_title != "", nav_title, if(title != "", title, id))';
		//$_inner->ref_column = 'below_page';
		//$_inner->self_ref = true;
		//$_inner->addblank = true;
		$_inner->alt = intl_get ('Internal');
		$_inner->data_value = $this->data_value_INNER;

		$_extern = new MF_Widget_text ('MF_' . $this->name . '_EXTERN');
		$_extern->nullable = $this->nullable;
		$_extern->alt = intl_get ('External');
		$_extern->data_value = $this->data_value_EXTERN;

		//$_inner->extra = $this->extra;
		$_extern->extra = $this->extra;

		$_page = new MF_Widget_hidden ($this->name);
		if ($generate_html) {
			page_add_script (site_prefix () . '/js/dialog.js');
			page_add_script (loader_box ('filechooser/js'));
			$data = $_page->display (0) . "\n";
			$data .= "\t<tr>\n\t\t<td class=\"label\" valign=\"top\"><label for=\"" . $this->name . '" id="' . $this->name . '-label" ' . $this->invalid () . '>' . template_simple ($this->label_template, $this, '', true) . "</label></td>\n\t\t<td class=\"field\">" . 
				'<table border="0" cellpadding="2" cellspacing="2"><tr><td>' . intl_get ('Internal') . '</td><td>' .
				$_inner->display (0) . '</td></tr><td>' . intl_get ('External') . '</td><td>' . $_extern->display (0);
			$data .= template_simple ('
				<script language="javascript" type="text/javascript">

					function filechooser_handler () {
						if (typeof dialogWin.returnedValue == \'object\') {
							url = \'{site/prefix}\' + dialogWin.returnedValue[\'src\'];
						} else {
							url = \'{site/prefix}\' + dialogWin.returnedValue;
						}
						filechooser_form.elements[\'MF_\' + filechooser_element + \'_EXTERN\'].value = url;
					}

					var bookmark_form = false;
					var bookmark_element = false;
					dialogWin.scrollbars = \'yes\';
					dialogWin.resizable = \'yes\';
					function bookmark () {
						openDGDialog (
							\'{site/prefix}/index/xed-bookmarks-action\',
							400,
							300,
							bookmark_handler
						);
					}

					function bookmarks (f, e) {
						bookmark_form = f;
						bookmark_element = e;
						bookmark ();
						return false;
					}

					function bookmark_handler () {
						if (typeof dialogWin.returnedValue == \'object\') {
							url = dialogWin.returnedValue[\'src\'];
						} else {
							url = dialogWin.returnedValue;
						}
						bookmark_form.elements[\'MF_\' + bookmark_element + \'_EXTERN\'].value = url;
					}

				</script>

				<input type="submit" onclick="bookmarks (this.form, \'{name}\'); return false" value="{intl Bookmarks}" />
				<input type="submit" onclick="filechooser_get_file (this.form, \'{name}\'); return false" value="{intl Files}" />
			', $this);
			$data .= '</td></tr></table>' .
				"</td>\n\t</tr>\n";
		} else {
			$data = $_page->display (0);
			$data .= $_inner->display (0) . '<br />' . $_extern->display (0);
		}
		return $data;
	}
}

?>