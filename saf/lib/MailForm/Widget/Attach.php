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
// Attach widget.  Implements the interface to an "attachment"
// component for messages.
//

/**
	 * Attach widget.  Implements the interface to an "attachment"
	 * component for messages.  Leaves the add and remove logic to separate
	 * scripts which are called through popup windows.  Used in the Sitellite
	 * Personal Workspace.
	 * 
	 * The submitted value of this widget does not represent the list of
	 * attachments, since adding and removing attachments are handled
	 * externally.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_attach ('name');
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
	 * @version	1.0, 2002-09-27, $Id: Attach.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_attach extends MF_Widget {
	/**
	 * A way to pass extra parameters to the HTML form tag, for
	 * example 'enctype="multipart/formdata"'.
	 * 
	 * @access	public
	 * 
	 */
	var $extra = '';

	/**
	 * Sets the size attribute of the select box that displays
	 * the current attachments.  Defaults to 3.
	 * 
	 * @access	public
	 * 
	 */
	var $size = 3;

	/**
	 * This is the short name for this widget.  The short name is
	 * the class name minus the 'MF_Widget_' prefix.
	 * 
	 * @access	public
	 * 
	 */
	var $type = 'attach';

	/**
	 * This is the link to send the 'Add' button to.  Defaults to
	 * 'addAttachment.php'.
	 * 
	 * @access	public
	 * 
	 */
	var $addLink = 'addAttachment.php';

	/**
	 * This is the link to send the 'Remove' button to.  Defaults to
	 * 'removeAttachment.php'.
	 * 
	 * @access	public
	 * 
	 */
	var $removeLink = 'removeAttachment.php';

	function buttons () {
		global $intl;
		$r = '<table border="0" cellpadding="2" cellspacing="0"><tr><td class="item" style="border-bottom: 0px none; border-right: 0px none">';
		// add button pops up a window to addAttachment.php?name= and the name of the current form,
		// and addAttachment.php is expected to set a new attachment entry after a brief conversation with the user
		// note that this means the form must be named
		$r .= '<input type="submit" value=" ' . $intl->get ('Add') . ' " onclick="window.open (\'' . $this->addLink . '?name=\' + this.form.name + \'&field=' . $this->name . '\', \'Attachment\', \'\'); return false" />';
		$r .= '</td></tr><tr><td class="item" style="border-bottom: 0px none; border-right: 0px none">';
		// remove button pops up a window to removeAttachment.php?id= and the value of the currently selected item,
		// then removes the currently selected item from the list.
		$r .= '<input type="submit" value="' . $intl->get ('Remove') . '" onclick="if (this.form.' . $this->name . '.options[this.form.' . $this->name . '.selectedIndex].value) { window.open (\'' . $this->removeLink . '?id=\' + this.form.' . $this->name . '.options[this.form.' . $this->name . '.selectedIndex].value, \'Attachment\', \'\'); this.form.' . $this->name . '.options[this.form.' . $this->name . '.selectedIndex] = null; } return false" />';
		$r .= '</td></tr></table>';
		return $r;
	}

	function validate () {
		return true;
	}

	function getValue () {
		return true;
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
		global $intl, $simple;
		if (! isset ($this->data_value)) {
			$this->data_value = $this->default_value;
		}
		if ($generate_html) {
			$data = "\t" . '<tr>' . "\n\t\t" . '<td valign="top" class="label"><label for="' . $this->name . '"' . $this->invalid () . '>' . $simple->fill ($this->label_template, $this, '', true) . '</label></td>' . "\n\t\t" .
				'<td class="field"><table border="0" cellpadding="0" cellspacing="0"><tr><td class="item"><select name="' . $this->name . '" size="' . $this->size . '" ' . $this->extra . ' >' . "\n";
			foreach ($this->value as $value => $display) {
				if ($value == $this->data_value) {
					$selected = ' selected="selected"';
				} else {
					$selected = '';
				}
				$data .= "\t" . '<option value="' . $value . '"' . $selected . '>' . $display . '</option>' . "\n";
			}
			return $data . '</select></td><td class="item">' . $this->buttons () . '</td></tr></table></td>' . "\n\t" . '</tr>' . "\n";
		} else {
			$data = '<table border="0" cellpadding="0" cellspacing="0"><tr><td class="field"><select name="' . $this->name . '" size="' . $this->size . '" ' . $this->extra . ' >' . "\n";
			foreach ($this->value as $value => $display) {
				if ($value == $this->data_value) {
					$selected = ' selected="selected"';
				} else {
					$selected = '';
				}
				$data .= "\t" . '<option value="' . htmlentities_compat ($value, ENT_COMPAT, $intl->charset) . '"' . $selected . '>' . $display . '</option>' . "\n";
			}
			return $data . '</select></td><td class="field">' . $this->buttons () . '</td></tr></table>';
		}
	}
}



?>