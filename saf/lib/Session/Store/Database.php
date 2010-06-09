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
// This is the session store driver for storing session variables in the
// database.  This enables session variables to be shared across multiple
// machines for load-balancing purposes.
//


/**
	 * This is the session store driver for storing session variables in the
	 * database.  This enables session variables to be shared across multiple
	 * machines for load-balancing purposes.
	 * 
	 * <code>
	 * Settings in inc/sessions/sitellite/settings.php:
	 * [Store]
	 *
	 * driver = Database
	 *
	 * Implementation code (executed by saf.Session automatically):
	 * <?php
	 * 
	 * $s = new SessionStore_Database;
	 * 
	 * $s->setProperties (array (
	 * 	'foo'	=> 'bar',
	 * ));
	 * 
	 * if ($s->start ($id)) {
	 * 	// connected
	 * 
	 * 	// set a few session variables
	 * 	$s->set ('name', 'Joe');
	 * 	$s->set ('age', '24');
	 * 
	 * 	// get the value of 'name'
	 * 	$name =$s->get ('name');
	 * 
	 * 	// rename 'name'
	 * 	$oldname = $s->set ('name', 'Jack');
	 * 
	 * 	// unset 'age'
	 * 	$s->set ('age', false);
	 * 
	 * 	// remember these for next time
	 * 	$s->save ();
	 * } else {
	 * 	// not connected
	 * 	echo $s->error;
	 * }
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	Session
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @access	public
	 * 
	 */

$GLOBALS['loader']->import ('saf.Session.Store');

class SessionStore_Database extends SessionStore {
	

	/**
	 * The error message if an error occurs.  Defaults to
	 * 'sitellite_session_id'.
	 * 
	 * @access	public
	 * 
	 */
	var $session_name = 'sitellite_session_id';

	/**
	 * The lifetime of the session cookie.
	 * 
	 * @access	public
	 * 
	 */
	var $cookieexpires = false;

	/**
	 * If 1 then the cookie will only be sent over secure connections.
	 * 
	 * @access	public
	 * 
	 */
	var $cookiesecure = 0;

	/**
	 * Domain value of the cookie.
	 * 
	 * @access	public
	 * 
	 */
	var $cookiedomain = '';

	/**
	 * Path value of the cookie.  Defaults to '/'.
	 * 
	 * @access	public
	 * 
	 */
	var $cookiepath = '/';

	

	/**
	 * Constructor method.
	 * 
	 * @access	public
	 * 
	 */
	function SessionStore_Database () {
		$this->cookiedomain = site_domain ();
		$this->start ();
	}

	/**
	 * Starts the session store, initializing any necessary connections,
	 * and retrieving any session values found from a previous web page request.
	 * $id is the session identifier of the current visitor.
	 * 
	 * @access	public
	 * @param	string	$id
	 * @return	boolean
	 * 
	 */
	function start ($id = false) {
		// Note: session_set_cookie_params() simply would not behave correctly
		// and so we have to set these values another way with ini_set().
		ini_set ('session.cookie_lifetime', 0);
		ini_set ('session.cookie_path', $this->cookiepath);
		ini_set ('session.cookie_domain', $this->cookiedomain);
		if ($this->cookiesecure) {
			ini_set ('session.cookie_secure', true);
		}

		//session_name ($this->session_name);
		//session_id ($id);
		@session_start ();

		//global $_SESSION;
		$this->_values =& $_SESSION;
		return true;
	}

	/**
	 * Retrieves a value from the session store.  Returns false if
	 * the value does not exist.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @return	mixed
	 * 
	 */
	function get ($name) {
		//global $_SESSION;
		if (isset ($_SESSION[$name])) {
			return $_SESSION[$name];
		} else {
			return false;
		}
	}

	/**
	 * Sets a value in the session store.  If the value is false,
	 * it will unset it in the store.  If the value is being unset or
	 * set to a new value, then the old value is returned.  If it is a
	 * new value, then the value itself will be returned.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	mixed	$value
	 * @return	mixed
	 * 
	 */
	function set ($name, $value = false) {
		//global $_SESSION;
		if (isset ($_SESSION[$name])) {
			$return = $_SESSION[$name];
			if ($value === false) {
				unset ($_SESSION[$name]);
			} else {
				$_SESSION[$name] = $value;
			}
		} else {
			$return = $value;
			$_SESSION[$name] = $value;
		}
		return $return;
	}

	/**
	 * Tells the session store to save the values within it.
	 * 
	 * @access	public
	 * @return	boolean
	 * 
	 */
	function save () {
		// this is automatic in PHP's built-in session handling
		return true;
	}

	/**
	 * Closes the session with the store, erasing the values
	 * for this session.
	 * 
	 * @access	public
	 * 
	 */
	function close () {
		return @session_destroy ();
	}
	
}

function session_store_database_open ($save_path, $session_name) {
	$GLOBALS['loader']->import ('saf.Database.PropertySet');
	$GLOBALS['session_store_database_ps'] = new PropertySet ('session_store_database', session_id ());
	return true;
}

function session_store_database_close () {
	return true;
}

function session_store_database_read ($key) {
	$val = $GLOBALS['session_store_database_ps']->get ();
	if (! $val) {
		return "";
	}
	return array_shift ($val);
}

function session_store_database_write ($key, $value) {
	if (! empty ($value)) {
		$GLOBALS['session_store_database_ps']->set ('session', $value);
	}
	return true;
}

function session_store_database_destroy ($id) {
	return $GLOBALS['session_store_database_ps']->delete ();
}

function session_store_database_gc ($maxlifetime) {
	return db_execute (
		'delete from sitellite_property_set where collection = "session_store_database" and data_value < ?',
		time () - 3600
	);
}

session_set_save_handler (
	'session_store_database_open',
	'session_store_database_close',
	'session_store_database_read',
	'session_store_database_write',
	'session_store_database_destroy',
	'session_store_database_gc'
);

register_shutdown_function ('session_write_close');

?>