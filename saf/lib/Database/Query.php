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
// Query is the counterpart class to Database.  It provides the framework
// for querying a database.
//

/**
	 * Query is the counterpart class to Database.  It provides the framework
	 * for querying a database.  As of version 2.0, Query supports automatic result caching
	 * through the use of PHP's dba functions and Sleepycat Software's Berkeley DB,
	 * in order to relieve the underlying database system of potentially a tons of
	 * valuable processing power.  Caching seems to show better results on more complex
	 * queries and queries that return large amounts of data.  Caching to the underlying
	 * filesystem is not supported due to the fact that Berkeley DB already handles
	 * concurrent reads and automatic locking and integrity guarantees on writes.
	 * 2.0 also introduces the fetchXML () method, which returns a row in XML
	 * format (ie. <row><id></id><title></title><etc></etc></row>),
	 * so that it can be easily manipulated with XSLT.
	 * 
	 * Note: the Query API has not changed other than the caching features and is
	 * still compatible with version 1.0.
	 * 
	 * To enable caching, you must define two constants: SITELLITE_QUERY_CACHING, which
	 * contains the duration of a cache in seconds, and SITELLITE_QC_LOCATION, which
	 * contains the absolute path to the folder where you want to store your Berkeley
	 * DB files.
	 * 
	 * New in 2.2:
	 * - Fixed a bug in the bind_values() logic.
	 * 
	 * New in 2.4:
	 * - Removed a bunch of duplicate code between fetch() and fetchXML().
	 * - Added a fetchArray() method.
	 * 
	 * New in 2.6:
	 * - Rewrote bind_values() to better handle quoted and escaped content, to only quote
	 *   binded values where appropriate (using the is_numeric() function), and to handle
	 *   both the ?? and ? syntax for denoting bind values.  It should also be a bit
	 *   faster now.
	 * - Made some improvements to the toXML() method, including an htmlentities_compat()
	 *   call and the ability to name the root node something other than 'row'.
	 * 
	 * New in 2.8:
	 * - Fixed a bug in bind_values() where a string like '0023' would not be quoted, and
	 *   so would end up stored simply as '23'.
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
	 * ? >
	 * </code>
	 * 
	 * @package	Database
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	2.8, 2003-05-13, $Id: Query.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class Query {
	/**
	 * Contains the SQL query to be executed.
	 * 
	 * @access	public
	 * 
	 */
	var $sql = '';

	/**
	 * Contains the result identifier for the current execution.
	 * 
	 * @access	public
	 * 
	 */
	var $result = '';

	/**
	 * Currently unused.
	 * 
	 * @access	public
	 * 
	 */
	var $field = '';

	/**
	 * Contains a local copy of the database connection resource.
	 * 
	 * @access	public
	 * 
	 */
	var $connection = '';

	/**
	 * @access	private
	 */
	var $_fetchModeFunctions = array ();

	/**
	 * Constructor method.
	 * 
	 * $sql is the SQL query you wish to execute with this object.
	 * 
	 * @access	public
	 * @param	string	$sql
	 * 
	 */
	function Query ($sql = '', &$connection, $cache = 0) {
		$this->sql = $sql;
		$this->connection =& $connection;
		$this->cache = $cache;
		$this->fields = 0;
	}

	/**
	 * Replaces any occurrences of ?? or ? in the $sql property with the
	 * proper value from $values.  Called automatically just prior to executing
	 * the current query.  Note: Quoted or escaped occurrences of ?? and ? are
	 * ignored.
	 * 
	 * $values is an array of values to substitute.  If $values[0] is an array,
	 * it will be used as the array of substitutes
	 * 
	 * Returns the new SQL query.  Note: does not modify the actual $sql property.
	 * Instead it sets a $tmp_sql property.
	 * 
	 * @access	private
	 * @param	array	$values
	 * @return	string
	 * 
	 */
	function bind_values ($values) {
		// accepts bind values using the ?? or ? notation

		// echo "<pre>$working_sql</pre>";
		$working_sql = $this->sql;

		if (isset ($values[0]) && is_array ($values[0])) {
			$values = $values[0];
		}

		$elements = preg_split ('/(\?\?|\?|\'|"|\\\)/', $this->sql, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
		$skipSingle = false;
		$skipDouble = false;
		$skipOnce = false;
		$working_sql = '';
		$count = 0;

		foreach ($elements as $element) {
			if (($element == '??' || $element == '?') && ! $skipSingle && ! $skipDouble && ! $skipOnce) {

				$working_sql .= db_quote ($values[$count]);
				$count++;

			} elseif ($element == '"' && ! $skipSingle && ! $skipOnce) {
				if ($skipDouble) {
					$skipDouble = false;
				} else {
					$skipDouble = true;
				}
				$working_sql .= $element;
			} elseif ($element == '\'' && ! $skipDouble && ! $skipOnce) {
				if ($skipSingle) {
					$skipSingle = false;
				} else {
					$skipSingle = true;
				}
				$working_sql .= $element;
			} elseif ($element == '\\' && ! $skipOnce) {
				$skipOnce = true;
				$working_sql .= $element;
				continue;
			} else {
				$working_sql .= $element;
			}
			if ($skipOnce) {
				$skipOnce = false;
			}
		}

		$this->tmp_sql = $working_sql;
		return $working_sql;

	}

	/**
	 * Executes the current SQL query.
	 * 
	 * $values is an array of values to substitute.
	 * 
	 * Returns the new SQL query.  Note: does not modify the actual $sql property.
	 * 
	 * @access	public
	 * @param	array	$values
	 * @return	resource
	 * 
	 */
	function execute () {

		// caching hooks
		$this->cache_action = 0; // don't
		if (($this->cache) && (SITELLITE_QUERY_CACHING)) {
			$GLOBALS['loader']->import ('saf.Database.BDB');

			// file access mode 'c' doesn't seem to work as documented on Win32, so we'll just avoid the issue entirely
			if (@file_exists (SITELLITE_QC_LOCATION . '/query_store.db')) {
				$this->bdb_expiry_table = new BDB (SITELLITE_QC_LOCATION . '/query_store.db', 'w', 'db3', 0);
			} else {
				$this->bdb_expiry_table = new BDB (SITELLITE_QC_LOCATION . '/query_store.db', 'c', 'db3', 0);
			}

			if ($this->bdb_expiry_table->exists ($this->tmp_sql)) {
				$dba_row = $this->bdb_expiry_table->fetch ($this->tmp_sql);
				list ($expiry, $storefile, $this->rows, $fields) = split (':_:_:', $dba_row);
				$this->fields = unserialize ($fields);
				//echo $expiry . ' : ' . $storefile . "\n";
				if ($expiry > time ()) {
					// fetch from $storefile
					$this->bdb_store_file = new BDB ($storefile, 'r', 'db3', 0); // r
					$this->cache_action = 1; // read

					// set the environment up to simply read from bdb store
					$this->bdb_key = $this->bdb_store_file->firstkey ();
					//print_r ($this);
					$this->result = true;
					//echo '<strong>action: ' . $this->cache_action . '</strong>' . "\n";
					return true;
				} else {
					// fetch from sql database, and re-cache results and update expiry time in bdb_expiry_table
					if (@file_exists ($storefile)) {
						$this->bdb_store_file = new BDB ($storefile, 'w', 'db3', 0);
					} else {
						$this->bdb_store_file = new BDB ($storefile, 'c', 'db3', 0);
					}
					$this->bdb_expiry_table_new_store_file = $storefile;
					$this->cache_action = 2; // update

					// start the cache over by deleting them all
					$this->bdb_store_file->delete_all ();
					$this->bdb_store_file_counter = 0;
				}
			} else {
				// fetch from sql database, and cache results, and add to bdb_expiry_table
				$this->bdb_store_file = new BDB (SITELLITE_QC_LOCATION . '/' . microtime () . md5 (mt_rand (0, mt_getrandmax ())) . $GLOBALS['REMOTE_ADDR'] . '.db', 'c', 'db3', 0);
				$this->bdb_expiry_table_new_store_file = $this->bdb_store_file->path;
				$this->cache_action = 3; // initial
				$this->bdb_store_file_counter = 0;
			}
		}
		//echo '<strong>action: ' . $this->cache_action . '</strong>' . "\n";
		return false;
	}			// executes the sql query

	/**
	 * Returns the name of the specified column in the table currently being queried.
	 * 
	 * $num is the column number.  Note: Some database systems begin with
	 * column 0, while others with 1.
	 * 
	 * @access	public
	 * @param	integer	$num
	 * @return	string
	 * 
	 */
	function field ($num = 0) {}	// returns a column name when given a number

	/**
	 * Returns the number of rows affected or found by the current query.
	 * 
	 * @access	public
	 * @return	integer
	 * 
	 */
	function rows () {}				// returns the number of rows affected or found

	/**
	 * Returns the row ID generated by the database during the previous
	 * SQL insert query.
	 * 
	 * @access	public
	 * @return	integer
	 * 
	 */
	function lastid () {}			// returns the last autonum from an insert

	/**
	 * Returns the next row of data from the current query, always in the
	 * form of an object, with each column as its properties.
	 * 
	 * @access	public
	 * @return	object
	 * 
	 */
	function fetch () {
		switch ($this->cache_action) {
			case 1:
				//echo '<h1>key: ' . $this->bdb_key . '</h1>' . "\n";
				if ($this->bdb_key != false) {
					$serialized = $this->bdb_store_file->fetch ($this->bdb_key);
					$next_val = unserialize ($serialized);
					if (empty ($next_val)) {
						$this->bdb_key = $this->bdb_store_file->nextkey ();
						return $this->fetch ();
					}
					$this->bdb_key = $this->bdb_store_file->nextkey ();
					return $next_val;
				} else {
					return false;
				}
			case 2:
				if (is_object ($this->_row)) {
					$this->bdb_store_file_counter++;
					$this->bdb_store_file->insert ($this->bdb_store_file_counter, serialize ($this->_row));
					if (! is_array ($this->fields)) {
						$this->fields = array_keys (get_object_vars ($this->_row));
					}
				}
				return $this->_row;
			case 3:
				if (is_object ($this->_row)) {
					$this->bdb_store_file_counter++;
					$this->bdb_store_file->insert ($this->bdb_store_file_counter, serialize ($this->_row));
					if (! is_array ($this->fields)) {
						$this->fields = array_keys (get_object_vars ($this->_row));
					}
				}
				return $this->_row;
		}
	}				// returns the next row

	/**
	 * Returns the given result object represented as XML.
	 * $root_node allows you to specify the name of the root node of
	 * the XML structure.
	 * 
	 * @access	public
	 * @param	object	$data_obj
	 * @param	string	$root_node
	 * @return	string
	 * 
	 */
	function toXML ($data_obj, $root_node = 'row') {
		if (is_object ($data_obj)) {
			$xml = "<$root_node>\n";
			foreach (get_object_vars ($data_obj) as $key => $value) {
				$value = htmlentities_compat ($value);
				$xml .= "\t<$key>$value</$key>\n";
			}
			return $xml . "</$root_node>\n";
		} else {
			return false;
		}
	}

	/**
	 * Returns the next row of data from the current query in XML format,
	 * so that it can easily be repurposed with XSL stylesheets or shared with external
	 * sources.
	 * 
	 * @access	public
	 * @return	string
	 * 
	 */
	function fetchXML () {
		$row = $this->fetch ();
		if ($row) {
			$this->_xml = $this->toXML ($row);
			return $this->_xml;
		} else {
			return $row;
		}
	}

	/**
	 * Returns the next row of data from the current query as an associative array,
	 * because sometimes an object isn't appropriate.
	 * 
	 * @access	public
	 * @return	associate array
	 * 
	 */
	function fetchArray () {
		$row = $this->fetch ();
		if ($row) {
			return get_object_vars ($row);
		} else {
			return $row;
		}
	}

	/**
	 * Tells the database system to let go of its data from the previous
	 * query.
	 * 
	 * @access	public
	 * 
	 */
	function free () {
		switch ($this->cache_action) {
			case 1:
				$this->bdb_store_file->close ();
				$this->bdb_expiry_table->close ();
				break;
			case 2:
				$this->bdb_store_file->optimize ();
				$this->bdb_store_file->close ();
				$this->bdb_expiry_table->replace ($this->tmp_sql,
					time () + SITELLITE_QUERY_CACHING . ':_:_:' .
					$this->bdb_expiry_table_new_store_file . ':_:_:' .
					$this->rows () . ':_:_:' .
					serialize ($this->fields)
				);
				$this->bdb_expiry_table->optimize ();
				$this->bdb_expiry_table->close ();
				break;
			case 3:
				$this->bdb_store_file->optimize ();
				$this->bdb_store_file->close ();
				$this->bdb_expiry_table->insert ($this->tmp_sql,
					time () + SITELLITE_QUERY_CACHING . ':_:_:' .
					$this->bdb_expiry_table_new_store_file . ':_:_:' .
					$this->rows () . ':_:_:' .
					serialize ($this->fields)
				);
				$this->bdb_expiry_table->optimize ();
				$this->bdb_expiry_table->close ();
				break;
		}
	}				// frees the query

	/**
	 * Returns the database error number in case of error.  This number
	 * will be database-specific, and in some cases may even be a string.  Please
	 * refer to your database documentation for a list of values and their
	 * meanings.
	 * 
	 * @access	public
	 * @return	integer
	 * 
	 */
	function errno () {}

	/**
	 * Returns the database error message in case of error.  This message
	 * will be database-specific.  Please refer to your database documentation for
	 * a list of messages and their meanings.
	 * 
	 * @access	public
	 * @return	string
	 * 
	 */
	function error () {}
}



?>