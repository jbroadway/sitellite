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
// Loader is a class that creates more of a Java-like 'import' mechanism
// for including libraries and files.
//

/**
	 * Loader is a class that creates more of a Java-like 'import' mechanism
	 * for including libraries and files.  That way you can specify include
	 * files not by their location but by which library they correspond to
	 * (ie. org.sitellite.XML.XSLT).
	 * 
	 * New in 1.2:
	 * - Added a 'return' option to the import() method's $method parameter.  Also
	 *   created three new methods which act as aliases to import() with the $method
	 *   already specified.  These are inc(), req(), and ret().  They are abbreviated
	 *   not for brevity's sake, but because include, require, and return are all
	 *   reserved words in PHP.
	 * - Separated the path translation logic into its own methods, translatePath() and
	 *   translateRealPath(), which cleaned up the code a lot and made the import()
	 *   method nice and small.
	 * 
	 * New in 1.4:
	 * - Fixed a regex bug in translateRealPath().
	 * 
	 * New in 1.6:
	 * - Added an $extension parameter to translatePath(), translateRealPath(),
	 *   import(), inc(), req(), and ret().
	 * 
	 * New in 1.8:
	 * - Fixed a bug in the loading order in translatePath(), where the file
	 *   foo/bar.php should be chosen before foo/bar/bar.php, which it wasn't
	 *   before.
	 * 
	 * New in 2.0:
	 * - Added box(), form(), getBoxSettings(), getFormSettings(), boxAllowed(),
	 *   and formAllowed() methods.
	 * - Added global functions that alias methods of a global $loader object.  These
	 *   include: loader_import(), loader_include(), loader_require(), loader_return(),
	 *   loader_box(), loader_form(), loader_box_get_settings(), loader_box_allowed(),
	 *   loader_form_get_fields(), and loader_form_allowed().
	 * - Added conf() function which aliases the values from a global $conf 2D array.
	 * 
	 * New in 2.2:
	 * - Added a 'dl' $method to import(), which calls dl() on the specified $lib in
	 *   one of the following directories: saf/dl/dll, saf/dl/dylib, or saf/dl/so.
	 *   Note that the suffix (.dl, .dylib, or .so) is provided by the
	 *   PHP_SHLIB_SUFFIX constant, which is also used to determine which directory
	 *   to find the extension in.
	 * - Added loader_dl() as an alias to the new import() $method.
	 * - Removed the html_marker() calls from box() and form() -- they now reside in
	 *   saf.XML.XT, so as not to interfere with the creation of web services,
	 *   command-line utilities, and reusable components, with predictable output
	 *   based on actions.
	 * 
	 * New in 2.4:
	 * - Box/form changes: added new access.php parameters.
	 * - Added $app property and getApp() method.  This introduces a new SCS
	 *   directory layout, being inc/app/$app/(boxes|forms|etc.)/$request.
	 * - Added the ability in translatePath() to call paths within custom apps
	 *   by specifying the app name instead of a defined path name.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $loader = new Loader (
	 * 	array (
	 * 		'sitellite' => 'J:/devel/inc/lib',
	 * 		'pear' => 'D:/PHP/pear'
	 * 	)
	 * );
	 * 
	 * $loader->import ('sitellite.Cookie');
	 * $loader->import ('pear.Date.Human');
	 * 
	 * $cookie = new Cookie;
	 * $cookie->set ('icamehereat', serialize (Date_Human::gregorianToHuman (29, 10, 2001)));
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	Loader
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	2.4, 2003-08-08, $Id: Loader.php,v 1.13 2008/03/14 16:57:25 lux Exp $
	 * @access	public
	 * 
	 */

class Loader {
	/**
	 * Contains a list of the different libraries and their locations.
	 * Some methods of Loader assume there is a 'default' location, and the
	 * constructor may set 'default' to the current directory if unspecified.
	 * 
	 * @access	public
	 * 
	 */
	var $paths = array ();

	/**
	 * Contains a list of the libraries that have already been included.
	 * Note: libraries included through the find () method will not be listed here.
	 * 
	 * @access	public
	 * 
	 */
	var $included = array ();

	/**
	 * The path to the boxes directory.
	 * 
	 * @access	public
	 * 
	 */
	var $boxPath = 'boxes';

	/**
	 * The path to the forms directory.
	 * 
	 * @access	public
	 * 
	 */
	var $formPath = 'forms';

	/**
	 * The name of the default SCS app.
	 * 
	 * @access	public
	 * 
	 */
	var $app;

	/**
	 * This is the prefix to the app directories.
	 *
	 * @access	public
	 *
	 */
	var $prefix = 'inc/app';

	/**
	 * This contains an array of the applications from inc/conf/auth/applications
	 * specifying which are core, enabled, and disabled.  If an application is
	 * disabled, the box() and form() calls will fail.
	 *
	 * @access	public
	 *
	 */
	var $applications = array ();

	/**
	 * Constructor method.  If $path is a string, it specifies the 'default'
	 * path location.  If $path is an associative array, it specifies a list of libraries
	 * and their locations, and 'default' is set to the current directory (unless overridden
	 * by being specified in $path).
	 * 
	 * @access	public
	 * @param	mixed	$path
	 * 
	 */
	function Loader ($path = '.') {
		if (is_string ($path)) {
			$this->paths['default'] = $path;
		} elseif (is_array ($path)) {
			$this->paths['default'] = preg_replace ('|\\\\|', '/', getcwd ());
			while (list ($n, $p) = each ($path)) {
				$this->paths[$n] = $p;
			}
		}
		$this->applications = @parse_ini_file ('inc/conf/auth/applications/index.php');
	}

