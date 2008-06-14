<?php
//
// +----------------------------------------------------------------------+
// | Sitellite - Content Management System                                |
// +----------------------------------------------------------------------+
// | Copyright (c) 2007 Simian Systems                                    |
// +----------------------------------------------------------------------+
// | This software is released under the GNU General Public License (GPL) |
// | Please see the accompanying file docs/LICENSE for licensing details. |
// |                                                                      |
// | You should have received a copy of the GPL Software License along    |
// | with this program; if not, write to Simian Systems, 242 Lindsay,     |
// | Winnipeg, MB, R3N 1H1, CANADA.  The License is also available at     |
// | the following web site address:                                      |
// | <http://www.sitellite.org/index/license>                             |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <lux@simian.ca>                                |
// +----------------------------------------------------------------------+
//
// Provides an HTTP Basic authentication client handler to the saf.Session
// Package.
//


$GLOBALS['loader']->import ('saf.Session.Handler');

/**
	 * Provides an HTTP Basic authentication client handler to the saf.Session
	 * Package.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $basic = new SessionHandler_Basic;
	 * 
	 * $basic->realm = 'Private Area';
	 * 
	 * // or
	 * 
	 * $c->setProperties (array (
	 * 	'realm' => 'Private Area',
	 * ));
	 * 
	 * $basic->sendAuthRequest ();
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	Session
	 * @author	John Luxford <lux@simian.ca>
	 * @copyright	Copyright (C) 2001-2003, Simian Systems Inc.
	 * @license	http://www.sitellite.org/index/license	Simian Open Software License
	 * @version	1.0, 2002-09-18, $Id: Basic.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class SessionHandler_Basic extends SessionHandler {
	// basic-specific properties
	
	var $realm;
	
	var $cancelmessage = 'You do not have permission to access this resource.';

	
	function start ($id, $authorized = false) {
		// doesn't have to do anything yet, so if the source authorized the request,
		// then it's all good.
		return true;
	}

	/**
	 * Sends the request for a username and password.
	 * 
	 * @access	public
	 * 
	 */
	function sendAuthRequest () {
		header ('WWW-Authenticate: Basic realm="' . $this->realm . '"');
		header ('HTTP/1.0 401 Unauthorized');
		echo $this->cancelmessage;
		exit;
	}

	
	function gatherParameters ($sessionidname = false) {
		global $_SERVER;
		$user = $_SERVER['PHP_AUTH_USER'];
		$pass = $_SERVER['PHP_AUTH_PW'];
		$id = false;
		return array ($user, $pass, $id);
	}
}



?>