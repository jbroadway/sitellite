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
// CGI is a class that is used to give GET and POST auto-generated
// variables their own distinct namespace, so as not to conflict with
// other auto-generated variables, such as Cookie data.
//

/**
	 * CGI is a class that is used to give GET and POST auto-generated
	 * variables their own distinct namespace, so as not to conflict with other
	 * auto-generated variables, such as Cookie data.  The CGI class gets its data
	 * from the $HTTP_POST_VARS and $HTTP_GET_VARS hashes.
	 * 
	 * New in 1.2:
	 * - Fixed a bug in the parseUri method where the values weren't being urldecoded.
	 * 
	 * New in 1.4:
	 * - Added reset () calls before accessing the $HTTP_* arrays, so that it doesn't
	 *   result in an error if you read through them yourself prior to constructing
	 *   your CGI object.
	 * - Added parsing and verifying of uploaded files, so that you don't have to
	 *   worry about is_uploaded_file (), move_uploaded_file (), and all those.
	 *   Note: CGI will automatically attempt to import the UploadedFile class file
	 *   using a global $loader object if $HTTP_POST_FILES contains values.
	 * 
	 * New in 1.6:
	 * - Added verify () method which allows you to check any value against either
	 *   a regular expression or a function for validity before trusting it.
	 * 
	 * New in 1.8:
	 * - Added support for the PHP $_GET, $_POST, etc. variables, via an if statement
	 *   so as to be backward-compatible with the deprecated $HTTP_*_VARS variables.
	 * 
	 * New in 2.0:
	 * - Now relies on the $_GET, $_POST, $_FILES, and $_SERVER superglobal only,
	 *   but calls "global $_GET, $_POST, $_FILES, $_SERVER;" so that in versions
	 *   of PHP below 4.1.0, you can create a reference to the old $HTTP_*_VARS
	 *   using the new $_* names.
	 * 
	 * New in 2.2:
	 * - Now understands multiple file uploads using the fieldname[] style.
	 * 
	 * New in 2.4:
	 * - Fixed a bug with the automatic stripslashes() and fields that are arrays.
	 * 
	 * New in 2.6:
	 * - Added a makeQuery() method, which returns the property list as a URL
	 *   query string.
	 * 
	 * New in 2.8:
	 * - Fixed a bug in makeQuery() where values that were arrays were being
	 *   passed to urlencode(), which expects a string.  Arrays are now joined
	 *   with commas before this step.  Objects are simply skipped.
	 * 
	 * New in 3.0:
	 * - Added a verifyRequestMethod() method.
	 * 
	 * New in 3.2:
	 * - Added the following functions as aliases to methods of a global $cgi
	 *   object: cgi_param($name), cgi_params(), cgi_files(), cgi_make_query($except),
	 *   cgi_verify_request_method($required), cgi_translate_uri($uri, $lose),
	 *   and cgi_verify($param, $type, $validator).
	 * 
	 * New in 3.4:
	 * - parseUri() now returns $_SERVER['argv'] as extras if the server is
	 *   called from the command-line.
	 * - Now calls urldecode() and if magic_quotes_gpc is set also stripslashes()
	 *   on the $_SERVER['REQUEST_URI'] prior to parsing it in parseUri().
	 * - Now parseUri() correctly parses requests like:
	 *   /index/user/name.Mr.%20Joe%20Smith
	 *   That used to cause problems due to the extra dot.
	 * - parseUri() also strips queries from the end of the REQUEST_URI via
	 *   a substr(REQUEST_URI,0,strpos(REQUEST_URI,'?')) command when a
	 *   question-mark is in the URI.
	 * 
	 * New in 3.6:
	 * - Added types 'type' and 'rule' to verify().
	 * 
	 * <code>
	 * <?php
	 * 
	 * $cgi = new CGI;
	 * 
	 * // if a variable called 'query' was passed to this script, it can
	 * // be accessed this way:
	 * echo $cgi->query;
	 * 
	 * // or you can use the 'param' property to retrieve all of the names
	 * // of the variables passed to this script:
	 * foreach ($cgi->param as $p) {
	 * 	echo $cgi->{$p};
	 * }
	 * 
	 * // verifying data
	 * if ($cgi->verify ('somevar', 'regex', '/^[a-z0-9_-]*$/i')) {
	 * 	// $cgi->somevar is okay
	 * } else {
	 * 	// $cgi->somevar did not pass
	 * 	echo $cgi->error;
	 * }
	 * 
	 * // handling uploaded files...
	 * foreach ($cgi->files as $f) {
	 * 	echo $cgi->{$f}->name;
	 * }
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	CGI
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	3.6, 2003-07-17, $Id: CGI.php,v 1.9 2008/05/07 08:49:43 lux Exp $
	 * @access	public
	 * 
	 */