	/**
	 * This is the method that translates the library name, etc. and makes the
	 * PHP '(include|require)_once' call.  $lib is the class to include (adds .php for you),
	 * and $method is the method to import the file with (either 'include', 'require', or
	 * 'return', which returns the contents of the specified package as opposed to simply
	 * including it and returning true if it succeeded).  If the $lib specified
	 * points to a directory, import () will look for a file with the same file name as
	 * that directory with a .php extension inside the directory (ie. ('sitellite.Database'
	 * would import 'sitellite/Database/Database.php').  You can optionally use a '.*' to
	 * tell Loader to load each file in a particular directory as well.  It will then
	 * proceed to load the entire directory, starting with the file of the same name as the
	 * directory, then in the order that readdir() reads them on your system.  Please Note:
	 * import() always calls include_once() or require_once().  If you need to call just
	 * include() or require(), you will have to do so manually.
	 * 
	 * @access	public
	 * @param	string	$lib
	 * @param	string	$method
	 * @param	string	$extension
	 * 
	 */
	function import ($lib, $method = 'include', $extension = 'php') {
		if (in_array ($lib, $this->included)) {
			// already been included, return successful though so external conditions
			// don't break.
			return true;
		}

		if ($method != 'dl') {
			$loadfile = $this->translatePath ($lib, $extension);
		}
		if ($method != 'return') {
			$this->included[] = $lib;
		}

		global $loader;

		if ($method != 'dl' && ! $loadfile) {
			list ($app, $pkg) = explode ('.', $lib);
			if (@file_exists ('inc/app/' . $app . '/lib/' . $pkg . '.ini.php')) {
				loader_import ('saf.Database.Generic');
				return Generic::load ($app, $pkg);
			}
			return false;
		} elseif (is_array ($loadfile)) {
			$ret = '';
			foreach ($loadfile as $file) {
				if ($method == 'include') {
					include_once ($file);
				} elseif ($method == 'require') {
					require_once ($file);
				} elseif ($method == 'return') {
					$ret .= join ('', file ($file));
				}
			}
			if ($method == 'return') {
				return $ret;
			}
		} else {
			if ($method == 'include') {
				include_once ($loadfile);
			} elseif ($method == 'require') {
				require_once ($loadfile);
			} elseif ($method == 'return') {
				return join ('', file ($loadfile));
			} elseif ($method == 'dl') {
				if (! extension_loaded ($lib)) {
					$cwd = getcwd ();
					chdir (SAF_DIR . '/dl/' . PHP_SHLIB_SUFFIX);
					if (! dl ($lib . '.' . PHP_SHLIB_SUFFIX)) {
						die ('Loading extension failed!');
					}
					chdir ($cwd);
				}
			}
		}

		return true;
	}

	/**
	 * This method controls your access to the $paths associative array, allowing
	 * you to add new libraries to it.  $new must be an associative array.
	 * 
	 * @access	public
	 * @param	associative array	$new
	 * 
	 */
	function addPath ($new) {
		if (! is_array ($new)) {
			return 0;
		} else {
			while (list ($n, $p) = each ($new)) {
				$this->paths[$n] = $p;
			}
		}
	}

	/**
	 * Returns the path to the library specified (which it gets from the $paths
	 * associative array).  This makes Loader potentially useful for directory-related
	 * tasks other than as an (include|require)_once replacement.  If $lib is not specified,
	 * it uses the 'default' path.
	 * 
	 * @access	public
	 * @param	string	$lib
	 * @return	string
	 * 
	 */
	function path ($lib = 'default') {
		return $this->paths[$lib];
	}

	/**
	 * Recursively searches a directory structure for the specified filename, and
	 * optionally includes that file.
	 * 
	 * @access	private
	 * @param	string	$dir
	 * @param	string	$package
	 * @param	boolean	$load
	 * 
	 */
	function _recurse ($dir, $package, $load) {
		//echo $dir . '<br />';
		if ($this->found) {
			return;
		}
		$d = dir ($dir);
		while ($file = $d->read ()) {
			if ($this->found) {
				$d->close ();
				return;
			}
			if (preg_match ('|^\.+$|', $file)) {
				continue;
			} elseif (($file == $package) && (@is_dir ($dir . '/' . $file))) {
				if ($load) {
					// load the file
					include_once ($dir . '/' . $file . '/' . $file . '.php');
				}
				echo $dir . '/' . $file . '/' . $file . '.php';
				$this->found++;
				$d->close ();
				return;
			} elseif ($file == $package . '.php') {
				if ($load) {
					// load the file
					include_once ($dir . '/' . $file);
				}
				echo $dir . '/' . $file;
				$this->found++;
				$d->close ();
				return;
			} elseif (@is_dir ($dir . '/' . $file)) {
				$this->_recurse ($dir . '/' . $file, $package, $load);
			}
		}
		$d->close ();
	}

	/**
	 * Search the Loader directories recursively for packages.  If $lib
	 * is unspecified, find () searches all of the Loader directories.  Returns
	 * the directory path to the file, if found.
	 * 
	 * @access	private
	 * @param	string	$package
	 * @param	string	$lib
	 * @param	boolean	$load
	 * @return	string
	 * 
	 */
	function find ($package, $lib = '', $load = 0) {
		$this->found = 0;
		ob_start ();
		if ($lib == '') {
			while (list ($n, $p) = each($this->paths)) {
				$this->_recurse ($p, $package, $load);
				// put if ob_contents hook in to break while
				if (ob_get_length () > 0) {
					break;
				}
			}
		} else {
			$this->_recurse ($this->paths[$lib], $package, $load);
		}
		$data = ob_get_contents ();
		ob_end_clean ();
		//echo $data;
		return $data;
	}

