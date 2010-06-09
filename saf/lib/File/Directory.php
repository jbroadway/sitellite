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
// Dir is basically a wrapper class around PHP's internal Directory
// class which provides a little more functionality.
//

/**
	 * Dir is basically a wrapper class around PHP's internal Directory
	 * class which provides a little more functionality.  It really only defines an
	 * additional method read_all(), which allows you to retrive directory lists
	 * sorted by a number of properties, including alphabetically (by name), by
	 * date (most recently modified first), and by size (largest on top).
	 * 
	 * New in 1.2:
	 * - find () method will search through directories (recusion is optional) for
	 *   files based on patterns, which are parsed by the matches () method.
	 * 
	 * New in 1.4:
	 * - find_in_files() method will search through directories (recursion is
	 *   optional) for files whose contents contain a specified string or pattern,
	 *   which relies on the File class for the content evaluation.
	 * 
	 * New in 1.6:
	 * - build() method will build a directory structure for you recursively if any
	 *   parts of the path do not exist.
	 * - rmdir_recursive() method will remove a directory and all of its contents.
	 *
	 * New in 1.8:
	 * - Added an open() method, so that you can reuse the same Directory object.
	 * 
	 * Note: All the other methods are identical to PHP's internal Directory class.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $dir = new Dir ("/path/to/dir/you/want");
	 * 
	 * // read the directory in the traditional method, where sorting must be
	 * // done yourself...
	 * while ($file = $dir->read ()) {
	 * 	// do something with $file
	 * }
	 * 
	 * $dir->rewind ();
	 * 
	 * // now let's do it with the new read_all() method, and sort by size
	 * $list = $dir->read_all ("size");
	 * foreach ($list as $file) {
	 * 	// do something with $file
	 * }
	 * 
	 * $dir->close ();
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	File
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.8, 2002-08-23, $Id: Directory.php,v 1.5 2008/02/22 09:53:34 lux Exp $
	 * @access	public
	 * 
	 */

class Dir extends Directory {
	/**
	 * Contains the handle of the currently open directory.
	 * 
	 * @access	public
	 * 
	 */
	var $handle = "";

	/**
	 * Contains the path provided to the currently open directory.
	 * 
	 * @access	public
	 * 
	 */
	var $path = "";

	/**
	 * Contains the list of files retrived from the previous read_all()
	 * method call.
	 * 
	 * @access	private
	 * 
	 */
	var $_ls = array ();

	/**
	 * Constructor method.  Opens the specified directory if one is provided.
	 * 
	 * @access	public
	 * @param	string	$dir_string
	 * 
	 */
	function Dir ($dir_string = "") {
		if (! empty ($dir_string)) {
			$this->open ($dir_string);
		}
	}

	/**
	 * Opens the specified directory.
	 * 
	 * @access	public
	 * @param	string	$dir_string
	 * 
	 */
	function open ($dir_string) {
		$this->dir = @dir ($dir_string);
		$this->handle =& $this->dir->handle;
		$this->path =& $this->dir->path;
		if (! $this->dir) {
			$this->error = 'Failed to open directory.';
			return false;
		}
		return true;
	}

	/**
	 * Read and sort the contents of the directory and return them as an array.
	 * 
	 * $sorting_method can be either 'alphabetical' (the default), 'date', or 'size'.
	 * 
	 * Note: read_all() does not remove any entries from this list, such as . and ..
	 * 
	 * @access	public
	 * @param	string	$sorting_method
	 * @return	array
	 * 
	 */
	function readAll ($sorting_method = "alphabetical") {
		if (! $this->handle) {
			return array ();
		}
		$this->_ls = array ();
		while ($file = $this->dir->read ()) {
			array_push ($this->_ls, $file);
		}
		$this->_sort ($sorting_method);
		return $this->_ls;
	}

	/**
	 * Deprecated.  Use readAll() instead.
	 * Exists for backward-compatibility.
	 *
	 */
	function read_all ($sorting_method = "alphabetical") {
		return $this->readAll ($sorting_method);
	}

	/**
	 * Read the contents of the directory and return them one at a time.
	 * 
	 * @access	public
	 * @return	string
	 * 
	 */
	function read () {
		return $this->dir->read ();
	}

	/**
	 * Rewinds the internal cursor for the purpose of re-reading the directory contents.
	 * 
	 * @access	public
	 * 
	 */
	function rewind () {
		return $this->dir->rewind ();
	}

	/**
	 * Closes the current directory.
	 * 
	 * @access	public
	 * 
	 */
	function close () {
		return $this->dir->close ();
	}

