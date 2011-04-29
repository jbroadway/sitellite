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
// DropMenuItem provides individual menu item facilities to the DropMenu
// class.
//

/**
	 * DropMenuItem provides individual menu item facilities to the
	 * DropMenu class, which generates HTML and JavaScript for DHTML drop menus.
	 * 
	 * New in 1.0:
	 * - Fixed some outdated javascript mouse tracking function references.
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
	 * @version	1.0, 2002-10-28, $Id: DropMenuItem.php,v 1.4 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

class DropMenuItem {
	/**
	 * The text of the menu item.
	 * 
	 * @access	public
	 * 
	 */
	var $text;

	/**
	 * The URL to link this item to.
	 * 
	 * @access	public
	 * 
	 */
	var $link;

	/**
	 * The template to use to generate the menu item.
	 * 
	 * @access	public
	 * 
	 */
	var $template;

	/**
	 * The menu template to use to generate child drop menus.
	 * 
	 * @access	public
	 * 
	 */
	var $menu_tpl;

	/**
	 * The x position of the drop menu this item is a part of.  Used for
	 * inheriting to a child menu.
	 * 
	 * @access	public
	 * 
	 */
	var $xpos;

	/**
	 * The y position of the drop menu this item is a part of.  Used for
	 * inheriting to a child menu.
	 * 
	 * @access	public
	 * 
	 */
	var $ypos;

	/**
	 * The width of the drop menu this item is a part of.  Used for
	 * inheriting to a child menu.
	 * 
	 * @access	public
	 * 
	 */
	var $menuWidth;

	/**
	 * The height of this item and each item in its parent drop menu.
	 * Used for inheriting to a child menu.
	 * 
	 * @access	public
	 * 
	 */
	var $lineHeight;

	/**
	 * The child DropMenu object of this item.
	 * 
	 * @access	public
	 * 
	 */
	var $child;

	/**
	 * The private mouseover data detailing this item's behaviour.
	 * 
	 * @access	private
	 * 
	 */
	var $mouseover;

	/**
	 * The direction of the drop menu this item is a part of.  Used for
	 * inheriting to a child menu.
	 * 
	 * @access	public
	 * 
	 */
	var $direction = 'right';

	/**
	 * Optional extra JavaScript to call when the item is in focus.
	 * 
	 * @access	public
	 * 
	 */
	var $extraMouseOver = '';

	/**
	 * Optional extra JavaScript to call when the item loses focus.
	 * 
	 * @access	public
	 * 
	 */
	var $extraMouseOut = '';

	/**
	 * Constructor method.
	 * 
	 * @access	public
	 * @param	string	$text
	 * @param	string	$link
	 * @param	integer	$xpos
	 * @param	integer	$ypos
	 * @param	integer	$menuWidth
	 * @param	integer	$lineHeight
	 * @param	string	$menu_tpl
	 * @param	string	$link_tpl
	 * 
	 */
	function DropMenuItem ($text, $link, $xpos = 0, $ypos = 0, $menuWidth = 150, $lineHeight = 18, $menu_tpl = '', $link_tpl = '') {
		$this->text = $text;
		$this->link = $link;
		$this->xpos = $xpos;
		$this->ypos = $ypos;
		$this->menuWidth = $menuWidth;
		$this->lineHeight = $lineHeight;
		$this->mouseover = '';
		if (! empty ($menu_tpl)) {
			$this->menu_tpl = $menu_tpl;
		} else {
			$this->menu_tpl = '';
		}
		if (! empty ($link_tpl)) {
			$this->template = $link_tpl;
		} else {
			$this->template = "\t<a href=\"{link}\"{mouseover}{mouseout}>{text}</a><br />\n";
		}
	}

	/**
	 * Writes the drop menu item, as well as any child menus, and returns
	 * it as a string.  The 'show' property is for internal use, as this method
	 * may be called again by one of the items inside this child's menu if it has
	 * any child menus.
	 * 
	 * @access	public
	 * @param	string	$show
	 * @return	string
	 * 
	 */
	function write ($show = '') {
		if (! empty ($this->extraMouseOver)) {
			$extraover = '; ' . template_simple ($this->extraMouseOver, $this);
		}
		if (! empty ($this->extraMouseOut)) {
			$this->mouseout = ' onmouseout="sdmMouseExitMenu(); ' . template_simple ($this->extraMouseOut, $this) . '"';
		} else {
			$this->mouseout = ' onmouseout="sdmMouseExitMenu()"';
		}

		if (is_object ($this->child)) {
			// show the proper trail of menus
			if (is_array ($show)) {
				$this->mouseover = ' onmouseover="sdmShowAndHide (\'' . join ("', '", $show) . "', '" . $this->child->layerID . '\')' . $extraover . '"';
			} elseif (! empty ($show)) {
				$this->mouseover = " onmouseover=\"sdmShowAndHide ('$show', '" . $this->child->layerID . "')" . $extraover . "\"";
			} else {
				$this->mouseover = " onmouseover=\"sdmShowAndHide ('" . $this->child->layerID . "')" . $extraover . "\"";
			}
			$results = array (template_simple ($this->template, $this));
			array_push ($results, $this->child->write ($show));
			return $results;
		} else {
			if (is_array ($show)) {
				$this->mouseover = ' onmouseover="sdmShowAndHide (\'' . join ("', '", $show) . '\')' . $extraover . '"';
			} elseif (! empty ($show)) {
				$this->mouseover = ' onmouseover="sdmShowAndHide (\'' . $show . '\')' . $extraover . '"';
			} else {
				$this->mouseover = ' onmouseover="sdmShowAndHide ()' . $extraover . '"';
			}
			return template_simple ($this->template, $this);
		}
	}

	/**
	 * Creates a new DropMenu class, passing on what info is required,
	 * and adding it to the child property.  Note: an item may have only one
	 * child menu.
	 * 
	 * @access	public
	 * @param	string	$name
	 * @return	object
	 * 
	 */
	function &addChild ($name) {
		if ($this->direction == 'right') {
			$this->child = new DropMenu (
				$name, $this->xpos + $this->menuWidth - 1, $this->ypos, $this->menuWidth, $this->lineHeight, $this->menu_tpl, $this->direction
			);
		} else {
			$this->child = new DropMenu (
				$name, $this->xpos - $this->menuWidth + 1, $this->ypos, $this->menuWidth, $this->lineHeight, $this->menu_tpl, $this->direction
			);
		}
		$this->child->itemTemplate = $this->template;
		$this->child->direction = $this->direction;
		$this->child->extraMouseOver = $this->extraMouseOver;
		$this->child->extraMouseOut = $this->extraMouseOut;
		return $this->child;
	}
}



?>