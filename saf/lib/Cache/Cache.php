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
// Cache provides a simple and lightweight system for caching dynamic web
// pages.
//

/**
	 * Cache provides a simple and lightweight system for caching dynamic web
	 * pages.  Offers ordinary filesystem caching, as well as caching to Berkeley
	 * Databases (BDB) (good for persistent objects), and optionally to a proxy server.
	 * In the proxy server situation, Cache does not actually store the data, instead,
	 * Cache simply passes an HTTP Expires header, setting a time for the proxy server
	 * to cache for us.  Can optionally use the saf.File.Store package to improve the
	 * performance of filesystem-based caching on large volume stores.
	 * 
	 * New in 1.2:
	 * - Added a shutdown() method.
	 *
	 * New in 1.4:
	 * - New logic in is_cacheable() allows subsequent rules to override previous ones.
	 * 
	 * New in 1.6:
	 * - Added memcache support (requires PHP's memcache extension). Just set the dir
	 *   to "memcache:servername:portnum". The memcached keys are the URI and the values
	 *   are uncompressed HTML.
	 *
	 * <code>
	 * <?php
	 *
	 * loader_import ("saf.Cache");
	 *
	 * $cache = new Cache ("cache");
	 * $uri = $_SERVER["REQUEST_URI"];
	 * 
	 * // set cache to expire every 15 minutes
	 * if ($cache->expired ($uri, 900)) {
	 * 	// re-cache the page
	 * 	ob_start ();
	 * 
	 * 	// go about creating the page
	 * 
	 * 	// now we need to send the data to the cache file
	 * 	// as well as to the visitor
	 * 	$data = ob_get_contents ();
	 * 	ob_end_clean ();
	 * 	$cache->file ($uri, $data);
	 * 	echo $data;
	 * } else {
	 * 	// show the cached version
	 * 	echo $cache->show ($uri);
	 * }
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	Cache
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.2, 2003-06-28, $Id: Cache.php,v 1.6 2008/05/22 21:16:28 cbrunet Exp $
	 * @access	public
	 * 
	 */

class Cache {
	/**
	 * Contains the path to the directory used to store the cached pages.
	 * 
	 * @access	public
	 * 
	 */
	var $dir;

	/**
	 * Stores whether the URI was determined cacheable.
	 * 
	 * @access	public
	 * 
	 */
	var $set;

	/**
	 * Contains the error message if an error occurs.
	 * 
	 * @access	public
	 * 
	 */
	var $error;

	/**
	 * The length of time to store cached objects before expiring them.
	 *
	 * @access	public
	 *
	 */
	var $duration = 3600;

	/**
	 * Constructor method.  Defines the directory Cache will use to store pages.
	 * If the string 'mod:proxy' is given instead, Cache will know not to cache the data
	 * locally, but rather to simply pass an HTTP Expires header for the proxy server to
	 * do the work.  If $dir is a string beginning with 'bdb:', Cache will store the data
	 * in a Berkeley Database identified by the rest of the value of $dir.  If the $dir
	 * string begins with 'store:', Cache will use the saf.File.Store package to store
	 * cached documents, which is recommended over the default file handling for caches
	 * with a larger number of documents.  See that package for more details.
	 * 
	 * @access	public
	 * @param	string	$dir
	 * @param	integer $duration
	 * 
	 */
	function Cache ($dir = '', $duration = 3600) {
		$this->dir = $dir;
		$this->duration = $duration;
		if (preg_match ('/^memcache:(.+)/', $dir, $regs)) {
			if (! class_exists ('Memcache')) {
				$this->set = false; // you need the php memcache extension
				return;
			}
			if (preg_match ('/(.+):(.+)$/', $regs[1], $r2)) {
				$server = $r2[1];
				$port = $r2[2];
			} else {
				$server = $regs[1];
				$port = 11211;
			}
			$this->memcache = new Memcache;
			if (! $this->memcache->connect ($server, $port)) {
				$this->set = false; // failed to connect
				return;
			}
		} elseif (preg_match ('/^bdb:(.+)/', $dir, $regs)) {
			if (preg_match ('/(.+):(.+)$/', $regs[1], $r2)) {
				$db = $r2[2];
				$regs[1] = $r2[1];
			} else {
				$db = 'db3';
			}
			global $loader;
			$loader->import ('saf.Database.BDB');
			$this->bdb = new BDB (getcwd () . '/' . $regs[1], 'w', $db, 0);
			if (! $this->bdb->connection) {
				echo '<p>Cache BDB creation failed: ' . $regs[1] . '</p>';
			}
		} elseif (preg_match ('/^store:(.+)/', $dir, $regs)) {
			if (preg_match ('/(.+):([a-zA-Z0-9_-]+)$/', $regs[1], $r)) {
				$path = $r[1];
				$table = $r[2];
			} else {
				$path = $regs[1];
				$table = false;
			}
			global $loader;
			$loader->import ('saf.File.Store');
			$this->fs = new FileStore ($path);
			$this->fs->autoInit = true;
			$this->fs->useMD5 = true;
			if ($table !== false) {
				$this->fs->dbTable = $table;
			}
		}
		$this->set = false;
	}