	/**
	 * Sorts the contents of the private array $_ls, by the method requested.
	 * 
	 * $method can be 'alphabetical', 'size', or 'date'.
	 * 
	 * @access	private
	 * @param	string	$method
	 * 
	 */
	function _sort ($method) {
		if ($method == "alphabetical") {
			natcasesort ($this->_ls);
			reset ($this->_ls);
		} elseif ($method == "date") {
			for ($i = 0; $i < count ($this->_ls) - 1; $i++) {
				for ($j = $i + 1; $j < count ($this->_ls); $j++) {
					if (filemtime ($this->path . "/" . $this->_ls[$j]) > filemtime ($this->path . "/" . $this->_ls[$i])) {
						$tmp = $this->_ls [$i];
						$this->_ls[$i] = $this->_ls[$j];
						$this->_ls[$j] = $tmp;
					} elseif (filemtime ($this->path . "/" . $this->_ls[$j]) == filemtime ($this->path . "/" . $this->_ls[$i])) {
						// if equal, sort alphabetically
						if ($this->_ls[$j] < $this->_ls[$i]) {
							$tmp = $this->_ls [$i];
							$this->_ls[$i] = $this->_ls[$j];
							$this->_ls[$j] = $tmp;
						}
					}
				}
			}
			reset ($this->_ls);
		} elseif ($method == "size") {
			for ($i = 0; $i < count ($this->_ls) - 1; $i++) {
				for ($j = $i + 1; $j < count ($this->_ls); $j++) {
					if ((! @is_dir ($this->path . "/" . $this->_ls[$j])) && (@is_dir ($this->path . "/" . $this->_ls[$i]))) {
						$tmp = $this->_ls[$i];
						$this->_ls[$i] = $this->_ls[$j];
						$this->_ls[$j] = $tmp;
					} elseif (filesize ($this->path . "/" . $this->_ls[$j]) > filesize ($this->path . "/" . $this->_ls[$i])) {
						$tmp = $this->_ls[$i];
						$this->_ls[$i] = $this->_ls[$j];
						$this->_ls[$j] = $tmp;
					} elseif (filesize ($this->path . "/" . $this->_ls[$j]) == filesize ($this->path . "/" . $this->_ls[$i])) {
						// if equal, sort alphabetically
						if ($this->_ls[$j] < $this->_ls[$i]) {
							$tmp = $this->_ls[$i];
							$this->_ls[$i] = $this->_ls[$j];
							$this->_ls[$j] = $tmp;
						}
					}
				}
			}
			reset ($this->_ls);
		}
	}

