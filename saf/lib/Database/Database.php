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
// Database is a database abstraction class; a unified means
// of accessing different relational database systems.
//

loader_import ('saf.Database.Query');
loader_import ('saf.Database.Table');

define ('DB_FETCHMODE_ASSOC', 2);
define ('DB_FETCHMODE_OBJECT', 3);

/**
	 * Database is a database abstraction class; a unified means
	 * of accessing different relational database systems.  It is accompanied
	 * by a second class called Query, and relies on driver classes to provide
	 * all the database specific functionality.
	 * 
	 * Database 2.0 is 100% backwards compatible with code written for 1.0,
	 * except that it has been updated to require the new Loader class, and
	 * to work with Query 2.0, which adds optional caching support via Berkeley
	 * DBs.
	 * 
	 * New in 2.2:
	 * - Added new $error and $row properties, and a new fetch() method, to
	 *   retrieve quick select queries in a single line of code.
	 * 
	 * New in 2.4:
	 * - Added new table() method, which creates a DatabaseTable object, which
	 *   you can use to execute high-level functions, such as fetchAll(), on.
	 * - Added an $sql property and an abstract() method, for query abstraction
	 *   using the saf.Database.SQL package.
	 * 
	 * New in 2.6:
	 * - Added an execute() method, which allows you to execute quick insert or
	 *   update statements in a single line of code.
	 * 
	 * New in 2.8:
	 * - Added connect() and close() methods, so that you can disconnect and
	 *   reconnect to the same database if necessary.
	 * 
	 * New in 3.0:
	 * - Added getTables() and tableExists() methods.
	 * - Added a $tables property.
	 * - saf.Database.Table is included automatically now, because it is
	 *   extended in the drivers.
	 * - Turned persistent connections OFF by default.
	 * 
	 * New in 3.2:
	 * - Fixed fetch() and execute() so that the bind values may be passed
	 *   as a single array which would be the second value, as opposed to
	 *   needing each value to bind to be declared separately.  This is handy
	 *   if you already have an array of values to bind.
	 * 
	 * New in 3.4:
	 * - Added new methods single() and shift(), which return the first object
	 *   only of a result set, or the first value from the first object of the
	 *   result set, respectively.
	 * - Added global functions that alias methods of a global $db object.  These
	 *   include db_fetch(), db_query(), db_table(), db_execute(), db_error(),
	 *   db_single(), and db_shift().
	 *
	 * New in 3.6:
	 * - Fixed a bug in the handling of hostnames including ports.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $db = new Database ("Driver:Host:Database", "Username", "Password", 1);
	 * 
	 * // create a database query object
	 * $q = $db->query ("SELECT * FROM table");
	 * 
	 * if ($q->execute ()) {
	 * 	while ($row = $q->fetch ()) {
	 * 		// do something with the $row object
	 * 	}
	 * 	$q->free ();
	 * } else {
	 * 	// query failed
	 * }
	 * 
	 * if ($row = $db->fetch ('SELECT * FROM table WHERE id = ??', $id)) {
	 * 	// do something with $row
	 * } else {
	 * 	// query failed
	 * 	echo $db->error;
	 * }
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	Database
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	3.6, 2004-03-12, $Id: Database.php,v 1.4 2007/09/01 17:02:17 lux Exp $
	 * @access	public
	 * 
	 */

class Database {
	/**
	 * Contains the database connection resource.
	 * 
	 * @access	public
	 * 
	 */
	var $connection;

	/**
	 * Boolean value denoting whether to enable transactions in the
	 * current database.
	 * 
	 * @access	public
	 * 
	 */
	var $transactions = 0;

	/**
	 * Contains the name of the database driver being used.
	 * 
	 * @access	public
	 * 
	 */
	var $driver;

	/**
	 * Contains the name of the database host.
	 * 
	 * @access	public
	 * 
	 */
	var $host;

	/**
	 * Contains the name of the database being used.
	 * 
	 * @access	public
	 * 
	 */
	var $name;

	/**
	 * Contains the username used to connect to the current database.
	 * 
	 * @access	public
	 * 
	 */
	var $user;

	/**
	 * Contains the password used to connect to the current database.
	 * 
	 * @access	public
	 * 
	 */
	var $pass;

	/**
	 * Contains the loaded database driver.
	 * 
	 * @access	private
	 * 
	 */
	var $dbd;

	/**
	 * Contains the error message if an error occurs here.  For most
	 * uses however, you will want to use the error() method of the Query
	 * object instead.
	 * 
	 * @access	public
	 * 
	 */
	var $error;

