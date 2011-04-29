<?php

loader_import ('saf.Database');

/**
 * Database connection manager.
 *
 * <code>
 * <?php
 *
 * loader_import ('saf.Database.Manager');
 *
 * $dbm = new DBM ();
 *
 * $dbm->add ('conn1', 'MySQL:localhost:DBNAME', 'USER', 'PASS');
 *
 * $db => $dbm->getCurrent ();
 *
 * ? >
 * </code>
 *
 * @package Database
 */
class DBM {
	/**
	 * Connection list.
	 *
	 * @access public
	 */
	var $connections = array ();

	/**
	 * Current connection.
	 *
	 * @access public
	 */
	var $current = false;

	/**
	 * Error message, in case of error.
	 *
	 * @access public
	 */
	var $error = false;

	/**
	 * Constructor method.
	 *
	 * @access public
	 */
	function DBM () {
	}

	/**
	 * Add a new connection.
	 *
	 * @access public
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param boolean
	 * @return boolean
	 */
	function add ($name, $connstr = '::', $user = '', $pass = '', $persistent = 0) {
		$newdb = new Database ($connstr, $user, $pass, $persistent);
		if (! $newdb->connection) {
			$this->error = 'Database connection failed';
			return false;
		}
		if ($newdb->error) {
			$this->error = $newdb->error;
			return false;
		}
		$this->connections[$name] =& $newdb;
		if ($this->connections[$name]->driver == 'MySQL') {
			if (! @mysql_select_db ($this->connections[$name]->name, $this->connections[$name]->connection)) {
				$this->error = 'Database connection established, but permission denied.';
				return false;
			}
		}
		return true;
	}

	/**
	 * Remove a connection.
	 *
	 * @access public
	 * @param string
	 * @param boolean
	 * @return boolean
	 */
	function remove ($name, $disconnect = true) {
		if (! is_object ($this->connections[$name])) {
			$this->error = 'Connection does not exist';
			return false;
		}
		if ($disconnect) {
			$this->connections[$name]->close ();
		}
		unset ($this->connections[$name]);
		return true;
	}

	/**
	 * Set the specified connection to be the currently active one.
	 *
	 * @access public
	 * @param string
	 * @param boolean
	 * @return boolean
	 */
	function setCurrent ($name, $affectGlobalDB = true) {
		if (! is_object ($this->connections[$name])) {
			$this->error = 'Connection does not exist';
			return false;
		}
		$this->current = $name;
		if ($this->connections[$name]->driver == 'MySQL') {
			@mysql_select_db ($this->connections[$name]->name, $this->connections[$name]->connection);
		}
		if ($affectGlobalDB) {
			global $db;
			unset ($db);
			$GLOBALS['db'] =& $this->connections[$name];
		}
		return true;
	}

	/**
	 * Retrieves the currently active database object.
	 *
	 * @access public
	 * @return object
	 */
	function &getCurrent () {
		return $this->connections[$this->current];
	}
}

/**
 * Add a new connection.  Alias of DBM::add().
 *
 * @access public
 * @param string
 * @param string
 * @param string
 * @param string
 * @param boolean
 * @return boolean
 */
function dbm_add ($name, $connstr = '::', $user = '', $pass = '', $persistent = 0) {
	return $GLOBALS['dbm']->add ($name, $connstr, $user, $pass, $persistent);
}

/**
 * Remove a connection.  Alias of DBM::remove()
 *
 * @access public
 * @param string
 * @param boolean
 * @return boolean
 */
function dbm_remove ($name, $disconnect = true) {
	return $GLOBALS['dbm']->remove ($name, $disconnect);
}

/**
 * Set the specified connection to be the currently active one.  Alias of
 * DBM::setCurrent().
 *
 * @access public
 * @param string
 * @param boolean
 * @return boolean
 */
function dbm_set_current ($name, $affectGlobalDB = true) {
	return $GLOBALS['dbm']->setCurrent ($name, $affectGlobalDB);
}

/**
 * Retrieves the currently active database object.  Alias of DBM::getCurrent().
 *
 * @access public
 * @return object
 */
function &dbm_get_current () {
	return $GLOBALS['dbm']->getCurrent ();
}

?>