	/**
	 * Compares a file name to a pattern string, which may contain
	 * simple * wildcards.
	 * 
	 * @access	public
	 * @param	string	$pattern
	 * @param	string	$file
	 * @return	boolean
	 * 
	 */
	function matches ($pattern = '*', $file) {
		$pattern = str_replace ('.', '\\.', $pattern);
		$pattern = str_replace ('*', '.*', $pattern);
		if (preg_match ("/^$pattern$/i", $file)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Searches a directory or directories (recursively) for a file
	 * that matches a given pattern.
	 * 
	 * @access	public
	 * @param	string	$pattern
	 * @param	string	$basedir
	 * @param	boolean	$recursive
	 * @return	array
	 * 
	 */
	function find ($pattern = '*', $basedir, $recursive = 0) {
		$dir = new Dir ($basedir);
		$res = array ();
		$files = $dir->read_all ();
		//echo "evaluating $basedir...\n";
		foreach ($files as $file) {
			if ($file == '.' || $file == '..') {
				continue;
			}
			//echo "trying file $file...\n";
			if ($dir->matches ($pattern, $file)) {
				array_push ($res, $basedir . '/' . $file);
			}
			//exit;
			if ($recursive && is_dir ($basedir . '/' . $file)) {
				$new_res = Dir::find ($pattern, $basedir . '/' . $file, $recursive);
				// add $new_res to $res
				foreach ($new_res as $n) {
					array_push ($res, $n);
				}
			}
		}
		$dir->close ();
		return $res;
	}

	/**
	 * Searches a directory or directories (recursively) for a file
	 * that contains a given string or pattern.  Note: this method requires
	 * the saf.File class.
	 * 
	 * @access	public
	 * @param	string	$string
	 * @param	string	$basedir
	 * @param	boolean	$recursive
	 * @param	boolean	$regex
	 * @return	array
	 * 
	 */
	function findInFiles ($string, $basedir, $recursive = 0, $regex = 0) {
		$dir = new Dir ($basedir);
		$res = array ();
		$files = $dir->read_all ();
		//echo "evaluating $basedir...\n";
		foreach ($files as $file) {
			if ($file == '.' || $file == '..') {
				continue;
			}
			//echo "trying file $file...\n";
			if (is_file ($basedir . '/' . $file) && File::contains ($string, $regex, $basedir . '/' . $file)) {
				array_push ($res, $basedir . '/' . $file);
			}
			//exit;
			if ($recursive && is_dir ($basedir . '/' . $file)) {
				$new_res = Dir::find_in_files ($string, $basedir . '/' . $file, $recursive, $regex);
				// add $new_res to $res
				foreach ($new_res as $n) {
					array_push ($res, $n);
				}
			}
		}
		$dir->close ();
		return $res;
	}

	/**
	 * Deprecated.  Use findInFiles() instead.
	 * Exists for backward-compatibility.
	 *
	 */
	function find_in_files ($string, $basedir, $recursive = 0, $regex = 0) {
		return $this->findInFiles ($string, $basedir, $recursive, $regex);
	}

	/**
	 * Takes a directory path and builds each level if they don't exist.
	 * For example: calling Dir::build('sitellite/mod/newmodule/pix') while in
	 * the base directory of your site would change directories to sitellite/mod
	 * then call mkdir('newmodule'), chdir('newmodule'), and finally mkdir('pix'),
	 * before decending back to the directory you started in.  Note: This method
	 * does not affect the current working directory.  Also note that the $mode
	 * must be specified as a 4-digit octal value.  See php.net/mkdir for more
	 * information on $mode values.
	 * 
	 * @access	public
	 * @param	string	$path
	 * @param	integer	$mode
	 * @return	boolean
	 */
	function build ($path, $mode = 0700) {
		$list = explode ('/', $path);
		$c = 0;
		foreach ($list as $dir) {
			if (empty ($dir)) {
				continue;
			}
			if (! is_dir ($dir)) {
				$res = @mkdir ($dir, $mode);
				if (! $res) {
					for ($i = 0; $i < $c; $i++) {
						chdir ('..');
					}
					return false;
				}
			}
			chdir ($dir);
			$c++;
		}
		for ($i = 0; $i < $c; $i++) {
			chdir ('..');
		}
		return true;
	}

	/**
	 * Removes a directory and all of its contents.
	 * 
	 * @access	public
	 * @param	string	$path
	 * @return	boolean
	 * 
	 */
	// recursively delete a directory and all of its contents
	function rmdirRecursive ($path = '') {
		$d = dir ($path);
		while ($file = $d->read ()) {
			if ($file != '.' && $file != '..') {
				if (@is_writeable ($path . '/' . $file)) {
					if (@is_dir ($path . '/' . $file) && ! @is_link ($path . '/' . $file)) {
						Dir::rmdirRecursive ($path . '/' . $file);
					} else {
						unlink ($path . '/' . $file);
					}
				} else {
					return 0;
				}
			}
		}
		$d->close ();
		return rmdir ($path);
	}

	/**
	 * Deprecated.  Use rmdirRecursive() instead.
	 * Exists for backward-compatibility.
	 *
	 */
	function rmdir_recursive ($path = '') {
		return $this->rmdirRecursive ($path);
	}

	/**
	 * Fetches all the files in a directory in a single command,
	 * ie. $files = Dir::fetch ('foo/bar');
	 *
	 * @access	public
	 * @param	string	$path
	 * @return	array	files
	 *
	 */
	function fetch ($path, $skipDots = false) {
		$d = new Dir ($path);
		if (! $d) {
			return array ();
		}
		$files = $d->readAll ();

		if ($skipDots) {
			foreach ($files as $k => $v) {
				if (strpos ($v, '.') === 0) {
					unset ($files[$k]);
				}
			}
		}

		//$d->close ();
		return $files;
	}

	/**
	 * Returns the "structure" of the filesystem below the specified path.
	 * This is a list of sub-directories, recursively.
	 *
	 * @access	public
	 * @param	string	$path
	 * @return	array	files
	 *
	 */
	function getStruct ($path) {
		$struct = array ();
		$files = Dir::fetch ($path);
		foreach ($files as $file) {
			if (strpos ($file, '.') === 0 || ! @is_dir ($path . '/' . $file)) {
				continue;
			}
			$struct[] = $path . '/' . $file;
			foreach (Dir::getStruct ($path . '/' . $file) as $file) {
				$struct[] = $file;
			}
		}
		return $struct;
	}
}

/*
// Test code
$dir = new Dir ('.');
while ($file = $dir->read ()) {
	echo $file . "<br />\n";
}
echo "<p>&nbsp;</p>";
$dir->rewind ();
$ls = $dir->read_all ("date");
foreach ($ls as $file) {
	echo $file . " - " . date ("M j, Y h:m:s a", filectime ($dir->path . "/" . $file)) . "<br />\n";
}
echo "<p>&nbsp;</p>";
$dir->rewind ();
$ls = $dir->read_all ("size");
foreach ($ls as $file) {
	echo $file . " - " . filesize ($dir->path . "/" . $file) . "<br />\n";
}
$dir->close ();
*/

?>