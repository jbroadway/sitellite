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
// A wrapper around the PHP LDAP extension.
//

/**
	 * A wrapper around the PHP LDAP extension.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $ldap = new LDAP ('server', 389, 'username', 'password');
	 * 
	 * if ($ldap->search ($dn, $filter)) {
	 * 	while ($entry = $ldap->fetch ()) {
	 * 		// do something with $entry
	 * 	}
	 * }
	 * 
	 * $ldap->free ();
	 * 
	 * $ldap->close ();
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	Database
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	0.2, 2002-08-27, $Id: LDAP.php,v 1.6 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class LDAP {
	/**
	 * The name of the server to connect to.
	 * 
	 * @access	public
	 * 
	 */
	var $server;

	/**
	 * The port to use to connect to the server.
	 * 
	 * @access	public
	 * 
	 */
	var $port;

	/**
	 * The rdn (username, essentially) to use to bind to
	 * the connection.
	 * 
	 * @access	public
	 * 
	 */
	var $rdn;

	/**
	 * The password to use to bind to the connection.
	 * 
	 * @access	public
	 * 
	 */
	var $password;

	/**
	 * Whether to use TLS for LDAP connections.
	 *
	 * @access	public
	 *
	 */
	var $secure;

	/**
	 * The current connection resource.
	 * 
	 * @access	public
	 * 
	 */
	var $connection;

	/**
	 * The current search resource.
	 * 
	 * @access	public
	 * 
	 */
	var $resource;

	/**
	 * Says whether or not the first search result entry has
	 * been returned.
	 * 
	 * @access	public
	 * 
	 */
	var $firstCalled;

	/**
	 * The errno of the previous error.
	 * 
	 * @access	public
	 * 
	 */
	var $errno;

	/**
	 * The message of the previous error.
	 * 
	 * @access	public
	 * 
	 */
	var $error;

	/**
	 * Constructor method.  Connects to the server and binds
	 * the username and password.
	 * 
	 * @access	public
	 * @param	string	$server
	 * @param	integer	$port
	 * @param	string	$rdn
	 * @param	string	$password
	 * 
	 */
	function LDAP ($server = 'localhost', $port = 389, $rdn = '', $password = '', $secure = false) {
		$this->server = $server;
		$this->port = $port;
		$this->secure = $secure;
		if ($this->connect ($server, $port, $secure)) {
			$this->bind ($rdn, $password);
		}
	}

	/**
	 * Connects to the server.
	 * 
	 * @access	public
	 * @param	string	$server
	 * @param	integer	$port
	 * @return	boolean
	 * 
	 */
	function connect ($server, $port = 389, $secure = false) {
		$connection = @ldap_connect ($server, $port);
		if (! $connection) {
			$this->errno = @ldap_errno ($connection);
			$this->error = @ldap_error ($connection);
			return false;
		} else {
			$this->connection = $connection;
			if ($secure) {
				if (! ldap_set_option ($connection, LDAP_OPT_PROTOCOL_VERSION, 3)) {
					$this->error = 'Failed to set LDAP Protocol version to 3, TLS not supported.';
					return false;
				}
				if (! ldap_start_tls ($connection)) {
					$this->error = 'Failed to start TLS';
					return false;
				}
			}
			return true;
		}
	}

	/**
	 * Verifies the $rdn and $password.
	 * 
	 * @access	public
	 * @param	string	$rdn
	 * @param	string	$password
	 * @return	boolean
	 * 
	 */
	function bind ($rdn = '', $password = '') {
		if (! empty ($rdn)) {
			$this->rdn = $rdn;
		}
		if (! empty ($password)) {
			$this->password = $password;
		}

		$res = @ldap_bind ($this->connection, $rdn, $password);

		if (! $res) {
			$this->errno = @ldap_errno ($this->connection);
			$this->error = @ldap_error ($this->connection);
			// disconnect since we aren't auth'ed
			$this->close();
			return false;
		}
		return true;
	}

	/**
	 * Queries the database.
	 * 
	 * @access	public
	 * @param	string	$dn
	 * @param	string	$filter
	 * @param	array	$attrs
	 * @return	boolean
	 * 
	 */
	function search ($dn, $filter, $attrs = array (), $scope = 'sub') {
		switch ($scope) {
			case 'base':
				$res = @ldap_read ($this->connection, $dn, $filter, $attrs);
				break;
			case 'one':
				$res = @ldap_list ($this->connection, $dn, $filter, $attrs);
				break;
			case 'sub':
			default:
				$res = @ldap_search ($this->connection, $dn, $filter, $attrs);
				break;
		}
		if (! $res) {
			$this->errno = @ldap_errno ($this->connection);
			$this->error = @ldap_error ($this->connection);
			return false;
		}
		$this->resource = $res;
		$this->firstCalled = false;
		return true;
	}

	/**
	 * Returns all of the results of the current search as
	 * one big associative array of objects.
	 * 
	 * @access	public
	 * @return	associative array
	 * 
	 */
	function getEntries () {
		$search = @ldap_get_entries ($this->connection, $this->resource);
		$return = array ();

		foreach ($search as $id => $attrs) {
			if (! is_array ($attrs)) {
				continue;
			}
			$dn = $attrs['dn'];
			$return[$dn] = $this->makeObj ($attrs);
		}

		return $return;
	}

	/**
	 * Fetches the next record in the search results as an
	 * associative array.  Returns false when there are no more results.
	 * 
	 * @access	public
	 * @return	associative array
	 * 
	 */
	function fetch () {
		if (! $this->firstCalled) {
			$this->firstCalled = true;
			$entry = @ldap_first_entry ($this->connection, $this->resource);
			if (! $entry) {
				return false;
			}
			$this->entry = $entry;
			return $this->makeObj (@ldap_get_attributes ($this->connection, $entry));
		} else {
			$entry = @ldap_next_entry ($this->connection, $this->resource);
			if (! $entry) {
				return false;
			}
			$this->entry = $entry;
			return $this->makeObj (@ldap_get_attributes ($this->connection, $entry));
		}
	}

	/**
	 * Convert the returned attributes into the properties of an object.
	 *
	 * @access	public
	 * @param	associative array
	 * @return	object
	 *
	 */
	function makeObj ($attrs) {
		$obj = new StdClass;

   	    for ($i = 0; $i < $attrs['count']; $i++) {
			unset ($attrs[$i]);
		}

		foreach ($attrs as $attr => $vals) {
			if (is_array ($vals)) {
				$count = $vals['count'];
				unset ($vals['count']);
				if ($count == 1) {
					$obj->$attr = $vals[0];
				} else {
					$obj->$attr = $vals;
				}
			} else {
				$obj->$attr = $vals;
			}
		}

		return $obj;
	}

	/**
	 * Clears the current search result resource.
	 * 
	 * @access	public
	 * @return	boolean
	 * 
	 */
	function free () {
		@ldap_free_result ($this->resource);
		$this->resource = false;
		$this->entry = false;
		return true;
	}

	/**
	 * Closes the connection to the server.
	 * 
	 * @access	public
	 * 
	 */
	function close () {
		return @ldap_close ($this->connection);
	}
}



?>