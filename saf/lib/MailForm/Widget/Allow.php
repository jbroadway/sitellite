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
// Allow widget.  Displays a list of HTML <input type="checkbox" /> form
// fields.
//

$GLOBALS['loader']->import ('saf.MailForm.Widget.Checkbox');

/**
	 * Allow widget.  Displays a list of HTML <input type="checkbox" />
	 * form fields.
	 * 
	 * New in 1.2:
	 * - Added new methods: getTables, getDirs, getMods, getStatus, and getAccess.
	 * 
	 * New in 1.4:
	 * - Fixed a bug where existing values weren't setting properly.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_allow ('name');
	 * $widget->validation ('is "foo"');
	 * $widget->tables = array ('foo' => 'Foo', 'bar' => 'Bar');
	 * $widget->error_message = 'Oops!  This widget is being unruly!';
	 * 
	 * ?>
	 * </code>
	 * 
	 * @package	MailForm
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.4, 2002-09-27, $Id: Allow.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_allow extends MF_Widget_checkbox {
	/**
	 * The list of elements that are to be displayed.
	 * 
	 * @access	public
	 * 
	 */
	var $tables = array ();

	/**
	 * The text to display for the first element, which defaults to 'Allow All'.
	 * 
	 * @access	public
	 * 
	 */
	var $allow_all_text = 'Allow All';

	/**
	 * This is the short name for this widget.  The short name is
	 * the class name minus the 'MF_Widget_' prefix.
	 * 
	 * @access	public
	 * 
	 */
	var $type = 'allow';

	/**
	 * Constructor Method.
	 * 
	 * @access	public
	 * @param	string	$name
	 * 
	 */
	function MF_Widget_allow ($name) {
		// initialize core Widget settings
		parent::MF_Widget_checkbox ($name);
	}

	/**
	 * Fills the $tables property with a list of database table names.
	 * 
	 * @access	public
	 * 
	 */
	function getTables () {
		global $db, $tables;
		foreach ($db->tables as $table) {
			$this->tables[$table] = $tables[$table]->alt;
		}
	}

	/**
	 * Fills the $tables property with a list of the directories
	 * in inc/conf/quicklinks.xml.
	 * 
	 * @access	public
	 * 
	 */
	function getDirs () {
		global $loader;
		$loader->import ('saf.Misc.Ini');
		$links = Ini::parse ('inc/conf/quicklinks.php');
		foreach ($links as $name => $link) {
			if ($name != 'all') {
				$this->tables[$name] = $link->display;
			}
		}
	}

	/**
	 * Fills the $tables property with a list of Sitellite modules.
	 * 
	 * @access	public
	 * 
	 */
	function getMods () {
		global $loader;
		$loader->import ('saf.App.Module');
		$loader->import ('saf.File.Directory');
		$dir = new Dir (getcwd () . '/mod');
		$files = $dir->read_all ();
		foreach ($files as $file) {
			if (preg_match ("/^\.+$/", $file)) {
				continue;
			}
			$module = new Module ($file);
			if (! empty ($module->name)) {
				$this->tables[$file] = $module->name;
			}
		}
	}

	/**
	 * Fills the $tables property with a list of statuses from the
	 * sitellite_status database table.
	 * 
	 * @access	public
	 * 
	 */
	function getStatus () {
		global $db;
		$q = $db->query ('select * from sitellite_status');
		if ($q->execute ()) {
			while ($a = $q->fetch ()) {
				$this->tables[$a->name] = ucfirst ($a->name);
			}
			$q->free ();
		}
	}

	/**
	 * Fills the $tables property with a list of access levels from the
	 * sitellite_access database table.
	 * 
	 * @access	public
	 * 
	 */
	function getAccess () {
		global $db;
		$q = $db->query ('select * from sitellite_access');
		if ($q->execute ()) {
			while ($a = $q->fetch ()) {
				$this->tables[$a->name] = ucfirst ($a->name);
			}
			$q->free ();
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
		global $simple;
		$this->value = $this->tables;
		if (! isset ($this->data_value)) {
			$this->data_value = $this->default_value;
		}
		$vals = preg_split ('/, ?/', $this->data_value);
		if ($generate_html) {
			//$data = "\t" . '<tr>' . "\n\t\t" . '<td colspan="2"><hr size="1" /></td>' . "\n\t" . '</tr>' . "\n";
			$data .= "\t" . '<tr>' . "\n\t\t" . '<td valign="top" class="label"><label for="' . $this->name . '"' . $this->invalid () . '>' . $simple->fill ($this->label_template, $this, '', true) . "</label></td>\n\t\t" .
				'<td valign="top" class="field">' . "\n";

			if (in_array ('all', $vals)) {
				$selected = ' checked="checked"';
			} else {
				$selected = '';
			}
			$data .= '<table border="0" cellpadding="3" cellspacing="0" width="100%">';
			$data .= "\n\t<tr>\n\t\t<td width=\"50%\" class=\"item\">";
			$data .= '<input type="checkbox" name="' .  $this->name . '[]" value="all"' . $selected . " /> <strong>" . $this->allow_all_text . "</strong></td>\n";

			$count = 1;

			foreach ($this->value as $value => $display) {
				if (in_array ($value, $vals)) {
					$selected = ' checked="checked"';
				} else {
					$selected = '';
				}
				if ($count % 2 == 0) {
					$data .= "\t</tr>\n\t<tr>\n";
				}
				$count++;
				$data .= "\t\t<td width=\"50%\" class=\"item\">" . '<input type="checkbox" name="' . $this->name . '[]" value="' . $value . '"' . $selected . ' ' . $this->extra . ' /> ' . $display . "</td>\n";
			}
			$data .= "\t</tr>\n</table>\n";

			return $data . '</td>' . "\n\t" . '</tr>' . "\n";
		} else {

			if (in_array ('all', $vals)) {
				$selected = ' checked="checked"';
			} else {
				$selected = '';
			}
			$data .= '<table border="0" cellpadding="3" cellspacing="0" width="100%">';
			$data .= "\n\t<tr>\n\t\t<td width=\"50%\" class=\"item\">";
			$data .= '<input type="checkbox" name="' .  $this->name . '[]" value="all"' . $selected . " /> <strong>" . $this->allow_all_text . "</strong></td>\n";

			$count = 1;

			foreach ($this->value as $value => $display) {
				if (in_array ($value, $vals)) {
					$selected = ' checked="checked"';
				} else {
					$selected = '';
				}
				if ($count % 2 == 0) {
					$data .= "\t</tr>\n\t<tr>\n";
				}
				$count++;
				$data .= "\t\t<td width=\"50%\" class=\"item\">" . '<input type="checkbox" name="' . $this->name . '[]" value="' . $value . '"' . $selected . ' ' . $this->extra . ' /> ' . $display . "</td>\n";
			}
			$data .= "\t</tr>\n</table>\n";
			return $data;
		}
	}
}



?>