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
// ModFinder contains methods for securely querying module distribution
// servers, managing server lists, and installing and upgrading modules
// from these servers.
//

/**
	 * ModFinder contains methods for securely querying module distribution
	 * servers, managing server lists, and installing and upgrading modules
	 * from these servers.
	 * 
	 * The distribution servers use a simple XML listing and directory structure,
	 * which makes them easy to parse and query.  Each distribution must employ a
	 * two-server approach to maintaining proper checksums on all listing and source
	 * files, so ModFinder may verify each file it comes across.  The reason for the
	 * two-server approach is so that if one server is compromised and any files are
	 * tainted, it will be immediately obvious to ModFinder, and will pose no threat
	 * to the client web site.
	 * 
	 * This package is used by the ModFinder module to provide a means of extending
	 * the Sitellite Content Manager through third-party tool makers.
	 * 
	 * New in 1.2:
	 * - _install and _update script evaluation was removed from the _install() and
	 *   _update() methods, which now merely download and verify the files listed
	 *   in _files on the distribution server.  This allows the ModFinder module
	 *   to implement the eval() stage, and allows these scripts much more control
	 *   over the install process.  The benefit of this control is that installations
	 *   can be broken into stages.
	 * - The following stages pertain to the install and upgrade processes:
	 *   - license
	 *   - payment
	 *   - verify
	 *   - download
	 *   - options
	 *   - configure
	 *   - done
	 *   These should be used to create a multi-screen install process including
	 *   a click-through license agreement, the ability to send users to a 3rd
	 *   party site to fulfill an e-commerce installation component, and the
	 *   ability to ask questions of the site administrator to add post-installation
	 *   configurations to the new module, and to verify that the site requesting
	 *   the installation meets certain compatibility requirements.
	 * - Cleaned up the DocReader docs a little.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $mfinder = new ModFinder ();
	 * 
	 * $mfinder->addDistro (
	 * 	'AppBuilders-Are-Us',
	 * 	'http://modules.appbuilders.com',
	 * 	'http://checksums.appbuilders.com',
	 * 	'http://www.appbuilders.com'
	 * );
	 * 
	 * $mfinder->current = 'AppBuilders-Are-Us';
	 * 
	 * // list all modules by AppBuilders-Are-Us
	 * foreach ($mfinder->getList () as $mod) {
	 * 	echo $simple->fill ($module_template, $mod);
	 * }
	 * 
	 * // install or update the AppBuilder 200 package
	 * $mfinder->installPackage ('AppBuilder 2000');
	 * 
	 * ?>
	 * </code>
	 * 
	 * @package	CMS
	 * @category Module
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.net/index/license GNU GPL License
	 * @version	1.2, 2002-09-16, $Id: Finder.php,v 1.1.1.1 2005/04/29 04:44:31 lux Exp $
	 * @access	public
	 * 
	 */

class ModFinder {
	/**
	 * Contains a list of distributions.  Each distribution is
	 * an StdClass object with $name, $downloadSite, $checksumSite, and
	 * $webSite properties.
	 * 
	 * @access	public
	 * 
	 */
	var $distros = array ();

	/**
	 * Contains the key of the default distribution in $distros.
	 * Set automatically if a distribution is specified upon creation of
	 * your ModFinder object, but otherwise you're on your own.
	 * 
	 * @access	public
	 * 
	 */
	var $current;

	/**
	 * Contains an error message if any error has occurred within
	 * any method of this class.
	 * 
	 * @access	public
	 * 
	 */
	var $error;

	/**
	 * Constructor method.  You may provide an optional default
	 * initial distribution, which will also become the default.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	string	$downloadSite
	 * @param	string	$checksumSite
	 * @param	string	$webSite
	 * 
	 */
	function ModFinder ($name = '', $downloadSite = '', $checksumSite = '', $webSite = '') {
		if (! empty ($name) &&
			! empty ($downloadSite) &&
			! empty ($checksumSite) &&
			! empty ($webSite)) {
			$this->addDistro ($name, $downloadSite, $checksumSite, $webSite);
			$this->current = $name;
		}
	}

