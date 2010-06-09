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
// Joiner widget.  Displays a select with the ability to create
// many-to-many joins with another table.
//

/**
	 * Joiner widget.  Displays a select with the ability to create
	 * many-to-many joins with another table.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $widget = new MF_Widget_selector ('name');
	 * echo $widget->display ();
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	MailForm
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.0, 2003-08-16, $Id: Selector.php,v 1.6 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MF_Widget_joiner extends MF_Widget {
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
	var $type = 'joiner';

	/**
	 * This is the primary key for the current item.
	 *
	 * @access public
	 *
	 */
	var $id = 0;

	/**
	 * This is the database table to get the list of items from.
	 *
	 * @access public
	 *
	 */
	var $table = false;

	/**
	 * This is the primary key of the list table.
	 *
	 * @access public
	 *
	 */
	var $key = false;

	/**
	 * This is the name/title field from the list table.
	 *
	 * @access public
	 *
	 */
    var $title = false;

	/**
	 * This is the join table.
	 *
	 * @access public
	 *
	 */
	var $join_table = false;

	/**
	 * This is the field referring to the main table in the join table.
	 *
	 * @access public
	 *
	 */
	var $join_main_key = false;

	/**
	 * This is the field referring to the foreign table in the join table.
	 *
	 * @access public
	 *
	 */
	var $join_foreign_key = false;

	/**
	 * Set this to false for more complex tables to disable the add/remove actions
	 * for adding/removing items from the foreign table.
	 *
	 * @access public
	 *
	 */
	var $editable = true;

	/**
	 * Name of the box that adds the new item or items to the database
	 * table underlying the selector.
	 *
	 * @access public
	 *
	 */
	var $addAction = 'cms/joiner/add';

	/**
	 * Name of the box that removes the specified item or items from the
	 * database table underlying the selector.
	 *
	 * @access public
	 *
	 */
	var $removeAction = 'cms/joiner/remove';

	var $_style = '<style type="text/css">

a.joiner-selected {
	font-weight: bold;
	color: #000;
}

a.joiner-unselected {
	/* no special style */
}

</style>';

	var $_script = '<script language="javascript">

{if not obj.loaded}
var rpc = new rpc ();
{end if}