	/**
	 * Sets the number of characters to ignore when using the 'store:' data
	 * store.  This allows for better distribution of files across folders by
	 * ignoring a specified number of characters at the beginning of the file name.
	 * Note that the serialize() method will be called before the characters are
	 * evaluated, so any non-valid characters will be stripped out and should not
	 * be counted in this number.
	 * 
	 * @access	public
	 * @param	integer	$ignore
	 * 
	 */
	function setIgnoreChars ($ignore = 0) {
		$this->fs->ignoreChars = $ignore;
	}

	/**
	 * Takes the string provided (which is usually the global $REQUEST_URI,
	 * and returns a version of it without illegal characters so it can be used as a
	 * file name to store a cached page.
	 * 
	 * @access	public
	 * @param	string	$file
	 * @return	string
	 * 
	 */
	function serialize ($file = '') {
		if (intl_lang () != intl_default_lang ()) {
			$file .= '_' . intl_lang ();
		}
		return preg_replace ("/[\/\\\?&%#~:]/", '', $file);
	}

	/**
	 * Takes a string (to serialize into a file name) and page data to be
	 * cached, and creates a cached file that can be recalled later.  Also serves as
	 * the caching call in BDB and proxy server caching situations.  Note: For BDB
	 * data stores, you will want to call $cache_obj->bdb->close (); after you have
	 * finished caching, so that BDB doesn't lock the database on you for the next
	 * user.
	 * 
	 * @access	public
	 * @param	string	$file
	 * @param	string	$data
	 * @return	boolean
	 * 
	 */
	function file ($file = '', $data = '') {
		if (is_object ($this->memcache)) {
			if (! $this->memcache->replace ($file, $data, 0, $this->duration)) {
				if (! $this->memcache->set ($file, $data, 0, $this->duration)) {
					$this->error = 'Failed to set memcache for key: ' . $file;
					return false;
				}
			}
		} elseif ($this->dir == 'mod:proxy') {
			// proxy caching, simply adds Expires header to data sent
			// in this case, file may contain the expiry time instead
			$mod_time = gmdate ('D, d M Y H:i:s', mktime () + SITELLITE_CACHE_DURATION) . ' GMT';
			header ('Expires: ' . $mod_time);
			header ('Last-Modified: ' . gmdate ('D, d M Y H:i:s', mktime ()) . ' GMT');
			header ('Content-Length: ' . strlen ($data));
		} elseif (is_object ($this->bdb)) {
			// $regs[1] is the bdb name
			if ($this->bdb->exists ($file)) {
				if (! $this->bdb->replace ($file, $data)) {
					$this->error = 'Cache update failed on file: ' . $file;
					return false;
				}
			} else {
				if (! $this->bdb->insert ($file, $data)) {
					$this->error = 'Cache insert failed on file: ' . $file;
					return false;
				}
			}
			// what about expiry?!

		} elseif (is_object ($this->fs)) {
			$file = $this->serialize ($file);
			$p = $this->fs->getPath ($file);
			if ($p === false) {
				$this->error = $this->fs->error;
				return false;
			}
			if (! $this->fs->put ($file, $data)) {
				$this->error = $this->fs->error;
				return false;
			}

		} else {
			if (empty ($file)) {
				$this->error = 'No filename specified';
				return false;
			}
			$file = $this->serialize ($file);
			if (! $fp = fopen ($this->dir . '/' . $file, 'wb')) {
				$this->error = 'Failed to open file: ' . $file;
				return false;
			}
			@flock ($fp, LOCK_EX);
			fwrite ($fp, $data);
			@flock ($fp, LOCK_UN);
			fclose ($fp);
		}
		return true;
	}

