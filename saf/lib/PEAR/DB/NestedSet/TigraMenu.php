<?php
//
// +----------------------------------------------------------------------+
// | PEAR :: DB_NestedSet_TigraMenu                                        |
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
// +----------------------------------------------------------------------+
//
// $Id: TigraMenu.php,v 1.1.1.1 2005/04/29 04:44:36 lux Exp $
//

// {{{ DB_NestedSet_TigraMenu:: class

/**
* This class can be used to generate the data to build javascript popup menu
* from a DB_NestedSet node array.
* The Javascript part is done using the free available TigraMenu
* available at http://www.softcomplex.com/products/tigra_menu/.
* Currently version 1.0 is supported.
* Parts of this class where taken ftom the TreemMenu driver by Jason Rust
*
* @author       Daniel Khan <dk@webcluster.at>
* @package      DB_NestedSet
* @version      $Revision: 1.1.1.1 $
* @access       public
*/
// }}}
class DB_NestedSet_TigraMenu extends DB_NestedSet_Output {
    // {{{{ properties
	
	/**
	* @var integer The depth of the current menu.
	* @access private
	*/
	var $_levels	= 1;

	/**
	* @var integer The level we started at
	* @access private
	*/	
	var $_levelOffset = false;
	
	
	/**
	* @var array The current menu structure
	* @access private
	*/
	var $_structTigraMenu = false;

	/**
	* @var array The longest text for each level
	* @access private
	*/	
	var $_strlenByLevel	= array();

    // }}}
	// {{{ DB_NestedSet_TigraMenu
	
	/**
	* Constructor
	*
	* @param array $params A hash with parameters needed by the class
	* @see _createFromStructure()
	* @return bool
	**/
	function &DB_NestedSet_TigraMenu($params) {
		$this->_menu_id = $params['menu_id'];
		$this->_structTigraMenu = $this->_createFromStructure($params);
		return true;
	}
	
	// }}}
	// {{{ _createFromStructure()
	
	/**
	* Creates the JavaScript array for TigraMenu
	* Initially this method was introduced for the TreeMenu driver by Jason Rust
	*
	* o 'structure' => the result from $nestedSet->getAllNodes(true)
	* o 'textField' => the field in the table that has the text for node
	* o 'linkField' => the field in the table that has the link for the node
	*
	* @access private
	* @return string The TigraMenu JavaScript array
	*/
	function &_createFromStructure($params)
	{
		// Basically we go through the array of nodes checking to see
		// if each node has children and if so recursing.  The reason this
		// works is because the data from getAllNodes() is ordered by level
		// so a root node will always be first, and sub children will always
		// be after them.
		
		static $rootlevel;
		
		// always start at level 1
		if (!isset($params['currentLevel'])) {
			$params['currentLevel'] = 1;
		}
		
		if (!isset($rootlevel)) {
			$rootlevel = $params['currentLevel'];
		}
		
		if (isset($params['tigraMenu'])) {
			$tigraMenu = $tigraMenu.$params['tigraMenu'];
		}
		
		if(!$this->_levelOffset) {
			$this->_levelOffset = $params['currentLevel'];
		}

		if($this->_levels < ($params['currentLevel']- $this->_levelOffset)) {
			$this->_levels = $params['currentLevel'] - $this->_levelOffset;
		}
		
		
		// have to use a while loop here because foreach works on a copy of the array and
		// the child nodes are passed by reference during the recursion so that the parent
		// will know when they have been hit.
		reset($params['structure']);
		while(list($key, $node) = each($params['structure'])) {
			// see if we've already been here before
			if (isset($node['hit']) || $node['level'] < $params['currentLevel']) {
				continue;
			}
			
			// mark that we've hit this node
			$params['structure'][$key]['hit'] = $node['hit'] = true;
			
			$tag = array(
			isset($node[$params['textField']]) ? "'".$node[$params['textField']]."'" : 'null',
			isset($node[$params['linkField']]) ? "'".$node[$params['linkField']]."'" : 'null'
			);
			
			if (!$this->_strlenByLevel[$params['currentLevel'] - $this->_levelOffset] || 
                strlen($node[$params['textField']]) > $this->_strlenByLevel[$params['currentLevel'] - $this->_levelOffset]) {
				$this->_strlenByLevel[$params['currentLevel'] - $this->_levelOffset] = strlen($node[$params['textField']]);
			};
			
			$tigraMenu = $tigraMenu.$this->_openSubMenu($tag);
			
			// see if it has children
			if (($node['r'] - 1) != $node['l']) {
				$children = array();
				// harvest all the children
				$tempStructure = $params['structure'];
				foreach ($tempStructure as $childKey => $childNode) {
					if (!isset($childNode['hit']) &&
					$node['rootid'] == $childNode['rootid'] &&
					$node['l'] < $childNode['l'] &&
					$node['r'] > $childNode['r'] &&
					$childNode['level'] > $params['currentLevel']) {
						// important that we assign it by reference here, so that when the child
						// marks itself 'hit' the parent loops will know
						$children[] =& $params['structure'][$childKey];
					}
				}
				
				$recurseParams = $params;
				$recurseParams['structure'] = $children;
				$recurseParams['currentLevel']++;
				$tigraMenu = $tigraMenu.$this->_createFromStructure($recurseParams);
			}
			
			$tigraMenu = $tigraMenu.$this->_closeSubMenu();
		}
		return $tigraMenu;
	}
	