	/**
	 * Translates a Loader-style path (ie. saf.MailForm.Widget.File) into
	 * the actual file name or names (for paths ending in .*), which can then be
	 * used by the import() method.  This can also be useful if you want to find
	 * out the actual file name that corresponds to the Loader path (since for
	 * instance saf.MailForm could mean either saf/lib/MailForm.php or
	 * saf/lib/MailForm/MailForm.php).  Returns the file name and path on success,
	 * an array of file names if the path ends in .* and it is successful, or
	 * false on failure.  Note that the first component to a path, which is
	 * considered the root of the path, must be either defined in the $paths
	 * property or else exist as an SCS app in inc/app.
	 * 
	 * @access	public
	 * @param	string	$lib
	 * @param	string	$extension
	 * 
	 */
	function translatePath ($lib, $extension = 'php') {
		$path = '';
		$location = explode ('.', $lib);
		if (isset ($this->paths[$location[0]])) {
			$namespace = array_shift ($location);
		} elseif (@is_dir ($this->prefix . '/' . $location[0] . '/lib')) {
			$namespace = array_shift ($location);
			$this->paths[$namespace] = $this->prefix . '/' . $namespace . '/lib';
		} else {
			$namespace = 'default';
		}
		$path .= $this->paths[$namespace];
		$file = array_pop ($location);
		$path .= '/' . join ('/', $location);

		if ($file == '*') {
			$files = array ();
			$dirname = array_pop ($location);

			// include dirname/dirname.php first
			if (@is_file ($path . '/' . $dirname . '.' . $extension)) {
				$files[] = $path . '/' . $dirname . '.' . $extension;
			}

			$dir = @opendir ($path);
			if (! $dir) {
				return false;
			}
			while (($f = readdir ($dir)) !== false) {
				if (preg_match ('/\.' . $extension . '$/i', $f) && $f != $dirname . '.' . $extension) {
					$files[] = $path . '/' . $f;
				}
			}
			closedir ($dir);

			return $files;

		} else {
			if (@is_file ($path . '/' . $file . '.' . $extension)) {
				return $path . '/' . $file . '.' . $extension;
			} elseif (@is_file ($path . '/' . $file . '/' . $file . '.' . $extension)) {
				return $path . '/' . $file . '/' . $file . '.' . $extension;
			} else {
				return false;
			}
		}
	}

	/**
	 * Translates an actual path into a Loader-style path.
	 * 
	 * @access	public
	 * @param	string	$lib
	 * @param	string	$extension
	 * 
	 */
	function translateRealPath ($lib, $extension = 'php') {
		// remove .php
		$lib = preg_replace ('/\.' . $extension . '$/i', '', $lib);
		// split by / or \
		$path = preg_split ('/[\/\\\]/', $lib);
		$file = array_pop ($path);
		if ($file == $path[count ($path) - 1]) {
			return join ('.', $path);
		} else {
			return join ('.', $path) . '.' . $file;
		}
	}

	/**
	 * Alias for import(), with the $method always specified as 'include'.
	 * 
	 * @access	public
	 * @param	string	$lib
	 * @param	string	$extension
	 * @return	boolean
	 * 
	 */
	function inc ($lib, $extension = 'php') {
		return $this->import ($lib, 'include', $extension);
	}

	/**
	 * Alias for import(), with the $method always specified as 'require'.
	 * 
	 * @access	public
	 * @param	string	$lib
	 * @param	string	$extension
	 * @return	boolean
	 * 
	 */
	function req ($lib, $extension = 'php') {
		return $this->import ($lib, 'require', $extension);
	}

	/**
	 * Alias for import(), with the $method always specified as 'return'.
	 * 
	 * @access	public
	 * @param	string	$lib
	 * @param	string	$extension
	 * @return	string
	 * 
	 */
	function ret ($lib, $extension = 'php') {
		return $this->import ($lib, 'return', $extension);
	}

