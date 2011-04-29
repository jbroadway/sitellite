<?php
//
// +----------------------------------------------------------------------+
// | PEAR :: DB_NestedSet_TreeMenu                                        |
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
// | Authors: Jason Rust <jrust@rustyparts.com>                           |
// +----------------------------------------------------------------------+
//
// $Id: TreeMenu.php,v 1.1.1.1 2005/04/29 04:44:36 lux Exp $
//

require_once 'HTML/TreeMenu.php';

// {{{ DB_NestedSet_TreeMenu:: class

/**
 * A helper class to translate the data from a nested set table into a HTML_TreeMenu object
 * so that it can be used to create a dynamic tree menu using the PEAR HTML_TreeMenu class.
 *
 * @see          docs/TreeMenu_example.php
 * @author       Jason Rust <jrust@rustyparts.com>
 * @package      DB_NestedSet
 * @version      $Revision: 1.1.1.1 $
 * @access       public
 */
// }}}
class DB_NestedSet_TreeMenu extends DB_NestedSet_Output {

	/**
	* @var array The current menu structure
	* @access private
	*/
	var $_structTigraMenu = false;

	// {{{ DB_NestedSet_TreeMenu

    function &DB_NestedSet_TreeMenu($params) {
    	$this->_structTreeMenu =& $this->_createFromStructure($params);
    }

    // }}}
    // {{{ _createFromStructure()

    /**
     * <pre>Creates a HTML_TreeMenu structure based off of the results from getAllNodes() method
     * of the DB_NestedSet class.  The needed parameters are:
     * o 'structure' => the result from $nestedSet->getAllNodes(true) 
     * o 'textField' => the field in the table that has the text for node
     * o 'linkField' => the field in the table that has the link for the node
     * o 'options' => (optional) an array of any additional options to pass to the node when
     * Additionally these parameters may be added to the individual nodes to control their
     * behavior:
     * o 'ensureVisible' => (optional) whether or not the field should be forced as visible
     *                creating it such as 'icon' or 'expandedIcon'
     * o 'events' => (optional) an array of any events to pass to the node when creating it
     *               such as 'onclick' or 'onexpand'</pre>
     *
     * @access public
     * @return object A HTML_TreeMenu object
     */
    function &_createFromStructure($params)
    {
        // Basically we go through the array of nodes checking to see 
        // if each node has children and if so recursing.  The reason this
        // works is because the data from getAllNodes() is ordered by level
        // so a root node will always be first, and sub children will always
        // be after them.
        if (!isset($params['treeMenu'])) {
            $treeMenu =& new HTML_TreeMenu();
        } else {
            $treeMenu =& $params['treeMenu'];
        }

        // always start at level 1
        if (!isset($params['currentLevel'])) {
            $params['currentLevel'] = 1;
        }

        // have to use a while loop here because foreach works on a copy of the array and
        // the child nodes are passed by reference during the recursion so that the parent 
        // will know when they have been hit. 
        reset($params['structure']);
        while(list($key, $node) = each($params['structure'])) {
            // see if we've already been here before
            if (isset($node['hit'])) {
                continue;
            }

            // mark that we've hit this node
            $params['structure'][$key]['hit'] = $node['hit'] = true;
            $tag = array(
                'text' => $node[$params['textField']], 
                'link' => $node[$params['linkField']],
                'ensureVisible' => isset($node['ensureVisible']) ? $node['ensureVisible'] : false,
            );
            $options = isset($params['options']) ? array_merge($params['options'], $tag) : $tag;
            $events = isset($node['events']) ? $node['events'] : array();
            $parentNode =& $treeMenu->addItem(new HTML_TreeNode($options, $events));
            // see if it has children
            if (($node['r'] - 1) != $node['l']) {
                $children = array();
                // harvest all the children
				$tempStructure = $params['structure'];
                foreach ($tempStructure as $childKey => $childNode) {
                    if (!isset($childNode['hit']) && 
                        $childNode['l'] > $node['l'] &&
                        $childNode['r'] < $node['r'] &&
                        $childNode['rootid'] == $node['rootid']) {
                        // important that we assign it by reference here, so that when the child
                        // marks itself 'hit' the parent loops will know
                        $children[] =& $params['structure'][$childKey];
                    }
                }

                $recurseParams = $params;
                $recurseParams['structure'] = $children;
                $recurseParams['treeMenu'] =& $parentNode;
                $recurseParams['currentLevel']++;
                $this->_createFromStructure($recurseParams);
            }
        }

        return $treeMenu;
    }

    // }}}
    // {{{ printTree()

    /**
     * Print's the current tree using the output driver
     *
     * @access public
     */		    
    function printTree() {
		$options = $this->_getOptions('printTree');
    	$tree =& new HTML_TreeMenu_DHTML($this->_structTreeMenu, $options);
    	$tree->printMenu();
    }

	// }}}
	// {{{ printListbox()

    /**
     * Print's a listbox representing the current tree
     *
     * @access public
     */			    
    function printListbox() {
    	$options = $this->_getOptions('printListbox');
    	$listBox  =& new HTML_TreeMenu_Listbox($this->_structTreeMenu, $options);
    	$listBox->printMenu();
    }

    // }}}
}
?>