class CGI {
	/**
	 * Contains a list of the names of all the variables passed to the
	 * current script through either the GET or POST methods.
	 * 
	 * @access	public
	 * 
	 */
	var $param = array ();

	/**
	 * Contains a list of the names of all the file upload variables
	 * passed to the current script through the POST method.
	 * 
	 * @access	public
	 * 
	 */
	var $files = array ();

	/**
	 * Contains an error message describing the reason for a verify ()
	 * failure.
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
	function CGI () {
		foreach ($_GET as $k => $v) {
			$this->{$k} = $v;
			array_push ($this->param, $k);
		}
		foreach ($_POST as $k => $v) {
			$this->{$k} = $v;
			array_push ($this->param, $k);
		}
		if ($_FILES) {
			loader_import ('saf.CGI.UploadedFile');
			foreach ($_FILES as $k => $v) {
			}
		}
		if (get_magic_quotes_gpc () == 1) {
			foreach ($this->param as $k) {
				if (is_array ($this->{$k})) {
					foreach (array_keys ($this->{$k}) as $key) {
						$this->{$k}[$key] = stripslashes ($this->{$k}[$key]);
					}
				} else {
					$this->{$k} = stripslashes ($this->{$k});
				}
			}
		}
		/*
		if ($_GET) {
			reset ($_GET);
			while (list ($k, $v) = each ($_GET)) {
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
		if ($_POST) {
			reset ($_POST);
			while (list ($k, $v) = each ($_POST)) {
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
		*/
		if ($_FILES) {
			$GLOBALS['loader']->import ('saf.CGI.UploadedFile');
			reset ($_FILES);
			while (list ($k, $v) = each ($_FILES)) {
				if (! is_array ($v['name'])) {
					if (is_uploaded_file ($v['tmp_name'])) {
						$this->{$k} = new UploadedFile ($v);
						array_push ($this->files, $k);
					}
				} else {
					reset ($_FILES);
					$this->{$k} = array ();
					for ($i = 0; $i < count ($_FILES[$k]['tmp_name']); $i++) {
						if (is_uploaded_file ($_FILES[$k]['tmp_name'][$i])) {
							array_push ($this->{$k}, new UploadedFile (array (
								'name' => $_FILES[$k]['name'][$i],
								'type' => $_FILES[$k]['type'][$i],
								'tmp_name' => $_FILES[$k]['tmp_name'][$i],
								'size' => $_FILES[$k]['size'][$i],
							)));
							array_push ($this->files, $k);
						}
					}
					break;
				}
			}
		}
	}

	/**
	 * Takes the global $REQUEST_URI variable and parses it as if
	 * each subdirectory listing is a key/value pair, separated by periods (.),
	 * and adds these pairs as properties of this object, and the keys to the
	 * param array.  Any subdirectories that do not contain a period are returned
	 * as extras.
	 * 
	 * @access	public
	 * @return	array
	 * 
	 */
	function parseUri () {
		if (php_sapi_name () != 'cli') {

		//global $_SERVER; // just in case
		if (conf ('Site', 'remove_index') && isset ($_GET['_rewrite_sticky'])) {
			$_SERVER['REQUEST_URI'] = '/index' . str_replace ('/index/', '/', $_SERVER['REQUEST_URI']);
		}
		if (strstr ($_SERVER['REQUEST_URI'], '?')) {
			$uri = urldecode (substr ($_SERVER['REQUEST_URI'], 0, strpos ($_SERVER['REQUEST_URI'], '?')));
		} else {
			$uri = urldecode ($_SERVER['REQUEST_URI']);
		}
		if (get_magic_quotes_gpc () == 1) {
			$uri = stripslashes ($uri);
		}

		$extra_vars = array ();
		$items = explode ('/', $uri);
		array_shift ($items); // lose first empty item
		array_shift ($items); // lose file name
		foreach ($items as $item) {
			if (empty ($item)) {
				continue;
			}
			$pos = strpos ($item, '.');
			if ($pos > 0) {
				$key = substr ($item, 0, $pos);
				$value = substr ($item, $pos + 1);
				$this->{$key} = $value;
				array_push ($this->param, $key);
				if ($value == 'html') {
					$this->page = $key;
					$_GET['page'] = $key;
					$this->mode = 'html';
					array_push ($extra_vars, $key);
				}
			} else {
				array_push ($extra_vars, $item);
			}
		}
		return $extra_vars;

		$extra_vars = array (); // directories that didn't separate with a .
		$path_split = explode ('/', $uri);
		array_shift ($path_split); // lose the www... part of the URI
		array_shift ($path_split); // lose the filename part of the URI
		foreach ($path_split as $one) {
			$one = urldecode ($one);
			$pos = strpos ($one, '.');
			if ($pos > 0) {
				$key = substr ($one, 0, $pos);
				$value = substr ($one, $pos + 1);
				$this->{$key} = $value;
				array_push ($this->param, $key);
			} else {
				array_push ($extra_vars, $one);
			}
		}
		return $extra_vars;

		} else { // sapi = cli
			// command-line, return args as $extras
			$r = $_SERVER['argv'];
			if ($r[0] == $_SERVER['SCRIPT_NAME']) {
				array_shift ($r);
			}
			return $r;
		}
	}

	/**
	 * Takes an ordinary URI with GET parameters in it, and returns
	 * a URI compatible with the parseUri method.  The optional lose parameter
	 * is a comma-separated list of key/value pairs in the URI to lose, but
	 * not from the parameter list (the stuff that follows the ?), but from
	 * the first part of the URI.
	 * 
	 * @access	public
	 * @param	string	$uri
	 * @param	string	$lose
	 * @return	string
	 * 
	 */
	function translateUri ($uri = '', $lose = '') {
		list ($start, $params) = split ("\?", $uri);
		$vars = array ();
		$lose_these = split (', ?', $lose);

		// compile array of key.value pairs
		foreach (split ('&', $params) as $p) {
			if (! empty ($p)) {
				array_push ($vars, str_replace ('=', '.', $p));
			}
		}

		// lose specified already translated parts of the URI
		foreach ($lose_these as $p) {
			if (empty ($p)) {
				continue;
			}
			$start = preg_replace ('/' . $p . '(\.?[^/]*)/', '/', $start);
			$start = preg_replace ('/' . $p . '(\.?[^/]*)/?$', '', $start);
		}
		return $start . '/' . join ('/', $vars);
	}

	/**
	 * Verifies the specified value against either a regular
	 * expression or a function to see whether or not it contains valid
	 * input.  Understood $type's are 'regex', 'func' or 'function',
	 * 'type' which checks for the type of the value (int, numeric, string,
	 * etc.), and 'rule' which evaluates a MailForm rule on the value.
	 * Functions must accept only the value of the variable and return
	 * a boolean value.
	 * 
	 * @access	public
	 * @param	string	$param
	 * @param	string	$type
	 * @param	string	$validator
	 * @return	boolean
	 * 
	 */
	function verify ($param, $type, $validator) {
		$this->error = '';
		if ($type == 'regex') {
			if (preg_match ($validator, $this->{$param})) {
				return true;
			} else {
				$this->error = 'Regex validator did not match value';
				return false;
			}
		} elseif ($type == 'func' || $type == 'function') {
			if (call_user_func ($validator, $this->{$param})) {
				return true;
			} else {
				$this->error = 'Validator did not return true';
				return false;
			}
		} elseif ($type == 'type') {
			if (call_user_func ('is_' . $validator, $this->{$param})) {
				return true;
			} else {
				$this->error = 'Type validator did not return true';
				return false;
			}
		} elseif ($type == 'rule') {
			loader_import ('saf.MailForm.Rule');
			$rule = new MailFormRule ($validator, $param);
			if ($rule->validate ($this->{$param}, array (), $this)) {
				return true;
			} else {
				$this->error = 'Rule validator did not return true';
				return false;
			}
		} else {
			$this->error = 'Unknown validation type';
			return false;
		}
	}

	/**
	 * Creates a URL query string (ie. ?foo=bar&foo2=bar2)
	 * from the properties of this object, excluding $param, $files,
	 * $error, and any properties listed in $except.  If $except
	 * is a string, it will be converted to an array with the
	 * first element being the original string.
	 * 
	 * @access	public
	 * @param	array	$except
	 * @return	string
	 * 
	 */
	function makeQuery ($except = array ()) {
		if (! is_array ($except)) {
			$except = array ($except);
		}
		$except[] = 'param';
		$except[] = 'files';
		$except[] = 'error';
		$q = false;
		$query = '';
		foreach (get_object_vars ($this) as $p => $v) {
			if (! in_array ($p, $except) && ! is_object ($v)) {
				if (! $q) {
					$query .= '?';
					$q = true;
				} else {
					$query .= '&';
				}
				if (is_array ($v)) {
					$v = join (',', $v);
				}
				$query .= $p . '=' . urlencode ($v);
			}
		}
		return $query;
	}

	/**
	 * Verifies that the request method made by the user
	 * was made a certain way.  This can be useful in cases where
	 * you don't want data passed in the URL (ie. use POST instead
	 * of GET).
	 * 
	 * @access	public
	 * @param	string	$required
	 * @return	boolean
	 * 
	 */
	function verifyRequestMethod ($required = 'POST') {
		if ($_SERVER['REQUEST_METHOD'] == strtoupper ($required)) {
			return true;
		}
		return false;
	}

	/**
	 * Verifies that the current request is made through a secure
	 * socket layer (SSL).
	 *
	 * @access	public
	 * @return	boolean
	 *
	 */
	function isHttps () {
		if (! isset ($_SERVER['HTTPS']) || strtolower ($_SERVER['HTTPS']) != 'on') {
			return false;
		}
		return true;
	}

	/**
	 * Force the current page to be made over HTTPS.  Note: Doesn't check
	 * the response from site_secure(), simply reloads the page.
	 *
	 * @access	public
	 *
	 */
	function forceHttps () {
		header ('Location: https://' . site_domain () . site_current () . '?' . $_SERVER['QUERY_STRING']);
		exit;
	}

	/**
	 * Force the current page to be made over HTTP.
	 *
	 * @access	public
	 *
	 */
	function forceHttp () {
		header ('Location: http://' . site_domain () . site_current () . '?' . $_SERVER['QUERY_STRING']);
		exit;
	}
}