	// }}}
	// {{{ _openMenu()
	
	/**
	* Returns the string which opens the JavaScript menu
	* 
	* @access private
	* @param int $menu_id ID of the menu needed to use more than one menu on a page
	* @return string The JavaScript piece
	*/
	function _openMenu($menu_id=1) 
    {
		$str = false;
		$str = $str."var MENU_ITEMS".$menu_id." = new Array();\n";
		$str = $str."MENU_ITEMS".$menu_id." = [\n";
		return $str;
	}

	// }}}
	// {{{ _openSubMenu()	
	
	/**
	* Returns the string which opens a submenu within the JavaScript menu
	* 
	* @access private
	* @param array $tag Contains the content of the current item (name, link)
	* @return string The JavaScript piece
	*/
	function _openSubMenu($tag) 
    {
		$rtag = implode(', ', $tag);
		return "\n[".$rtag.',';
	}

	// }}}
	// {{{ _closeMenu()	
		
	/**
	* Closes the JavaScript array
	* 
	* @access private
	* @return string The JavaScript piece
	*/	
	function _closeMenu() 
    {
		
		return '];';
	}

	// }}}
	// {{{ _closeSubMenu()	
		
	/**
	* Closes the JavaScript array of a submenu
	* 
	* @access private
	* @return string The JavaScript piece
	*/		
	function _closeSubMenu() 
    {
		return "\n],";
	}
	
	// }}}
	// {{{ _addStyles()

	/**
	* Creates the JavaScript code which sets the styles for each level
	* 
	* @access private
	* @param int $menu_id ID of the menu needed to use more than one menu on a page
	* @param array $rootStyles Array of style attributes for the top items
	* @param array $childStyles Array of style attributes for the sub items
	* @return string The JavaScript piece
	*/		
	function _addStyles($menu_id, $rootStyles, $childStyles = false) 
    {
		if (!$childStyles) {
			$childStyles = $rootStyles;
		}
		
		$styles = array();
		foreach ($rootStyles as $key => $val) {
			foreach ($val as $skey => $sval) {
				$styles["'$key'"][$skey][] = "'$sval'";
			}
		}
		
		foreach ($childStyles as $key => $val) {
			foreach ($val as $skey => $sval) {
				for ($i = 1; $i <= $this->_levels; $i++) {
					$styles["'$key'"][$skey][] = "'$sval'";
				}
			}
		}
		
		$menustyles = false;
		$menustyles = $menustyles . 'var MENU_STYLES'.$menu_id." = new Array();\n";
		foreach ($styles as $key => $val) {
			$menustyles = $menustyles.'MENU_STYLES'.$menu_id."[$key] = [\n";
			foreach ($val as $skey => $sval) {
				$menustyles = $menustyles . "'$skey', [".implode(', ', $sval)."],\n";
			}
			$menustyles = $menustyles."];\n";
		}
		
		return $menustyles;
	}