	/**
	 * If abstract() has been called, this will contain the SQL
	 * abstraction object.
	 * 
	 * @access	private
	 * 
	 */
	var $sql;

	/**
	 * Contains the number of rows returned by the previous fetch() call.
	 * 
	 * @access	public
	 * 
	 */
	var $rows;

	/**
	 * Contains the lastid() of the last insert query sent to the execute()
	 * method.
	 * 
	 * @access	public
	 * 
	 */
	var $lastid;

	/**
	 * Contains a list of tables in the database.  Set by getTables().
	 * 
	 * @access	public
	 * 
	 */
	var $tables = false;

	var $pearEmu = false; // PEAR::DB emulation property.  set to true to emulate

	var $sequenceFormat = '%s_seq';

	var $fetchMode = DB_FETCHMODE_OBJECT;

	/**
	 * Constructor method.  Establishes a connection to the specified
	 * database system.
	 * 
	 * $constr is the connection string that is used to tell Database
	 * how to find the database you are looking for.  It takes the
	 * format: "Driver:Hostname:Database".
	 * 
	 * $user is the username required to connect to the database.
	 * 
	 * $pass is the password required to connect to the database.
	 * 
	 * $persistent is a 1 or 0 (true or false) value denoting whether
	 * to establish a persistent connection or not.  Default is 1 (true).
	 * 
	 * @access	public
	 * @param	string	$constr
	 * @param	string	$user
	 * @param	string	$pass
	 * @param	boolean	$persistent
	 * 
	 */
	function Database ($connstr = '::', $user = '', $pass = '', $persistent = 0) {
		list ($this->driver, $this->host, $this->name) = preg_split ('/:/', $connstr, 3);

		//echo $this->driver . '-' . $this->host . '-' . $this->name . '<br />';
		if (strstr ($this->name, ':')) {
			list ($port, $this->name) = explode (':', $this->name);
			$this->host .= ':' . $port;
		}

		$this->user = $user;
		$this->pass = $pass;
		$this->persistent = $persistent;
		if (! $GLOBALS['loader']->import ('saf.Database.Driver.' . $this->driver)) {
			return 0;
		}
		$driver = $this->driver . '_Driver';
		$this->dbd = new $driver ($this->name, $this->host, $this->user, $this->pass, $this->persistent);
		$this->connection =& $this->dbd->connection;
		if ($this->dbd->error) {
			$this->error = $this->dbd->error;
		}
	}

	/**
	 * Creates and returns a Query object.
	 * 
	 * $sql is the SQL query you wish to execute with this object.
	 * 
	 * Note: If $pearEmu is set to true, this method merely aliases execute().
	 * 
	 * @access	public
	 * @param	string	$sql
	 * @return	object
	 * 
	 */
	function query ($sql = '', $cache = 0) {
		if ($this->pearEmu) {
			return $this->execute ($sql);
		}
		$classname = $this->driver . '_Query';

		if (is_object ($this->sql)) {
			$sql = $this->sql->abstractSql ($sql);
		}

		$q = new $classname ($sql, $this->connection, $cache);
		return $q;
		// $this->dbd->query ($sql, $this->connection);
	}

	/**
	 * Creates and returns a DatabaseTable object.  $pkey
	 * is the name of the primary key column.  Automatically loads the
	 * saf.Database.Table package on calling, instead of loading the
	 * extra package at the start.
	 * 
	 * @access	public
	 * @param	string	$table
	 * @param	string	$pkey
	 * @return	object
	 * 
	 */
	function table ($table, $pkey = '') {
		$classname = $this->driver . '_DatabaseTable';
		$tbl = new $classname ($this, $table, $pkey);
		return $tbl;
	}

	/**
	 * Creates an saf.Database.SQL object, which inherits all
	 * of its functionality from saf.I18n, and serves as an abstraction
	 * layer for database queries.
	 * 
	 * @access	public
	 * @param	string	$path
	 * 
	 */
	function abstractSql ($path = 'inc/sql') {
		global $loader;
		$loader->import ('saf.Database.SQL');
		$this->sql = new SQL ($this->driver, $path);
	}

