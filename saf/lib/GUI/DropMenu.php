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
// DropMenu generates HTML and JavaScript for DHTML drop menus.
//

loader_import ('saf.GUI.DropMenuItem');

/**
	 * DropMenu generates HTML and JavaScript for DHTML drop menus.
	 * 
	 * This class is accompanied by a JavaScript script which provides the
	 * necessary JavaScript components to the drop menu system.
	 * 
	 * Also requires the DropMenuItem and Template classes.
	 * 
	 * New in 1.0:
	 * - Fixed some outdated javascript mouse tracking function references.
	 * 
	 * New in 1.2:
	 * - Updated the javascript function names and modified the generated templates
	 *   to use the new SimpleTemplate syntax.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $dropMenu = new DropMenu ('company', 10, 30);
	 * 
	 * // set a background colour change on each item
	 * $dropMenu->extraMouseOver = 'sdmSetBgcolor (\'_td_{layerID}\', \'#CCCCCC\')';
	 * $dropMenu->extraMouseOut = 'sdmSetBgcolor (\'_td_{layerID}\', \'#FFFFFF\');
	 * 
	 * $aboutItem =& $dropMenu->addItem ('About Us', '/index/about');
	 * $aboutMenu =& $aboutItem->addChild ('about');
	 * 
	 * $aboutMenu->addItem ('Corporate Profile', '/index/corporate');
	 * $aboutMenu->addItem ('History', '/index/history');
	 * $invItem =& $aboutMenu->addItem ('Investor Relations', '/index/investors');
	 * $invMenu =& $invItem->addChild ('inv');
	 * $invMenu->addItem ('Annual Report', '/index/annual_report');
	 * 
	 * $aboutMenu->addItem ('Contact Us', '/index/contact');
	 * 
	 * // create the drop menu divs
	 * echo $dropMenu->write ();
	 * 
	 * // next, we need the dropmenu.js file
	 * echo '<script language="javascript1.2" src="/js/dropmenu.js"></script>';
	 * 
	 * // we also need a javascript list of menus called menuList
	 * echo '<script language="javascript1.2"><!--' . "\n";
	 * echo "sdmMenuList = new Array ('" . join ("', '", $dropMenu->getList ()) . "');\n";
	 * echo '// --></script>';
	 * 
	 * // attach this drop menu to something visible on the page.
	 * echo '<a href="/index/company" onmouseover="sdmShowAndHide(\'company\')">Company</a>';
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	GUI
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	1.2, 2003-04-15, $Id: DropMenu.php,v 1.4 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class DropMenu {
	/**
	 * The name of the menu.
	 * 
	 * @access	public
	 * 
	 */
	var $name;

	/**
	 * The x position of the top-left corner of the menu.
	 * 
	 * @access	public
	 * 
	 */
	var $xpos;

	/**
	 * The y position of the top-left corner of the menu.
	 * 
	 * @access	public
	 * 
	 */
	var $ypos;

	/**
	 * The width of the menu.
	 * 
	 * @access	public
	 * 
	 */
	var $menuWidth;

	/**
	 * The height of each item in the menu.  Default is 19.
	 * 
	 * @access	public
	 * 
	 */
	var $lineHeight = 19;

	/**
	 * The template used to draw the drop menu DIV.
	 * 
	 * @access	public
	 * 
	 */
	var $template;

	/**
	 * The template used to draw each drop menu item.
	 * 
	 * @access	public
	 * 
	 */
	var $itemTemplate;

	/**
	 * The direction the menu should open to a new level.  Defaults
	 * to 'right'.
	 * 
	 * @access	public
	 * 
	 */
	var $direction = 'right';

	/**
	 * An array of DropMenuItem objects
	 * 
	 * @access	public
	 * 
	 */
	var $items = array ();

	/**
	 * Optional extra JavaScript to call when each item is in focus.
	 * 
	 * @access	public
	 * 
	 */
	var $extraMouseOver = '';

	/**
	 * Optional extra JavaScript to call in each item loses focus.
	 * 
	 * @access	public
	 * 
	 */
	var $extraMouseOut = '';

	/**
	 * Constructor method.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @param	integer	$xpos
	 * @param	integer	$ypos
	 * @param	integer	$menuWidth
	 * @param	integer	$lineHeight
	 * @param	string	$template
	 * @param	string	$direction
	 * 
	 */
	function DropMenu ($name, $xpos = 0, $ypos = 0, $menuWidth = 150, $lineHeight = 18, $template = '', $direction = 'right') {
		$this->name = $name;
		$this->layerID = preg_replace ('/[^a-zA-Z0-9-]+/', '-', $name);
		$this->xpos = $xpos;
		$this->ypos = $ypos;
		$this->menuWidth = $menuWidth;
		$this->lineHeight = $lineHeight;
		$this->direction = $direction;
		if (! empty ($template)) {
			$this->template = $template;
		} else {
			$this->template = "<!-- {name}: {xpos}, {ypos} -->\n<div id=\"{layerID}\" class=\"sdm\" style=\"\n";
			$this->template .= "\tposition: absolute;\n";
			$this->template .= "\ttop: {ypos}px;\n";
			$this->template .= "\tleft: {xpos}px;\n";
			$this->template .= "\twidth: {menuWidth}px;\n";
			$this->template .= "\tborder: #000 1px solid;\n";
			$this->template .= "\tvisibility: hidden;\n";
			$this->template .= "\tbackground-color: #fff;\n";
			if ($this->direction == 'right') {
				$operator = '+';
			} else {
				$operator = '-';
			}
//			$this->template .= "\" onmouseout=\"mouseTracker ('##layerID##', ##xpos##, ##ypos##,\n (##xpos## $operator ##menuWidth##), (##ypos## + (##lineHeight## * ##itemCount##)))\">\n##itemList##</div>\n\n";
			$this->template .= "\">\n{itemList}</div>\n\n";
		}
		//echo htmlentities ($this->template); exit;
		$this->itemTemplate = '';
	}

	/**
	 * Creates a new DropMenuItem class, passing on what info is required,
	 * and adding it to the items array.
	 * 
	 * @access	public
	 * @param	string	$text
	 * @param	string	$link
	 * @return	object
	 * 
	 */
	function &addItem ($text, $link) {
		array_push ($this->items, new DropMenuItem (
			$text, $link, $this->xpos, $this->ypos + ($this->lineHeight * count ($this->items)), $this->menuWidth, $this->lineHeight, $this->template, $this->itemTemplate)
		);
		$this->items[count ($this->items) - 1]->direction = $this->direction;
		$this->items[count ($this->items) - 1]->extraMouseOver = $this->extraMouseOver;
		$this->items[count ($this->items) - 1]->extraMouseOut = $this->extraMouseOut;
		return $this->items[count ($this->items) - 1];
	}

	/**
	 * Writes the drop menu div and returns it as a string.  The 'show'
	 * property is for internal use, as this method may be called again by one
	 * of the items inside this menu if it has any child menus.
	 * 
	 * @access	public
	 * @param	string	$show
	 * @return	string
	 * 
	 */
	function write ($show = '') {
		$res = '';
		$this->itemList = '';
		$children = '';
		$this->itemCount = count ($this->items);
		foreach ($this->items as $item) {
			// pass along a trail of menu's not to hide
			if (is_array ($show)) {
				array_push ($show, $this->layerID);
				$items = $item->write ($show);
			} elseif (! empty ($show)) {
				$items = $item->write (array ($this->layerID, $show));
			} else {
				$items = $item->write ($this->layerID);
			}

			if (is_array ($items)) {
				$this->itemList .= array_shift ($items);
				$children .= array_shift ($items);
			} else {
				$this->itemList .= $items;
			}
		}
		$res = template_simple ($this->template, $this);
		return $res . $children;
	}

	/**
	 * Returns a list of menu names, including the current menu
	 * and all its children.
	 * 
	 * @access	public
	 * @return	array
	 * 
	 */
	function getList () {
		$menuList = array ($this->layerID);
		foreach ($this->items as $item) {
			if (is_object ($item->child)) {
				$new = $item->child->getList ();
				foreach ($new as $menu) {
					array_push ($menuList, $menu);
				}
			}
		}
		return $menuList;
	}
}



?>