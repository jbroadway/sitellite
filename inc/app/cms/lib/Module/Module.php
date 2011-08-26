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
// Module is a class that gathers information about Sitellite
// modules, and loads them.
//

/**
	 * Module is a class that gathers information about Sitellite
	 * modules, and loads them.  Modules execute in the Module namespace, so
	 * existing global objects such as $db and $tpl must be included via a
	 * global $obj1, $obj2; call.  Modules are used to extend the functionality
	 * of the Sitellite Content Manager, which is the administrative interface
	 * of the Sitellite Content Management System.
	 * 
	 * For information about front-end "modules", see the box/block features of
	 * saf.Template, how to write custom run modes for the Content Server, and
	 * how to add custom tags to EasyText.
	 * 
	 * New in 1.2:
	 * - Added a $description property.
	 * 
	 * New in 1.4:
	 * - Added a listAll() method.
	 * 
	 * New in 1.6:
	 * - Updated the parsing logic and .mod document format to be faster and
	 *   more compatible with new developments in saf.XML.Doc.
	 * - Added the ability to display a JavaScript alert popup with a 'This
	 *   is a demo copy...' notice for demo modules.
	 * 
	 * New in 1.8:
	 * - Removed the install() and update() methods in favour of the
	 *   saf.App.Module.Finder package.
	 * 
	 * New in 2.0:
	 * - Added the ability for modules with a status of 'demo' to include
	 *   demo alert messages in their .mod file, so that they can have
	 *   a custom list of messages to display as alerts each time a user
	 *   clicks on a demo module.  It still defaults to the 'This is a
	 *   demo...' message if no
	 *     <demoAlerts>
	 *       <msg>One</msg>
	 *       <msg>Two</msg>
	 *     </demoAlerts>
	 *   are found.
	 * 
	 * New in 2.2:
	 * - Removed the usage of a modname.mod file in XML format in favour
	 *   of a settings.php file in INI format.  This is faster to parse,
	 *   easier to maintain, and cleaner to code.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $module = new Module ($cgi->name);
	 * 
	 * if (! $module->load ()) {
	 * 	// if module fails to load, send user back to the site index
	 * 	header ("Location: index.php");
	 * }
	 * 
	 * ?>
	 * </code>
	 * 
	 * @package	CMS
	 * @category Module
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.net/index/license GNU GPL License
	 * @version	2.0, 2002-09-14, $Id: Module.php,v 1.1.1.1 2005/04/29 04:44:31 lux Exp $
	 * @access	public
	 * 
	 */

class Module {
	/**
	 * The folder of the current module.
	 * 
	 * @access	public
	 * 
	 */
	var $folder;

	/**
	 * The path to the icon image of the current module, as read
	 * from the modules info file (.mod file).
	 * 
	 * @access	public
	 * 
	 */
	var $icon;

	/**
	 * The name of the current module, as read
	 * from the modules info file (.mod file).
	 * 
	 * @access	public
	 * 
	 */
	var $name;

	/**
	 * The index or first page to load of the current module, as read
	 * from the modules info file (.mod file).
	 * 
	 * @access	public
	 * 
	 */
	var $index;

	/**
	 * A description of the module.
	 * 
	 * @access	public
	 * 
	 */
	var $description;

	/**
	 * Constructor method.  You must provide it with the name
	 * of a sub-folder of the 'mod' directory.  Sitellite modules are
	 * all simply sub-directories stored there.
	 * 
	 * @access	public
	 * @param	string	$folder
	 * 
	 */
	function Module ($folder = '') {
		$this->folder = $folder;
		// verify module exists
		if (! @file_exists ('mod/' . $folder . '/settings.php')) {
			return false;
		}

		$doc = parse_ini_file ('mod/' . $folder . '/settings.php');
		foreach ($doc as $key => $value) {
			if (is_array ($value)) {
				foreach ($value as $k => $v) {
					$this->{$k} = $v;
				}
			} else {
				$this->{$key} = $value;
			}
		}

		if (! isset ($doc['Settings']['show']) && ! isset ($doc['show'])) {
			$this->show = true;
		}

		if ($this->release == 'demo') {
			$this->demo_notice = 'onclick="alert (\'' . $intl->get ('Demo Alert!\nThis is a demo copy of this module.  If you find it useful, please consider upgrading to the full version.  For more information, visit') . ' ' . $this->link . '\')"';
		} else {
			$this->demo_notice = '';
		}
	}

	/**
	 * Loads a module by including it into the php file.  On failure,
	 * it returns 0, on success it returns 1.
	 * 
	 * @access	public
	 * @return	boolean
	 * 
	 */
	function load () {
		if (@file_exists ('mod/' . $this->folder . '/' . $this->index)) {
			include_once ('mod/' . $this->folder . '/' . $this->index);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Checks whether or not a module is installed.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @return	boolean
	 * 
	 */
	function isInstalled ($name) {
		if (@file_exists ('mod/' . $name . '/settings.php')) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Checks whether or not the copy available on sitellite.org/modfinder
	 * is an updated version from the one currently installed.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	string	$version
	 * @return	boolean
	 * 
	 */
	function isUpdate ($name, $version) {
		if ($version > $this->version) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Returns an array of all installed modules as Module objects.
	 * 
	 * @access	public
	 * @return	array
	 * 
	 */
	function listAll () {
		global $loader;
		$loader->import ('saf.File.Directory');
		$d = new Dir ('mod');
		$dirs = $d->read_all ();

		$mods = array ();
		
		foreach ($dirs as $dir) {
			if ($dir == '_backup' || $dir == '.' || $dir == '..') {
				continue;
			}
			if (@file_exists ('mod/' . $dir . '/settings.php')) {
				$tmp = new Module ($dir);
				if (! $tmp->error) {
					array_push ($mods, $tmp);
				}
			}
		}
		return $mods;
	}
}



?>