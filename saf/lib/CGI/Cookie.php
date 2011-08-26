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
// Cookie is a class that is used to give auto-generated Cookie
// variables their own distinct namespace, so as not to conflict with
// other auto-generated variables, such as CGI data.
//

/**
	 * Cookie is a class that is used to give auto-generated Cookie
	 * variables their own distinct namespace, so as not to conflict with
	 * other auto-generated variables, such as CGI data.  The Cookie class gets
	 * its data from the $HTTP_COOKIE_VARS hash.
	 * 
	 * New in 1.2:
	 * - Added support for the PHP $_COOKIE, etc. variables, via an if statement
	 *   so as to be backward-compatible with the deprecated $HTTP_*_VARS variables.
	 * 
	 * New in 1.4:
	 * - Added the prependDot() method.
	 * 
	 * New in 1.6:
	 * - Now relies on the $_COOKIE superglobal only, but calls "global $_COOKIE;"
	 *   so that in versions of PHP below 4.1.0, you can create a reference to
	 *   the old $HTTP_*_VARS using the new $_* names.
	 * 
	 * New in 1.8:
	 * - Fixed a bug with the automatic stripslashes() and fields that are arrays.
	 * 
	 * New in 2.0:
	 * - Removed a preg_match() call and replaced it with a substr_count call,
	 *   which is faster AND more effective, in the prependDot() method.
	 * 
	 * New in 2.2:
	 * - Fixed a syntax error that was causing problems with cookies with positive
	 *   expiry times.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $cookie = new Cookie;
	 * 
	 * if (! empty ($cookie->session_id)) {
	 * 	// do something with the cookie
	 * } else {
	 * 	// set the cookie
	 * 	$cookie->set ("session_id", "value", 1800, "/", ".site.com", 0);
	 * }
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	CGI
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	2.2, 2002-11-01, $Id: Cookie.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class Cookie {
	/**
	 * Contains a list of the names of all the cookie variables
	 * available to the current script.
	 * 
	 * @access	public
	 * 
	 */
	var $param = array ();

	/**
	 * Constructor method.
	 * 
	 * @access	public
	 * 
	 */
	function Cookie () {
		if ($_COOKIE) {
			reset ($_COOKIE);
			while (list ($k, $v) = each ($_COOKIE)) {
				if (get_magic_quotes_gpc () == 1) {
					if (is_array ($v)) {
						for ($i = 0; $i < count ($v); $i++) {
							$v[$i] = stripslashes ($v[$i]);
						}
						$this->{$k} = $v;
					} else {
						$this->{$k} = stripslashes ($v);
					}
				} else {
					$this->{$k} = $v;
				}
				array_push ($this->param, $k);
			}
		}
	}

	/**
	 * Returns the domain passed to it, with a prepended dot (.) if
	 * the domain contains only a single dot (since the cookie specs say that
	 * two dots are required).  This is a workaround that lets us pass our
	 * Session object a $site with a domain like 'yoursite.com', and have it
	 * automatically know to set the cookie for '.yoursite.com'.
	 * 
	 * @access	public
	 * @param	string	$domain
	 * @return	string
	 * 
	 */
	function prependDot ($domain) {
		if (substr_count ($domain, '.') <= 1) {
			return '.' . $domain;
		} else {
			return $domain;
		}
	}

	/**
	 * Sends a 'set-cookie' HTTP header to the browser.  Must be called
	 * before any data has been sent.
	 * 
	 * $expire is the number of seconds from the current time.
	 * $domain must contain at least two periods (.).
	 * $secure accepts a 1 or a 0 to denote true or false.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	string	$value
	 * @param	integer	$expire
	 * @param	string	$path
	 * @param	string	$domain
	 * @param	boolean	$secure
	 * @return	boolean
	 * 
	 */
	function set ($name = '', $value = '', $expire = '', $path = '', $domain = '', $secure = 0) {
		/* if (ereg ("MSIE", getenv("HTTP_USER_AGENT"))) { #ie doesn't handle cookies properly...
			if (! empty ($path)) { $path = "; path=" . $path; }
			if (! empty ($domain)) { $domain = "; domain=" . $domain; }
			if ($secure > 0) { $secure = "; secure"; } else { $secure = ""; }
			if (! empty ($expire)) { $expire = "; expires=" . date ("l, d-M-y H:i:s", time () + $expire) . " GMT"; }
			header ("set-cookie: $name=$value$domain$expire$path$secure");
		} else { */
			if (! empty ($expire)) {
				$expire = time () + $expire;
			} else {
				$expire = 0;
			}
			if (empty ($domain)) {
				$domain = $_SERVER['HTTP_HOST'];
			}
			if (preg_match ('/:/', $domain)) {
				$domain = preg_replace ('/:.*$/', '', $domain);
			}
			$domain = $this->prependDot ($domain);
			return @setcookie ($name, $value, $expire, $path, $domain, $secure);
			//echo $name . ';' . $value . ';' . $expire . ';' . $path . ';' . $domain . ';' . $secure;
		// }
	}
}

?>