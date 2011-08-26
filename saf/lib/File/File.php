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
// File is a simple class to keep basic file information gathering
// structured and readable.
//

/**
	 * File is a simple class to keep basic file information gathering structured
	 * and readable.  Stores everything as a property of the class instead of as methods, so
	 * that it integrates easily with the Template class.
	 * 
	 * New in 1.2:
	 * - Added $filemtime, $gid, $uid, $perms, $filesize, $formatted_perms, $group, and $owner
	 *   properties, and a formatPerms() method.
	 * 
	 * New in 1.4:
	 * - Added setStatus(), getStatus(), and clearStatus() methods.
	 * 
	 * New in 1.6:
	 * - Added a $content property and a contents() method.
	 * 
	 * New in 1.8:
	 * - Happy St. Valentine's day!
	 * - Had a fling with a Windows bug today. :)
	 * 
	 * New in 2.0:
	 * - Removed getStatus(), setStatus(), and clearStatus().  The status functionality
	 *   is now part of saf.App.Versioning.Rev.
	 *
	 * New in 2.2:
	 * - Added overwrite() and append() methods, and functions file_overwrite() and
	 *   file_append() as aliases to them.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $file = new File ('/absolute/path/to/file.txt', '/web/path');
	 * 
	 * // check if the file is a directory
	 * if ($file->is_dir) {
	 * 	// file is a directory
	 * }
	 * 
	 * // output the file's name, size and date it was last modified
	 * echo $file->name;
	 * echo $file->size;
	 * echo $file->last_modified;
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	File
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	2.2, 2003-09-29, $Id: File.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class File {
	/**
	 * The description of the file.
	 * 
	 * @access	public
	 * 
	 */
	var $description = "Unknown Document Type";

	/**
	 * An icon to help describe the file visually.
	 * 
	 * @access	public
	 * 
	 */
	var $icon = "pix/icons/unknown.gif";

	/**
	 * Type can be either 'ascii', 'binary', or 'folder'.  Sitellite
	 * treats files differently depending on their type.
	 * 
	 * @access	public
	 * 
	 */
	var $type = "ascii";

	/**
	 * The file's name.
	 * 
	 * @access	public
	 * 
	 */
	var $name = "";

	/**
	 * The web path to the file, beginning from the document root.
	 * 
	 * @access	public
	 * 
	 */
	var $path = "";

	/**
	 * The file's extension (ie. the extension of 'file.gif' is 'gif').
	 * 
	 * @access	public
	 * 
	 */
	var $extension = "";

	/**
	 * The absolute path to the file, including its name.
	 * 
	 * @access	public
	 * 
	 */
	var $absolute = "";

	/**
	 * The actual size of this file in bytes.
	 * 
	 * @access	public
	 * 
	 */
	var $filesize;

	/**
	 * The size of the file in a formatted string.
	 * 
	 * @access	public
	 * 
	 */
	var $size = "";

	/**
	 * The actual value returned by the filemtime() call on this file.
	 * 
	 * @access	public
	 * 
	 */
	var $filemtime;

	/**
	 * The date and time the file was last modified, in a formatted string.
	 * 
	 * @access	public
	 * 
	 */
	var $last_modified = "";

	/**
	 * The group id of the file.
	 * 
	 * @access	public
	 * 
	 */
	var $gid;

	/**
	 * The user id of the file.
	 * 
	 * @access	public
	 * 
	 */
	var $uid;

	/**
	 * The octal permissions of the file.
	 * 
	 * @access	public
	 * 
	 */
	var $perms;

	/**
	 * The permissions of the file formatted by the formatPerms()
	 * method.
	 * 
	 * @access	public
	 * 
	 */
	var $formatted_perms;

	/**
	 * The group name of the file.  Not available if PHP is running
	 * in safe_mode.
	 * 
	 * @access	public
	 * 
	 */
	var $group = '';

	/**
	 * The owner name of the file.  Not available if PHP is running
	 * in safe_mode.
	 * 
	 * @access	public
	 * 
	 */
	var $owner = '';

	/**
	 * Determines whether or not this file is writeable by the
	 * current script.
	 * 
	 * @access	public
	 * 
	 */
	var $is_writeable = false;

	/**
	 * Determines whether or not this file is a directory.
	 * 
	 * @access	public
	 * 
	 */
	var $is_dir = false;

	/**
	 * Stores the content of the file after a call to contents().
	 * 
	 * @access	public
	 * 
	 */
	var $content = false;

	/**
	 * Constructor method.  $abs_file_path is the absolute directory path
	 * to the file, including its name.  $www_file_path is the path from the web
	 * document root to same, minus the file name.
	 * 
	 * @access	public
	 * @param	string	$abs_file_path
	 * @param	string	$www_file_path
	 * 
	 */
	function File ($abs_file_path = '', $www_file_path = '') {
		$this->absolute = $abs_file_path;
		if (@is_dir ($abs_file_path)) {
			$this->extension = 'folder';
		} elseif (preg_match ("/\.([^\.]+)$/", $abs_file_path, $regs)) {
			$this->extension = $regs[1];
		}
		if (preg_match ("/([^\\/]+)$/", $abs_file_path, $regs)) {
			$this->name = $regs[1];
		}

		// we gather all the info now into variables instead of methods, that way
		// they are accessible using our template system.
		$this->path = $www_file_path;
		$this->get_mime ();
		$this->filesize = filesize ($this->absolute);
		$this->size = $this->format_filesize ($this->filesize);
		$this->filemtime = filemtime ($this->absolute);
		$this->last_modified = date ("M j, Y h:m:s a", $this->filemtime);
		$this->is_writeable = @is_writeable ($this->absolute);
		$this->is_dir = @is_dir ($this->absolute);

		$this->gid = filegroup ($this->absolute);
		$this->uid = fileowner ($this->absolute);
		$this->perms = fileperms ($this->absolute);
		$this->formatted_perms = $this->formatPerms ($this->perms);
		if (! ini_get ('safe_mode')) {
			if (function_exists ('posix_getgrgid')) {
				$this->group = posix_getgrgid ($this->gid);
				$this->group = $this->group['name'];
			} else {
				$this->group = 'unknown';
			}
			if (function_exists ('posix_getpwuid')) {
				$this->owner = posix_getpwuid ($this->uid);
				$this->owner = $this->owner['name'];
			} else {
				$this->owner = 'unknown';
			}
		}
	}

	/**
	 * Formats the size of the file into a string appropriate for human consumption.
	 * 
	 * @access	public
	 * @param	integer	$size
	 * @return	string
	 * 
	 */
	function format_filesize ($size = 0) {
		if (@is_dir ($this->absolute)) {
			return "-";
		}
		if ($size >= 1073741824) {
			return round ($size / 1073741824 * 10) / 10 . " Gb";
		} elseif ($size >= 1048576) {
			return round ($size / 1048576 * 10) / 10 . " Mb";
		} elseif ($size >= 1024) {
			return round ($size / 1024) . " Kb";
		} else {
			return $size . " b";
		}
	}

	/**
	 * Gets the file's mime data from the database.  Sets the values for
	 * the following properties: description, icon and type.
	 * 
	 * $db is an optional Database object.  If not supplied, File will look
	 * for a Database object called $db in the global namespace.
	 * 
	 * @access	private
	 * @param	object	$db
	 * 
	 */
	function get_mime ($db = "") {
		if (! empty ($db)) {
			$q = $db->query ("select * from sitellite_mime_type where extension = ??");
		} else {
			$q = $GLOBALS["db"]->query ("select * from sitellite_mime_type where extension = ??");
		}
		if (($q->execute ($this->extension)) && ($q->rows () > 0)) {
			$mime = $q->fetch ();
			$q->free ();
			$this->description = $mime->description;
			$this->icon = $mime->icon;
			$this->type = $mime->type;
		}
		// return $mime;
	}

	/**
	 * Returns the contents of the file as a string.  If the $file is
	 * not specified, it uses the $absolute property of the current File
	 * object.  Also stores it in the $content property so you don't have
	 * to read it from the file system twice.
	 * 
	 * @access	public
	 * @param	string	$file
	 * @return	string
	 * 
	 */
	function contents ($file = '') {
		if (! empty ($file)) {
			// use $file
			$filename = $file;
		} else {
			// use $this->absolute
			$filename = $this->absolute;
		}
		$this->content = @join ('', @file ($filename));
		return $this->content;
	}

	/**
	 * Opens a file and returns whether or not it contains a specified
	 * string or pattern.  Can be called using the File::contains syntax, as
	 * well as through an instantiated object.
	 * 
	 * @access	public
	 * @param	string	$string
	 * @param	boolean	$regex
	 * @param	string	$file
	 * @param	boolean $count
	 * @param	string	$filter
	 * @return	boolean
	 * 
	 */
	function contains ($string, $regex = 0, $file = '', $count = false, $filter = false) {
		if (! empty ($file)) {
			// use $file
			$filename = $file;
		} else {
			// use $this->absolute
			$filename = $this->absolute;
		}
		//$fh = fopen ($filename, 'r');
		$filedata = @join ('', @file ($filename));

		if ($filter !== false && function_exists ($filter)) {
			$filedata = $filter ($filedata);
		}

		if (! is_array ($string)) {
			$string = array ($string);
		}

		if (! $count) {
			if ($regex) {
				foreach ($string as $str) {
					if (preg_match ('/' . $str . '/', $filedata)) {
						return true;
					}
				}
				return false;
			} else {
				foreach ($string as $str) {
					if (stristr ($filedata, $str)) {
						return true;
					}
				}
				return false;
			}
		} else {
			$c = 0;
			if ($regex) {
				foreach ($string as $str) {
					$pieces = preg_split ('/' . $str . '/i', $filedata);
					$c += count ($pieces) - 1;
				}
			} else {
				foreach ($string as $str) {
					$pieces = preg_split ('/' . preg_quote ($str, '/') . '/i', $filedata);
					$c += count ($pieces) - 1;
				}
			}
			return $c;
		}

		/*while (! feof ($fh)) {
			//echo "reading line...\n";
			$line = fgets ($fh, 4096);
			if ($regex) {
				if (preg_match ('/' . $string . '/', $line)) {
					fclose ($fh);
					if ($count) {
						$c++;
					} else {
						return true;
					}
				}
			} else {
				if (stristr ($line, $string)) {
					fclose ($fh);
					if ($count) {
						$c++;
					} else {
						return true;
					}
				}
			}
		}
		fclose ($fh);
		return false;*/
	}

	/**
	 * Reads an octal mode value and returns a string of the
	 * format -rwxrwxrwx, similar to what is returned by an "ls -al"
	 * command on a Unix command prompt.  This method was borrowed
	 * from the user-contributed notes at http://www.php.net/fileperms
	 * 
	 * @access	public
	 * @param	integer	$mode
	 * @return	string
	 * 
	 */
	function formatPerms ($mode) {
		/* Determine Type */
		if (($mode & 0xC000) === 0xC000) { // Unix domain socket
			$type = 's';
		} elseif (($mode & 0x4000) === 0x4000) { // Directory
			$type = 'd';
		} elseif (($mode & 0xA000) === 0xA000) { // Symbolic link
			$type = 'l';
		} elseif (($mode & 0x8000) === 0x8000) { // Regular file
			$type = '-';
		} elseif (($mode & 0x6000) === 0x6000) { // Block special file
			$type = 'b';
		} elseif (($mode & 0x2000) === 0x2000) { // Character special file
			$type = 'c';
		} elseif (($mode & 0x1000) === 0x1000) { // Named pipe
			$type = 'p';
		} else { // Unknown
			$type = '?';
		}

		/* Determine permissions */
		$owner["read"] = ($mode & 00400) ? 'r' : '-';
		$owner["write"] = ($mode & 00200) ? 'w' : '-';
		$owner["execute"] = ($mode & 00100) ? 'x' : '-';
		$group["read"] = ($mode & 00040) ? 'r' : '-';
		$group["write"] = ($mode & 00020) ? 'w' : '-';
		$group["execute"] = ($mode & 00010) ? 'x' : '-';
		$world["read"] = ($mode & 00004) ? 'r' : '-';
		$world["write"] = ($mode & 00002) ? 'w' : '-';
		$world["execute"] = ($mode & 00001) ? 'x' : '-';

		/* Adjust for SUID, SGID and sticky bit */
		if ( $mode & 0x800 ) {
			$owner["execute"] = ($owner[execute]=='x') ? 's' : 'S';
		}
		if ( $mode & 0x400 ) {
			$group["execute"] = ($group[execute]=='x') ? 's' : 'S';
		}
		if ( $mode & 0x200 ) {
			$world["execute"] = ($world[execute]=='x') ? 't' : 'T';
		}

		$out = '';
		$out .= sprintf("%1s", $type);
		$out .= sprintf("%1s%1s%1s", $owner[read], $owner[write], $owner[execute]);
		$out .= sprintf("%1s%1s%1s", $group[read], $group[write], $group[execute]);
		$out .= sprintf("%1s%1s%1s\n", $world[read], $world[write], $world[execute]);
		return $out;
	}

	/**
	 * Overwrite the specified file (or the file contained in the $absolute
	 * property) with the specified $data.
	 *
	 * @access public
	 * @param string
	 * @param string
	 * @return boolean
	 */
	function overwrite ($data, $file = '') {
		if (! empty ($file)) {
			// use $file
			$filename = $file;
		} else {
			// use $this->absolute
			$filename = $this->absolute;
		}

		$fh = @fopen ($filename, 'wb');
		if (! $fh) {
			return false;
		}
		fwrite ($fh, $data);
		fclose ($fh);
		return true;
	}

	/**
	 * Append to the specified file (or the file contained in the $absolute
	 * property) with the specified $data.
	 *
	 * @access public
	 * @param string
	 * @param string
	 * @return boolean
	 */
	function append ($data, $file = '') {
		if (! empty ($file)) {
			// use $file
			$filename = $file;
		} else {
			// use $this->absolute
			$filename = $this->absolute;
		}

		$fh = @fopen ($filename, 'ab');
		if (! $fh) {
			return false;
		}
		fwrite ($fh, $data);
		fclose ($fh);
		return true;
	}

	/**
	 * Select a random file from the specified directory.  The $ext is
	 * an optional extension, or array of extensions, to limit the
	 * selection to.  Also automatically eliminates dot-files and folders.
	 *
	 * @access public
	 * @param string
	 * @param mixed
	 * @return string
	 */
	function rand ($path, $ext = false) {
		if ($ext !== false && ! is_array ($ext)) {
			$ext = array ($ext);
		}

		$dir = opendir ($path);
		if (! $dir) {
			return false;
		}

		$list = array ();

		while (false !== ($file = readdir ($dir))) {
			if (@is_dir ($path . '/' . $file) || strpos ($file, '.') === 0) {
				continue;
			} elseif ($ext && ! preg_match ('/\.(' . join ('|', $ext) . ')$/i', $file)) {
				continue;
			}
			$list[] = $file;
		}

		return $list[array_rand ($list)];
	}

	/**
	 * Selects the first file that exists from the specified list.
	 * This can be used to cascade possible choices for displaying
	 * images, including templates, or other context-sensitive
	 * data.
	 *
	 * @access public
	 * @param array
	 * @param string
	 * @return string
	 */
	function determine ($list, $path = false) {
		if (! $path) {
			$path = '';
		} else {
			$path .= '/';
		}
		foreach ($list as $file) {
			if (@file_exists ($path . $file)) {
				return $file;
			}
		}
		return false;
	}
}

