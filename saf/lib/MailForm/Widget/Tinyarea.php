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
// Tinyarea widget.  Displays a TinyMCE wysiwyg editor.
//

/**
	 * Tinyarea widget.  Displays a TinyMCE WYSIWYG editor.  For more info on
	 * TinyMCE, visit http://tinymce.moxiecode.com/
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_tinyarea ('name');
	 * $widget->validation ('is "foo"');
	 * $widget->setValue ('foo');
	 * $widget->error_message = 'Oops!  This widget is being unruly!';
	 * 
	 * ?>
	 * </code>
	 * 
	 * @package	MailForm
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.0, 2001-11-28, $Id: Tinyarea.php,v 1.1 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_tinyarea extends MF_Widget {
	/**
	 * The height of the textarea widget in rows.
	 * 
	 * @access	public
	 * 
	 */
	var $rows = 16;

	/**
	 * The width of the textarea widget in columns.
	 * 
	 * @access	public
	 * 
	 */
	var $cols = 60;

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
	var $type = 'tinyarea';

	/**
	 * This is the position of the label for the textarea.  It may
	 * be set to 'top' (the default), or 'left' to be pushed to the side.
	 * 
	 * @access	public
	 * 
	 */
	var $labelPosition = 'top';

	var $isHtml = true;

	var $_template_left = '
		<tr>
			<td class="label" valign="top">
				<label for="{name}" id="{name}-label" {invalid}>{label}</label>
			</td>
			<td class="field">
				<textarea {attrstr|none} rows="{rows}" cols="{cols}" {extra|none} class="tinyarea">{data_value}</textarea>
			</td>
		</tr>';

	var $_template_top = '
		{if not empty (obj.alt)}
			<tr>
				<td class="label" colspan="2">
					<label for="{name}" id="{name}-label" {invalid}>{label}</label>
				</td>
			</tr>
		{end if}
		<tr>
			<td class="field" colspan="2">
				<textarea {attrstr|none} rows="{rows}" cols="{cols}" {extra|none} class="tinyarea">{data_value}</textarea>
			</td>
		</tr>';

	var $_template_no_table = '<textarea {attrstr|none} rows="{rows}" cols="{cols}" {extra|none} class="tinyarea">{data_value}</textarea>';

	var $tinyButtons1 = 'bold,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright,justifyfull,bullist,numlist,undo,redo,link,unlink,emotions,separator,formatselect';
	var $tinyButtons2 = '';
	var $tinyButtons3 = '';
	var $tinyPlugins = 'emotions';
	var $tinyValidElements = 'a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]';
	var $tinyPathLocation = 'bottom';

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
		parent::display ($generate_html);

		if ($this->cols == '') {
			$this->cols = 60;
		}

		if ($this->rows == '') {
			$this->rows = 16;
		}

		if ($this->isHtml) {
			page_add_script ('/js/tiny_mce/tiny_mce.js');
			page_add_script (template_simple ('
				tinyMCE.init ({
					mode : "textareas",
					theme : "advanced",
					editor_selector : "tinyarea",
					plugins : "{tinyPlugins}",
					relative_urls : false,
					theme_advanced_buttons1 : "{tinyButtons1}",
					theme_advanced_buttons2 : "{tinyButtons2}",
					theme_advanced_buttons3 : "{tinyButtons3}",
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_path_location : "{tinyPathLocation}",
					extended_valid_elements : "{tinyValidElements}"
				});
			', $this));
		}

		$this->attrstr = $this->getAttrs ();
		$this->label = template_simple ($this->label_template, $this, '', true);
		if ($generate_html) {
			if ($this->labelPosition == 'left') {
				return template_simple ($this->_template_left, $this);
			}
			return template_simple ($this->_template_top, $this);
		}
		return template_simple ($this->_template_no_table, $this);
	}
}

?>
