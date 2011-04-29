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
// Provides a Cookie-based authentication client handler to the saf.Session
// Package.
//


$GLOBALS['loader']->import ('saf.Session.Handler');

/**
	 * Provides a Cookie-based authentication client handler to the saf.Session
	 * Package.
	 * 
	 * New in 1.2:
	 * - Added a $cookieexpires property, so you can have sessions that can expire
	 *   in a certain amount of time, not just when the browser closes.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $c = new SessionHandler_Cookie;
	 * 
	 * $c->cookiename = 'sessid';
	 * $c->cookiedomain = 'www.sitename.com';
	 * 
	 * // or
	 * 
	 * $c->setProperties (array (
	 * 	'cookiename'	=> 'sessid',
	 * 	'cookiedomain'	=> 'www.sitename.com',
	 * ));
	 * 
	 * $c->sendAuthRequest ();
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	Session
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.2, 2002-11-09, $Id: Cookie.php,v 1.4 2008/02/20 12:32:32 lux Exp $
	 * @access	public
	 * 
	 */

class SessionHandler_Cookie extends SessionHandler {
	// cookie-specific properties
	

	/**
	 * Name of the cookie.  Defaults to 'sitellite_session_id'.
	 * 
	 * @access	public
	 * 
	 */
	var $cookiename = 'sitellite_session_id';

	/**
	 * Expiration of cookie.  Defaults to 0, which means the cookie
	 * will expire when the browser closes.
	 * 
	 * @access	public
	 * 
	 */
	var $cookieexpires = 0;

	/**
	 * Domain value of the cookie.
	 * 
	 * @access	public
	 * 
	 */
	var $cookiedomain;

	/**
	 * Path value of the cookie.  Defaults to '/'.
	 * 
	 * @access	public
	 * 
	 */
	var $cookiepath = '/';

	/**
	 * Secure value of the cookie.  May be 1 or 0, and defaults to 0.
	 * 
	 * @access	public
	 * 
	 */
	var $cookiesecure = 0;

	// auth request form properties (uses saf.MailForm)
	/**
	 * The name of the username field in the form (uses saf.MailForm)
	 * created by sendAuthRequest().  Defaults to 'username'.
	 * 
	 * @access	public
	 * 
	 */
	var $usernamefield = 'username';

	/**
	 * The name of the password field in the form
	 * created by sendAuthRequest().  Defaults to 'password'.
	 * 
	 * @access	public
	 * 
	 */
	var $passwordfield = 'password';

	/**
	 * A list of hidden form fields and their values.
	 * 
	 * @access	public
	 * 
	 */
	var $hiddenfields = array ();

	/**
	 * The alt text of the username field in the form
	 * created by sendAuthRequest().  Defaults to 'Username'.
	 * 
	 * @access	public
	 * 
	 */
	var $usernametext = 'Username';

	/**
	 * The alt text of the password field in the form
	 * created by sendAuthRequest().  Defaults to 'Password'.
	 * 
	 * @access	public
	 * 
	 */
	var $passwordtext = 'Password';

	/**
	 * The alt text of the submit button in the form
	 * created by sendAuthRequest().  Defaults to 'Sign In'.
	 * 
	 * @access	public
	 * 
	 */
	var $submittext = 'Sign In';

	/**
	 * The template of the form created by sendAuthRequest().
	 * Defaults to false (no template).
	 * 
	 * @access	public
	 * 
	 */
	var $formtemplate = false;

	/**
	 * A list of rules for the username form field.  The keys are
	 * the rules and the values are the invalid messages.
	 * 
	 * @access	public
	 * 
	 */
	var $usernamerules = array ();

	/**
	 * A list of rules for the password form field.  The keys are
	 * the rules and the values are the invalid messages.
	 * 
	 * @access	public
	 * 
	 */
	var $passwordrules = array ();

	/**
	 * The welcome message of the form.  Defaults to
	 * 'Please enter your username and password.'
	 * 
	 * @access	public
	 * 
	 */
	var $formmessage = 'Please enter your username and password.';

	/**
	 * The invalid message of the form.  Defaults to
	 * 'Sorry, the password you specified was invalid. Please try again.'
	 * 
	 * @access	public
	 * 
	 */
	var $invalidmessage = 'Sorry, the password you specified was invalid. Please try again.';

	/**
	 * The timeout message of the form.  Defaults to
	 * 'Sorry, your session has timed out.  Please sign in again to continue.'
	 * 
	 * @access	public
	 * 
	 */
	var $timeoutmessage = 'Sorry, your session has timed out.  Please sign in again to continue.';

	

