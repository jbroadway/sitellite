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
// BDB is a very minimal wrapper around the Berkeley Database functions
// in PHP.
//

/**
	 * BDB is a very minimal wrapper around the Berkeley Database functions
	 * in PHP.  Its purpose is simply to create an object oriented interface
	 * to those functions.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $bdb = new BDB ('/tmp/object_store.db', 'w', 'db3', 0);
	 * 
	 * if ($bdb->exists ($cgi->key)) {
	 * 	echo $bdb->fetch ($cgi->key);
	 * }
	 * 
	 * $bdb->close ();
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	Database
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.0, 2001-10-22, $Id: BDB.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class BDB {
	/**
	 * This is a Berkeley Database connection resource returned
	 * by the dba_open () or dba_popen () function.
	 * 
	 * @access	public
	 * 
	 */
	var $connection;

	/**
	 * Path to the Berkeley Database.
	 * 
	 * @access	public
	 * 
	 */
	var $path;

	/**
	 * Mode to use when opening the database (corresponds to
	 * the modes available to the dba_open () function.
	 * 
	 * @access	public
	 * 
	 */
	var $mode;

	/**
	 * The database implementation used (db2, db3, gdbm, etc.).
	 * 
	 * @access	public
	 * 
	 */
	var $handler;

	/**
	 * Whether or not to use persistence when connecting to the database.
	 * 
	 * @access	public
	 * 
	 */
	var $persistent;

	/**
	 * Constructor Method.  Establishes a connection to the database,
	 * which can be verified by an if ($dbd->connection) {.
	 * 
	 * @access	public
	 * @param	string	$path
	 * @param	string	$mode
	 * @param	string	$handler
	 * @param	boolean	$persistent
	 * 
	 */
	function BDB ($path, $mode = 'r', $handler = 'db3', $persistent = 0) {
		$this->path = $path;
		$this->mode = $mode;
		$this->handler = $handler;
		$this->persistent = $persistent;
		if ($persistent) {
			$this->connection = @dba_popen ($path, $mode, $handler);
		} else {
			$this->connection = @dba_open ($path, $mode, $handler);
		}
	}

	/**
	 * Disconnects from the database.
	 * 
	 * @access	public
	 * @param	string	$path
	 * 
	 */
	function close () {
		@dba_close ($this->connection);
	}

	/**
	 * Deletes a record from the database.
	 * 
	 * @access	public
	 * @param	string	$key
	 * @return	boolean
	 * 
	 */
	function delete ($key) {
		return @dba_delete ($key, $this->connection);
	}

	function delete_all () {
		$key = $this->firstkey ();
		$list = array ();
		while ($key != false) {
			$list[] = $key;
			$key = $this->nextkey ();
		}
		for ($i = 0; $i < count ($list); $i++) {
			$this->delete ($list[$i]);
		}
	}

	/**
	 * Checks if a key/value pair exists in the database.
	 * 
	 * @access	public
	 * @param	string	$key
	 * @return	boolean
	 * 
	 */
	function exists ($key) {
		return @dba_exists ($key, $this->connection);
	}

	/**
	 * Retrieves a value corresponding to the provided key, from
	 * the database.  Returns false on error.
	 * 
	 * @access	public
	 * @param	string	$key
	 * @return	string
	 * 
	 */
	function fetch ($key) {
		return @dba_fetch ($key, $this->connection);
	}

	/**
	 * Retrieves the first key in the database.
	 * 
	 * @access	public
	 * @return	string
	 * 
	 */
	function firstkey () {
		return @dba_firstkey ($this->connection);
	}

	/**
	 * Insert a new key/value pair into the database.
	 * 
	 * @access	public
	 * @param	string	$key
	 * @param	string	$value
	 * @return	boolean
	 * 
	 */
	function insert ($key, $value) {
		return @dba_insert ($key, $value, $this->connection);
	}

	/**
	 * Retrieves the next key in the database.  Used with firstkey to
	 * loop through records.
	 * 
	 * @access	public
	 * @return	string
	 * 
	 */
	function nextkey () {
		return @dba_nextkey ($this->connection);
	}

	/**
	 * Optimizes the database.
	 * 
	 * @access	public
	 * @return	boolean
	 * 
	 */
	function optimize () {
		return @dba_optimize ($this->connection);
	}

	/**
	 * Replaces the value of an existing pair with the new value
	 * provided.
	 * 
	 * @access	public
	 * @param	string	$key
	 * @param	string	$value
	 * @return	boolean
	 * 
	 */
	function replace ($key, $value) {
		return @dba_replace ($key, $value, $this->connection);
	}

	/**
	 * Synchronizes the database.  This usually writes the database
	 * to disk.
	 * 
	 * @access	public
	 * @return	boolean
	 * 
	 */
	function sync () {
		return @dba_sync ($this->connection);
	}
}



?>