	/**
	 * Executes the specified box using the Sitellite box API,
	 * which is essentially just an include.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	associative array	$parameters
	 * @param	string	$context
	 * @return	string
	 * 
	 */
	function box ($name, $parameters = array (), $context = 'normal') {

		if (! is_array ($this->boxAccess)) {
			if (! $this->boxAllowed ($name, $context)) {
				return '';
			}
		}

		if (isset ($this->boxAccess['sitellite_secure']) && $this->boxAccess['sitellite_secure']) {
			if (site_secure ()) {
				if (! cgi_is_https ()) {
					cgi_force_https ();
				}
			} else {
				die ('The requested box requires an SSL connection, but Sitellite does not have SSL enabled.');
			}
		} elseif (isset ($this->boxAccess['sitellite_secure']) && $this->boxAccess['sitellite_secure'] === '') {
			if (cgi_is_https ()) {
				cgi_force_http ();
			}
		}

		$app = $this->getApp ($name);
		$name = $this->removeApp ($name, $app);
		$this->apps[] = $app;

		//echo 'App: ' . $app . ', Box: ' . $name . '<br />'; exit;

		if (isset ($this->boxAccess['sitellite_fname']) && $this->boxAccess['sitellite_fname'] && ! @is_dir ($this->prefix . '/' . $app . '/' . $this->boxPath . '/' . $name)) {
			$name = preg_split ('/\//', $name);
			$file = array_pop ($name);
			$name = join ('/', $name);
		} else {
			$file = 'index';
		}

		if (@file_exists ($this->prefix . '/' . $app . '/' . $this->boxPath . '/' . $name . '/' . $file . '.php')) {

			global $intl;

			$old_intl_path = $intl->directory;
			$intl->directory = $this->prefix . '/' . $app . '/lang';
			$intl->getIndex ();

			if (@file_exists ($this->prefix . '/' . $app . '/conf/properties.php')) {
				include_once ($this->prefix . '/' . $app . '/conf/properties.php');
			}

			/*if (@file_exists ($this->prefix . '/' . $app . '/conf/settings.ini.php')) {
				$settings = ini_parse ($this->prefix . '/' . $app . '/conf/settings.ini.php', true);
				foreach ($settings as $k => $v) {
					appconf_set ($k, $v['value']);
				}
			}*/
			appconf_default_settings ();

			ob_start ();

			$box = $this->getBoxSettings ($name, $app);
			$box['context'] = $context;
			$box['parameters'] =& $parameters;

			// automatic input validation
			loader_import ('saf.MailForm');
			foreach (array_keys ($box) as $field) {
				if ($field == 'Meta' || $field == 'context' || $field == 'parameters') {
					continue;
				}
				foreach ($box[$field] as $key => $value) {
					if (strpos ($key, 'rule ') === 0) {
						list ($rule, $msg) = preg_split ('/, ?/', $value, 2);
						$r = new MailFormRule ($rule, $field, $msg);
						if (! $r->validate ($box['parameters'][$field], new StdClass, new StdClass)) {
							ob_end_clean ();
							if ($context == 'action') {
								echo '<h1>Input validation failed!</h1>';
								echo '<p>Parameter: <strong>' . $field . '</strong></p>';
								echo '<p>Message: <strong>' . $msg . '</strong></p>';
								exit;
							} else {
								$this->boxAccess = false;
								array_pop ($this->apps);
								return '<p class="notice">Input validation failed (' . $field . '): ' . $msg . '</p>';
							}
						}
					}
				}
			}

			// special behaviour changes for global objects when in a box
			global $simple, $tpl;

			$old_simple_path = $simple->path;
			$simple->path = $this->prefix . '/' . $app . '/html';

			$old_tpl_path = $tpl->path;
			$tpl->path = $this->prefix . '/' . $app . '/html';

			if (isset ($this->boxAccess['sitellite_chdir']) && $this->boxAccess['sitellite_chdir']) {
				$this->originalDirectory = getcwd ();
				//echo $this->boxPath . '/' . $name;
				//exit;
				chdir ($this->prefix . '/' . $app . '/' . $this->boxPath . '/' . $name);
				include ($file . '.php');
				chdir ($this->originalDirectory);
			} else {
				include ($this->prefix . '/' . $app . '/' . $this->boxPath . '/' . $name . '/' . $file . '.php');
			}

			$simple->path = $old_simple_path;
			$tpl->path = $old_tpl_path;
			$intl->directory = $old_intl_path;

			$contents = ob_get_contents ();
			ob_end_clean ();
			$contents = $this->boxRewrite ($contents);
			if (isset ($this->boxAccess['sitellite_exit']) && $this->boxAccess['sitellite_exit']) {
				echo $contents;
				$this->boxAccess = false;
				exit;
			}
			$this->boxAccess = false;
			array_pop ($this->apps);
			return $contents;
		} else {
			$this->boxAccess = false;
			array_pop ($this->apps);
			global $errno;
			$errno = E_NOT_FOUND;
			switch (conf ('Server', 'error_handler_type')) {
				case 'box':
					return $this->box (conf ('Server', 'error_handler'));
				case 'form':
					return $this->form (conf ('Server', 'error_handler'));
				default:
					header ('Location: ' . site_prefix () . '/index/' . conf ('Server', 'error_handler'));
					exit;
			}
		}
		$this->boxAccess = false;
		array_pop ($this->apps);
		return '';
	}

	function boxRewrite ($data) {
		if (! empty ($this->boxAccess['sitellite_rewrite_pattern'])) {
			// rewrite urls
			$data = preg_replace (
				$this->boxAccess['sitellite_rewrite_pattern'],
				$this->boxAccess['sitellite_rewrite_replace'],
				$data
			);
		}
		return $data;
	}