	/**
	 * Add a distribution to the list.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	string	$downloadSite
	 * @param	string	$checksumSite
	 * @param	string	$webSite
	 * 
	 */
	function addDistro ($name, $downloadSite, $checksumSite, $webSite) {
		$this->distros[$name] = new StdClass;
		$this->distros[$name]->name = $name;
		$this->distros[$name]->downloadSite = $downloadSite;
		$this->distros[$name]->checksumSite = $checksumSite;
		$this->distros[$name]->webSite = $webSite;
	}

	/**
	 * Load a distribution list from a file.  The format is an
	 * XML format which can be found in sitellite/mod/modfinder/distributions.xml
	 * in your Sitellite CMS installation.
	 * 
	 * @access	public
	 * @param	string	$filename
	 * @return	boolean
	 * 
	 */
	function loadDistros ($filename) {
		global $loader;
		$loader->inc ('saf.XML.Sloppy');
		$sloppy = new SloppyDOM ();

		$doc = $sloppy->parseFromFile ($filename);
		if (! $doc) {
			$this->error = $sloppy->error;
			return false;
		}

		foreach ($doc->query ('/modfinderDistributions/distribution') as $dist) {
			$dist = $dist->makeObj ();
			$this->distros[$dist->name] = $dist;
		}

		return true;
	}

	/**
	 * Takes the internal $distros array and returns an XML
	 * document representation of it.
	 * 
	 * @access	public
	 * @return	string
	 * 
	 */
	function buildDistFile () {
		global $loader;
		$loader->inc ('saf.XML.Doc');

		$doc = new XMLDoc ();
		$root =& $doc->addRoot ('modfinderDistributions');

		foreach ($this->distros as $distro) {
			$dist =& $root->addChild ('distribution');
			$dist->addChild ('name', $distro->name);
			$dist->addChild ('downloadSite', $distro->downloadSite);
			$dist->addChild ('checksumSite', $distro->checksumSite);
			$dist->addChild ('webSite', $distro->webSite);
			unset ($dist);
		}
		
		return $doc->write ();
	}