	/**
	 * Executes a single query against the database and returns either
	 * the single result object if there is only one, an array of result
	 * objects all at once if there are more than one, or false upon failure
	 * or no results.  This method is good for shortening code on quick select
	 * queries.  If an error occurs, you can retrieve the error message from
	 * the $error property.  The number of rows returned is stored in the $rows
	 * property.  Bind values may be passed as additional parameters.
	 * 
	 * @access	public
	 * @param	string	$sql
	 * @param	mixed	$bind_values
	 * @return	mixed
	 * 
	 */
	function fetch () {
		$this->error = false;
		$vals = func_get_args ();
		$sql = array_shift ($vals);
		$classname = $this->driver . '_Query';

		if (is_object ($this->sql)) {
			$sql = $this->sql->abstractSql ($sql);
		}

		if (isset ($vals[0]) && is_array ($vals[0])) {
			$vals = $vals[0];
		}

		$q = new $classname ($sql, $this->connection, 0);
		if ($q->execute ($vals)) {
			$this->rows = $q->rows ();
			if ($this->rows == 1) {
				$row = $q->fetch ();
				$q->free ();
				return $row;
			} elseif ($this->rows > 1) {
				$rows = array ();
				while ($row = $q->fetch ()) {
					array_push ($rows, $row);
				}
				$q->free ();
				return $rows;
			}
		} else {
			$this->rows = 0;
			$this->error = $q->error ();
			$this->err_sql = $sql;
			$this->err_bind = $vals;
			return false;
		}
	}

	/**
	 * Executes a single query against the database and returns a
	 * single result object.  This method is good for shortening code on quick select
	 * queries.  If an error occurs, you can retrieve the error message from
	 * the $error property.  The number of rows returned is stored in the $rows
	 * property.  Bind values may be passed as additional parameters.
	 * 
	 * @access	public
	 * @param	string	$sql
	 * @param	mixed	$bind_values
	 * @return	mixed
	 * 
	 */
	function single () {
		$this->error = false;
		$vals = func_get_args ();
		$sql = array_shift ($vals);
		$classname = $this->driver . '_Query';

		if (is_object ($this->sql)) {
			$sql = $this->sql->abstractSql ($sql);
		}

		if (is_array ($vals[0])) {
			$vals = $vals[0];
		}

		$q = new $classname ($sql, $this->connection, 0);
		if ($q->execute ($vals)) {
			$this->rows = $q->rows ();
			if ($this->rows > 0) {
				$row = $q->fetch ();
				$q->free ();
				return $row;
			}
		} else {
			$this->rows = 0;
			$this->error = $q->error ();
			$this->err_sql = $sql;
			$this->err_bind = $vals;
			return false;
		}
	}

	/**
	 * Executes a single query against the database and returns the
	 * first value from the first result.  This method is good for shortening
	 * code on quick select queries.  If an error occurs, you can retrieve the
	 * error message from the $error property.  The number of rows returned is
	 * stored in the $rows property.  Bind values may be passed as additional
	 * parameters.
	 * 
	 * @access	public
	 * @param	string	$sql
	 * @param	mixed	$bind_values
	 * @return	mixed
	 * 
	 */
	function shift () {
		$args = func_get_args ();
		$obj = call_user_func_array (array (&$this, 'single'), $args);
		if (is_object ($obj)) {
			return array_shift (get_object_vars ($obj));
		}
		return false;
	}

	/**
	 * Executes a single query against the database and returns true
	 * or false depending on the success of the query.  This method is good for
	 * shortening code on quick insert or update queries.  If an error occurs,
	 * you can retrieve the error message from the $error property.  The lastid()
	 * of an insert query is stored in the $lastid property.  Bind values may be
	 * passed as additional parameters.
	 * 
	 * @access	public
	 * @param	string	$sql
	 * @param	mixed	$bind_values
	 * @return	boolean
	 * 
	 */
	function execute () {
		$this->error = false;
		$vals = func_get_args ();
		$sql = array_shift ($vals);
		$classname = $this->driver . '_Query';

		if (is_object ($this->sql)) {
			$sql = $this->sql->abstractSql ($sql);
		}

		if (isset ($vals[0]) && is_array ($vals[0])) {
			$vals = $vals[0];
		}

		$q = new $classname ($sql, $this->connection, 0);
		if ($q->execute ($vals)) {
			$this->lastid = $q->lastid ();
			return true;
		} else {
			$this->lastid = 0;
			$this->error = $q->error ();
			$this->err_sql = $sql;
			$this->err_bind = $vals;
			return false;
		}
	}