	/**
	 * Executes the specified form using the Sitellite form API,
	 * which is essentially just an include of a file that defines a
	 * subclass of saf.MailForm.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	string	$context
	 * @return	string
	 * 
	 */
	function form ($name, $context = 'normal') {

		if (! is_array ($this->formAccess)) {
			if (! $this->formAllowed ($name, $context)) {
				return '';
			}
		}

		if ($this->formAccess['sitellite_secure']) {
			if (site_secure ()) {
				if (! cgi_is_https ()) {
					cgi_force_https ();
				}
			} else {
				die ('The requested form requires an SSL connection, but Sitellite does not have SSL enabled.');
			}
		} elseif ($this->formAccess['sitellite_secure'] === '') {
			if (cgi_is_https ()) {
				cgi_force_http ();
			}
		}

		$app = $this->getApp ($name);
		$name = $this->removeApp ($name, $app);
		$this->apps[] = $app;

		if (@file_exists ($this->prefix . '/' . $app . '/' . $this->formPath . '/' . $name . '/index.php')) {
			loader_import ('saf.MailForm');

			if (@file_exists ($this->prefix . '/' . $app . '/conf/properties.php')) {
				include_once ($this->prefix . '/' . $app . '/conf/properties.php');
			}

			/*if (@file_exists ($this->prefix . '/' . $app . '/conf/settings.ini.php')) {
				$settings = ini_parse ($this->prefix . '/' . $app . '/conf/settings.ini.php', true);
				foreach ($settings as $k => $v) {
					appconf_set ($k, $v['value']);
				}
			}*/
			appconf_default_settings ();

			ob_start ();

			// special behaviour changes for global objects when in a box
			global $simple, $tpl, $intl;

			$old_simple_path = $simple->path;
			$simple->path = $this->prefix . '/' . $app . '/html';

			$old_tpl_path = $tpl->path;
			$tpl->path = $this->prefix . '/' . $app . '/html';

			$old_intl_path = $intl->directory;
			$intl->directory = $this->prefix . '/' . $app . '/lang';
			$intl->getIndex ();

			include ($this->prefix . '/' . $app . '/' . $this->formPath . '/' . $name . '/index.php');

			$contents .= ob_get_contents ();
			ob_end_clean ();

			$contents = trim ($contents);

			if (empty ($contents)) {
				$class = ucfirst ($app);
				foreach (explode ('/', $name) as $p) {
					$class .= ucfirst ($p);
				}
				$class .= 'Form';

				if (class_exists ($class)) {
					ob_start ();

					$form = new $class;
					$form->context = $context;
					echo $form->run ();

					$contents .= ob_get_contents ();
					ob_end_clean ();
				}
			}

			$simple->path = $old_simple_path;
			$tpl->path = $old_tpl_path;
			$intl->directory = $old_intl_path;

			$this->formAccess = false;
			array_pop ($this->apps);
			return $contents;
		} else {
			$this->formAccess = false;
			array_pop ($this->apps);
			global $errno;
			$errno = E_NOT_FOUND;
			switch (conf ('Server', 'error_handler_type')) {
				case 'box':
					return $this->box (conf ('Server', 'error_handler'));
				case 'form':
					return $this->form (conf ('Server', 'error_handler'));
				default:
					header ('Location: ' . site_prefix () . '/index/' . conf ('Server', 'error_handler'));
					exit;
			}
		}
		$this->formAccess = false;
		array_pop ($this->apps);
		return '';
	}

	function getBoxSettings ($name, $app) {
		if (@file_exists ($this->prefix . '/' . $app . '/' . $this->boxPath . '/' . $name . '/settings.php')) {
			$settings = ini_parse ($this->prefix . '/' . $app . '/' . $this->boxPath . '/' . $name . '/settings.php', true);
			return $settings;
		}
		return array ();
	}

	function getBoxAccess ($name, $app) {
		if (session_admin () && session_is_resource ('app_' . $app) && ! session_allowed ('app_' . $app, 'rw', 'resource')) {
			return false;
		}

		$dir = $this->prefix . '/' . $app . '/' . $this->boxPath . '/' . $name;
		while ($dir != $this->prefix . '/' . $app . '/' . $this->boxPath) {
			if (@file_exists ($dir . '/access.php')) {
				return parse_ini_file ($dir . '/access.php');
			}

			$dir = preg_split ('/\//', $dir);
			array_pop ($dir);
			$dir = join ('/', $dir);
		}

		return false;
	}

	function getFormAccess ($name, $app) {
		if (session_admin () && session_is_resource ('app_' . $app) && ! session_allowed ('app_' . $app, 'rw', 'resource')) {
			return false;
		}

		$dir = $this->prefix . '/' . $app . '/' . $this->formPath . '/' . $name;
		while ($dir != $this->prefix . '/' . $app . '/' . $this->formPath) {
			if (@file_exists ($dir . '/access.php')) {
				return parse_ini_file ($dir . '/access.php');
			}

			$dir = preg_split ('/\//', $dir);
			array_pop ($dir);
			$dir = join ('/', $dir);
		}

		return false;
	}

