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
// MenuItem is the item class that compliments the Menu class and provides
// much of its recursive logic.
//

/**
	 * MenuItem is the item class that compliments the Menu class and provides
	 * much of its recursive logic.
	 * 
	 * New in 1.2:
	 * - The $id property value now has no bearing on which template to use if an array
	 *   of templates is provided.  This was a problem when the template array keys
	 *   and $id values were both numeric.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $foo = new MenuItem ('foo', 'Foo');
	 * 
	 * $bar =& $foo->addChild ('bar', 'Bar');
	 * 
	 * echo $foo->display ('html', '<a href="/index/##id##">##title##</a>');
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	GUI
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.2, 2002-09-27, $Id: MenuItem.php,v 1.3 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class MenuItem {
	/**
	 * The (usually) unique id of this item.
	 * 
	 * @access	public
	 * 
	 */
	var $id;

	/**
	 * The title of this item.
	 * 
	 * @access	public
	 * 
	 */
	var $title;

	/**
	 * A reference to the parent of this item, or false if there
	 * is no parent.
	 * 
	 * @access	public
	 * 
	 */
	var $parent;

	/**
	 * An array of child items.
	 * 
	 * @access	public
	 * 
	 */
	var $children;

	/**
	 * An array of background colours for this item, which is the
	 * same as the $colours property of saf.GUI.Menu.
	 * 
	 * @access	public
	 * 
	 */
	var $colours;

	/**
	 * Constructor method.
	 * 
	 * @access	public
	 * @param	string	$id
	 * @param	string	$title
	 * 
	 */
	function MenuItem ($id, $title) {
		$this->id = $id;
		$this->title = $title;
		$this->parent = false;
		$this->children = array ();
		$this->colours = array ('#ffffff', '#eeeeee', '#dddddd', '#cccccc', '#bbbbbb', '#aaaaaa', '#999999', '#aaaaaa', '#bbbbbb', '#cccccc', '#dddddd', '#eeeeee', '#dddddd', '#cccccc', '#bbbbbb', '#aaaaaa', '#999999', '#aaaaaa', '#bbbbbb', '#cccccc', '#dddddd', '#eeeeee');
		$this->template = false;
	}

	/**
	 * Creates a new child item below the current item.
	 * 
	 * @access	public
	 * @param	string	$id
	 * @param	string	$title
	 * @return	object reference
	 * 
	 */
	function &addChild ($id, $title) {
		$this->children[] = new MenuItem ($id, $title);
		return $this->children[count ($this->children) - 1];
	}

	/**
	 * Creates a breadcrumb trail as an array of item objects.
	 * 
	 * @access	public
	 * @return	array
	 * 
	 */
	function trail () {
		$res = array ();
		if ($this->parent) {
			$t = $this->parent->trail ();
			foreach ($t as $i) {
				$res[] = $i;
			}
		}
		$res[] = $this;
		return $res;
	}

	/**
	 * Renders this item and those below it.  $mode can be 'html' or
	 * 'collapsible'.  $tplt can be a single template or an array of templates.
	 * If $tplt is an associative array, the appropriate template will be chosen.
	 * If there aren't an equal number of templates in the array to the number
	 * of levels in the menu, the last template in the list will be used
	 * repeatedly for all subsequent levels.  If $recursive is set to false,
	 * it will only render a single level of the tree.
	 * 
	 * @access	public
	 * @param	string	$mode
	 * @param	mixed	$tplt
	 * @param	boolean	$recursive
	 * @return	string
	 * 
	 */
	function display ($mode = 'html', $tplt = '', $recursive = 1) {
		global $simple, $page;

		if (is_array ($tplt)) {
			if ($tplt[$recursive - 1]) {
				$use_template = $tplt[$recursive - 1];
			} else {
				$use_template = $tplt[count ($tplt) - 1];
			}
		} else {
			$use_template = $tplt;
		}

		if ($mode == 'html') {
			$res = '<li';
			if ($this->id == $page->id) {
				$res .= ' id="sitellite-menu-current"';
			}
			$res .= '>';
			$res .= $simple->fill ($use_template, $this);
			if ($recursive && count ($this->children) > 0) {
				$res .= '<ul>';
				foreach ($this->children as $child) {
					$res .= $child->display ($mode, $tplt, $recursive + 1);
				}
				$res .= '</ul>';
			}
			$res .= '</li>';
			return $res;
		} elseif ($mode == 'collapsible') {
			$res = '';
			if (is_object ($this->parent)) {
				$key = $this->parent->id;
			} else {
				$key = 'SITELLITE_EMPTY';
			}
			if ($recursive && count ($this->children) > 0) {
				$res .= 'List_' . $this->id . ' = new List (false, width, height, "' . $this->colours[$recursive] . '");' . "\n";
				foreach ($this->children as $child) {
					$res .= $child->display ($mode, $tplt, $recursive + 1);
				}
				$res .= 'List_' . $key . '.addList (List_' . $this->id . ', "' . str_replace ('"', '\\"', $simple->fill ($use_template, $this)) . '");' . "\n";
				$res .= 'List_' . $key . '.collapsedImageURL = falsePic;' . "\n";
				$res .= 'List_' . $key . '.expandedImageURL = truePic;' . "\n";
			} else {
				$res .= 'List_' . $key . '.addItem ("' . str_replace ('"', '\\"', $simple->fill ($use_template, $this)) . '");' . "\n";
			}
			return $res;
		}
	}
}



?>