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
// Team widget.  Displays a list of teams, or an info box if you're not
// allowed to change the team.
//

/**
	 * Team widget.  Displays a list of teams, or an info box if you're not
	 * allowed to change the team.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_team ('sitellite_team');
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
	 * @version	1.0, 2001-11-28, $Id: Team.php,v 1.7 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_team extends MF_Widget {
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
	var $type = 'team';

	function getOwner () {
		foreach (array_keys ($this->form->widgets) as $k) {
			if ($this->form->widgets[$k]->name == 'sitellite_owner') {
				global $cgi;
				$v = $this->form->widgets[$k]->getValue ();
				if (empty ($v)) {
					return session_username ();
				} else {
					return $v;
				}
			}
		}
		return session_username ();
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
		$attrstr = $this->getAttrs ();

		if (empty ($this->data_value)) {
			$this->data_value = session_team ();
		}

		if (empty ($this->owner)) {
			$this->owner = $this->getOwner ($this->owner);
		}

		if (session_role () == 'master') {
			// allow all
			$this->value = assocify (session_get_teams ());
		} elseif (session_username () == $this->owner) {
			// allow specific ones
			$this->value = assocify (session_allowed_teams_list (true));
		} else {
			// show info instead
			loader_import ('saf.MailForm.Widget.Info');
			$info = new MF_Widget_info ($this->name);
			$info->extra = $this->extra;
			$info->setValue ($this->data_value);
			return $info->display ($generate_html);
		}

		asort ($this->value);

		$adv = ($this->advanced) ? ' class="advanced"' : '';

		if ($generate_html) {
			$data = "\t" . '<tr' . $adv . '>' . "\n\t\t" . '<td class="label"><label for="' . $this->name . '" id="' . $this->name . '-label"' . $this->invalid () . '>' . $simple->fill ($this->label_template, $this, '', true) . '</label></td>' . "\n\t\t" .
				'<td class="field"><select ' . $attrstr . ' ' . $this->extra . ' >' . "\n";
			foreach ($this->value as $value => $display) {
				$display = str_replace ('_', ' ', ucwords ($display));
				if ($value == $this->data_value) {
					$selected = ' selected="selected"';
				} else {
					$selected = '';
				}
				$data .= "\t" . '<option value="' . $value . '"' . $selected . '>' . $display . '</option>' . "\n";
			}
			return $data . '</select></td>' . "\n\t" . '</tr>' . "\n";
		} else {
			$data = '<select ' . $attrstr . ' ' . $this->extra . ' >' . "\n";
			foreach ($this->value as $value => $display) {
				$display = str_replace ('_', ' ', ucwords ($display));
				if ($value == $this->data_value) {
					$selected = ' selected="selected"';
				} else {
					$selected = '';
				}
				$data .= "\t" . '<option value="' . htmlentities_compat ($value, ENT_COMPAT, $intl->charset) . '"' . $selected . '>' . $display . '</option>' . "\n";
			}
			return $data . '</select>';
		}
	}
}



?>