	/**
	 * Checks recursively in the box directory and parent directories
	 * until it checks $boxPath finally for an access.php file.  It then
	 * parses that file as an INI file and determines whether the box is
	 * accessible by the current user.  If a template is specified in the
	 * access.php file, that template name is returned on success, otherwise
	 * a boolean true value is returned on success.  False is always returned
	 * if the user is not allowed.  Allowed access values are:
	 * 
	 * - sitellite_access - string - the access level of the box
	 * - sitellite_status - string - the status of the box
	 * - sitellite_action - boolean - whether the box can be called as an action
	 * - sitellite_inline - boolean - whether the box can be called inline within a page
	 * - sitellite_goto - string - the location to redirect to if the box permissions fail
	 * - sitellite_template - string - template to use for this box, applies only to boxes called as actions
	 * - sitellite_template_set - string - template set to use for this box, applies only to boxes called as actions
	 * - sitellite_chdir - boolean - whether to change the base directory during the execution of the box,
	 *     to more easily resolve includes in ported apps
	 * - sitellite_fname - boolean - whether to check for filenames other than index.php to execute
	 *     (ie. myapp-hello-world-action resolving to inc/boxes/hello/world.php or inc/boxes/hello/world/index.php
	 *     -- note that Loader resolves the latter first, if available)
	 * - sitellite_rewrite_pattern - string - a pattern within the box output (usually urls from ported apps) to rewrite
	 * - sitellite_rewrite_replace - string - the replacement for the rewrite pattern
	 * - sitellite_exit - boolean - calls exit() at the end of the box, bypassing all post-output processing.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	string	$context
	 * @return	mixed
	 * 
	 */
	function boxAllowed ($name, $context = 'normal') {
		$app = $this->getApp ($name);
		$name = $this->removeApp ($name, $app);

		if (session_admin () && session_is_resource ('app_' . $app) && ! session_allowed ('app_' . $app, 'r', 'resource')) {
			return false;
		}

		if (isset ($this->applications[$app]) && ! $this->applications[$app]) {
			// app is disabled
			return false;
		}

		$dir = $this->prefix . '/' . $app . '/' . $this->boxPath . '/' . $name;
		while ($dir != $this->prefix . '/' . $app . '/' . $this->boxPath) {
			if (@file_exists ($dir . '/access.php')) {
				$access = parse_ini_file ($dir . '/access.php');
				$this->boxAccess = $access;
				if (! session_allowed ($access['sitellite_access'], 'r', 'access')) {
					if (isset ($access['sitellite_goto'])) {
						header ('Location: ' . site_prefix () . '/index/' . $access['sitellite_goto']);
						exit;
					}
					return false;
				} elseif (! session_allowed ($access['sitellite_status'], 'r', 'status')) {
					if (isset ($access['sitellite_goto'])) {
						header ('Location: ' . site_prefix () . '/index/' . $access['sitellite_goto']);
						exit;
					}
					return false;
				} elseif ($context == 'action' && ! $access['sitellite_action']) {
					if (isset ($access['sitellite_goto'])) {
						header ('Location: ' . site_prefix () . '/index/' . $access['sitellite_goto']);
						exit;
					}
					return false;
				} elseif ($context != 'normal' && isset ($access['sitellite_' . $context]) && ! $access['sitellite_' . $context]) {
					return false;
//				} elseif ($context == 'inline' && ! $access['sitellite_inline']) {
//					return false;
				} else {
					if ($context == 'action' && isset ($access['sitellite_template_set'])) {
						page_template_set ($access['sitellite_template_set']);
					}
					if ($context == 'action' && isset ($access['sitellite_template'])) {
						return $access['sitellite_template'];
					} else {
						return true;
					}
				}
			}

			$dir = preg_split ('/\//', $dir);
			array_pop ($dir);
			$dir = join ('/', $dir);
		}

		// check for a global access.php file
		if (@file_exists ($this->prefix . '/' . $app . '/' . $this->boxPath . '/access.php')) {
			$access = parse_ini_file ($this->prefix . '/' . $app . '/' . $this->boxPath . '/access.php');
			$this->boxAccess = $access;
			if (! session_allowed ($access['sitellite_access'], 'r', 'access')) {
				if (isset ($access['sitellite_goto'])) {
					header ('Location: ' . site_prefix () . '/index/' . $access['sitellite_goto']);
					exit;
				}
				return false;
			} elseif (! session_allowed ($access['sitellite_status'], 'r', 'status')) {
				if (isset ($access['sitellite_goto'])) {
					header ('Location: ' . site_prefix () . '/index/' . $access['sitellite_goto']);
					exit;
				}
				return false;
			} elseif ($context == 'action' && ! $access['sitellite_action']) {
				if (isset ($access['sitellite_goto'])) {
					header ('Location: ' . site_prefix () . '/index/' . $access['sitellite_goto']);
					exit;
				}
				return false;
			} elseif ($context == 'inline' && ! $access['sitellite_inline']) {
				return false;
			} else {
				if (isset ($access['sitellite_template_set'])) {
					page_template_set ($access['sitellite_template_set']);
				}
				if (isset ($access['sitellite_template'])) {
					return $access['sitellite_template'];
				} else {
					return true;
				}
			}
		}

		// no access.php found at all, revert to logical defaults
		if ($context == 'action') {
			return false;
		}
		return true;
	}

	function getFormSettings ($name) {
		$app = $this->getApp ($name);
		$name = $this->removeApp ($name, $app);

		if (@file_exists ($this->prefix . '/' . $app . '/' . $this->formPath . '/' . $name . '/settings.php')) {
			return ini_parse ($this->prefix . '/' . $app . '/' . $this->formPath . '/' . $name . '/settings.php', true);
		}
		return array ();
	}

	function getFormFields ($name) {
		$settings = $this->getFormSettings ($name);

		$ignoreTypes = array (
			'section',
			'separator',
			'submit',
			'msubmit',
		);

		unset ($settings['Form']);

		$list = array ();
		foreach ($settings as $key => $value) {
			if (! in_array ($value['type'], $ignoreTypes)) {
				$list[] = $key;
			}
		}
		return $list;
	}