	/**
	 * Returns whether or not the specified distribution
	 * exists.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @return	boolean
	 * 
	 */
	function distExists ($name) {
		foreach ($this->distros as $distro) {
			if ($name == $distro->name) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Save a distribution list to a file.  The format is an
	 * XML format which can be found in inc/conf/shared/distributions.xml
	 * in your Sitellite CMS installation.
	 * 
	 * @access	public
	 * @param	string	$filename
	 * @return	boolean
	 * 
	 */
	function saveDistros ($filename) {
		$doc = $this->buildDistFile ();

		$fp = fopen ($filename, 'w');
		if (! $fp) {
			$this->error = 'File open failed!';
			return false;
		}

		fwrite ($fp, $doc);
		fclose ($fp);
		return true;
	}

	/**
	 * Retrieves the contents of a remote file as a single string.
	 * Returns false on failure.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	boolean	$binary
	 * @return	string
	 * 
	 */
	function getFile ($name, $binary = false) {
		if (! $binary) {
			return @join ('', @file ($name));
		} else {
			$fp = fopen ($name, 'rb');
			if (! $fp) {
				return false;
			}
			$contents = fread ($fp, 1024000); // 1 meg max
			fclose ($fp);
			return $contents;
		}
	}

	/**
	 * Retrieves the contents of a remote md5 checksum file.
	 * Also parses any trailing spaces from the file, as well as an
	 * "MD5(path/file)= " string from the start of the file, which
	 * is left by openssl if openssl is used to create the checksum
	 * file.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @return	string
	 * 
	 */
	function getChecksum ($name) {
		return @preg_replace ('/^MD5\([^)]+\)= /', '', @rtrim (@join ('', @file ($name))));
	}

	/**
	 * Retrieves a list of all modules found in the specified
	 * (or default) distribution.  Each module is returned as an
	 * object of the type StdClass.
	 * 
	 * @access	public
	 * @param	string	$distro
	 * @return	array
	 * 
	 */
	function getList ($distro = '') {
		if (empty ($distro)) {
			$distro = $this->current;
		}

		$contents = $this->getFile ($this->distros[$distro]->downloadSite . '/source/_list');
		if (! $contents) {
			$this->error = 'List file not found!';
			return false;
		}

		$checksum = $this->getChecksum ($this->distros[$distro]->checksumSite . '/checksums/_list.md5');
		if (! $checksum) {
			$this->error = 'Checksum file not found!';
			return false;
		}

		if ($checksum != md5 ($contents)) {
			$this->error = 'List file and checksum do not match!';
			return false;
		}

		global $loader;
		$loader->inc ('saf.XML.Sloppy');
		$loader->import ('saf.App.Module');
		$sloppy = new SloppyDOM ();

		$doc = $sloppy->parse ($contents);
		if (! $doc) {
			$this->error = $sloppy->error;
			return false;
		}

		$res = array ();

		$distros = $doc->query ('/modules/distro');
		if (is_array ($distros)) {
			foreach ($distros as $d) {
				$d = $d->makeObj ();
				$d->isDistro = true;
				if (! $this->distExists ($d->name)) {
					$res[] = $d;
				}
			}
		}

		foreach ($doc->query ('/modules/module') as $mod) {
			$mod = $mod->makeObj ();
			if (! Module::isInstalled ($mod->dirname) || $this->isUpdate ($mod->dirname, $mod->version)) {
				$res[] = $mod;
			}
		}

		return $res;
	}

	/**
	 * Determines whether or not the specified $mod (dirname) and
	 * $version is an update of an installed module.
	 * 
	 * @access	public
	 * @param	string	$mod
	 * @param	string	$version
	 * @return	boolean
	 * 
	 */
	function isUpdate ($mod, $version) {
		if (! is_array ($this->modules)) {
			global $loader;
			$loader->import ('saf.App.Module');
			$this->modules = Module::listAll ();
		}
		foreach ($this->modules as $module) {
			if ($mod == $module->folder && $version > $module->version) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Compares a given query against a module object as
	 * returned by a getList() or search() call and returns whether
	 * or not the module is a match.
	 * 
	 * @access	public
	 * @param	string	$query
	 * @param	object	$mod
	 * @return	boolean
	 * 
	 */
	function matches ($query, $mod) {
		if (strstr ($mod->name, $query)) {
			return true;
		} elseif (strstr ($mod->description, $query)) {
			return true;
		}
		return false;
	}

	/**
	 * Retrieves a list of all modules found in the specified
	 * (or default) distribution whose name or description contains
	 * the specified query string.  Each module is returned as an
	 * object of the type StdClass.
	 * 
	 * @access	public
	 * @param	string	$query
	 * @param	string	$distro
	 * @return	array
	 * 
	 */
	function search ($query, $distro = '') {
		if (empty ($distro)) {
			$distro = $this->current;
		}

/*
		$contents = $this->getFile ($this->distros[$distro]->downloadSite . '/source/_list');
		if (! $contents) {
			$this->error = 'List file not found!';
			return false;
		}

		$checksum = $this->getChecksum ($this->distros[$distro]->checksumSite . '/checksums/_list.md5');
		if (! $checksum) {
			$this->error = 'Checksum file not found!';
			return false;
		}

		if ($checksum != md5 ($contents)) {
			$this->error = 'List file and checksum do not match!';
			return false;
		}

		global $loader;
		$loader->inc ('saf.XML.Sloppy');
		$sloppy = new SloppyDOM ();

		$doc = $sloppy->parse ($contents);
		if (! $doc) {
			$this->error = $sloppy->error;
			return false;
		}

		$res = array ();
		foreach ($doc->query ('/modules/module') as $mod) {
			$mod = $mod->makeObj ();
			if ($this->matches ($query, $mod)) {
				$res[] = $mod;
			}
		}
		return $res;
*/

		$modules = $this->getList ($distro);
		if (! $modules) {
			return false;
		}
		$res = array ();
		foreach ($modules as $mod) {
			if ($this->matches ($query, $mod)) {
				$res[] = $mod;
			}
		}
		return $res;
	}

	/**
	 * Compares the specified package to the local modules
	 * to determine whether the installation request is a new one
	 * or an update of an existing module.  It then calls
	 * either _install() or _update(), whichever it chooses.
	 * Returns true on success and false on failure.  If an
	 * _install() call fails, it will remove the partially
	 * completed installation for you.  _update() is on its
	 * own, but calls the restoreBackup() method itself.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	string	$distro
	 * @return	boolean
	 * 
	 */
	function installPackage ($name, $distro = '') {
		if (empty ($distro)) {
			$distro = $this->current;
		}

		global $loader;
		$loader->import ('saf.App.Module');

		if (Module::isInstalled ($name)) {
			$m = new Module ($name);
			$res = $this->search ($name);
			if (! $res) {
				return false;
			}
			foreach ($res as $found) {
				if ($m->name == $found->name) {
					if (! $m->isUpdate ($found->dirname, $found->version)) {
						$this->error = 'Already installed!';
						return false;
					}
					break;
				}
			}

			// compare $m->version with a $version retrieved from the _list file
			// and if it's not an update, return false, error: Already installed!

			// it's an update
			return $this->_update ($name, $distro);
		} else {
			if (! $this->_install ($name, $distro)) {
				$loader->import ('saf.File.Directory');
				Dir::rmdir_recursive ('mod/' . $name);
				return false;
			} else {
				return true;
			}
		}
	}

	/**
	 * Installs the specified module from the specified distribution.
	 * The order of operations is as follows:  1) Downloads _files list,
	 * _files.md5 checksum, _install script, and _install.md5 checksum,
	 * and compares them for validity.  2) Parses _files list and creates
	 * all directories, then downloads each file from the _files list
	 * and verifies it before saving it to disk.  3) Runs the _install
	 * script by eval()ing it.  If at any time the installation fails,
	 * it sets an error message and returns false immediately.
	 * 
	 * @access	private
	 * @param	string	$name
	 * @param	string	$distro
	 * @return	boolean
	 * 
	 */
	function _install ($name, $distro) {
		$_files = $this->getFile ($this->distros[$distro]->downloadSite . '/source/' . $name . '/_files');
		if (! $_files) {
			$this->error = 'File list not found!';
			return false;
		}

		$checksum = $this->getChecksum ($this->distros[$distro]->checksumSite . '/checksums/' . $name . '/_files.md5');
		if (! $checksum) {
			$this->error = 'File list checksum not found!';
			return false;
		}

		if ($checksum != md5 ($_files)) {
			$this->error = 'List file and checksum do not match!';
			return false;
		}

/*
		$_install = $this->getFile ($this->distros[$distro]->downloadSite . '/source/' . $name . '/_install');
		if (! $_install) {
			$this->error = 'Install script not found!';
			return false;
		}

		$checksum = $this->getChecksum ($this->distros[$distro]->checksumSite . '/checksums/' . $name . '/_install.md5');
		if (! $checksum) {
			$this->error = 'Checksum file not found!';
			return false;
		}

		if ($checksum != md5 ($_install)) {
			$this->error = 'Install script and checksum do not match!';
			return false;
		}
*/

		// parse _files and create all directories
		global $loader;
		$loader->inc ('saf.XML.Sloppy');
		$loader->inc ('saf.File.Directory');
		$sloppy = new SloppyDOM ();

		$doc = $sloppy->parse ($_files);
		if (! $doc) {
			$this->error = $sloppy->error;
			return false;
		}

		chdir ('mod');
		foreach ($doc->query ('/filelist/directories/name') as $dir) {
			Dir::build ($dir->content, 0775);
		}
		chdir ('..');

		// download and check each file for validity
		foreach ($doc->query ('/filelist/files/file') as $file) {
			$file = $file->makeObj ();
			if ($file->type == 'binary') {
				$contents = $this->getFile ($file->link, true);
			} else {
				$contents = $this->getFile ($file->link);
			}

			if ($file->md5sum != md5 ($contents)) {
				$this->error = 'File ' . $file->name . ' and checksum did not match!';
				return false;
			}

			$fp = fopen ('mod/' . $file->directory . '/' . $file->name, 'w');
			if (! $fp) {
				$this->error = 'Could not create file ' . $file->name . '!';
				return false;
			}
			fwrite ($fp, $contents);
			fclose ($fp);
		}

		// run the _install script
		//eval ('?//>' . $_install);
		return true;
	}

	/**
	 * Updates the specified module from the specified distribution.
	 * The order of operations is as follows:  1) Downloads _files list,
	 * _files.md5 checksum, _update script, and _update.md5 checksum,
	 * and compares them for validity.  2) Backs up the existing module
	 * to _backup/MODULENAME/VERSION/MODULENAME.  3) Parses _files list and
	 * creates any new directories, then downloads each file from the _files
	 * list and verifies it before saving it to disk.  Files that have a
	 * special <skipIfUpdate>Yes</skipIfUpdate> tag are skipped.
	 * 4) Runs the _update script by eval()ing it.  If at any time the
	 * installation fails, it sets an error message, calls restoreBackup(),
	 * and returns false immediately.
	 * 
	 * @access	private
	 * @param	string	$name
	 * @param	string	$distro
	 * @return	boolean
	 * 
	 */
	function _update ($name, $distro) {
		$_files = $this->getFile ($this->distros[$distro]->downloadSite . '/source/' . $name . '/_files');
		if (! $_files) {
			$this->error = 'File list not found!';
			return false;
		}

		$checksum = $this->getChecksum ($this->distros[$distro]->checksumSite . '/checksums/' . $name . '/_files.md5');
		if (! $checksum) {
			$this->error = 'Checksum file not found!';
			return false;
		}

		if ($checksum != md5 ($_files)) {
			$this->error = 'List file and checksum do not match!';
			return false;
		}

/*
		$_update = $this->getFile ($this->distros[$distro]->downloadSite . '/source/' . $name . '/_update');
		if (! $_update) {
			$this->error = 'Install script not found!';
			return false;
		}

		$checksum = $this->getChecksum ($this->distros[$distro]->checksumSite . '/checksums/' . $name . '/_update.md5');
		if (! $checksum) {
			$this->error = 'Checksum file not found!';
			return false;
		}

		if ($checksum != md5 ($_update)) {
			$this->error = 'Update script and checksum do not match!';
			return false;
		}
*/

		// backup the existing module to _backup/modulename/version
		global $loader;
		$loader->inc ('saf.App.Module');
		$loader->inc ('saf.XML.Sloppy');
		$loader->inc ('saf.File.Directory');

		$module = new Module ($name);
		if (! is_dir ('mod/_backup/' . $name)) { // up to /mod/_backup/MODULENAME
			chdir ('mod/_backup');
			mkdir ($name, 0775);
			chdir ($name);
		} else { // up to /mod/_backup/MODULENAME
			chdir ('mod/_backup/' . $name);
		}
		if (! is_dir ($module->version)) {
			mkdir ($module->version, 0775);
		}
		chdir ('../..'); // down to /mod

		if (eregi ('^Win', PHP_OS)) {
			$msg = @exec ('xcopy/S/E ' .
				$name . ' ' .
				preg_replace ('/\//', '\\', '_backup/' . $name . '/' . $module->version),
				$arr, $res);
			if (! $res) {
				$this->error = 'Backup failed!  ' . join (', ', $arr);
				return false;
			}
		} else {
			$msg = @system ('cp -R ' . $name . ' _backup/' . $name . '/' . $module->version, $res);
			if ($res === false) {
				$this->error = 'Backup failed!  ' . $res;
				return false;
			}
		}

		// parse _files and create any missing directories
		$sloppy = new SloppyDOM ();

		$doc = $sloppy->parse ($_files);
		if (! $doc) {
			$this->error = $sloppy->error;
			$this->restoreBackup ($name, $module->version);
			return false;
		}

		foreach ($doc->query ('/filelist/directories/name') as $dir) {
			Dir::build ($dir->content, 0775);
		}
		chdir ('..'); // down to /

		// download and check each file for validity
		foreach ($doc->query ('/filelist/files/file') as $file) {
			$file = $file->makeObj ();

			// skip any file that contains a <skipIfUpdate>Yes</skipIfUpdate> tag
			if ($file->skipIfUpdate) {
				continue;
			}

			if ($file->type == 'binary') {
				$contents = $this->getFile ($file->link, true);
			} else {
				$contents = $this->getFile ($file->link);
			}

			if ($file->md5sum != md5 ($contents)) {
				$this->error = 'File ' . $file->name . ' and checksum did not match!';
				$this->restoreBackup ($name, $module->version);
				return false;
			}

			$fp = fopen ('mod/' . $file->directory . '/' . $file->name, 'w');
			if (! $fp) {
				$this->error = 'Could not create file ' . $file->name . '!';
				$this->restoreBackup ($name, $module->version);
				return false;
			}
			fwrite ($fp, $contents);
			fclose ($fp);
		}

		// run the _update script
		//eval ('?//>' . $_update);
		return true;
	}

	/**
	 * Removes whatever is in the module's proper place (ie. a
	 * half-competed installation or update), and replaces it with the
	 * copy that is in mod/_backup/MODULENAME/VERSION/MODULENAME.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	string	$version
	 * @return	boolean
	 * 
	 */
	function restoreBackup ($name, $version) {
		global $loader;
		$loader->import ('saf.File.Directory');
		Dir::rmdir_recursive ('mod/' . $name);

		if (eregi ('^Win', PHP_OS)) {
			$msg = @exec ('xcopy/S/E ' .
				preg_replace ('/\//', '\\', 'mod/_backup/' . $name . '/' . $version . '/' . $name) . ' ' .
				'mod/' . $name,
				$arr, $res);
			if (! $res) {
				$this->error = 'Backup restoration failed!  ' . join (', ', $arr);
				return false;
			}
		} else {
			$msg = @system ('cp -R mod/_backup/' . $name . '/' . $version . '/' . $name . ' ' . $name, $res);
			if ($res === false) {
				$this->error = 'Backup restoration failed!  ' . $res;
				return false;
			}
		}
		return true;
	}

	/**
	 * Returns a valid file list XML document from the specified
	 * module and distribution.  Returns false on failure or on an invalid
	 * file.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	string	$distro
	 * @return	string
	 * 
	 */
	function getFileList ($name, $distro = '') {
		if (empty ($distro)) {
			$distro = $this->current;
		}

		$_files = $this->getFile ($this->distros[$distro]->downloadSite . '/source/' . $name . '/_files');
		if (! $_files) {
			$this->error = 'File list not found!';
			return false;
		}

		$checksum = $this->getChecksum ($this->distros[$distro]->checksumSite . '/checksums/' . $name . '/_files.md5');
		if (! $checksum) {
			$this->error = 'File list checksum not found!';
			return false;
		}

		if ($checksum != md5 ($_files)) {
			$this->error = 'List file and checksum do not match!';
			return false;
		}

		return $_files;
	}
}



?>