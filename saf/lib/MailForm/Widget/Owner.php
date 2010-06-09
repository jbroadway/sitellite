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
// Owner widget.  Displays the owner of the current document.
//

/**
	 * Info widget.  Displays text and a hidden widget.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_owner ('name');
	 * $widget->setValue ('foo');
	 * 
	 * ?>
	 * </code>
	 * 
	 * @package	MailForm
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.0, 2003-07-23, $Id: Owner.php,v 1.4 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_owner extends MF_Widget {
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
	var $type = 'owner';

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
		global $intl, $simple;
		$attrstr = $this->getAttrs ();

		if (empty ($this->data_value)) {
			if (empty ($this->default_value)) {
				$this->data_value = session_username ();
			} else {
				$this->data_value = $this->default_value;
			}
		}

		$adv = ($this->advanced) ? ' class="advanced"' : '';

		if ($generate_html) {
			return "\t" . '<tr' . $adv . '>' . "\n\t\t" . '<td class="label"><label for="' . $this->name . '"' . $this->invalid () . '>' . $simple->fill ($this->label_template, $this, '', true) . '</label></td>' . "\n\t\t" .
				'<td class="field"><strong>' . htmlentities_compat ($this->data_value, ENT_COMPAT, $intl->charset) . '</strong><input type="hidden" name="' . $this->name . '" value="' . htmlentities_compat ($this->data_value, ENT_COMPAT, $intl->charset) . '" /></td>' . "\n\t" . '</tr>' . "\n";
		} else {
			return '<input type="text" ' . $attrstr . ' value="' . htmlentities_compat ($this->data_value, ENT_COMPAT, $intl->charset) . '" ' . $this->extra . ' />';
		}
	}
}



?>