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
// FileStore implements a directory layout that allows for higher
// performance filesystem access on very large document collections.
//


/**
	 * FileStore implements a directory layout that allows for higher
	 * performance filesystem access on very large document collections.
	 * 
	 * FileStore may optionally use a database table to maintain a list of
	 * files in the repository, since an 'ls' command wouldn't be efficient.
	 * However, the database table must be created beforehand in order to
	 * be in sync with all of the files in the repository, since FileStore
	 * will not synchronize existing files.
	 * 
	 * The schema for such a table is:
	 * 
	 * CREATE TABLE table_name (
	 *   filename char(255) not null unique primary key
	 * );
	 * 
	 * To create a new FileStore table, copy this into the SQL Shell module,
	 * change the name, and execute it.  That's all there is to it.
	 *
	 * New in 1.2:
	 * - Added move() and copy() commands, for dealing with pre-existing files.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $fs = new FileStore ('cache');
	 * 
	 * $fname = 'some_file.txt';
	 * $fdata = 'fake data...';
	 * 
	 * if (! $fs->exists ($fname)) {
	 * 	if ($fs->write ($fname, $fdata)) {
	 * 		// written
	 * 	} else {
	 * 		echo $fs->error; // failed
	 * 	}
	 * } else {
	 * 	// file already exists, but we don't want to overwrite it
	 * }
	 * 
	 * $fh = $fs->open ($fname);
	 * if ($fh) {
	 * 	// $fh is an ordinary file handler
	 * 	$data = fread ($fh);
	 * 	fclose ($fh);
	 * }
	 * 
	 * if ($fs->remove ($fname)) {
	 * 	// deleted
	 * } else {
	 * 	$fs->error; // delete failed
	 * }
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	File
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.2, 2003-10-26, $Id: Store.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class FileStore {
	

	/**
	 * The path to the root of the file store.  Default
	 * is empty.
	 * 
	 * @access	public
	 * 
	 */
	var $path = '';

	/**
	 * The number of levels deep the directory structure
	 * should be set.  Defaults to 2, and past that is likely
	 * very unnecessary.  Consider: A depth of 2 creates 3844
	 * directories and stores up to about 1.8 million files, and a
	 * depth of 3 creates 238,328 directories and stores up to about
	 * 120 million files (theoretically).
	 * 
	 * @access	public
	 * 
	 */
	var $depth = 2; // past 2 is probably unnecessary (2 = 3844 dirs, 3 = 238328 dirs!)

	/**
	 * The number of characters to ignore at the beginning
	 * of file names.  Defaults to 0.  This is useful when you have
	 * a collection of files that all use the same naming prefix,
	 * so that the files are still evenly distributed within the
	 * file storage system.
	 * 
	 * @access	public
	 * 
	 */
	var $ignoreChars = 0;

	/**
	 * If the file names are all very similar, and
	 * $ignoreChars won't solve this, then it may be useful
	 * to set $useMD5 to true (defaults to false), which makes
	 * FileStore take an MD5 string of the file name and stores
	 * it as if it was named after the MD5 string.  This
	 * should produce a consistently random distribution of
	 * files, but renders the file system unviewable (or at
	 * least not easily interpreted) by other means.
	 * 
	 * @access	public
	 * 
	 */
	var $useMD5 = false;

	/**
	 * $autoInit determines whether you want to create the
	 * entire directory structure at once or to do so incrementally
	 * as new files are stored.  This comes down to whether you
	 * care about the extra mkdir() calls on file creation, or
	 * would rather call init() once at the beginning and have it
	 * do its thing for 5-10 minutes.  Defaults to false.
	 * 
	 * @access	public
	 * 
	 */
	var $autoInit = false;

	/**
	 * Sets the directory mode to use when calling mkdir()
	 * within buildDirs() and initDir().  Default is 0755.
	 * 
	 * @access	public
	 * 
	 */
	var $dirMode = 0755;

	/**
	 * Sets the write mode to use in the put() method.
	 * Default is 'wb'.
	 * 
	 * @access	public
	 * 
	 */
	var $writeMode = 'wb';

	/**
	 * Sets the write mode to use in the get() method.
	 * Default is 'rb'.
	 * 
	 * @access	public
	 * 
	 */
	var $readMode = 'rb';

	/**
	 * Sets the write mode to use in the append() method.
	 * Default is 'ab'.
	 * 
	 * @access	public
	 * 
	 */
	var $appendMode = 'ab';

	/**
	 * If the $dbTable is set, FileStore will use the
	 * specified database table to maintain a list of all of
	 * the files in the repository, which can be retrieved
	 * via the listAll() method.  Note: The table must be created
	 * prior to the storage repository containing any files,
	 * as FileStore does not synchronize existing files.  See
	 * above for an example 'CREATE TABLE' SQL command.
	 * 
	 * @access	public
	 * 
	 */
	var $dbTable = false;

	/**
	 * Contains the error message if one occurs within
	 * FileStore.
	 * 
	 * @access	public
	 * 
	 */
	var $error;

	

	/**
	 * Constructor method.
	 * 
	 * @access	public
	 * @param	string	$path
	 * @param	integer	$ignoreChars
	 * 
	 */
	function FileStore ($path = '', $ignoreChars = 0) {
		$this->path = $path;
		$this->ignoreChars = $ignoreChars;
	}

	/**
	 * Initializes the storage location by ensuring that
	 * the base directory exists and calls buildDirs() to ensure
	 * that each level of directory is pre-created.  This has
	 * the benefit that $autoInit can be set to false, and
	 * eliminates an extra mkdir() call or twoon the first request
	 * to store a file, but has the drawback that the first time
	 * it is called, it can take up to several minutes.  init()
	 * must be called manually if $autoInit is false.
	 * 
	 * @access	public
	 * @return	boolean
	 * 
	 */
	function init () {
		if (! is_dir ($this->path)) {
			if (! @mkdir ($this->path, $this->dirMode)) {
				$this->error = 'Failed to create root directory';
				return false;
			}
		}
		if (! is_dir ($this->path . '/a')) {
			if (! $this->buildDirs ($this->path, $this->depth)) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Builds the recursive directory structure used by this
	 * package, stopping at the depth specified (called usually by
	 * init(), which uses the $depth property to determine this number).
	 * The directory structure is a series of directories named 'a'
	 * through 'z', 'A' through 'Z', and '0' through '9', defaulting
	 * to two levels deep.  At two levels this creates 3844 directories,
	 * which if they each stored a maximum of 500 files, after which
	 * point a degradation in filesystem performance would be noticed,
	 * could potentially store about 1.8 million files.  We recommend
	 * against an increase in $depth, as one more level would bring
	 * the number of directories to 238,328 (and the number of files
	 * in theory to about 120 million, far larger than most web sites
	 * should need).
	 * 
	 * @access	public
	 * @param	string	$dir
	 * @param	integer	$depth
	 * @return	boolean
	 * 
	 */
	function buildDirs ($dir, $depth = 2) {
		$dirs = array (
			range ('a', 'z'),
			range ('A', 'Z'),
			range (0, 9),
		);
		foreach ($dirs as $range) {
			foreach ($range as $i) {
				if (! @mkdir ($dir . '/' . $i, $this->dirMode)) {
					$this->error = 'Failed to create directory: ' . $dir . '/' . $i;
					return false;
				}
				if ($depth > 1) {
					set_time_limit (30);
					if (! $this->buildDirs ($dir . '/' . $i, $depth - 1)) {
						return false;
					}
				}
			}
		}
		return true;
	}

	/**
	 * Initializes a single directory within the structure.
	 * Used when the $autoInit property is set to true.
	 * 
	 * @access	private
	 * @param	string	$path
	 * @param	array	$dirs
	 * @return	boolean
	 * 
	 */
	function initDir ($path, $dirs) {
		if (count ($dirs) <= 0) {
			return true;
		}
		$dir = array_shift ($dirs);
		if (! is_dir ($path . '/' . $dir)) {
			if (! @mkdir ($path . '/' . $dir, $this->dirMode)) {
				$this->error = 'Failed to create directory: ' . $path . '/' . $dir;
				return false;
			} else {
				return $this->initDir ($path . '/' . $dir, $dirs);
			}
		} else {
			return $this->initDir ($path . '/' . $dir, $dirs);
		}
	}

	/**
	 * Gets the absolute path to the file requested.
	 * 
	 * @access	public
	 * @param	string	$file
	 * @return	string
	 * 
	 */
	function getPath ($file) {
		if ($this->useMD5) {
			$file = md5 ($file) . '_' . $file;
			$ignore = 0;
		} else {
			$ignore = $this->ignoreChars;
		}
		$dirs = preg_split ('//', substr ($file, $ignore, $this->depth), -1, PREG_SPLIT_NO_EMPTY);
		if ($this->autoInit) {
			if (! is_dir ($this->path)) {
				if (! @mkdir ($this->path, $this->dirMode)) {
					$this->error = 'Failed to create root directory';
					return false;
				}
			}
			if (! $this->initDir ($this->path, $dirs)) {
				return false;
			}
		}
		return $this->path . '/' . join ('/', $dirs) . '/' . $file;
	}

	/**
	 * Determines whether a file exists within the storage
	 * system or not.
	 * 
	 * @access	public
	 * @param	string	$file
	 * @return	boolean
	 * 
	 */
	function exists ($file) {
		$p = $this->getPath ($file);
		if (! $p) {
			return false;
		} else {
			if (@file_exists ($p)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Opens a file within the system for writing.  Note
	 * that this method does not synchronize a newly created file
	 * with the $dbTable.  For that, use the get() and remove()
	 * methods.
	 * 
	 * @access	public
	 * @param	string	$file
	 * @param	string	$mode
	 * @return	file handler
	 * 
	 */
	function open ($file, $mode) {
		$p = $this->getPath ($file);
		if (! $p) {
			return false;
		}
		return @fopen ($p, $mode);
	}

	/**
	 * Reads from the specified file, either in entirety,
	 * or to the specified length.  Handles opening and closing
	 * of the file automatically.  Returns false on error.
	 * 
	 * @access	public
	 * @param	string	$file
	 * @param	integer	$length
	 * @return	string
	 * 
	 */
	function get ($file, $length = false) {
		if ($fp = $this->open ($file, $this->readMode)) {
			if ($length !== false) {
				$data = fread ($fp, $length);
			} else {
				$data = @fread ($fp, filesize ($this->getPath ($file)));
			}
			fclose ($fp);
			return $data;
		} else {
			if (empty ($this->error)) {
				$this->error = 'Failed to open file';
			}
			return false;
		}
	}

	/**
	 * Writes to the specified file, either in entirety,
	 * or to the specified length.  Handles opening and closing
	 * of the file automatically.  Returns false on error, or
	 * the number of bytes written on success.
	 * 
	 * @access	public
	 * @param	string	$file
	 * @param	string	$data
	 * @param	integer	$length
	 * @return	integer
	 * 
	 */
	function put ($file, $data, $length = false) {
		if ($fp = $this->open ($file, $this->writeMode)) {
			flock ($fp, LOCK_EX);
			if ($length !== false) {
				$bytes = fwrite ($fp, $data, $length);
			} else {
				$bytes = fwrite ($fp, $data);
			}
			flock ($fp, LOCK_UN);
			fclose ($fp);

			if (! empty ($this->dbTable)) {
				global $db;
				$db->execute ('INSERT INTO ' . $this->dbTable . ' VALUES (?)', $file);
			}

			return $bytes;
		} else {
			if (empty ($this->error)) {
				$this->error = 'Failed to open file';
			}
			return false;
		}
	}

	/**
	 * Writes to the end of the specified file.  Can
	 * be limited by the $length parameter.  Handles opening and
	 * closing of the file automatically.  Returns false on error,
	 * or the number of bytes written on success.
	 * 
	 * @access	public
	 * @param	string	$file
	 * @param	string	$data
	 * @param	integer	$length
	 * @return	integer
	 * 
	 */
	function append ($file, $data, $length = false) {
		if ($fp = $this->open ($file, $this->appendMode)) {
			flock ($fp, LOCK_EX);
			if ($length !== false) {
				$bytes = fwrite ($fp, $data, $length);
			} else {
				$bytes = fwrite ($fp, $data);
			}
			flock ($fp, LOCK_UN);
			fclose ($fp);
			return $bytes;
		} else {
			if (empty ($this->error)) {
				$this->error = 'Failed to open file';
			}
			return false;
		}
	}

	/**
	 * Moves the specified $oldFile to the repository, under the name
	 * provided in the $file parameter.  If $isUploaded is true, then
	 * it uses the move_uploaded_file() function instead of the
	 * rename() function.
	 *
	 * @access	public
	 * @param	string	$file
	 * @param	string	$oldFile
	 * @param	boolean	$isUploaded
	 * @return	boolean
	 *
	 */
	function move ($file, $oldFile, $isUploaded = false) {
		$p = $this->getPath ($file);
		if (! $p) {
			return false;
		}

		if ($isUploaded) {
			$res = move_uploaded_file ($oldFile, $p);
			if (! $res) {
				$this->error = 'Failed to save file';
				return false;
			}
		} else {
			$res = rename ($oldFile, $p);
			if (! $res) {
				$this->error = 'Failed to save file';
				return false;
			}
		}
		return true;
	}

	/**
	 * Copies the specified $oldFile to the repository, under the name
	 * provided in the $file parameter.
	 *
	 * @access	public
	 * @param	string	$file
	 * @param	string	$oldFile
	 * @return	boolean
	 *
	 */
	function copy ($file, $oldFile) {
		$p = $this->getPath ($file);
		if (! $p) {
			return false;
		}

		$res = copy ($oldFile, $p);
		if (! $res) {
			$this->error = 'Failed to save file';
			return false;
		}
		return true;
	}

	/**
	 * Deletes a file from the storage system.
	 * 
	 * @access	public
	 * @param	string	$file
	 * @return	boolean
	 * 
	 */
	function remove ($file) {
		$p = $this->getPath ($file);
		if (! $p) {
			return false;
		} else {
			if (is_file ($p)) {
				if (! @unlink ($p)) {
					$this->error = 'File could not be deleted';
					return false;
				}
			} else {
				$this->error = 'File not found';
				return false;
			}
		}

		if (! empty ($this->dbTable)) {
			global $db;
			$db->execute ('DELETE FROM ' . $this->dbTable . ' WHERE filename = ?', $file);
		}

		return true;
	}

	/**
	 * Searches for a list of files within the repository
	 * database table, optionally beginning with the specified
	 * prefix.  Returns an array of objects each with one
	 * property ($filename), or false on error.  Also sets the
	 * $error property with any database error message.  An
	 * optional $limit and $offset may be specified to allow
	 * for the ability to page through the list of files.
	 * 
	 * @access	public
	 * @param	string	$prefix
	 * @param	integer	$limit
	 * @param	integer	$offset
	 * @return	array
	 * 
	 */
	function listAll ($prefix = '', $limit = 0, $offset = 0) {
		if (empty ($this->dbTable)) {
			return false;
		}

		$query = 'SELECT * FROM ' . $this->dbTable;
		$bind = array ();
		if (! empty ($prefix)) {
			$query .= ' WHERE filename LIKE ?';
			$bind[] = $prefix . '%';
		}
		if ($limit > 0) {
			if ($offset > 0) {
				$query .= ' LIMIT ' . $offset . ', ' . $limit;
			} else {
				$query .= ' LIMIT ' . $limit;
			}
		}

		global $db;
		$res = $db->fetch ($query, $bind);
		if ($res === false) {
			$this->error = $db->error;
		} elseif (is_object ($res)) {
			$res = array ($res);
		}
		return $res;
	}

	/**
	 * Returns the total number of documents in the
	 * repository, as listed in the accompanying database
	 * table.  A $prefix is optional.  Returns false and
	 * sets $error on database error.
	 * 
	 * @access	public
	 * @param	string	$prefix
	 * @return	integer
	 * 
	 */
	function countAll ($prefix = '') {
		if (empty ($this->dbTable)) {
			return false;
		}

		$query = 'SELECT count(*) as total FROM ' . $this->dbTable;
		$bind = array ();
		if (! empty ($prefix)) {
			$query .= ' WHERE filename LIKE ?';
			$bind[] = $prefix . '%';
		}

		global $db;
		$res = $db->fetch ($query, $bind);
		if ($res === false) {
			$this->error = $db->error;
		} elseif (is_object ($res)) {
			$res = $res->total;
		}
		return $res;
	}

	
}



?>