	/**
	 * Connects to the database.  A connection is automatically created
	 * when a new Database object is created, so there is no need to use this
	 * method unless you need to disconnect and reconnect again.
	 * 
	 * @access	public
	 * @return	boolean
	 * 
	 */
	function connect () {
		$this->dbd->connect ();
		if ($this->dbd->connection) {
			$this->connection = $this->dbd->connection;
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Disconnects from the database.  This method is usually unnecessary
	 * since connections should be automatically terminated or should persist after
	 * the script finishes executing, depending on your settings.
	 * 
	 * @access	public
	 * @return	boolean
	 * 
	 */
	function close () {
		if ($this->dbd->close ()) {
			$this->connection = false;
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Retrieves a list of tables in the database and places them in the
	 * $tables property, which is an array.  Returns true on success and false
	 * on failure.
	 * 
	 * @access	public
	 * @return	boolean
	 * 
	 */
	function getTables () {
		$this->tables = $this->dbd->getTables ($this);
		if (! is_array ($this->tables)) {
			return false;
		}
		return true;
	}

	/**
	 * Determines whether the specified table exists or not by comparing
	 * it to the $tables list.  If $tables is not set, then it will call
	 * getTables() automatically as well.
	 * 
	 * @access	public
	 * @param	string	$tbl
	 * @return	boolean
	 * 
	 */
	function tableExists ($tbl) {
		if (! is_array ($this->tables)) {
			$this->getTables ();
		}
		if (in_array ($tbl, $this->tables)) {
			return true;
		}
		return false;
	}

	function setFetchMode ($mode) {
		$prev = $this->fetchMode;
		$this->fetchMode = $mode;
		return $prev;
	}

	function getFetchMode () {
		return $this->fetchMode;
	}

	// PEAR::DB emulation methods below

	function getAll ($sql) {
		$res = $this->fetch ($sql);
		if (! $res) {
			return false;
		} elseif (is_object ($res)) {
			$res = array ($res);
		}
		foreach ($res as $k => $row) {
			$res[$k] = (array) $row;
		}
		return $res;
	}

	function quote ($string) {
		if (method_exists ($this->dbd, 'quote')) {
			return $this->dbd->quote ($string);
		}
		return ($string === null) ? 'NULL' : "'" . str_replace ("'", "''", $string) . "'";
	}

	function setOption ($option, $val) {}

	function createSequence ($seq_name) {
		if (method_exists ($this->dbd, 'createSequence')) {
			return $this->dbd->createSequence ($seq_name);
		}
		$this->error = 'Unsupported feature';
		return false;
	}

	function dropSequence ($seq_name) {
		if (method_exists ($this->dbd, 'dropSequence')) {
			return $this->dbd->dropSequence ($seq_name);
		}
		$this->error = 'Unsupported feature';
		return false;
	}

	function getSequenceName ($sqn) {
		return sprintf (
			$this->sequenceFormat,
			preg_replace ('/[^a-z0-9_]/i', '_', $sqn)
		);
	}

	function nextId ($seq_name) {
		if (method_exists ($this->dbd, 'nextId')) {
			return $this->dbd->nextId ($seq_name);
		}
		$this->error = 'Unsupported feature';
		return false;
	}

}


/**
 * Alias of the query() method above.
 *
 */
function db_query ($sql = '', $cache = 0) {
	return $GLOBALS['db']->query ($sql, $cache);
}

/**
 * Alias of the table() method above.
 *
 */
function db_table ($table, $pkey = '') {
	return $GLOBALS['db']->table ($table, $pkey);
}

/**
 * Returns the results of a single database query.  Returns
 * false if no results are returned, or on error.  Returns an
 * object if there is only one result.  Returns an array of
 * objects if there are multiple results.
 *
 * @access public
 * @param string
 * @param mixed bind values
 * @return mixed
 *
 */
function db_fetch () {
	$args = func_get_args ();
	global $db;
	return call_user_func_array (array (&$db, 'fetch'), $args);
}

/**
 * Same as db_fetch(), except it always returns an array, including
 * an empty array on error or no results (fetch the error with
 * db_error() to differentiate between the two), and an array
 * of one item if there is only one result (whereas db_fetch()
 * would return that as an object).
 *
 * @access public
 * @param string
 * @param mixed bind values
 * @return array
 *
 */
function db_fetch_array () {
	$args = func_get_args ();
	global $db;
	$res = call_user_func_array (array (&$db, 'fetch'), $args);
	if (! $res) {
		return array ();
	} elseif (is_object ($res)) {
		return array ($res);
	}
	return $res;
}

/**
 * Same as db_fetch_array(), except it returns an array of the first
 * value of each object (like db_shift() does for a single result).
 * Please note: Keys are not preserved.
 *
 * @access public
 * @param string
 * @param mixed bind values
 * @return array
 *
 */
function db_shift_array () {
	$args = func_get_args ();
	global $db;
	$res = call_user_func_array (array (&$db, 'fetch'), $args);
	if (! $res) {
		return array ();
	} elseif (is_object ($res)) {
		return array (array_shift (get_object_vars ($res)));
	}
	$res2 = array ();
	foreach ($res as $row) {
		$res2[] = array_shift (get_object_vars ($row));
	}
	return $res2;
}

/**
 * Similar to db_shift_array(), except it returns an associative array
 * of the first two columns of the query results, where the first column
 * is the key and the second column is the value.
 *
 * @access public
 * @param string
 * @param mixed bind values
 * @return array
 *
 */
function db_pairs () {
	$args = func_get_args ();
	global $db;
	$res = call_user_func_array (array (&$db, 'fetch'), $args);
	if (! $res) {
		return array ();
	} elseif (is_object ($res)) {
		$res = (array) $res;
		return array (array_shift ($res) => array_shift ($res));
	}
	$res2 = array ();
	foreach ($res as $row) {
		$row = (array) $row;
		$res2[array_shift ($row)] = array_shift ($row);
	}
	return $res2;
}

/**
 * Similar to db_fetch(), but used for SQL statements that return no
 * results (ie. inserts, updates, deletes, etc.).  Returns true or
 * false as to whether the statement executed successfully, or on
 * inserts with an auto-incrementing primary key, returns the last
 * inserted value.
 *
 * @access public
 * @param string
 * @param mixed bind values
 * @return boolean
 *
 */
function db_execute () {
	$args = func_get_args ();
	global $db;
	return call_user_func_array (array (&$db, 'execute'), $args);
}

/**
 * Similar to db_fetch(), but only returns a single result as an
 * object, even if there are multiple results.  Returns false on
 * error or if there are no results.
 *
 * @access public
 * @param string
 * @param mixed bind values
 * @return object
 *
 */
function db_single () {
	$args = func_get_args ();
	global $db;
	return call_user_func_array (array (&$db, 'single'), $args);
}

/**
 * Similar to db_shift(), but only returns the first column of
 * the first result.  Returns false on error or if there are no
 * results.
 *
 * @access public
 * @param string
 * @param mixed bind values
 * @return mixed
 *
 */
function db_shift () {
	$args = func_get_args ();
	global $db;
	return call_user_func_array (array (&$db, 'shift'), $args);
}

/**
 * Returns the number of rows from the last executed query.
 *
 * @access public
 * @return int
 *
 */
function db_rows () {
	return $GLOBALS['db']->rows;
}

/**
 * Returns the last inserted value from an insert to a table with
 * an auto-incrementing primary key.
 *
 * @access public
 * @return int
 *
 */
function db_lastid () {
	return $GLOBALS['db']->lastid;
}

/**
 * Returns the error message, if an error occurred during the last
 * query.
 *
 * @access public
 * @return string
 *
 */
function db_error () {
	return $GLOBALS['db']->error;
}

/**
 * Returns the erroneous SQL statement, if an error occurred during
 * the last query.
 *
 * @access public
 * @return string
 *
 */
function db_err_sql () {
	return $GLOBALS['db']->err_sql;
}

/**
 * Returns the specified string as a value quoted and ready for
 * insertion into an SQL statement.  This is called automatically
 * on bind values.
 *
 * @access public
 * @param string
 * @return string
 *
 */
function db_quote ($string) {
	return $GLOBALS['db']->quote ($string);
}

function db_create_sequence ($sqn) {
	return $GLOBALS['db']->createSequence ($sqn);
}

function db_drop_sequence ($sqn) {
	return $GLOBALS['db']->dropSequence ($sqn);
}

function db_next_id ($sqn) {
	return $GLOBALS['db']->nextId ($sqn);
}

function db_get_sequence_name ($sqn) {
	return $GLOBALS['db']->getSequenceName ($sqn);
}

function db_fetch_mode ($mode = false) {
	if (! $mode) {
		return $GLOBALS['db']->getFetchMode ();
	}
	return $GLOBALS['db']->setFetchMode ($mode);
}

function db_pear_emu ($bool = true) {
	$GLOBALS['db']->pearEmu = $bool;
}

?>