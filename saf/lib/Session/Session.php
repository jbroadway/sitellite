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
// Session is a class that manages visitor sessions.  Note: Session does
// not handle anonymous sessions, but rather serves as a validation process
// for login-only site components.
//


loader_import ('saf.Session.Acl');

/**
	 * Session is a class that manages visitor sessions.
	 * Note: Session does not handle anonymous sessions, but
	 * rather serves as a validation process for login-only site components.
	 * Session is relatively easy to integrate into web sites, but
	 * relies on a number of other Sitellite classes, such as
	 * Cookie, and CGI.  It is also easy to extend in functionality, as we
	 * did with the SitelliteSession class by adding Sitellite CMS-specific
	 * permission functions.
	 * 
	 * Note: mt_srand() must also be called prior to creating a Session object,
	 * because the random number generator must be seeded.
	 * 
	 * New in 2.0:
	 * - This is a complete rewrite that breaks backward compatibility with 1.x.
	 *   It offers an abstracted Session class that uses drivers for both the
	 *   validation source and the visitor protocol handler.  Initially, there
	 *   is only one source driver, which is a database driver, and there are
	 *   two handler drivers, one for storing session ids using cookies and the
	 *   other challenges the client with an HTTP Basic authentication request.
	 * 
	 * New in 2.2:
	 * - Added a session store driver system, which allows variables to be
	 *   assigned to a session.  This required a change in the parameter order
	 *   of the constructor method (sorry!), so you'll have to make a tiny change
	 *   there.  Also required the addition of two new properties and four new
	 *   methods:
	 *   - $store (store driver object)
	 *   - $autoSave (whether to call save() after each call to set())
	 *   - setStoreProperties ()
	 *   - get ()
	 *   - set ()
	 *   - append()
	 *   - save ()
	 * 
	 * New in 2.4:
	 * - Added session_get(), session_set(), session_append(), and session_save()
	 *   global functions that simply call the equivalent methods on a global
	 *   $session object.
	 * 
	 * New in 3.0:
	 * - Added the use of a new SessionAcl package which provides access control
	 *   functionality to this package, meant to serve as a replacement for the
	 *   SitelliteSession overriding package.  SessionAcl adds finer granularity
	 *   in defining and controlling users through the separation of read and
	 *   write access, and also boasts a new INI-based definition format in
	 *   place of the MySQL sitellite_role, sitellite_team, sitellite_access,
	 *   and sitellite_status tables.  This will not only improve flexibility,
	 *   but it should also make this package significantly faster too.
	 * - Broke backward compatibility in the parameters of the allowed()
	 *   method.  As compared to the SitelliteSession package, this also
	 *   eliminates the getPermissions() method, which is replaced by internal
	 *   handling of this functionality.
	 * 
	 * Historical
	 * ----------
	 * 
	 * New in 1.2:
	 * - Updated to use $site->webpath instead of $site->path, which is
	 *   deprecated in saf.Site.
	 * 
	 * New in 1.4:
	 * - Added the ability to make accounts pending, so that they must be verified
	 *   via email.  This is done by storing a 24 character random string,
	 *   preceded by 'PENDING:' in the $sidcol column.  This key string can be
	 *   generated with the new makePendingKey() method.
	 * 
	 * New in 1.6:
	 * - Traded $tpl->fill() calls for sprintf(), which should increase
	 *   performance a little.
	 * - Moved the encryption of the password field into the PHP level, so as to
	 *   eliminate the reliance on MySQL-specific functions (ie. password()).
	 *   Unfortunately, this breaks backward compatibility and all passwords will
	 *   need to be reset, since both MySQL's password() and PHP's crypt() are
	 *   one-way encryption methods.
	 * 
	 * New in 1.8:
	 * - Added the following methods as aliases of properties or methods of a
	 *   global $session object: session_username(), session_valid(),
	 *   session_get(), session_set(), session_append(), and session_save().
	 * 
	 * <code>
	 * <?php
	 * 
	 * // seed the "better" random number generator
	 * mt_srand ((double) microtime () * 1000000);
	 * 
	 * $sessionCookieName = 'cisforcookie';
	 * $sessionHandler = 'Cookie';
	 * $sessionSource = 'Database';
	 * 
	 * list ($user, $pass, $id) = Session::gatherParameters ($sessionHandler, $sessionCookieName);
	 * 
	 * $session = new Session ($sessionHandler, $sessionSource, $user, $pass, $id);
	 * 
	 * // time out in 1 hour
	 * $session->setTimeout (3600);
	 * 
	 * $session->setSourceProperties (array (
	 * 	'database' => 'db',
	 * ));
	 * 
	 * $session->setHandlerProperties (array (
	 * 	'cookiename' => 'cisforcookie',
	 * 	'cookiedomain' => 'www.yourwebsite.com',
	 * 	'cookiepath' => '/',
	 * ));
	 * 
	 * $session->start ();
	 * 
	 * if ($session->error) {
	 * 	// something is not right
	 * 	echo $session->error;
	 * 
	 * } elseif ($session->valid) {
	 * 	// valid session
	 * 	// put all of your private stuff here
	 * 
	 * } else {
	 * 	// invalid or new session
	 * 	$session->sendAuthRequest ();
	 * 
	 * }
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	Session
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	2.6, 2003-04-23, $Id: Session.php,v 1.6 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class Session {
	

	/**
	 * The session id value.
	 * 
	 * @access	public
	 * 
	 */
	var $id; // will contain the session identifier

	/**
	 * Says whether this session is valid.
	 * 
	 * @access	public
	 * 
	 */
	var $valid = false; // boolean

	/**
	 * Contains the error message if any error occurs within
	 * this class, the handler or the source drivers, or false if no
	 * error has occurred.
	 * 
	 * @access	public
	 * 
	 */
	var $error = false;

	/**
	 * Contains the username of the user.
	 * 
	 * @access	public
	 * 
	 */
	var $username;

	/**
	 * Contains the password of the user.
	 * 
	 * @access	public
	 * 
	 */
	var $password;

	/**
	 * Specifies whether or not to use a session id.  Some handlers or
	 * sources may not support session ids (ie. the Basic handler), and so they
	 * may be disabled.
	 * 
	 * @access	public
	 * 
	 */
	var $useID = true; // yes, use a session identifier after initial login

	/**
	 * Specifies a length in seconds that the session may be inactive
	 * for before automatically logging the user out.  This is also optional,
	 * as some handlers or sources may not support it.
	 * 
	 * @access	public
	 * 
	 */
	var $timeout = 3600; // an hour, if 0 then it never times out

	/**
	 * Specifies whether calls to set() should also call save()
	 * automatically.  Defaults to true, since for most intents and purposes
	 * this is a nice way of not having to think about it.
	 * 
	 * @access	public
	 * 
	 */
	var $autoSave = true;

	/**
	 * The handler driver object.
	 * 
	 * @access	public
	 * 
	 */
	var $handler;

	/**
	 * The source driver object.
	 * 
	 * @access	public
	 * 
	 */
	var $source;

	/**
	 * The store driver object.
	 * 
	 * @access	public
	 * 
	 */
	var $store;

	

	/**
	 * Constructor method.
	 * 
	 * @access	public
	 * @param	string	$handler
	 * @param	string	$source
	 * @param	string	$store
	 * @param	string	$username
	 * @param	string	$password
	 * @param	string	$id
	 * 
	 */
	function Session ($handler, $sources, $store, $username, $password, $id = '') {
		$this->username = $username;
		$this->password = $password;
		$this->id = $id;

		global $loader;
//
		if (! $loader->import ('saf.Session.Handler.' . $handler)) {
			$this->error = 'Failed to create handler.  Import failed on saf.Session.Handler.' . $handler;
			return false;
		}
//
		$handler = 'SessionHandler_' . $handler;
		$this->handler = new $handler;
//
		foreach ($sources as $source) {
			if (! $loader->import ('saf.Session.Source.' . $source)) {
				$this->error = 'Failed to create source.  Import failed on saf.Session.Source.' . $source;
				return false;
			}
//
			$s = 'SessionSource_' . $source;
			$this->sources[$source] = new $s;
		}
//
		if (! $loader->import ('saf.Session.Store.' . $store)) {
			$this->error = 'Failed to create store.  Import failed on saf.Session.Store.' . $store;
			return false;
		}
//
		$store = 'SessionStore_' . $store;
		$this->store = new $store;

		$this->acl = new SessionAcl ($this->username);
	}

	/**
	 * Initializes the session objects, which is necessary to do outside of
	 * the constructor because a constructor can't properly reference $this
	 * inside of itself it seems.
	 *
	 * @access	public
	 * @param	string
	 */
	function init ($path = 'inc/conf/auth') {
		$this->store->sessObj =& $this;
		foreach (array_keys ($this->sources) as $k) {
			$this->sources[$k]->sessObj =& $this;
		}
		$this->handler->sessObj =& $this;

		$this->acl->init ($path);

		foreach (array_keys ($this->sources) as $source) {
			if (! isset ($this->_wsource) && ! $this->sources[$source]->readOnly) {
				$this->_wsource = $source;
				$this->_source = $source;
				$this->source =& $this->sources[$source];
			}
    }
  }

	/**
	 * Sets the value of the $timeout property.
	 * 
	 * @access	public
	 * @param	string	$timeout
	 * 
	 */
	function setTimeout ($timeout) {
		$this->timeout = (int) $timeout;
	}

	/**
	 * Sets any custom properties of the source driver.
	 * 
	 * @access	public
	 * @param	associative array	$properties
	 * 
	 */
	function setSourceProperties ($source, $properties) {
		$this->sources[$source]->setProperties ($properties);
	}

	/**
	 * Sets any custom properties of the handler driver.
	 * 
	 * @access	public
	 * @param	associative array	$properties
	 * 
	 */
	function setHandlerProperties ($properties) {
		$this->handler->setProperties ($properties);
	}

	/**
	 * Sets any custom properties of the store driver.
	 * 
	 * @access	public
	 * @param	associative array	$properties
	 * 
	 */
	function setStoreProperties ($properties) {
		$this->store->setProperties ($properties);
	}

	/**
	 * Check the autorization of a user but do not log them in.  Useful for
	 * web services or external applications requiring authentication but
	 * which should not log the person out on the website through their use.
	 *
	 * @access	public
	 * @return	boolean
	 *
	 */
	function authorize ($user, $pass, $id = false) {
		$useID = $this->useID;
		if ($id == false) {
			$this->useID = false;
		}
		foreach (array_keys ($this->sources) as $source) {
			if ($this->sources[$source]->authorize ($user, $pass, $id)) {
				$this->useID = $useID;
				return true;
			}
		}
		$this->useID = $useID;
		return false;
	}

	/**
	 * Starts the session logic.  This is typically the stage where
	 * the username/password or session id will be verified, so after this
	 * stage you will be able to check the $valid property to see if the
	 * user is valid.
	 * 
	 * @access	public
	 * @return	boolean
	 * 
	 */
	function start () {
		foreach (array_keys ($this->sources) as $source) {
			if (! empty ($this->username) && ! empty ($this->password) && $this->id = $this->sources[$source]->authorize ($this->username, $this->password, $this->id)) {
				if ($this->handler->start ($this->id, true)) {
					$this->valid = true;

					$this->_source = $source;

					// acl initializations
					$this->acl->user['role'] = $this->sources[$source]->getRole ();
					$this->acl->user['team'] = $this->sources[$source]->getTeam ();
					$this->acl->user['teams'] = $this->sources[$source]->getTeams ();
					$this->acl->initPrefs ();
					if (! $this->acl->verify ($this->sources[$source]->isDisabled ())) {
						$this->valid = false;
						$this->error = 'Account currently disabled';
						return false;
					}
					if ($this->acl->isAdmin ()) {
						$this->admin = true;
					}

					return true;

					/*if ($this->store->start ($this->id)) {
						return true;
					} else {
						$this->error = $this->store->error;
						return false;
					}*/
				} else {
					$this->error = $this->handler->error;
					return false;
				}
			} elseif (! empty ($this->id) && $this->username = $this->sources[$source]->authorize ($this->username, $this->password, $this->id)) {
				if ($this->handler->start ($this->id, true)) {
					$this->valid = true;

					$this->_source = $source;

					// acl initializations
					$this->acl->user['name'] = $this->username;
					$this->acl->user['role'] = $this->sources[$source]->getRole ();
					$this->acl->user['team'] = $this->sources[$source]->getTeam ();
					$this->acl->user['teams'] = $this->sources[$source]->getTeams ();
					$this->acl->initPrefs ();
					if (! $this->acl->verify ($this->sources[$source]->isDisabled ())) {
						$this->valid = false;
						$this->error = 'Account currently disabled';
						return false;
					}
					if ($this->acl->isAdmin ()) {
						$this->admin = true;
					}

					return true;

					/*if ($this->store->start ($this->id)) {
						return true;
					} else {
						$this->error = $this->store->error;
						return false;
					}*/
				} else {
					$this->error = $this->handler->error;
					return false;
				}
			} elseif (! empty ($this->sources[$source]->error)) {
				$this->error = $this->sources[$source]->error;
				return false;
			}
		}
		return false;
	}

	/**
	 * This method issues a request for authorization to the visitor.
	 * This request may be an HTTP WWW-Authenticate header, an HTML sign in
	 * form, a SOAP message (providing you have a SOAP handler driver), or
	 * just about any conceivable way of making this request.
	 * 
	 * @access	public
	 * @return	boolean
	 * 
	 */
	function sendAuthRequest () {
		if ($this->handler->sendAuthRequest ()) {
			return true;
		} else {
			$this->error = $this->handler->error;
			return false;
		}
	}

	/**
	 * Retrieves the appropriate username, password, and session id
	 * values from anywhere in the script, which are gathered through the
	 * gatherParameters() method of the specified $handler, because the
	 * handlers often know more about the environment than this class because
	 * they must interact with the visitor.
	 * 
	 * @access	public
	 * @param	string	$handler
	 * @param	string	$sessionidname
	 * @return	array
	 * 
	 */
	function gatherParameters ($handler, $sessionidname) {
		global $loader;
		if (! $loader->import ('saf.Session.Handler.' . $handler)) {
			return array (false, false, false);
		} else {
			$h = 'SessionHandler_' . $handler;
			$h = new $h;
			return $h->gatherParameters ($sessionidname);
		}
	}

	/**
	 * Creates a 32 character string of the form 'PENDING:' plus a 24
	 * character long random string.  Used for creating pending accounts.
	 * 
	 * @access	public
	 * @return	string
	 * 
	 */
	function makePendingKey () {
		$key = 'PENDING:';
		$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		for ($i = 0; $i < 24; $i++) {
			$key .= $chars[mt_rand (0, strlen ($chars) - 1)];
		}
		return $key;
	}

	/**
	 * Closes the session handler, source, and the store.  Call this to
	 * log a user out and terminate their session.  Please note: when a session
	 * is terminated, all data stored in it that is not written to a permanent
	 * storage location is lost.
	 * 
	 * @access	public
	 * 
	 */
	function close () {
		if (! is_object ($this->sources[$this->_source])) {
			return;
		}
		$this->sources[$this->_source]->close ();
		$this->handler->close ();
		$this->store->close ();
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
		return $this->store->get ($name);
	}

	/**
	 * Sets a value in the session store.  If the value is false,
	 * it will unset it in the store.  If the value is being unset or
	 * set to a new value, then the old value is returned.  If it is a
	 * new value, then the value itself will be returned.  If $autoSave
	 * is on, check $error if you want to make sure it worked.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	mixed	$value
	 * @return	mixed
	 * 
	 */
	function set ($name, $value = false) {
		$res = $this->store->set ($name, $value);
		if ($this->autoSave) {
			$this->save ();
		}
		return $res;
	}

	/**
	 * Sets an array value in the session store.  If the array
	 * is empty, it will create a new array.  If the value is false, it
	 * will empty the array.  If $autoSave is on, check $error if you
	 * want to make sure it worked.  Returns the array prior to making
	 * the change.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	mixed	$value
	 * @return	array
	 * 
	 */
	function append ($name, $value = false) {
		$old = $this->store->get ($name);
		if ($value === false) {
			$new = array ();
			$this->store->set ($name, $new);
		} else {
			if (is_array ($old)) {
				$new = $old;
				$new[] = $value;
				$this->store->set ($name, $new);
			} else {
				$new = array ($value);
				$this->store->set ($name, $new);
			}
		}
		if ($this->autoSave) {
			$this->save ();
		}
		return $old;
	}

	/**
	 * Tells the session store to save the values within it.
	 * 
	 * @access	public
	 * @return	boolean
	 * 
	 */
	function save () {
		$res = $this->store->save ();
		if (! $res) {
			$this->error = $this->store->error;
		}
		return $res;
	}

	/**
	 * Specifies whether the user is allowed to access the requested
	 * resource.  $resource may be a string, or an object or associative array
	 * with the properties name, sitellite_acces, and sitellite_status.
	 * Valid $access values are r, w, and rw (read, write, and read/write).
	 * Valid $type values are resource, access, and status.
	 * 
	 * @access	public
	 * @param	string	$resource
	 * @param	string	$access
	 * @param	string	$type
	 * @return	boolean
	 * 
	 */
	function allowed ($resource = 'documents', $access = 'rw', $type = 'resource') {
		return $this->acl->allowed ($resource, $access, $type);
	}

	/**
	 * Returns a piece of SQL that can be slipped into the WHERE clause of
	 * a query to check for proper permissions.
	 * 
	 * @access	public
	 * @return	string
	 * 
	 */
	function allowedSql () {
		return $this->acl->allowedSql ();
	}

	/**
	 * Creates a 32 character string of the form 'RECOVER:' plus a 24
	 * character long random string.  Used for recovering passwords.
	 * 
	 * @access	public
	 * @return	string
	 * 
	 */
	function makeRecoverKey () {
		$key = 'RECOVER:';
		$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		for ($i = 0; $i < 24; $i++) {
			$key .= $chars[mt_rand (0, strlen ($chars) - 1)];
		}
		return $key;
	}

	/**
	 * Checks the validity of the specified recovery key.
	 *
	 * @param string
	 * @param string
	 * @return boolean
	 */
	function isValidKey ($user, $key) {
		return $this->sources[$this->_source]->isValidKey ($user, $key);
	}

	/**
	 * Updates the user's data in the data source.  May update another user
	 * than the current one by specifying a username as the second parameter.
	 *
	 * @param array hash of new values
	 * @param string
	 * @return boolean
	 */
	function update ($data = array (), $user = false) {
		if (! $user) {
			$user = $this->username;
		}
		if (! $user) {
			return false;
		}

		$res = $this->sources[$this->_source]->update ($data, $user);
		if (! $res) {
			$this->error = $this->sources[$this->_source]->error;
		}
		return $res;
	}

	/**
	 * Finds a user by their email address.
	 *
	 * @param string
	 * @param object
	 */
	function getUser ($username = false) {
		if (! $username) {
			if (isset ($this->sources[$this->_source]->resultObj)) {
				return $this->sources[$this->_source]->resultObj;
			}
			return $this->sources[$this->_source]->getUser ($this->username);
		} else {
			return $this->sources[$this->_source]->getUser ($username);
		}
	}

	/**
	 * Finds a user by their email address.
	 *
	 * @param string
	 * @param object
	 */
	function getUserByEmail ($email) {
		return $this->sources[$this->_source]->getUserByEmail ($email);
	}

	/**
	 * Retrieves a copy of the session manager object.
	 *
	 * @return object reference
	 */
	function &getManager () {
		if (isset ($this->manager)) {
			return $this->manager;
		}
		loader_import ('saf.Session.Manager');
		$this->manager = new SessionManager ();
		return $this->manager;
	}
}