/**
 * Overwrite the specified file (or the file contained in the $absolute
 * property) with the specified $data.
 *
 * Please note that here the parameters are reversed from how they
 * appear in the File class, because the $file parameter is required.
 * This function does not operate on a global instance of File, as
 * some other functions of its type do.
 *
 * @access public
 * @param string
 * @param string
 * @return boolean
 */
function file_overwrite ($file, $data) {
	return File::overwrite ($data, $file);
}

/**
 * Append to the specified file (or the file contained in the $absolute
 * property) with the specified $data.
 *
 * Please note that here the parameters are reversed from how they
 * appear in the File class, because the $file parameter is required.
 * This function does not operate on a global instance of File, as
 * some other functions of its type do.
 *
 * @access public
 * @param string
 * @param string
 * @return boolean
 */
function file_append ($file, $data) {
	return File::append ($data, $file);
}

/**
 * Alias of File::rand().  Returns a random file from the specified
 * directory.
 *
 * @access public
 * @param string
 * @param mixed
 * @return string
 */
function file_rand ($path, $ext = false) {
	return File::rand ($path, $ext);
}

/**
 * Alias of File::determine().  Returns the first file that exists
 * from the specified list.
 *
 * @access public
 * @param array
 * @param string
 * @return string
 */
function file_determine ($list, $path = false) {
	return File::determine ($list, $path);
}

/* // Test code
$file = new File (getcwd () . '/' . "File.php");
if ($file->is_writeable) {
	echo "writes...";
} else {
	echo "no write...";
}
echo $file->last_modified ();
*/

/*
header ('Content-type: text/plain');
echo File::rand ('../../../inc/html/default', array ('tpl', 'spt'));
*/

/*
header ('Content-type: text/plain');
echo File::determine (
	array (
		'html.wide.tpl',
		'html.default.tpl',
	),
	'../../../inc/html/default'
);
*/

?>