/**
 * Get the specified parameter from the global $cgi object.
 */
function cgi_param ($name) {
	return $GLOBALS['cgi']->{$name};
}

/**
 * Get a list of parameters from the global $cgi object.
 */
function cgi_params () {
	return $GLOBALS['cgi']->param;
}

/**
 * Get a list of files from the global $cgi object.
 */
function cgi_files () {
	return $GLOBALS['cgi']->files;
}

/**
 * Alias of CGI::makeQuery(). Makes a query string based on the
 * parameters of the global $cgi object.
 */
function cgi_make_query ($except = array ()) {
	return $GLOBALS['cgi']->makeQuery ($except);
}

/**
 * Verifies that the request method is the one $required.
 */
function cgi_verify_request_method ($required = 'POST') {
	return $GLOBALS['cgi']->verifyRequestMethod ($required);
}

/**
 * Alias of CGI::translateUri(). Creates a search-engine-friendly URL.
 */
function cgi_translate_uri ($uri = '', $lose = '') {
	return $GLOBALS['cgi']->translateUri ($uri, $lose);
}

/**
 * Alias of CGI::verify(). Validates the specified parameter.
 */
function cgi_verify ($param, $type, $validator) {
	return $GLOBALS['cgi']->verify ($param, $type, $validator);
}

