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
// This is the base client handler package for saf.Session.  Custom
// handlers are created by sub-classing this package.
//


/**
	 * This is the base client handler package for saf.Session.  Custom
	 * handlers are created by sub-classing this package.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $h = new SessionHandler;
	 * 
	 * $h->setProperties (array (
	 * 	'foo'	=> 'bar',
	 * ));
	 * 
	 * $h->sendAuthRequest ();
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	Session
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.0, 2002-10-10, $Id: Handler.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class SessionHandler {
	

	/**
	 * A reference to the main session object, in case it's
	 * needed by a specific handler.
	 * 
	 * @access	public
	 * 
	 */
	var $sessObj;

	/**
	 * The error message if an error occurs.
	 * 
	 * @access	public
	 * 
	 */
	var $error;

	

	/**
	 * Constructor method.
	 * 
	 * @access	public
	 * 
	 */
	function SessionHandler () {
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
			$this->{$key} = $value;
		}
	}

	/**
	 * Initializes the communication with the client.
	 * 
	 * @access	public
	 * @param	string	$id
	 * @param	boolean	$authorized
	 * @return	boolean
	 * 
	 */
	function start ($id, $authorized = false) {
	}

	/**
	 * Sends an authorization request to the user.
	 * 
	 * @access	public
	 * 
	 */
	function sendAuthRequest () {
	}

	/**
	 * Closes the session with the user.
	 * 
	 * @access	public
	 * 
	 */
	function close () {
	}

	/**
	 * Resets the client info with a new timeout value.
	 *
	 * @access	public
	 * @param	integer
	 */
	function changeTimeout ($newduration) {
	}
}

/**
 * Alias of SessionHandler's changeTimeout() method.
 *
 * @access	public
 * @param	integer
 */
function session_change_timeout ($newduration) {
	$GLOBALS['session']->handler->changeTimeout ($newduration);
}

?>