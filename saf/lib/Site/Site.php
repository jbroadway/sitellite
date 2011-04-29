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
// Site is a class that is used to store information about your site in
// a nice and simple location.
//

/**
	 * Site is a class that is used to store information about your site in
	 * a nice and simple location.The Site class contains 3 pieces of information
	 * about your web site, used mainly for input into the Session object.  These
	 * include the domain name of your site, which must contain at least 2 periods (.)
	 * in order to function properly; the web path from the document root of the
	 * site, and a 1 or 0 denoting whether or not we're running behind a Secure
	 * Socket Layer (SSL).
	 * 
	 * The nice part about Site though is that you can store any other properties
	 * you want to in it on the fly, because PHP allows you to do so without
	 * extending the class.  This makes Site a nice place to store information
	 * you want to maintain throughout your application.
	 * 
	 * New in 1.2:
	 * - Added a prefix() method.
	 * 
	 * New in 1.4:
	 * - Moved the prefix() method to saf.Site.SitelliteSite instead, since it uses
	 *   the Sitellite-specific SITELLITE_LEVEL constant.
	 * 
	 * New in 2.0:
	 * - All new property names, including $webpath, $adminpath, $docroot, and $appdir.
	 *   These changes are necessary in order to support sites with code outside of the
	 *   document root.  Note: The old $wwwpath is now an alias to $webpath, $abspath
	 *   is an alias to $docroot, and $path is an alias to $adminpath.  Also, the
	 *   constructor's parameters have changed.
	 * 
	 * New in 2.2:
	 * - Moved SitelliteSite::prefix() to this class and made it a property (the method
	 *   exists for b/c), so as to consolidate the two packages.  SitelliteSite::runmode
	 *   is also not used any more by SCS, so that "went West" as they say.
	 * - Added $current property, and site_current ().
	 * 
	 * <code>
	 * <?php
	 * 
	 * // create a Site object that sits behind SSL
	 * $site = new SitelliteSite (array (
	 * 	"domain"    => "www.yoursite.com",
	 * 	"docroot"   => "/home/yoursite/public_html",
	 * 	"appdir"    => "/home/yoursite/public_html",
	 * 	"webpath"   => "/",
	 * 	"adminpath" => "/sitellite",
	 * 	"secure"    => 1,
	 * ));
	 * 
	 * // output the site's url
	 * echo $site->url;
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	Site
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	2.2, 2002-07-17, $Id: Site.php,v 1.4 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class Site {
	/**
	 * Contains the domain of the site.
	 * 
	 * @access	public
	 * 
	 */
	var $domain;

	/**
	 * Contains the path of the site from the document root.
	 * 
	 * @access	public
	 * 
	 */
	var $webpath;

	/**
	 * Contains the path to the admin system from the document root.
	 * 
	 * @access	public
	 * 
	 */
	var $adminpath;

	/**
	 * Contains the absolute path to the document root.
	 * 
	 * @access	public
	 * 
	 */
	var $docroot;

	/**
	 * Contains the absolute path to the application directory (allows Sitellite
	 * to live outside of the $docroot).
	 * 
	 * @access	public
	 * 
	 */
	var $appdir;

	/**
	 * A 1 or 0 (true or false) denoting whether the site is running
	 * behind SSL.
	 * 
	 * @access	public
	 * 
	 */
	var $secure;

	/**
	 * The url of the web site, including the 'http://' or 'https://',
	 * the domain, and the webpath.  Created during the construction of the object.
	 * 
	 * @access	public
	 * 
	 */
	var $url;

	/**
	 * The path of the web site, which is essentially the webpath.
	 * Created during the construction of the object.
	 * 
	 * @access	public
	 * 
	 */
	var $prefix;

	/**
	 * The path of the web site, which is essentially the webpath,
	 * but also including everything but the ?parameters and #anchor.
	 * Created during the construction of the object.
	 * 
	 * @access	public
	 * 
	 */
	var $current;

	/**
	 * Constructor method.
	 * 
	 * @access	public
	 * @param	string	$domain
	 * @param	string	$path
	 * @param	boolean	$secure
	 * 
	 */
	function Site (&$params) {
		$this->domain =& $params['domain'];
		$this->secure =& $params['secure'];

		$this->docroot =& $params['docroot']; // ie. /home/user/public_html
		$this->appdir =& $params['appdir']; // ie. /usr/local/sitellite
		$this->webpath =& $params['webpath']; // ie. /
		$this->adminpath =& $params['adminpath']; // ie. /sitellite

		// for b/c
		$this->wwwpath =& $this->webpath;
		$this->abspath =& $params['abspath'];
		$this->path =& $this->adminpath;

		if ($this->secure) {
			$this->url = 'https://' . $this->domain . preg_replace ('/\/$/', '', $this->webpath);
		} else {
			$this->url = 'http://' . $this->domain . preg_replace ('/\/$/', '', $this->webpath);
		}

		if ($params['level'] > 0 && $this->webpath != '/') {
			$this->prefix = $this->webpath;
		} else {
			$this->prefix = '';
		}

		$this->current = $_SERVER['REQUEST_URI'];

		$pos = strpos ($this->current, '?');
		if ($pos > 0) {
			$this->current = substr ($this->current, 0, $pos);
		}

 		$pos = strpos ($this->current, '#');
		if ($pos > 0) {
			$this->current = substr ($this->current, 0, $pos);
		}
	}

	/**
	 * Returns the $site->webpath if a $conf['Site']['level'] value
	 * is defined as greater than 0, otherwise returns ''.  NOTE: DEPRECATED.
	 * 
	 * @access	public
	 * @param	object	$tpl
	 * @return	string
	 * 
	 */
	function prefix ($tpl = '') {
		return $this->prefix;
	}
}



function site_prefix () {
	return $GLOBALS['site']->prefix;
}

function site_url () {
	return $GLOBALS['site']->url;
}

function site_domain () {
	return $GLOBALS['site']->domain;
}

function site_docroot () {
	return $GLOBALS['site']->docroot;
}

function site_webpath () {
	return $GLOBALS['site']->webpath;
}

function site_secure () {
	return $GLOBALS['site']->secure;
}

function site_current () {
	return $GLOBALS['site']->current;
}

function site_name () {
	return str_replace ('www.', '', $GLOBALS['site']->domain);
}

?>