	/**
	 * Takes a string (to serialize into a file name) and a duration (the number
	 * of seconds to keep the cached file for), and returns whether or not the file has
	 * expired.  True means it has.
	 * 
	 * @access	public
	 * @param	string	$file
	 * @param	integer	$duration
	 * @return	boolean
	 * 
	 */
	function expired ($file = '', $duration = 0) {
		if (@file_exists ('cache/.flushed')) {
			$flushed = filemtime ('cache/.flushed');
		} else {
			$flushed = 0;
		}

		if (is_object ($this->memcache)) {
			if ($this->memcache->get ($file)) {
				return false;
			}
			return true;
		} elseif ($this->dir == 'mod:proxy') {
			$file = SITELLITE_CACHE_PROXY_STORE . '/PROXY_' . $this->serialize ($file);
			//echo "$file\n";
			if (
					(! empty ($GLOBALS['HTTP_IF_MODIFIED_SINCE'])) &&
					($mod_time = filemtime ($file))
				) {
				//echo "if-modified-since: " . $GLOBALS['HTTP_IF_MODIFIED_SINCE'] . " and $mod_time all good\n";
				if ((mktime () - $mod_time) > SITELLITE_CACHE_DURATION || strtotime ($GLOBALS['HTTP_IF_MODIFIED_SINCE']) <= $flushed) {
					touch ($file);
					$mod_time = mktime ();
				}
				$gmt_mtime = gmdate ('D, d M Y H:i:s', $mod_time) . ' GMT';
				//echo "expired, so we generate a new time: $gmt_mtime\n";
				if ($GLOBALS['HTTP_IF_MODIFIED_SINCE'] == $gmt_mtime) {
					//echo "send 'em a 304\n";
					if (preg_match ('/^WIN/i', PHP_OS)) {
						header ('Status: 304 Not Modified');
					} else {
						header ('HTTP/1.1 304 Not Modified');
					}
					exit;
				} else {
					touch ($file);
					//echo "modified since - touch the file and give 'em a freshy\n";
					return true;
				}
			} else {
				touch ($file);
				//echo "no header or no file - touch the file and give 'em a freshy\n";
				return true;
			}

//			$mod_time = gmdate ('D, d M Y H:i:s', mktime () + SITELLITE_CACHE_DURATION) . ' GMT';

			// let's see what we've got to compare first
			//echo '<pre>';
			//echo $mod_time . "\n";
			//echo $GLOBALS['HTTP_IF_MODIFIED_SINCE'];
			//echo '</pre>';
			//
/*
			// Make sure If-Modified-Since requests get handled properly
			if (
					(! empty ($GLOBALS['HTTP_IF_MODIFIED_SINCE']))
		//			&&
			//		(strtotime ($GLOBALS['HTTP_IF_MODIFIED_SINCE']) <= mktime ())
				) {

				$gmt_mtime = gmdate ('D, d M Y H:i:s', mktime ()) . ' GMT';
				echo 'cached : ' . $GLOBALS['HTTP_IF_MODIFIED_SINCE'] . ' ' . strtotime ($GLOBALS['HTTP_IF_MODIFIED_SINCE']) . ' ' . mktime () . ' ' . $gmt_mtime;
				if (eregi ('^WIN', PHP_OS)) {
					header ('Status: 304 Not Modified');
				} else {
					header ('HTTP/1.1 304 Not Modified');
				}
				exit;

			} else {

				//echo $GLOBALS['HTTP_IF_MODIFIED_SINCE'] . ' | ' . $mod_time;
				//echo strtotime ($GLOBALS['HTTP_IF_MODIFIED_SINCE']);
				//echo mktime ();
				//echo 'expired';
				return true;

			}
*/

		} elseif (is_object ($this->fs)) {
			$file = $this->serialize ($file);
			$p = $this->fs->getPath ($file);
			if ($p === false) {
				$this->error = $this->fs->error;
				return true; // regenerate on error, there's an issue with the cache
			}
			if (! @file_exists ($p)) {
				return true;
			}
			$fm = filemtime ($p);
			if ($fm < time () - $duration || $fm <= $flushed) {
				return true;
			}
			return false;

		} else {
			if (empty ($file)) { return false; }
			$file = $this->serialize ($file);
			if (! @file_exists ($this->dir . '/' . $file)) {
				return true;
			}
			$fm = filemtime ($this->dir . '/' . $file);
			if ($fm < time () - $duration || $fm <= $flushed) {
				return true;
			}
			return false;
		}
	}