/**
 * Alias of CGI::isHttps(). Checks whether HTTPS is being used on the
 * current request.
 */
function cgi_is_https () {
	return $GLOBALS['cgi']->isHttps ();
}

/**
 * Alias of CGI::forceHttps(). Forces HTTPS for the current request.
 */
function cgi_force_https () {
	return $GLOBALS['cgi']->forceHttps ();
}

/**
 * Alias of CGI::forceHttp(). Forces HTTP for the current request.
 */
function cgi_force_http () {
	return $GLOBALS['cgi']->forceHttp ();
}

/**
 * A simple rewrite filter for removing /index/ from URLs.
 */
function cgi_rewrite_filter ($out) {
	if (conf ('Site', 'remove_index')) {
		global $intl;
		if ($intl->url_increase_level) {
			$out = str_replace ('"/index/', '"/' . $intl->language . '/', $out);
			$out = str_replace ('"/index"', '"/' . $intl->language . '/"', $out);
			return str_replace (site_domain () . site_prefix () . '/index/', site_domain () . site_prefix () . '/' . $intl->language . '/', $out);
		}
		$out = str_replace ('"/index/', '"/', $out);
		$out = str_replace ('"/index"', '"/"', $out);
		return str_replace (site_domain () . site_prefix () . '/index/', site_domain () . site_prefix () . '/', $out);
	}
	return $out;
}

?>