	/**
	 * Checks recursively in the form directory and parent directories
	 * until it checks $formPath finally for an access.php file.  It then
	 * parses that file as an INI file and determines whether the form is
	 * accessible by the current user.  If a template is specified in the
	 * access.php file, that template name is returned on success, otherwise
	 * a boolean true value is returned on success.  False is always returned
	 * if the user is not allowed.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	string	$context
	 * @return	mixed
	 * 
	 */
	function formAllowed ($name, $context = 'normal') {
		$app = $this->getApp ($name);
		$name = $this->removeApp ($name, $app);

		if (session_admin () && session_is_resource ('app_' . $app) && ! session_allowed ('app_' . $app, 'rw', 'resource')) {
			return false;
		}

		if (isset ($this->applications[$app]) && ! $this->applications[$app]) {
			// app is disabled
			return false;
		}

		$dir = $this->prefix . '/' . $app . '/' . $this->formPath . '/' . $name;
		while ($dir != $this->prefix . '/' . $app . '/' . $this->formPath) {
			if (@file_exists ($dir . '/access.php')) {
				$access = parse_ini_file ($dir . '/access.php');
				$this->formAccess = $access;
				if (! session_allowed ($access['sitellite_access'], 'r', 'access')) {
					if (isset ($access['sitellite_goto'])) {
						header ('Location: ' . site_prefix () . '/index/' . $access['sitellite_goto']);
						exit;
					}
					return false;
				} elseif (! session_allowed ($access['sitellite_status'], 'r', 'status')) {
					if (isset ($access['sitellite_goto'])) {
						header ('Location: ' . site_prefix () . '/index/' . $access['sitellite_goto']);
						exit;
					}
					return false;
				} elseif ($context == 'action' && ! $access['sitellite_action']) {
					if (isset ($access['sitellite_goto'])) {
						header ('Location: ' . site_prefix () . '/index/' . $access['sitellite_goto']);
						exit;
					}
					return false;
				} elseif ($context != 'normal' && isset ($access['sitellite_' . $context]) && ! $access['sitellite_' . $context]) {
					return false;
//				} elseif ($context == 'inline' && ! $access['sitellite_inline']) {
//					return false;
				} else {
					if (isset ($access['sitellite_template_set'])) {
						page_template_set ($access['sitellite_template_set']);
					}
					if (isset ($access['sitellite_template'])) {
						return $access['sitellite_template'];
					} else {
						return true;
					}
				}
			}

			$dir = preg_split ('/\//', $dir);
			array_pop ($dir);
			$dir = join ('/', $dir);
		}

		// check for a global access.php file
		if (@file_exists ($this->prefix . '/' . $app . '/' . $this->formPath . '/access.php')) {
			$access = parse_ini_file ($this->prefix . '/' . $app . '/' . $this->formPath . '/access.php');
			$this->formAccess = $access;
			if (! session_allowed ($access['sitellite_access'], 'r', 'access')) {
				if (isset ($access['sitellite_goto'])) {
					header ('Location: ' . site_prefix () . '/index/' . $access['sitellite_goto']);
					exit;
				}
				return false;
			} elseif (! session_allowed ($access['sitellite_status'], 'r', 'status')) {
				if (isset ($access['sitellite_goto'])) {
					header ('Location: ' . site_prefix () . '/index/' . $access['sitellite_goto']);
					exit;
				}
				return false;
			} elseif ($context == 'action' && ! $access['sitellite_action']) {
				if (isset ($access['sitellite_goto'])) {
					header ('Location: ' . site_prefix () . '/index/' . $access['sitellite_goto']);
					exit;
				}
				return false;
			} elseif ($context == 'inline' && ! $access['sitellite_inline']) {
				return false;
			} else {
				if (isset ($access['sitellite_template_set'])) {
					page_template_set ($access['sitellite_template_set']);
				}
				if (isset ($access['sitellite_template'])) {
					return $access['sitellite_template'];
				} else {
					return true;
				}
			}
		}

		// no access.php found at all, revert to logical defaults
		if ($context == 'action') {
			return false;
		}
		return true;
	}

	/**
	 * Determines which SCS app to run, based on the request path
	 * and the default provided.
	 * 
	 * @access	public
	 * @param	string	$request
	 * @param	string	$default
	 * @return	string
	 * 
	 */
	function getApp ($request) {
		if (strstr ($request, '/')) {
			$app = substr ($request, 0, strpos ($request, '/'));
			if (! @file_exists ($this->prefix . '/' . $app)) {
				return $this->app;
			}
			return $app;
		}
		return $this->app;
	}

	/**
	 * Removes the $app name from the $request path.
	 * 
	 * @access	public
	 * @param	string	$request
	 * @param	string	$app
	 * @return	string
	 * 
	 */
	function removeApp ($request, $app = false) {
		if (! $app) {
			$app = $this->app;
		}
		if (strpos ($request, $app . '/') === 0) {
			return preg_replace ('/^' . preg_quote ($app) . '\//', '', $request);
		}
		return $request;
	}

	/**
	 * Gets the current running app -- used by appconf() and appconf_set()
	 * or inside a box or form that needs to know.  Basically returns the top
	 * item on the $apps stack.
	 * 
	 * @access	public
	 * @return	string
	 * 
	 */
	function app () {
		return $this->apps[count ($this->apps) - 1];
	}

	/**
	 * Determines whether the specified app is enabled or disabled.
	 *
	 * @access	public
	 * @param	string $app
	 * @return	boolean
	 *
	 */
	function isEnabled ($app) {
		if (isset ($this->applications[$app]) && ! $this->applications[$app]) {
			// app is disabled
			return false;
		}
		return true;
	}
}

/**
 * Alias of Loader::addPath().
 */
function loader_add_path ($name, $path) {
	return $GLOBALS['loader']->addPath (array ($name => $path));
}

/**
 * Deprecated.
 */
function loader_box_rewrite ($data) {
	return 'foo'; // . $GLOBALS['loader']->boxRewrite ($data);
}

/**
 * Imports the specified library.  This is the default function used to
 * import libraries into your code.
 */
function loader_import ($lib, $method = 'include', $extension = 'php') {
	return $GLOBALS['loader']->import ($lib, $method, $extension);
}

/**
 * Includes the specified library.
 */
