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
// This is the base store driver package for saf.Session.  Custom
// drivers are created by sub-classing this package.
//


/**
	 * This is the base store driver package for saf.Session.  Custom
	 * drivers are created by sub-classing this package.
	 * 
	 * New in 1.2:
	 * - Fixed an issue where session_set_cookie_params() was causing duplicate
	 *   cookies to be set, which could cause lockouts upon multiple login/logout
	 *   attempts.
	 * - Silenced the session_destroy() method in close(), which under some
	 *   conditions was calling session_destroy() on an already-destroyed session.
	 * 
	 * New in 1.4:
	 * - Decided against naming the session cookie the same as the SCS session cookie.
	 *   It was causing too many problems.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $s = new SessionStore;
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
	 * @version	1.4, 2003-08-10, $Id: PHP.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

$GLOBALS['loader']->import ('saf.Session.Store');

class SessionStore_PHP extends SessionStore {
	

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
	function SessionStore_PHP () {
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



?>