	// }}}	
	// {{{ _addGeometry()

	/**
	* Creates the JavaScript code which sets the position and geometry of the menu
	* 
	* @access private
	* @param int $menu_id ID of the menu needed to use more than one menu on a page
	* @param array $rootGeometry Array of geometry attributes for the top items
	* @param array $childGeometry  Array of geometry attributes for the sub items
	* @return string The JavaScript piece
	*/		
	function _addGeometry($menu_id, $rootGeometry, $childGeometry = false) 
    {
		if (!$childGeometry) {
			$childGeometry = $rootGeometry;
		}
		
		$params = array();
		$geometry = array();
		foreach ($rootGeometry as $key => $val) {
			$geometry["'$key'"][] = $val;
			$incr = false;
			if (strpos($val, ',') !== false) {
				list($start, $interval) = explode(',',$val);
				$incr = true;
			}
			
			$ratio = false;
			if ($key == 'width' && strpos($val, '*') !== false) {
				$ratio = trim(str_replace('*','', $val));
			}
			if ($incr) {
				$val = trim($interval);
				if ($key == 'left' && preg_match('/[+-]/', $interval)) {
					$val = $params[0]['width'] + trim($val);
				}
			} elseif ($incr) {
				$val = trim($start);
			} elseif ($ratio) {
				$val = $ratio * $this->_strlenByLevel[0];
			}
			$geometry["'$key'"][0] = $val;
			$params[0][$key] = $val;
		}
		
		foreach($childGeometry as $key => $val) {
			$incr = false;
			if (strpos($val, ',') !== false) {
				list($start, $interval) = explode(',', $val);
				$incr = true;
			}
			
			$ratio = false;
			if ($key == 'width' && strpos($val, '*') !== false) {
				$ratio = trim(str_replace('*', '', $val));
			}
			
			for ($i = 1; $i <= $this->_levels; $i++) {
				if ($incr && isset($lastval[$key])) {
					$val = trim($interval);
					if($key == 'block_left' && preg_match('/[+-]/', $interval)) {
						$val = $params[$i - 1]['width'] + trim($val);
					}
				} elseif($incr) {
					$val = trim($start);
				} elseif ($ratio) {
					$val = $ratio * $this->_strlenByLevel[$i];
					if($val < $params[0]['width']) {
						$val = 	$params[0]['width'];
					}
				}
				
				$lastval[$key] = $val;
				$geometry["'$key'"][] = $val;
				$params[$i][$key] = $val;
			}
			
		}
		
		$pos = false;
		$pos = $pos . 'var MENU_POS'.$menu_id." = new Array();\n";
		foreach ($geometry as $key => $val) {
			$pos = $pos . 'MENU_POS' . $menu_id . "[$key] = [" . implode(', ', $val) . "];\n";
		}
		
		return $pos;
	}

	// }}}	
	// {{{ printTree()
		
	/**
	* Print's the current tree using the output driver
	*
	* @access public
	*/
	function printTree() 
    {
		if (!$options = $this->_getOptions('printTree')) {
            return PEAR::raiseError("TigraMenu::printTree() needs options. See TigraMenu::setOptions()", NESEO_ERROR_NO_OPTIONS, PEAR_ERROR_TRIGGER, E_USER_ERROR);
		}
		
		echo $this->_openMenu($options['menu_id']) . $this->_structTigraMenu  .$this->_closeMenu();
		echo "\n\n";
		echo $this->_addStyles($options['menu_id'], $options['rootStyles'], $options['childStyles']);
		echo "\n\n";
		echo $this->_addGeometry($options['menu_id'], $options['rootGeometry'], $options['childGeometry']);
	}
	
	// }}}
}
?>
