<?php
//
// +----------------------------------------------------------------------+
// | PEAR :: DB_NestedSet_Output                                          |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Daniel Khan <dk@webcluster.at>                              |
// | 		  Jason Rust  <jason@rustyparts.com>                          |
// +----------------------------------------------------------------------+
// $Id: Output.php,v 1.1.1.1 2005/04/29 04:44:36 lux Exp $
//

require_once 'PEAR.php';

// {{{ constants

define('NESEO_ERROR_NO_METHOD',    'E1000');
define('NESEO_DRIVER_NOT_FOUND',   'E1100');
define('NESEO_ERROR_NO_OPTIONS',   'E2100');

// }}}
// {{{ DB_NestedSet_Output:: class

/**
* DB_NestedSet_Output is a unified API for other output drivers
* Status is beta
*
* At the moment PEAR::HTML_TreeMenu written by Jason Rust is supported
* A driver for treemenu.org will follow soon.
*
* Usage example:
*
* require_once('DB_NestedSet/NestedSet/Output.php');
* $icon         = 'folder.gif';
* $expandedIcon = 'folder-expanded.gif';
* // get data (important to fetch it as an array, using the true flag)
* $data = $NeSe->getAllNodes(true);
* // change the events for one of the elements
* $data[35]['events'] = array('onexpand' => 'alert("we expanded!");');
* // add links to each item
* foreach ($data as $a_data) {
*	$a_data['link'] = 'http://foo.com/foo.php?' . $a_data['id'];
* }
* $params = array(
* 'structure' => $data,
* 'options' => array(
* 'icon' => $icon,
* 'expandedIcon' => $expandedIcon,
* ),
* 'textField' => 'name',
* 'linkField' => 'link',
* );
* $menu =& DB_NestedSet_Output::factory('TreeMenu', $params);
* $menu->printListbox();
*
* @author       Daniel Khan <dk@webcluster.at>
* @package      DB_NestedSet
* @version      $Revision: 1.1.1.1 $
* @access       public
* 
*/

// }}}
class DB_NestedSet_Output {
    // {{{ properties

    /**
     * @var object The tree menu structure
     * @access private
     */
	var $_structTreeMenu	= false;

	/**
	* @var array Array of options to be passed to the ouput methods
	* @access public
	*/
	var $options	= array();
		
    // }}}
	// {{{ factory()

    /**
     * Returns a output driver object
     *
     * @param array $params A DB_NestedSet nodeset
     * @param string $driver (optional) The driver, such as TreeMenu (default)
     *
     * @access public
     * @return object The DB_NestedSet_Ouput object
     */		
	function &factory ($params, $driver = 'TreeMenu') {
		
		$path = dirname(__FILE__).'/'.$driver.'.php';
		
		if(is_dir($path) || !file_exists($path)) {
			PEAR::raiseError("The output driver '$driver' wasn't found", NESEO_DRIVER_NOT_FOUND, PEAR_ERROR_TRIGGER, E_USER_ERROR);
		}
		
		require_once($path);
		$driverClass = 'DB_NestedSet_'.$driver;
		return new $driverClass($params);
	}

	// }}}
	// {{{ setOptions()

    /**
     * Set's options for a specific output group (printTree, printListbox)
     * This enables you to set specific options for each output method
     *
     * @param string $group Output group ATM 'printTree' or 'printListbox'
     * @param array $options Hash with options
     *
     * @access public
     * @return bool
     */		
	function setOptions($group, $options) {
		$this->options[$group] = $options;
		return true;
	}

	// }}}
	// {{{ _getOptions()

    /**
     * Get's all option for a specific output group (printTree, printListbox)
     *
     * @param string $group Output group ATM 'printTree' or 'printListbox'
     *
     * @access private
     * @return array Options
     */			
	function _getOptions($group) {
		
		if (!isset($this->options[$group])) {
			return array();	
		}
		return $this->options[$group];
	}

	// }}}
	// {{{ printTree()

    /**
     * Print's the current tree using the output driver
     * Overriden by the driver class
     *
     * @access public
     */		
	function printTree() {
		PEAR::raiseError("Method not available for this driver", NESEO_ERROR_NO_METHOD, PEAR_ERROR_TRIGGER, E_USER_ERROR);
	}

	// }}}
	// {{{ printListbox()

    /**
     * Print's a listbox representing the current tree
     * Overriden by the driver class
     *
     * @access public
     */			
	function printListbox() {
		PEAR::raiseError("Method not available for this driver", NESEO_ERROR_NO_METHOD, PEAR_ERROR_TRIGGER, E_USER_ERROR);
	}

	// }}}
}
?>