var {name}joiner = {
	url: \'{site/prefix}/index/cms-joiner-rpc-action\',
	action: rpc.action,
	name: \'{name}\',
	join_table: \'{join_table}\',
	join_main_key: \'{join_main_key}\',
	join_foreign_key: \'{join_foreign_key}\',

	add: function (main_id, foreign_id) {
		this._fid = foreign_id;
		rpc.call (
			this.action (\'add\', [this.name, main_id, foreign_id, this.join_table, this.join_main_key, this.join_foreign_key]),
			function (request) {
				res = eval (request.responseText);
				// highlight item
				document.getElementById (\'{name}\' + {name}joiner._fid).className = \'joiner-selected\';
				document.getElementById (\'{name}\' + {name}joiner._fid).setAttribute (\'onclick\', \'return {name}joiner.remove ({id}, \' + {name}joiner._fid + \')\');
				document.getElementById (\'{name}\' + {name}joiner._fid).setAttribute (\'title\', \'Click to de-select\');
				return false;
			}
		);
		return false;
	},

	remove: function (main_id, foreign_id) {
		this._fid = foreign_id;
		rpc.call (
			this.action (\'remove\', [this.name, main_id, foreign_id, this.join_table, this.join_main_key, this.join_foreign_key]),
			function (request) {
				res = eval (request.responseText);
				// un-highlight item
				document.getElementById (\'{name}\' + {name}joiner._fid).className = \'joiner-unselected\';
				document.getElementById (\'{name}\' + {name}joiner._fid).setAttribute (\'onclick\', \'return {name}joiner.add ({id}, \' + {name}joiner._fid + \')\');
				document.getElementById (\'{name}\' + {name}joiner._fid).setAttribute (\'title\', \'Click to select\');
				return false;
			}
		);
		return false;
	}
}

</script>';

	var $_output = '
	<tr>
		<td class="label" valign="top" colspan="2"><label for="{name}" id="{name}-label"{invalid}>{display_value}</label></td>
	</tr>
	<tr>
		<td class="field" valign="top" colspan="2">{intl Click to select}:<br />
{loop obj._list}
{if loop.selected}
{if loop._key ne 0}| {end if}<a href="#" onclick="return {name}joiner.remove ({id}, {loop/id})" class="joiner-selected" title="Click to de-select" id="{name}{loop/id}">{loop/title}</a>
{end if}
{if not loop.selected}
{if loop._key ne 0}| {end if}<a href="#" onclick="return {name}joiner.add ({id}, {loop/id})" class="joiner-unselected" title="Click to select" id="{name}{loop/id}">{loop/title}</a>
{end if}
{end loop}
{if obj.addAction}<br /><br />
<a href="{site/prefix}/index/cms-joiner-manage-action?table={table}&key={key}&title={title}" target="_blank">{intl Add/Remove Items}</a>
		</td>
	</tr>
';

	/**
	 * Constructor Method.
	 * 
	 * @access	public
	 * @param	string	$name
	 * 
	 */
	function MF_Widget_joiner ($name) {
		// initialize core Widget settings
		parent::MF_Widget ($name);
		$this->passover_isset = true;
		$this->ignoreEmpty = true;
	}

	/**
	 * Retrieve the list of available items.
	 *
	 * @return array
	 *
	 */
	function getList () {
        if ($this->title) {
            $res = db_fetch_array ('select ' . $this->key . ' as id, ' . $this->title . ' as title from ' . $this->table . ' order by title asc');
        } else {
            $res = db_fetch_array ('select ' . $this->key . ' as id, ' . $this->key . ' as title from ' . $this->table . ' order by title asc');
        }
		return $res;
	}

	/**
	 * Retrieve the list of items associated with the current item.
	 *
	 * @return array
	 *
	 */
	function getSelected () {
		if (! $this->id) {
			return array ();
		}
		return db_shift_array (
			sprintf (
				'select %s from %s where %s = ?',
				$this->join_foreign_key,
				$this->join_table,
				$this->join_main_key
			),
			$this->id
		);
	}

	/**
	 * Call this on adding a new item to save the values specified for it.
	 *
	 * @param string
	 *
	 */
	function saveSelected ($id) {
		$list = session_get ($this->name . '_joiner');
		if (! is_array ($list)) {
			$list = array ();
		}
		foreach ($list as $fid) {
			db_execute (
				sprintf (
					'insert into %s (%s, %s) values (?, ?)',
					$this->join_table,
					$this->join_main_key,
					$this->join_foreign_key
				),
				$id,
				$fid
			);
		}
		session_set ($this->name . '_joiner', null);
		return true;
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

		if (session_is_resource ($this->table) && ! session_allowed ($this->table, 'rw', 'resource')) {
			$allowed = false;
		} else {
			$allowed = true;
		}

		$this->_list = $this->getList ();
		$this->_selected = $this->getSelected ();
		foreach ($this->_list as $k => $v) {
			if (in_array ($v->id, $this->_selected)) {
				$this->_list[$k]->selected = true;
			} else {
				$this->_list[$k]->selected = false;
			}
		}

		if (! $this->id) {
			$this->_id = $this->id;
			$this->id = 'false';
		}

		static $loaded = false;
		if (! $loaded) {
			page_add_style ($this->_style);
			page_add_script (site_prefix () . '/js/rpc-compressed.js');
		}
		$this->loaded = $loaded;
		page_add_script (template_simple ($this->_script, $this));
		$loaded = true;

		if (isset ($this->_id)) {
			$this->id = $this->_id;
			unset ($this->_id);
		}
		return template_simple ($this->_output, $this);
	}
}

?>