	/**
	 * Initializes the communication with the client.  In the case
	 * of this handler, if the $authorized value is true it sets the session
	 * cookie with the $id value.
	 * 
	 * @access	public
	 * @param	string	$id
	 * @param	boolean	$authorized
	 * @return	boolean
	 * 
	 */
	function start ($id, $authorized = false) {
		if ($authorized) {
			global $loader;
			$loader->import ('saf.CGI.Cookie');
			$cookie = new Cookie;
			$cookie->set ($this->cookiename, $id, $this->cookieexpires, $this->cookiepath, $this->cookiedomain, $this->cookiesecure);
			$this->id = $id;

			// user agent checking, cross-reference with the original UA that was authenticated
			$ua = session_get ('session_ua');
			if (! $ua) {
				session_set ('session_ua', $_SERVER['HTTP_USER_AGENT']);
			} else {
				if ($ua != $_SERVER['HTTP_USER_AGENT']) {
					header ('HTTP/1.1 500 Internal Server Error');
					echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n"
						. "<html><head>\n<title>500 Internal Server Error</title>\n</head><body>\n<h1>Internal Server Error</h1>\n"
						. "<p>The server encountered an internal error or misconfiguration and was unable to complete your request.<p>\n"
						. "<p>Please contact the server administrator, you@example.com and inform them of the time the error occurred, and anything you might have done that may have caused the error.</p>\n"
						. "<p>More information about this error may be available in the server error log.</p></body></html>";
					exit;
				}
			}

			return true;
		} else {
			return false;
		}
	}

	/**
	 * Re-sends the session cookie with a new timeout value.
	 *
	 * @access	public
	 * @param	integer
	 */
	function changeTimeout ($newduration) {
		loader_import ('saf.CGI.Cookie');
		$cookie = new Cookie;
		$cookie->set ($this->cookiename, $this->id, $newduration, $this->cookiepath, $this->cookiedomain, $this->cookiesecure);
	}

	/**
	 * Sends the request for a username and password.  This would be
	 * an HTML form in this case.
	 * 
	 * @access	public
	 * 
	 */
	function sendAuthRequest () {
		global $loader, $cgi, $cookie;
		$loader->import ('saf.MailForm');

		$form = new MailForm;
		$form->message = $this->formmessage;
		$form->template = $this->template;

		$user =& $form->addWidget ('text', $this->usernamefield);
		$user->display_text = $this->usernametext;
		foreach ($this->usernamerules as $rule => $msg) {
			if (is_numeric ($rule)) {
				$user->addRule ($msg);
			} else {
				$user->addRule ($rule, $msg);
			}
		}

		$pass =& $form->addWidget ('password', $this->passwordfield);
		$pass->display_text = $this->passwordtext;
		foreach ($this->passwordrules as $rule => $msg) {
			if (is_numeric ($rule)) {
				$pass->addRule ($msg);
			} else {
				$pass->addRule ($rule, $msg);
			}
		}

		foreach ($this->hiddenfields as $name => $type) {
			if (is_numeric ($name)) {
				$h =& $form->addWidget ('hidden', $type);
				$h->setValue ($cgi->{$type});
			} else {
				$h =& $form->addWidget ($type, $name);
				$h->setValue ($cgi->{$name});
			}
		}

		$sub =& $form->addWidget ('submit', 'submit_button');
		$sub->setValues ($this->submittext);

		if ($form->invalid ($cgi)) {
			$form->setValues ($cgi);
			echo $form->show ();
		} elseif (isset ($cookie->{$this->cookiename}) && $this->sessObj->timeout > 0) {
			$form->message = $this->timeoutmessage;
			$form->setValues ($cgi);
			echo $form->show ();
		} else {
			$form->message = $this->invalidmessage;
			$form->setValues ($cgi);
			echo $form->show ();
		}
	}

	/**
	 * Provides values for the username, password, and session id
	 * (if applicable) to the main Session object.  This is passed off to
	 * the handlers because they know more about the client than the main
	 * object does.
	 * 
	 * @access	public
	 * @param	string/boolean	$sessionidname
	 * @return	array
	 * 
	 */
	function gatherParameters ($sessionidname = 'sitellite_session_id') {
		global $cgi, $cookie;
		$user = $cgi->username;
		$pass = $cgi->password;
		$id = $cookie->{$sessionidname};
		return array ($user, $pass, $id);
	}

	/**
	 * Unsets the session cookie.
	 * 
	 * @access	public
	 * 
	 */
	function close () {
		global $cookie;
		$cookie->set ($this->cookiename, '', $this->cookieexpires, $this->cookiepath, $this->cookiedomain, $this->cookiesecure);
	}
	
}



?>