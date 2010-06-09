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
// Bookmarks widget.  Displays a text box to allow external links, with a
// bookmark button to select bookmarked links.
//

/**
	 * Bookmarks widget.  Displays a text box to allow external links, with a
	 * bookmark button to select bookmarked links.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_bookmarks ('name');
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

class MF_Widget_bookmarks extends MF_Widget {
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
	var $type = 'bookmarks';

	/**
	 * Constructor Method.
	 * 
	 * @access	public
	 * @param	string	$name
	 * 
	 */
	function MF_Widget_bookmarks ($name) {
		// initialize core Widget settings
		parent::MF_Widget ($name);

		// load Hidden, Text and Ref widgets, on which this widget depends
		$GLOBALS['loader']->import ('saf.MailForm.Widget.Text');
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
		$attrstr = $this->getAttrs ();

		if ($generate_html) {
			page_add_script (site_prefix () . '/js/dialog.js');
			$data .= "\t<tr>\n\t\t<td class=\"label\" valign=\"top\"><label for=\"" . $this->name . '" id="' . $this->name . '-label" ' . $this->invalid () . '>' . template_simple ($this->label_template, $this, '', true) . "</label></td>\n\t\t<td class=\"field\">";
			$data .= template_simple ('
				<script language="javascript" type="text/javascript">

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
						bookmark_form.elements[bookmark_element].value = url;
					}

				</script>
				<input type="text" ' . $attrstr . ' value="{data_value}" {extra} />&nbsp;
				<input type="submit" onclick="bookmarks (this.form, \'{name}\'); return false" value="{intl Bookmarks}" />
			', $this);
			$data .= "</td>\n\t</tr>\n";
		} else {
			$data = '';
		}
		return $data;
	}
}

?>