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
// DatabaseTable is an extension of the Database class, which provides
// high-level functions, such as fetchAll(), that aim to lessen the need
// to type common queries out each time you use them.
//

/**
	 * DatabaseTable is an extension of the Database class, which provides
	 * high-level functions, such as fetchAll(), that aim to lessen the need
	 * to type common queries out each time you use them.
	 * 
	 * Note: This class does not worry about database abstraction, since that will
	 * be handled by subclassing the I18n class and calling its get() method on
	 * each query automatically in the Database class.
	 * 
	 * New in 1.2:
	 * - Added the following properties: $info, $columns, $pkey, $alt, $datefield,
	 *   $statusfield, $queryfield, $orderfield, $treefield, $accessfield,
	 *   $add_form_template, $add_form_message, $edit_form_template, and
	 *   $edit_form_message.
	 * - Added the following methods: getInfo(), getPkey(), _makeWidget(), and
	 *   changeType().
	 * - This class is now not accessed directly, but through the saf.Database
	 *   driver system.  This is done transparently however, since table objects
	 *   are always created using the saf.Database table() method.
	 * 
	 * New in 1.4:
	 * - Added the addFacet() method.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $dt = new DatabaseTable ($db, 'tablename', 'pkeycolumn');
	 * 
	 * if ($res = $dt->fetchAll ()) {
	 * 	// do something with $res
	 * } else {
	 * 	echo $dt->error;
	 * }
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	Database
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.4, 2002-01-14, $Id: Table.php,v 1.4 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class DatabaseTable {
	/**
	 * Contains a reference to the database connection resource.
	 * 
	 * @access	public
	 * 
	 */
	var $db;

	/**
	 * The name of the table.
	 * 
	 * @access	public
	 * 
	 */
	var $name;

	/**
	 * Contains the error message if there is one, or false otherwise.
	 * 
	 * @access	public
	 * 
	 */
	var $error;

	/**
	 * Contains the error number if there is one, or false otherwise.
	 * 
	 * @access	public
	 * 
	 */
	var $errno;

	/**
	 * The number of rows returned by the previous query.
	 * 
	 * @access	public
	 * 
	 */
	var $rows;

	/**
	 * The sql from the previous query, if an error occurred.
	 * 
	 * @access	public
	 * 
	 */
	var $sql;

	/**
	 * The unparsed info from the database about this table and
	 * its columns.
	 * 
	 * @access	public
	 * 
	 */
	var $info = false;

	/**
	 * An array of MailForm widgets created based on the $info data.
	 * 
	 * @access	public
	 * 
	 */
	var $columns = array ();

	/**
	 * The name of the primary key field.  This property is also duplicated
	 * in the $primary_key property, which is a reference to this one.
	 * 
	 * @access	public
	 * 
	 */
	var $pkey = false;

	/**
	 * An alternate name to display for this table, which makes it nicer to
	 * read for people.  By default the $alt is the $name with underscores
	 * converted to spaces and the first letter (of the first word only)
	 * capitalized.
	 * 
	 * @access	public
	 * 
	 */
	var $alt = '';

	/**
	 * Contains the column to search for date values in the Sitellite CMS
	 * database manager.
	 * 
	 * @access	public
	 * 
	 */
	var $datefield = '';

	/**
	 * Contains the column to search for status values in the Sitellite CMS
	 * database manager.
	 * 
	 * @access	public
	 * 
	 */
	var $statusfield = '';

	/**
	 * Contains the column to search for any string values in the Sitellite
	 * CMS database manager.
	 * 
	 * @access	public
	 * 
	 */
	var $queryfield = '';

	/**
	 * Contains the column to order search results by in the Sitellite CMS
	 * database manager.
	 * 
	 * @access	public
	 * 
	 */
	var $orderfield = '';

	/**
	 * Contains the column that makes a self-reference (a reference to
	 * the primary key of its own table), which can be used in the Sitellite CMS
	 * database manager to show a tree representation of this table.
	 * 
	 * @access	public
	 * 
	 */
	var $treefield = '';

	/**
	 * Contains the column to search for access level values in the Sitellite
	 * CMS database manager.
	 * 
	 * @access	public
	 * 
	 */
	var $accessfield = '';

	/**
	 * Contains the template to use for displaying the Add Record form
	 * in the Sitellite CMS.
	 * 
	 * @access	public
	 * 
	 */
	var $add_form_template = '';

	/**
	 * Contains the message to use for displaying the Add Record form
	 * in the Sitellite CMS.
	 * 
	 * @access	public
	 * 
	 */
	var $add_form_message = '';

	/**
	 * Contains the template to use for displaying the Edit Record form
	 * in the Sitellite CMS.
	 * 
	 * @access	public
	 * 
	 */
	var $edit_form_template = '';

	/**
	 * Contains the message to use for displaying the Edit Record form
	 * in the Sitellite CMS.
	 * 
	 * @access	public
	 * 
	 */
	var $edit_form_message = '';

	/**
	 * Contains a key/value list of database types (regular
	 * expressions are used here to save repeating ourselves) and their
	 * corresponding MailForm widget types.  Used internally by the drivers.
	 * 
	 * @access	public
	 * 
	 */
	var $typemap = array ();

	var $facets = array ();

	/**
	 * Constructor method.
	 * 
	 * @access	public
	 * @param	Database object reference	$db
	 * @param	string	$name
	 * @param	string	$pkey
	 * 
	 */
	function DatabaseTable (&$db, $name, $pkey) {
		$this->db =& $db;
		$this->name = $name;
		if (empty ($pkey)) {
			$this->pkey = $this->getPkey ();
		} else {
			$this->pkey = $pkey;
		}
		$this->primary_key =& $this->pkey;
		$this->alt = ucwords (str_replace ('_', ' ', $this->name));
	}

	/**
	 * Creates a SELECT query based on the key given to it.
	 * $keyval may be a value to query against the primary key column
	 * or an associative array of values to be
	 * placed in the WHERE clause of the SELECT query.  $columns is an
	 * optional array of columns to return.  $ascdesc can be either
	 * 'asc' or 'desc'.  Returns an object if the query returns a
	 * single result (ie. for queries against the primary key column),
	 * or an array of objects, or false.
	 * 
	 * @access	public
	 * @param	mixed	$keyval
	 * @param	array	$columns
	 * @param	string	$order
	 * @param	string	$ascdesc
	 * @return	mixed
	 * 
	 */
	function fetch ($keyval, $columns = '', $order = '', $ascdesc = '') {
		$sql = 'SELECT ';
		if (is_array ($columns)) {
			$sql .= join (', ', array_values ($columns)) . ' FROM ' . $this->name . ' WHERE ';
		} else {
			$sql .= '* FROM ' . $this->name . ' WHERE ';
		}
		if (is_array ($keyval)) {
			$sql .= join (' = ?? AND ', array_keys ($keyval));
			$sql .= ' = ??';
			$binds = array_values ($keyval);
		} else {
			$binds = array ($keyval);
			$sql .= $this->pkey . ' = ??';
		}
		if (! empty ($order)) {
			$sql .= ' ORDER BY ' . $order;
		}
		if (! empty ($ascdesc)) {
			$sql .= ' ' . $ascdesc;
		}

		$q = $this->db->query ($sql);
		if ($q->execute ($binds)) {
			$this->rows = $q->rows ();
			if ($this->rows == 1) {
				$row = $q->fetch ();
				$q->free ();
				return $row;
			} else {
				$rows = array ();
				while ($row = $q->fetch ()) {
					array_push ($rows, $row);
				}
				$q->free ();
				return $rows;
			}
		} else {
			$this->errno = $q->errno ();
			$this->error = $q->error ();
			$this->sql = $sql;
			return false;
		}
	}

	/**
	 * Creates a SELECT query without a WHERE clause, returning
	 * all columns from the table.  $columns is an
	 * optional array of columns to return.  $ascdesc can be either
	 * 'asc' or 'desc'.  Returns an array of objects, or false on failure.
	 * 
	 * @access	public
	 * @param	array	$columns
	 * @param	string	$order
	 * @param	string	$ascdesc
	 * @return	array
	 * 
	 */
	function fetchAll ($columns = '', $order = '', $ascdesc = '') {
		$sql = 'SELECT ';
		if (is_array ($columns)) {
			$sql .= join (', ', array_values ($columns)) . ' FROM ' . $this->name;
		} else {
			$sql .= '* FROM ' . $this->name;
		}
		if (! empty ($order)) {
			$sql .= ' ORDER BY ' . $order;
		}
		if (! empty ($ascdesc)) {
			$sql .= ' ' . $ascdesc;
		}

		$q = $this->db->query ($sql);
		if ($q->execute ()) {
			$this->rows = $q->rows ();
			$rows = array ();
			while ($row = $q->fetch ()) {
				array_push ($rows, $row);
			}
			$q->free ();
			return $rows;
		} else {
			$this->errno = $q->errno ();
			$this->error = $q->error ();
			$this->sql = $sql;
			return false;
		}
	}

	/**
	 * Creates an INSERT query from the columns provided.  Returns the
	 * lastid() of the query, or the $columns[$this->pkey] value if there is
	 * no sequential key in this table, or false on failure.
	 * 
	 * @access	public
	 * @param	associative array	$columns
	 * @return	mixed
	 * 
	 */
	function insert ($columns) {
		if (is_array ($columns)) {
			$and = 0;
			$binds = array ();
			$esc = array ();
			$sql = 'INSERT INTO ' . $this->name . ' (' . join (', ', array_keys ($columns)) . ') VALUES (';
			foreach ($columns as $key => $val) {
				array_push ($binds, $val);
				array_push ($esc, '??');
			}
			$sql .= join (', ', array_values ($esc)) . ')';
		} elseif (is_object ($columns)) {
			$and = 0;
			$binds = array ();
			$esc = array ();
			$sql = 'INSERT INTO ' . $this->name . ' (' . join (', ', array_keys (get_object_vars ($columns))) . ') VALUES (';
			foreach (get_object_vars ($columns) as $key => $val) {
				array_push ($binds, $val);
				array_push ($esc, '??');
			}
			$sql .= join (', ', array_values ($esc)) . ')';
		}
		$q = $this->db->query ($sql);
		if ($q->execute ($binds)) {
			$id = $q->lastid ();
			if ($id > 0) {
				return $id;
			} else {
				return $columns[$this->pkey];
			}
		} else {
			$this->errno = $q->errno ();
			$this->error = $q->error ();
			$this->sql = $sql;
			return false;
		}
	}

	/**
	 * Creates an UPDATE query from the $keyval and $columns provided.
	 * Returns true if successful, false on failure.
	 * 
	 * @access	public
	 * @param	mixed	$keyval
	 * @param	associative array	$columns
	 * @return	boolean
	 * 
	 */
	function update ($keyval, $columns) {
		if (is_object ($columns)) {
			$columns = get_object_vars ($columns);
		}
		$sql = 'UPDATE ' . $this->name . ' SET ';
		$sql .= join (' = ??, ', array_keys ($columns));
		$sql .= ' = ??';
		$binds = array_values ($columns);

		if (is_array ($keyval)) {
			$sql .= ' WHERE ';
			$sql .= join (' = ?? AND ', array_keys ($keyval));
			$sql .= ' = ??';
			foreach ($keyval as $key => $val) {
				array_push ($binds, $val);
			}
		} else {
			$sql .= ' WHERE ' . $this->pkey . ' = ??';
			array_push ($binds, $keyval);
		}

		$q = $this->db->query ($sql);
		if ($q->execute ($binds)) {
			return true;
		} else {
			$this->errno = $q->errno ();
			$this->error = $q->error ();
			$this->sql = $sql;
			return false;
		}
	}

	/**
	 * Creates an DELETE query from the key value or values provided.
	 * 
	 * @access	public
	 * @param	string or associative array	$keyval
	 * @return	boolean
	 * 
	 */
	function delete ($keyval) {
		if (is_array ($keyval)) {
			$sql = 'DELETE FROM ' . $this->name . ' WHERE ';
			$sql .= join (' = ?? AND ', array_keys ($keyval));
			$sql .= ' = ??';
			$binds = array_values ($keyval);
		} else {
			$sql = 'DELETE FROM ' . $this->name . ' WHERE ' . $this->pkey . ' = ??';
			$binds = array ($keyval);
		}
		$q = $this->db->query ($sql);
		if ($q->execute ($binds)) {
			return true;
		} else {
			$this->errno = $q->errno ();
			$this->error = $q->error ();
			$this->sql = $sql;
			return false;
		}
	}

	/**
	 * Gets the name of the primary key field for this table using the
	 * results of getInfo() (see below).  Used by the constructor method to
	 * set the $pkey property if a primary key column was not specified.
	 * Returns false on failure.  This method is left entirely to the drivers
	 * to implement, as its workings will always be database-specific.
	 * 
	 * @access	public
	 * @return	string
	 * 
	 */
	function getPkey () {
		// leave to driver to implement
	}

	/**
	 * Gets all the info it can from the database about this table and
	 * its columns, stores it in the $info property, and parses it into an
	 * associative array ($columns) of MailForm widgets.  This method is left
	 * entirely to the drivers to implement, as its workings will always be
	 * database-specific.
	 * 
	 * @access	public
	 * @return	boolean
	 * 
	 */
	function getInfo () {
		// leave to driver to implement
	}

	/**
	 * Uses the global $tables array (Sitellite CMS specific) to get
	 * the primary key, referencing column, display column, and a true or false
	 * self-reference value, when given a Ref column name and its corresponding
	 * table.  This method is left to the drivers to implement, since some of
	 * it may be database-specific, namely the logic that the display column
	 * should be a concatenation of the first two columns that are not of the
	 * types Hidden, File, or Password.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	string	$table
	 * @return	boolean
	 * 
	 */
	function getRefInfo ($name, $table) {
		// leave to driver to implement
	}

	/**
	 * Takes an object with at least $name and $type properties and
	 * converts it into a MailForm widget.  Additional properties are also
	 * transfered.  Returns the new MailForm widget.
	 * 
	 * @access	private
	 * @param	object	$col
	 * @return	object
	 * 
	 */
	function _makeWidget ($col) {
		global $loader;
		$loader->import ('saf.MailForm.Widget');
		//$cols = array ();
		//foreach ($this->columns as $col) {
		$widget_name = 'MF_Widget_' . $col->type;
		$loader->import ('saf.MailForm.Widget.' . ucfirst ($col->type));
		$widget = new $widget_name ($col->name);
		$widget->type = $col->type;
		foreach (get_object_vars ($col) as $key => $value) {
			$widget->{$key} = $value;
		}
		//}
		//return $cols;
		/*if ($col->type == 'ref' || $col->type == 'mref') {
			list (
				$widget->primary_key,
				$widget->ref_column,
				$widget->display_column,
				$widget->self_ref
			) = $this->getRefInfo ($col->name, $col->table);
		}*/
		return $widget;
	}

	/**
	 * PLEASE NOTE: This method is deprecated in favour of the built-in
	 * changeType() method in the base MF_Widget class.
	 *
	 * Takes a widget name, a new type, and any extra properties
	 * that should be set immediately, and translates one MailForm widget
	 * (from the $columns list) into another type.  This is the new way
	 * to change types of columns in the columns.php configuration file
	 * in the Sitellite CMS.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	string	$type
	 * @param	associative array	$extra
	 * @return	boolean
	 * 
	 */
	function changeType ($name, $type, $extra = array ()) {
		global $loader;
		$new_type = 'MF_Widget_' . $type;
		$loader->import ('saf.MailForm.Widget.' . ucfirst ($type));
		$old = $this->columns[$name];
		$this->columns[$name] = new $new_type ($old->name);
		foreach (get_object_vars ($old) as $key => $value) {
			if ($key == 'passover_isset') {
				continue;
			}
			$this->columns[$name]->{$key} = $value;
		}
		if (! is_array ($extra)) {
			$extra = array ('table' => $extra);
		}
		foreach ($extra as $key => $value) {
			$this->columns[$name]->{$key} = $value;
		}

		/*if ($type == 'ref' || $type == 'mref') {
			if (empty ($extra['table'])) {
				return false;
			} else {
				list (
					$this->columns[$name]->primary_key,
					$this->columns[$name]->ref_column,
					$this->columns[$name]->display_column,
					$this->columns[$name]->self_ref
				) = $this->getRefInfo ($name, $extra['table']);
			}
		} elseif ($type == 'tie') {
			if (empty ($extra['table'])) {
				return false;
			} else {
				$this->columns[$name]->tie = $extra['table'];
			}
		}*/

		$this->columns[$name]->type = $type;

		return true;
	}

	/**
	 * Creates a "facet" about this widget based on one of its
	 * columns.  See saf.Database.Facet for more info about those.
	 * 
	 * @access	public
	 * @param	string	$column
	 * @param	string	$title
	 * @param	string	$extra
	 * @return	object reference
	 * 
	 */
	function &addFacet ($column, $title, $extra = '') {
		global $loader;
		$loader->import ('saf.Database.Facet');
		$f = new DatabaseFacet ($this, $column, $title, $extra);
		$this->facets[] =& $f;
		return $f;
	}
}



?>