	/**
	 * Cause a single cached file to expire so it can be regenerated on the next
	 * request for it.
	 *
	 * @access	public
	 * @param	string $file
	 *
	 */
	function expire ($file) {
		if (is_object ($this->memcache)) {
			return $this->memcache->delete ($file);
		} elseif (! is_object ($this->bdb) && $this->dir != 'mod:proxy') {
			if (is_object ($this->fs)) {
				$f = $this->serialize ($file);
				$p = $this->fs->getPath ($f);
				if ($p === false) {
					return;
				}
				if (@file_exists ($p)) {
					@unlink ($p);
				}
			} else {
				$f = $this->serialize ($file);
				if (@file_exists ($this->dir . '/' . $f)) {
					@unlink ($this->dir . '/' . $f);
				}
			}
		}
	}

	/**
	 * Takes a string (to serialize into a file name), and returns its contents
	 * in a string (for the purpose of passing on to the visitor).
	 * 
	 * @access	public
	 * @param	string	$file
	 * @return	string
	 * 
	 */
	function show ($file = '') {
		$charset = intl_charset ();
		if (! empty ($charset)) {
			header ('Content-Type: text/html; charset=' . intl_charset ());
		}
		if (is_object ($this->memcache)) {
			return $this->memcache->get ($file);
		} elseif (is_object ($this->bdb)) {
			// use bdb
			if ($this->bdb->exists ($file)) {
				return $this->bdb->fetch ($file);
			}

		} elseif ($this->dir == 'mod:proxy') {
			//

		} elseif (is_object ($this->fs)) {
			$file = $this->serialize ($file);
			return $this->fs->get ($file);

		} else {
			// use the fs
			if (empty ($file)) { return ''; }
			$file = $this->serialize ($file);
			if (@file_exists ($this->dir . '/' . $file)) {
				return join ('', file ($this->dir . '/' . $file));
			}
		}
	}

	/**
	 * Checks a URI against a list of cacheable pages, and returns a true/false
	 * as to whether or not that URI is cacheable.  Pages in the cacheable list may
	 * include a wildcard (*) character to imply any number of characters.
	 * 
	 * @access	public
	 * @param	string	$uri
	 * @param	array	$list
	 * @return	boolean
	 * 
	 */
	function is_cacheable ($uri, $list) {
		if (php_sapi_name () == 'cli') {
			return false;
		}

		if (isset ($_COOKIE['sitellite_session_id'])) {
			return false;
		}

		if ($_SERVER['REQUEST_METHOD'] != 'GET') {
			return false;
		}

		$this->set = false;
		foreach ($list as $page => $rule) {
			//echo "Evaluating rule: " . $page . "<br />\n";
			if (
					($uri == $page) ||
					($uri == '/' . $page) ||
					($uri == '/index/' . $page)
				) {
				//echo "Standard match.<br />\n";
				if ($rule) {
					//echo "T<br />\n";
					$this->set = true;
				} else {
					//echo "F<br />\n";
					$this->set = false;
				}
			} elseif (
					(strpos ($page, '*') !== false) &&
					(preg_match ('|^' . str_replace ('*', '.*', $page) . '$|', $uri))
			) {
				//echo "Regexp match.<br />\n";
				if ($rule) {
					//echo "T<br />\n";
					$this->set = true;
				} else {
					//echo "F<br />\n";
					$this->set = false;
				}
			}
		}
		return $this->set;
	}

	/**
	 * Performs any possible shutdown logic, depending on the cache
	 * storage method.
	 * 
	 * @access	public
	 * 
	 */
	function shutdown () {
		// The all important $bdb->close () call,
		// just in case you're using BDB to handle caching
		if (is_object ($this->memcache)) {
			$this->memcache->close ();
		} elseif (is_object ($this->bdb)) {
			$this->bdb->close ();
		}
	}

	/**
	 * Flush the entire cache at once so that all files will be regenerated
	 * on their next request.
	 *
	 * @access	public
	 *
	 */
	function clear () {
		if (is_object ($this->memcache)) {
			return $this->memcache->flush ();
		}
		return @touch ('cache/.flushed');
	}
}



?>