/**
 * Retrieves the current user's username from the global $session object.
 *
 * @return string
 */
function session_username () {
	return $GLOBALS['session']->username;
}

/**
 * Retrieves the current user's encrypted password from the global $session
 * object.  Useful for custom authentication situations, such as the changing
 * of one's password.
 *
 * @return string
 */
function session_password () {
	global $session;
	return $session->sources[$session->_source]->resultObj->{$session->sources[$session->_source]->passwordcolumn};
}

/**
 * Determines whether the current user is valid.
 *
 * @return boolean
 */
function session_valid () {
	return $GLOBALS['session']->valid;
}

/**
 * Retrieves the value of the specified session variable.
 *
 * @param string
 * @return mixed
 */
function session_get ($name) {
	return $GLOBALS['session']->get ($name);
}

/**
 * Modifies the value of the specified session variable.
 *
 * @param string
 * @param mixed
 * @return boolean
 */
function session_set ($name, $value = false) {
	return $GLOBALS['session']->set ($name, $value);
}

/**
 * Appends to the value of the specified session variable.
 *
 * @param string
 * @param string
 * @return boolean
 */
function session_append ($name, $value = false) {
	return $GLOBALS['session']->append ($name, $value);
}

/**
 * Saves the current user's session data.
 *
 * @return boolean
 */
function session_save () {
	return $GLOBALS['session']->save ();
}

/**
 * Retrieves a copy of the session manager object.
 *
 * @return object reference
 */
function &session_get_manager () {
	return $GLOBALS['session']->getManager ();
}

function session_get_user ($user = false) {
	return $GLOBALS['session']->getUser ($user);
}

function session_make_pending_key () {
	return $GLOBALS['session']->makePendingKey ();
}

function session_is_valid_key ($user, $key) {
	return $GLOBALS['session']->isValidKey ($user, $key);
}

function session_authorize ($user, $pass, $id = false) {
	return $GLOBALS['session']->authorize ($user, $pass, $id);
}

?>