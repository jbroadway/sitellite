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
// Pseudo-Widget that displays a section header.
//

/**
	 * Pseudo-Widget that displays a section header.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_section ('section2');
	 * $widget->setValue ('Section Title');
	 * $widget->display ();
	 * 
	 * ?>
	 * </code>
	 * 
	 * @package	MailForm
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.0, 2002-08-31, $Id: Section.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_section extends MF_Widget {
	/**
	 * The title of this section.
	 * 
	 * @access	public
	 * 
	 */
	var $title;

	/**
	 * This is the short name for this widget.  The short name is
	 * the class name minus the 'MF_Widget_' prefix.
	 * 
	 * @access	public
	 * 
	 */
	var $type = 'section';

	/**
	 * Set this to false if you don't want the text to be bolded.
	 *
	 * @access	public
	 *
	 */
	var $bold = true;

	/**
	 * Constructor Method.
	 * 
	 * @access	public
	 * @param	string	$name
	 * 
	 */
	function MF_Widget_section ($name) {
		$this->name = $name;
		$this->passover_isset = true;
		$this->error_message = '';
	}

	/**
	 * Validates the widget against its set of $rules.  Returns false
	 * on failure to pass any rule.
	 * 
	 * @access	public
	 * @param	string	$value
	 * @param	object	$form
	 * @param	object	$cgi
	 * @return	boolean
	 * 
	 */
	function validate ($value, $form, $cgi) {
		return true;
	}

	/**
	 * Sets the section title.  The second parameter is ignored.
	 * 
	 * @access	public
	 * @param	string	$key
	 * @param	string	$value
	 * 
	 */
	function setValue ($key = '', $value = '') {
		if (! empty ($key)) {
			$this->title = $key;
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
		return '';
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
		global $intl;
		if (is_object ($intl)) {
			$title = $intl->get ($this->title);
		} else {
			$title = $this->title;
		}

		if ($generate_html) {
			if (! $this->bold) {
				return sprintf ("\t<tr>\n\t\t<td colspan=\"2\" class=\"section-header\">%s</td>\n\t</tr>\n", $title);
			} else {
				return sprintf ("\t<tr>\n\t\t<td colspan=\"2\" class=\"section-header\"><strong>%s</strong></td>\n\t</tr>\n", $title);
			}
		} else {
			return $title;
		}
	}
}



?>
