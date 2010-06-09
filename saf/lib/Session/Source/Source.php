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
// This is the base source driver package for saf.Session.  Custom
// drivers are created by sub-classing this package.
//


/**
	 * This is the base source driver package for saf.Session.  Custom
	 * drivers are created by sub-classing this package.
	 * 
	 * New in 1.2:
	 * - Added the getRole(), getTeam(), and isDisabled() methods.
	 *
	 * New in 1.4:
	 * - Added getActive(), session_user_get_active(), session_user_get_email(),
	 *   and session_user_is_unique()
	 * 
	 * <code>
	 * <?php
	 * 
	 * $s = new SessionSource;
	 * 
	 * $s->setProperties (array (
	 * 	'foo'	=> 'bar',
	 * ));
	 * 
	 * if ($s->authorize ($user, $pass, $id)) {
	 * 	// in
	 * } else {
	 * 	// out
	 * }
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	Session
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.2, 2003-04-23, $Id: Source.php,v 1.5 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class SessionSource {
	

	/**
	 * A reference to the main session object, in case it's
	 * needed by a specific handler.
	 * 
	 * @access	public
	 * 
	 */
	var $sessObj;

	/**
	 * An object containing all of the values returned from
	 * the data source regarding the user.
	 * 
	 * @access	public
	 * 
	 */
	var $resultObj;

	/**
	 * The error message if an error occurs.
	 * 
	 * @access	public
	 * 
	 */
	var $error;

	/**
	 * Source fields to map to the specified fields.
	 *
	 * @access	public
	 *
	 */
	var $map = array ();

	/**
	 * User values to set automatically to the specified values.
	 *
	 * @access	public
	 *
	 */
	var $set = array ();

	/**
	 * If this is set to true, the session source is considered "read-only"
	 * and can't be modified via Sitellite's user administration capabilities.
	 *
	 * @access	public
	 *
	 */
	var $readOnly = false;

	/**
	 * Constructor method.
	 * 
	 * @access	public
	 * 
	 */
	function SessionSource () {
	}

	/**
	 * Sets the properties of this object.
	 * 
	 * @access	public
	 * @param	associative array	$properties
	 * 
	 */
	function setProperties ($properties) {
		foreach ($properties as $key => $value) {
			if (strpos ($key, ' ') > 0) {
				list ($k, $v) = explode (' ', $key);
				if (! is_array ($this->{$k})) {
					$this->{$k} = array ($v => $value);
				} else {
					$this->{$k}[$v] = $value;
				}
			} else {
				$this->{$key} = $value;
			}
		}
		foreach ($this->map as $k => $v) {
			$this->{$k . 'column'} = $v;
		}
	}

	/**
	 * Authorizes the user against the data source.
	 * 
	 * @access	public
	 * @param	string	$username
	 * @param	string	$password
	 * @param	string	$id
	 * @return	boolean
	 * 
	 */
	function authorize ($username, $password, $id) {
	}

	/**
	 * Closes the session with the source.
	 * 
	 * @access	public
	 * 
	 */
	function close () {
	}

	/**
	 * Returns the role of the current user.
	 * 
	 * @access	public
	 * 
	 */
	function getRole () {
	}

	/**
	 * Returns the team of the current user.
	 * 
	 * @access	public
	 * 
	 */
	function getTeam () {
	}

	/**
	 * Returns the list of teams whose documents are accessible by the
	 * current user.
	 * 
	 * @access	public
	 * 
	 */
	function getTeams () {
	}

	/**
	 * Returns the whether or not the current user account
	 * is disabled.
	 * 
	 * @access	public
	 * 
	 */
	function isDisabled () {
	}

	/**
	 * Retrieves a user by their username.  Returns all of the user's
	 * data as an object.
	 *
	 * @access  public
	 *
	 */
	function getUser ($user) {
	}

	/**
	 * Retrieves a user by their email address.  Returns just the username.
	 *
	 * @access  public
	 *
	 */
	function getUserByEmail ($email) {
	}

	/**
	 * Determines whether the specified verification key is valid.
	 *
	 * @access  public
	 *
	 */
	function isValidKey ($user, $key) {
	}

	/**
	 * Adds a new user.
	 *
	 * @access  public
	 *
	 */
	function add ($data) {
	}

	/**
	 * Updates the data of the specified user.
	 *
	 * @access  public
	 *
	 */
	function update ($data, $user) {
	}

	/**
	 * Removes the specified user.
	 *
	 * @access  public
	 *
	 */
	function delete ($user) {
	}

	/**
	 * Retrieves the total number of users.  $role and $team allow you to
	 * retrieve a total for specific roles and teams.  $public allows
	 * you to specify whether to limit it to public users or not.
	 *
	 * @access  public
	 *
	 */
	function getTotal ($role, $team, $public) {
	}

	/**
	 * Retrieves the total number of active (ie. currently logged in) users.
	 *
	 * @access  public
	 *
	 */
	function getActive () {
	}

	/**
	 * Retrieves a list of users.
	 *
	 * @access  public
	 *
	 */
	function getList ($offset, $limit, $order, $ascdesc, $role, $team, $name) {
	}
}

/**
 * Alias of $GLOBALS['session']->source->getUser().
 */
function session_user_get ($user) {
	return $GLOBALS['session']->sources[$GLOBALS['session']->_wsource]->getUser ($user);
}

/**
 * Alias of $GLOBALS['session']->source->getList().
 */
function session_user_get_list ($offset, $limit, $order, $ascdesc, $role, $team, $name, $disabled, $public, $teams) {
	return $GLOBALS['session']->sources[$GLOBALS['session']->_wsource]->getList ($offset, $limit, $order, $ascdesc, $role, $team, $name, $disabled, $public, $teams);
}

/**
 * Alias of $GLOBALS['session']->source->getTotal().
 */
function session_user_get_total ($role, $team, $public) {
	return $GLOBALS['session']->sources[$GLOBALS['session']->_wsource]->getTotal ($role, $team, $public);
}

/**
 * Alias of $GLOBALS['session']->source->getActive().
 */
function session_user_get_active () {
	return $GLOBALS['session']->sources[$GLOBALS['session']->_wsource]->getActive ();
}

/**
 * Alias of $GLOBALS['session']->source->add().
 */
function session_user_add ($data) {
	return $GLOBALS['session']->sources[$GLOBALS['session']->_wsource]->add ($data);
}

/**
 * Alias of $GLOBALS['session']->source->update().  Note however,
 * the reversal of the property order.
 */
function session_user_edit ($user, $data) {
	return $GLOBALS['session']->sources[$GLOBALS['session']->_wsource]->update ($data, $user);
}

/**
 * Alias of $GLOBALS['session']->source->delete().
 */
function session_user_delete ($user) {
	return $GLOBALS['session']->sources[$GLOBALS['session']->_wsource]->delete ($user);
}

/**
 * Alias of $GLOBALS['session']->source->total.
 */
function session_user_total () {
	return $GLOBALS['session']->sources[$GLOBALS['session']->_wsource]->total;
}

/**
 * Alias of $GLOBALS['session']->source->error.
 */
function session_user_error () {
	return $GLOBALS['session']->sources[$GLOBALS['session']->_wsource]->error;
}

/**
 * Determines whether the specified username exists.
 */
function session_user_is_unique ($user) {
	if (session_user_get ($user)) {
		return false;
	}
	return true;
}

/**
 * Retrieves the email address of the specified user.
 */
function session_user_get_email ($user) {
	$data = (array) session_user_get ($user);
	return $data['email'];
}

?>