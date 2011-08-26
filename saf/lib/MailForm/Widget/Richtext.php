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
// Richtext widget.  Displays a WYSIWYG (What-You-See-Is-What-You-Get)
// form field, which provides a Microsoft Word-like interface to your
// documents.
//

/**
	 * Richtext widget.  Displays a WYSIWYG (What-You-See-Is-What-You-Get)
	 * form field, which provides a Microsoft Word-like interface to your
	 * documents.
	 * 
	 * This widget is based on the Open Source RichText project, which can be found
	 * at http://richtext.sourceforge.net/
	 * 
	 * Note: To use this widget, you must also set the following property of your
	 * MailForm object:
	 * 
	 * $form->extra = 'name="mainform" onsubmit="rt_copyValue ()"';
	 * 
	 * New in 1.2:
	 * - Added a $directory property which contains the path to the directory containing
	 *   the richedit.html file and accompanying Richtext resources.
	 * 
	 * New in 1.4:
	 * - Added a clean() method.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_richtext ('name');
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
	 * @version	1.4, 2003-01-21, $Id: Richtext.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_richtext extends MF_Widget {
	/**
	 * The height of the widget.
	 * 
	 * @access	public
	 * 
	 */
	var $height = 400;

	/**
	 * The width of the widget.
	 * 
	 * @access	public
	 * 
	 */
	var $width = 600;

	/**
	 * The height of the widget in rows, used for incompatible
	 * browsers.
	 * 
	 * @access	public
	 * 
	 */
	var $rows = 8;

	/**
	 * The width of the widget in columns, used for incompatible
	 * browsers.
	 * 
	 * @access	public
	 * 
	 */
	var $cols = 40;

	/**
	 * The directory to find richedit.html in.  Defaults to 'inc/rte'.
	 * 
	 * @access	public
	 * 
	 */
	var $directory = 'inc/rte';

	/**
	 * The list of RichText configuration options, including 'history',
	 * 'dragdrop', 'source', 'style', 'font', 'fontSize', and 'colour'.  Please
	 * refer to the help docs for more information.
	 * 
	 * @access	public
	 * 
	 */
	var $options = array (
		'history' => 'off',
		'dragdrop' => 'on',
		'source' => 'yes',
		'style' => 'yes',
		'font' => 'no',
		'fontSize' => 'no',
		'colour' => 'yes',
	);

	/**
	 * A way to pass extra parameters to the HTML form tag, for
	 * example 'enctype="multipart/formdata"'.
	 * 
	 * @access	public
	 * 
	 */
	var $extra = '';

	var $js = '<script language="JavaScript">

function rt_copyValue () {
	// this assumes your form is named mainform 
	mainform.{name}.innerText = document.getElementById (\'rt_{name}\').docHtml;
}

</script>
<script language="JavaScript" event="onload" for="window">

document.getElementById (\'rt_{name}\').options = "{options_formatted}";
document.getElementById (\'rt_{name}\').docHtml = mainform.{name}.innerText;
		
</script>';

	/**
	 * This is the short name for this widget.  The short name is
	 * the class name minus the 'MF_Widget_' prefix.
	 * 
	 * @access	public
	 * 
	 */
	var $type = 'richtext';

	/**
	 * Cleans the input using saf.HTML.Messy, so that tags conform to
	 * XHTML specs.  Note: This is nowhere near 100% effective.
	 * 
	 * @access	public
	 * @param	string	$input
	 * @return	string
	 * 
	 */
	function clean ($input) {
		global $loader;
		$loader->import ('saf.HTML.Messy');
		$messy = new Messy;
		return $messy->clean ($input);
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
		$this->options_formatted = '';
		foreach ($this->options as $k => $v) {
			$this->options_formatted .= $k . '=' . $v . ';';
		}
		// this next line relies on a properly configured browscap.ini file,
		// which can be found at http://www.cyscape.com/browscap/
		global $loader, $intl;
		$loader->import ('saf.Ext.phpsniff');
		$ua = new phpSniff ();
		if (
				$ua->property ('browser') == 'ie' &&
				$ua->property ('platform') == 'win' &&
				$ua->property ('version') >= '5.5'
		) {
			if ($generate_html) {
				return "\t" . '<tr>' . "\n\t\t" . '<td colspan="2" class="label"><label for="' . $this->name . '"' . $this->invalid () . '>' . $simple->fill ($this->label_template, $this, '', true) . '</label></td>' . "\n\t" .
					'</tr>' . "\n\t" . '<tr>' . "\n\t\t" . '<td colspan="2" class="field"><textarea name="' .
					$this->name . '" ' . $this->extra . ' style="display:none">' .
					htmlentities ($this->data_value) . '</textarea>' .
					'<object id="rt_' . $this->name . '" style="BACKGROUND-COLOR: buttonface" data="' . $this->directory . '/richedit.html" width="' . $this->width . '" height="' . $this->height . '" type="text/x-scriptlet" VIEWASTEXT></object></td>' . "\n\t" . '</tr>' .
					$simple->fill ($this->js, $this) . "\n";
			} else {
				return '<textarea name="' . $this->name . '" ' . $this->extra . ' style="display:none">' . htmlentities_compat ($this->data_value, ENT_COMPAT, $intl->charset) . '</textarea>' .
					'<object id="rt_' . $this->name . '" style="BACKGROUND-COLOR: buttonface" data="' . $this->directory . '/richedit.html" width="' . $this->width . '" height="' . $this->height . '" type="text/x-scriptlet" VIEWASTEXT></object>' .
					$simple->fill ($this->js, $this);
			}
		} else {

			if ($this->cols == '') {
				$this->cols = 40;
			}

			if ($this->rows == '') {
				$this->rows = 8;
			}

			if ($generate_html) {
				return "\t" . '<tr>' . "\n\t\t" . '<td colspan="2" class="label"><label for="' . $this->name . '"' . $this->invalid () . '>' . $simple->fill ($this->label_template, $this, '', true) . '</label></td>' . "\n\t" .
					'</tr>' . "\n\t" . '<tr>' . "\n\t\t" . '<td colspan="2" class="field"><textarea name="' .
					$this->name . '" rows="' . $this->rows . '" cols="' . $this->cols . '" ' . $this->extra . ' >' .
					htmlentities ($this->data_value) . '</textarea></td>' . "\n\t" . '</tr>' . "\n";
			} else {
				return '<textarea name="' . $this->name . '" rows="' . $this->rows . '" cols="' . $this->cols . '" ' . $this->extra . ' >' . htmlentities_compat ($this->data_value, ENT_COMPAT, $intl->charset) . '</textarea>';
			}
		}
	}
}



?>