function loader_include ($lib, $extension = 'php') {
	return $GLOBALS['loader']->inc ($lib, $extension);
}

/**
 * Requires the specified library.
 */
function loader_require ($lib, $extension = 'php') {
	return $GLOBALS['loader']->req ($lib, $extension);
}

/**
 * Returns the specified library file.
 */
function loader_return ($lib, $extension = 'php') {
	return $GLOBALS['loader']->ret ($lib, $extension);
}

/**
 * Dynamically loads the specified library.
 */
function loader_dl ($lib) {
	return $GLOBALS['loader']->import ($lib, 'dl');
}

/**
 * Calls the specified box.  Alias of Loader::box().
 */
function loader_box ($name, $parameters = array (), $context = 'normal') {
	return $GLOBALS['loader']->box ($name, $parameters, $context);
}

/**
 * Alias of Loader::getBoxSettings().
 */
function loader_box_get_settings ($name, $app) {
	return $GLOBALS['loader']->getBoxSettings ($name, $app);
}

/**
 * Alias of Loader::getBoxAccess().
 */
function loader_box_get_access ($name, $app) {
	return $GLOBALS['loader']->getBoxAccess ($name, $app);
}

/**
 * Alias of Loader::boxAllowed().
 */
function loader_box_allowed ($name, $context = 'normal') {
	return $GLOBALS['loader']->boxAllowed ($name, $context);
}

/**
 * Calls the specified form.  Alias of Loader::form().
 */
function loader_form ($name, $context = 'normal') {
	return $GLOBALS['loader']->form ($name, $context);
}

/**
 * Alias of Loader::getFormSettings().
 */
function loader_form_get_settings ($name, $app) {
	return $GLOBALS['loader']->getFormSettings ($name, $app);
}

/**
 * Alias of Loader::getFormAccess().
 */
function loader_form_get_access ($name, $app) {
	return $GLOBALS['loader']->getFormAccess ($name, $app);
}

/**
 * Alias of Loader::getFormFields().
 */
function loader_form_get_fields ($name) {
	return $GLOBALS['loader']->getFormFields ($name);
}

/**
 * Alias of Loader::formAllowed().
 */
function loader_form_allowed ($name, $context = 'normal') {
	return $GLOBALS['loader']->formAllowed ($name, $context);
}

/**
 * Retrieves a global setting value.
 */
function conf () {
	$args = func_get_args ();
	global $conf;
	if (is_object ($conf)) {
		$return = clone ($conf); // duplicate
	} else {
		$return = $conf;
	}
	foreach ($args as $arg) {
		$return = $return[$arg];
	}
	return $return;
}

/**
 * Sets a global setting value.
 */
function conf_set ($key, $value) {
	global $conf;
	$conf[$key] = $value;
}

/**
 * Retrieves a setting value for the currently active app.
 */
function appconf () {
	$args = func_get_args ();
	global $conf, $loader;
	if (is_object ($conf['App'][$loader->app ()])) {
		$return = clone ($conf['App'][$loader->app ()]); // duplicate
	} else {
		$return = $conf['App'][$loader->app ()];
	}
	foreach ($args as $arg) {
		$return = $return[$arg];
	}
	return $return;
}

/**
 * Sets a setting value for the currently active app.
 */
function appconf_set ($key, $value) {
	global $conf, $loader;
	$conf['App'][$loader->app ()][$key] = $value;
}

/**
 * Loads the default app settings from the conf/settings.ini.php file in
 * the currently active app.  Parses each block like a form's settings.php
 * file, but instead looks for the 'value' setting for each block.  Also
 * parses the value with template_simple() if any {} braces are found in it.
 */
function appconf_default_settings () {
	global $conf, $loader;
	$app = $loader->app ();
	if (! isset ($loader->{'_appconf_loaded_' . $app}) && @file_exists ($loader->prefix . '/' . $app . '/conf/settings.ini.php')) {
		$settings = ini_parse ($loader->prefix . '/' . $app . '/conf/settings.ini.php', true);
		foreach ($settings as $k => $v) {
			if (strpos ($v['value'], '{') !== false && strpos ($v['value'], '}') !== false) {
				$v['value'] = template_simple ($v['value']);
			}
			appconf_set ($k, $v['value']);
		}
	}
	$loader->{'_appconf_loaded_' . $app} = true;
}

/**
 * Returns the currently active app.
 */
function loader_app () {
	return $GLOBALS['loader']->app ();
}

/**
 * Determines whether the specified app is enabled or disabled. Alias of
 * Loader::isEnabled().
 */
function loader_app_enabled ($app) {
	return $GLOBALS['loader']->isEnabled ($app);
}

/**
 * Loads the specified library (parameter 1), and calls the specified static
 * method (parameter 2), with the specified parameters (the rest of the
 * parameters), and returns the result.  This can be used to call a specific
 * method or function in a library in a single line of code, for example:
 *
 * <code>
 * echo loader_call ('saf.Date', 'Date::format', '2004-05-06', 'F jS, Y');
 * </code>
 *
 * @access public
 */
function loader_call () {
	$args = func_get_args ();
	if (! loader_import (array_shift ($args))) {
		return false;
	}

	$code = CLOSE_TAG . OPEN_TAG . ' return ' . array_shift ($args) . ' (';
	$sep = '';
	foreach (array_keys ($args) as $k) {
		$code .= $sep . '$args[' . $k . ']';
		$sep = ', ';
	}
	$code .= '); ' . CLOSE_TAG;

	return @eval ($code);
}

?>
