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
// Recipients widget.  Displays a multiple-select box with extra fields
// listing users, so that they can be added or removed from the list.
//

/**
	 * Recipients widget.  Displays a multiple-select box with extra fields
	 * listing users, so that they can be added or removed from the list.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_recipients ('name');
	 * echo $widget->display ();
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	CMS
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.0, 2003-08-16, $Id: Recipients.php,v 1.1.1.1 2005/04/29 04:44:31 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_recipients extends MF_Widget {
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
	var $type = 'recipients';

	/**
	 * Constructor Method.
	 * 
	 * @access	public
	 * @param	string	$name
	 * 
	 */
	function MF_Widget_recipients ($name) {
		// initialize core Widget settings
		parent::MF_Widget ($name);
		$this->passover_isset = true;
	}

	function getUsers () {
		$snm =& session_get_manager ();
		$users = $snm->user->getList (false, false, 'lastname', 'asc');
		if (! $users) {
			return array ();
		}
		foreach ($users as $k => $v) {
			if (! empty ($v->lastname)) {
				$users[$v->username] = $v->lastname . ', ' . $v->firstname;
			} else {
				$users[$v->username] = $v->username;
			}
			unset ($users[$k]);
		}
		return $users;
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

		page_add_script ('
			function cms_recipient_add (f) {
				user = f.elements[\'' . $this->name . '_users\'].options[f.elements[\'' . $this->name . '_users\'].selectedIndex].value;
				if (user.length == 0) {
					user = f.elements[\'' .$this->name . '_email\'].value;
					if (user.length == 0) {
						return false;
					}
					name = user;
				} else {
					name = f.elements[\'' . $this->name . '_users\'].options[f.elements[\'' . $this->name . '_users\'].selectedIndex].text;
				}

				// 2. add the selected user to the list
				if (document.all) {
					f.elements[\'' . $this->name . '[]\'].options[f.elements[\'' . $this->name . '[]\'].options.length] = new Option (name, user, false, false);
				} else {
					o = document.createElement (\'option\');
					o.text = name;
					o.value = user;
					f.elements[\'' . $this->name . '[]\'].add (o, null);
				}

				f.elements[\'' . $this->name . '_users\'].selectedIndex = null;

				f.elements[\'' . $this->name . '_email\'].value = \'\';

				// 3. cancel the click
				return false;
			}

			function cms_recipient_remove (f) {
				for (i = f.elements[\'' . $this->name . '[]\'].options.length - 1; i >= 0; i--) {
					if (f.elements[\'' . $this->name . '[]\'].options[i].selected) {
						// remove
						if (document.all) {
							f.elements[\'' . $this->name . '[]\'].options.remove (i);
						} else {
							f.elements[\'' . $this->name . '[]\'].options[i] = null;
						}
					}
				}

				return false;
			}

			function cms_recipient_select_all (f) {
				for (i = 0; i < f.elements[\'' . $this->name . '[]\'].options.length; i++) {
					f.elements[\'' . $this->name . '[]\'].options[i].selected = true;
				}
			}
		');

		if ($generate_html) {
			global $cgi;
			$users = $this->getUsers ();
			if (is_array ($cgi->{$this->name})) {
				$list = $cgi->{$this->name};
			} else {
				$list = array ();
			}
			$data .= '<tr>
				<td class="label"' . $this->invalid () . ' valign="top">
					<label for="' . $this->name . '" id="' . $this->name . '-label">' . template_simple ($this->label_template, $this, '', true) . '</label>
				</td>
				<td class="field">
					<table border="0" cellpadding="3" cellspacing="0">
						<tr>
							<td valign="top">
					<select name="' . $this->name . '[]" multiple="multiple" size="5" ' . $attrstr . ' ' . $this->extra . '>' . NEWLINE;
			foreach ($list as $value) {
				$data .= TABx2 . TABx2 . TABx2 . '<option value="' . $value . '"';
				if (isset ($users[$value])) {
					$data .= '>' . $users[$value] . '</option>' . NEWLINE;
				} else {
					$data .= '>' . $value . '</option>' . NEWLINE;
				}
			}
			$data .= '</select>
							</td>
							<td valign="top">
					<table cellpadding="3" cellspacing="1" border="0">
					<tr><td>User</td><td><select name="' . $this->name . '_users">
						<option value="">- ' . intl_get ('SELECT') . ' -</option>' . NEWLINE;
			foreach ($users as $user => $name) {
				if ($user == session_username ()) {
					continue;
				}
				$data .= TABx2 . TABx2 . TABx2 . '<option value="' . $user . '"';
				$data .= '>' . $name . '</option>' . NEWLINE;
			}
			$data .= '</select></td></tr>
					<tr><td>Email</td><td><input type="text" name="' . $this->name . '_email" size="10" /> <input type="submit" value="' . intl_get ('Add') . '" onclick="return cms_recipient_add (this.form)" /></td></tr>
					</table><br />
					<input type="submit" value="' . intl_get ('Remove Selected') . '" onclick="return cms_recipient_remove (this.form)" />
							</td>
						</tr>
					</table>
				</td>
			</tr>' . NEWLINEx2;

		} else {

		}

		return $data;
	}
}

?>