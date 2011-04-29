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
// Menu is a class that is used to generate navigation systems on the
// fly on web sites, based on a self-referencial database table structure.
// It is mostly for use through EasyText.
//
// resolved tickets:
// #172 error in menu structure.
//

$GLOBALS['loader']->import ('saf.GUI.MenuItem');

/**
	 * Menu is a class that is used to generate navigation systems on the
	 * fly on web sites, based on a self-referencial database table structure.
	 * 
	 * New in 1.2:
	 * - Fixed a PHP4.2+ compatibility issue where it was calling $GLOBALS['PHP_SELF'],
	 *   now it calls $_SERVER['PHP_SELF'], and calls global $_SERVER so that versions
	 *   of PHP prior to 4.1 can rely on a $_SERVER reference to $HTTP_SERVER_VARS.
	 * 
	 * New in 2.0:
	 * - Rewritten so that Menu is no longer so strongly tied to EasyText.  The PHP
	 *   API is much improved.  New features include loading of the site tree in a
	 *   single database call (in the getTree() method), and storing the resulting
	 *   tree in memory or in a file as PHP code (created using the buildConfig()
	 *   method) which can be retrived using the loadConfig() method.  Trees can also
	 *   be built manually using the addItem() method, which takes care off all the
	 *   messy stuff for you.
	 * - Moved from saf.EasyText.Menu to saf.GUI.Menu.
	 * 
	 * New in 2.2:
	 * - Changed addItem() so that it returns a reference to the new item object.
	 * 
	 * New in 2.4:
	 * - Changed sorting order to be by $showcol instead of $idcol.
	 * 
	 * New in 2.6:
	 * - Removed the EasyText() and EasyTextInit() methods.
	 * - Added the countChildren() and getChildren() methods.
	 *
	 * New in 2.8:
	 * - Added sort_weight field.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $menu = new Menu ('sitellite_page', 'id', 'title', 'below_page');
	 * 
	 * $menu->getTree ();
	 * 
	 * echo $menu->trail ('contact', '<a href="/index/##id##">##title##</a>');
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	GUI
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @version	2.6, 2003-06-18, $Id: Menu.php,v 1.12 2007/12/20 09:07:53 lux Exp $
	 * @access	public
	 * 
	 */

class Menu {
	/**
	 * Contains a multi-dimensional array of MenuItem objects.
	 * 
	 * @access	public
	 * 
	 */
	var $tree;

	/**
	 * The name of the database table (if any) that this menu is being
	 * pulled from.
	 * 
	 * @access	public
	 * 
	 */
	var $table;

	/**
	 * The name of the column in the database table that contains the
	 * primary key values.
	 * 
	 * @access	public
	 * 
	 */
	var $idcolumn;

	/**
	 * The name of the column in the database table that is to be
	 * displayed.
	 * 
	 * @access	public
	 * 
	 */
	var $showcolumn;

	/**
	 * The name of the column in the database table that makes a
	 * self-reference to the primary key, or id, column.
	 * 
	 * @access	public
	 * 
	 */
	var $refcolumn;

	/**
	 * The name of the column in the database table that says
	 * whether or not to display each record as an item.
	 * 
	 * @access	public
	 * 
	 */
	var $listcolumn;

	/**
	 * The field to sort by, in case of a sort field.  A sort field outweighs
	 * the default sorting alphabetically.
	 *
	 * @access	public
	 *
	 */
	var $sortcolumn;

	/**
	 * The sort order, either 'ASC' (ascending) or 'DESC' (descending).
	 *
	 * @access	public
	 *
	 */
	var $sortorder;

	/**
	 * Specifies whether or not to use the global $sitellite object
	 * (from the Sitellite CMS-only package saf.App.Conf.Sitellite) to add
	 * automatic permission limits on the database query.  For more information,
	 * see saf.App.Conf.Sitellite in DocReader.
	 * 
	 * @access	public
	 * 
	 */
	var $sitelliteAllowed;

	var $cache = false;
	var $cacheLocation = 'cache/.menu';

	/**
	 * Contains the most recent error message if an error has occurred,
	 * false otherwise.
	 * 
	 * @access	public
	 * 
	 */
	var $error;

	/**
	 * The path to the image to use for the'"closed' graphic in the
	 * collapsible display mode.  Defaults to '/pix/false.gif'.
	 * 
	 * @access	public
	 * 
	 */
	var $closedPic;

	/**
	 * The path to the image to use for the'"opened' graphic in the
	 * collapsible display mode.  Defaults to '/pix/true.gif'.
	 * 
	 * @access	public
	 * 
	 */
	var $openedPic;

	/**
	 * The distance from the left edge of the browser window to display
	 * the menu in collapsible display mode.  Default is 0.
	 * 
	 * @access	public
	 * 
	 */
	var $xpos;

	/**
	 * The distance from the top edge of the browser window to display
	 * the menu in collapsible display mode.  Default is 0.
	 * 
	 * @access	public
	 * 
	 */
	var $ypos;

	/**
	 * The value that the database column in $listcolumn is supposed
	 * to contain when a field is not to be shown.  Defaults to 'no'.
	 * 
	 * @access	public
	 * 
	 */
	var $hideValue;

	/**
	 * A list of background colours to use to differentiate between
	 * levels in the collapsible display mode.  Starts at #ffffff (white) and
	 * goes down to #999999 (medium grey) and back.
	 * 
	 * @access	public
	 * 
	 */
	var $colours;

	/**
	 * The directory to find the JavaScript for the collapsible view
	 * in.  Default is '/js'.
	 * 
	 * @access	public
	 * 
	 */
	var $jsDir;

	/**
	 * Remembers whether or not the JavaScript for the collapsible
	 * display mode has been included yet or not.
	 * 
	 * @access	public
	 * 
	 */
	var $includedJs;

	/**
	 * The name of the JavaScript init() function for this menu.
	 * Different menus on the same page need separate init() functions.
	 * The default is 'saf_xbinit_' plus the name of the database table
	 * linked to this menu.
	 * 
	 * @access	public
	 * 
	 */
	var $initFunc;

	/**
	 * Constructor method.
	 * 
	 * @access	public
	 * @param	string	$table
	 * @param	string	$idcol
	 * @param	string	$showcol
	 * @param	string	$refcol
	 * @param	string	$listcol
	 * 
	 */
	function Menu ($table = '', $idcol = '', $showcol = '', $refcol = '', $listcol = '', $sectioncol = '', $templatecol = '') {
		$this->table = $table;
		$this->idcolumn = $idcol;
		$this->showcolumn = $showcol;
		$this->refcolumn = $refcol;
		$this->listcolumn = $listcol;
		$this->sectioncolumn = $sectioncol;
		$this->templatecolumn = $templatecol;
		$this->sitelliteAllowed = 0;
		$this->tree = array ();
		$this->error = false;
		$this->closedPic = '/pix/false.gif';
		$this->openedPic = '/pix/true.gif';
		$this->xpos = 0;
		$this->ypos = 0;
		$this->hideValue = 'no';
		$this->colours = array ('#ffffff', '#eeeeee', '#dddddd', '#cccccc', '#bbbbbb', '#aaaaaa', '#999999', '#aaaaaa', '#bbbbbb', '#cccccc', '#dddddd', '#eeeeee', '#dddddd', '#cccccc', '#bbbbbb', '#aaaaaa', '#999999', '#aaaaaa', '#bbbbbb', '#cccccc', '#dddddd', '#eeeeee');
		$this->jsDir = '/js';
		$this->includedJs = false;
		$this->initFunc = 'saf_xbinit_' . $table;
	}

	/**
	 * Builds the item tree from the database table specified.
	 * 
	 * @access	public
	 * @return	boolean
	 * 
	 */
	function getTree () {
		if (! session_valid () && $this->cache > 0) {
			if (intl_lang () != intl_default_lang ()) {
				$this->cacheLocation .= '_' . intl_lang ();
			}
			if (@file_exists ($this->cacheLocation) && filemtime ($this->cacheLocation) > time () - $this->cache) {
				$this->loadConfig ($this->cacheLocation);
				return true;
			}
		}
		$table = $this->table;
		$idcol = $this->idcolumn;
		$showcol = $this->showcolumn;
		$refcol = $this->refcolumn;
		$listcol = $this->listcolumn;
		$hidevalue = $this->hideValue;

		if (! empty ($listcol)) {
//			$list = "where $listcol != '$hidevalue' and $listcol is not null";
			$listcolumn = ', ' . $listcol;
		} else {
			$list = '';
			$listcolumn = '';
		}

		if (! empty ($this->sectioncolumn)) {
			$sectioncolumn = ', ' . $this->sectioncolumn;
		} else {
			$sectioncolumn = '';
		}

		if (! empty ($this->templatecolumn)) {
			$templatecolumn = ', ' . $this->templatecolumn;
		} else {
			$templatecolumn = '';
		}

		if ($this->sitelliteAllowed) {

			global $session;

			if (! empty ($list)) {
				$allowed = 'and ' . $session->allowedSql ();
			} else {
				$allowed = 'where ' . $session->allowedSql ();
			}
		} else {
			$allowed = '';
		}

		if ($this->sortcolumn) {
			$sort = ', ' . $this->sortcolumn;
			if ($this->sortorder) {
				$sort .= ' ' . $this->sortorder;
			}
		} else {
			$sort = '';
		}

        // Fetch all pages
        $tree = db_fetch ("select $idcol, $showcol, $refcol $listcolumn $sectioncolumn $templatecolumn from $table $list $allowed group by $refcol $sort, $showcol asc");

		if (! $tree) {
			$this->error = db_error ();
			return false;
		} elseif (is_object ($tree)) {
			$tree = array ($tree);
		}

        // Build menu tree
		$this->initTree ($tree);

        // Translate items
        if (intl_lang () != intl_default_lang ()) {
			loader_import ('multilingual.Translation');
			$tr = new Translation ($table, intl_lang ());
			$titles = $tr->getTitles ();
			foreach ($titles as $key => $title) {
				$this->{'items_' . $key}->title = $title;
			}
		}

		if (! session_valid () && $this->cache > 0) {
			if (@is_writeable ($this->cacheLocation) || (! @file_exists ($this->cacheLocation) && @is_writeable (dirname ($this->cacheLocation)))) {
				$fp = fopen ($this->cacheLocation, 'w');
				if ($fp) {
					fwrite ($fp, $this->makeConfig ($tree));
					fclose ($fp);
				}
			}
		}

		return true;
	}

	function initTree ($tree) {
		// add the items to the list
		$parents = array ();
		foreach ($tree as $k => $v) {
			if ($v->{$this->listcolumn} == 'no') {
				$parents[$v->{$this->idcolumn}] = $v->{$this->refcolumn};
			}
		}

		foreach ($tree as $k => $v) {
			if ($v->{$this->listcolumn} == 'no') {
				continue;
			} elseif (in_array ($v->{$this->refcolumn}, array_keys ($parents))) {
				while (in_array ($v->{$this->refcolumn}, array_keys ($parents))) {
					$new_parent = $parents[$v->{$this->refcolumn}];
					$tree[$k]->{$this->refcolumn} = $new_parent;
					$v->{$this->refcolumn} = $new_parent;
				}
			}
            // Create menu items
            $this->addItem (
				$v->{$this->idcolumn},
				$v->{$this->showcolumn},
				$v->{$this->refcolumn},
				$v->{$this->sectioncolumn},
				$v->{$this->templatecolumn}
			);
		}

		// link the children to their parents
		foreach ($tree as $k => $v) {
			if ($v->{$this->listcolumn} == 'no') {
				continue;
			}

// Start: SEMIAS #172 error in menu structure.
// -----------------
// foreach ($tree as $k => $v) {
//			if ($v->{$this->listcolumn} == 'no') {
//				continue;
//			}
// -----------------
            if ($v->{$this->refcolumn}) {

				$ref = $v->{$this->refcolumn};
                $parent = $this->{'items_' . $v->{$this->idcolumn}}->parent;

                // When parent is of type StdClass, no id is set.
                // Below: fix this and make it type MenuItem
                if(!$parent->{$this->idcolumn}) {
				    // link the parent attr to the parent object
				    $this->{'items_' . $v->{$this->idcolumn}}->parent =& $this->{'items_' . $ref};

   				    // link the children list to the child object
				    $this->{'items_' . $ref}->children[] =& $this->{'items_' . $v->{$this->idcolumn}};
                }

			}
		}
// END: SEMIAS

		return true;
	}

	function findParent ($ref) {
		if (is_object ($this->{'items_' . $ref})) {
			return $ref;
		}
		$ref = db_shift ('select below_page from sitellite_page where id = ?', $ref);
		return $this->findParent ($ref);
	}

	/**
	 * Adds an item to the tree.  $ref is the id of the parent item.
	 * 
	 * @access	public
	 * @param	string	$id
	 * @param	string	$title
	 * @param	string	$ref
	 * @return	object reference
	 * 
	 */
	function &addItem ($id, $title, $ref = '', $sect = '', $template = '') {
		$this->{'items_' . $id} =& new MenuItem ($id, $title);
		if (empty ($ref)) {
			$this->tree[] =& $this->{'items_' . $id};
		} else {
			$this->{'items_' . $ref}->children[] =& $this->{'items_' . $id};
			$this->{'items_' . $id}->parent =& $this->{'items_' . $ref};
		}

		$this->{'items_' . $id}->colours = $this->colours;
		$this->{'items_' . $id}->is_section = ($sect == 'yes') ? true : false;
		if (! empty ($template)) {
			$this->{'items_' . $id}->template = $template;
		}
		return $this->{'items_' . $id};
	}

	/**
	 * Loads the item tree from an include file or string.
	 * 
	 * @access	public
	 * @param	string	$file
	 * 
	 */
	function loadConfig ($file) {
		/* include file should define all menu items as follows:
		 *
		 * top level items:
		 * $this->tree[] = new MenuItem ($id, $title);
		 * $this->{'items_' . $id} =& $this->tree[count ($this->tree) - 1];
		 *
		 * inner items:
		 * $this->{'items_' . $ref}->addChild ($id, $title);
		 * $this->{'items_' . $id} =& $this->{'items_' . $ref}->children[count ($this->{'items_' . $ref}->children) - 1];
		 * $this->{'items_' . $id}->parent =& $this->{'items_' . $ref};
		 *
		 * you don't have to make config files by hand though, since the
		 * makeConfig() method will create them for you from an existing
		 * menu structure.
		 */
		if (@file_exists ($file)) {
			include_once ($file);
		} else {
			eval (CLOSE_TAG . $file);
		}
	}

	/**
	 * Creates the PHP code for a config file from the item tree.
	 * 
	 * @access	public
	 * @return	string
	 * 
	 */
	function makeConfig ($tree) {
		// makes a config file of the current $tree, which can be saved and reused
		$conf = OPEN_TAG . "\n\n";
		$conf .= '$tree = unserialize ("' . str_replace ('"', '\\"', serialize ($tree)) . "\");\n";
		$conf .= '$this->initTree ($tree);' . "\n\n";

/*
		foreach (get_object_vars ($this) as $key => $value) {
			if (is_object ($value) && preg_match ('/^items_.*$/', $key)) {
				$id = str_replace ('"', '\\"', $value->id);
				$title = str_replace ('"', '\\"', $value->title);
				if (! is_object ($value->parent)) {
					// top level item
//					$conf .= '$this->tree[] = new MenuItem ("' . $id . '", "' . $title . '");' . "\n";
//					$conf .= '$this->{"items_' . $id . '"} =& $this->tree[count ($this->tree) - 1];' . "\n";
//					$conf .= '$this->{"items_' . $id . '"}->colours = $this->colours;' . "\n\n";
					$conf .= '$this->addItem ("' . $id . '", "' . $title . '");' . "\n\n";
				} else {
					// inner item
					$ref = str_replace ('"', '\\"', $value->parent->id);
//					$conf .= '$this->{"items_' . $ref . '"}->addChild ("' . $id . '", "' . $title . '");' . "\n";
//					$conf .= '$this->{"items_' . $id . '"} =& $this->{"items_' . $ref . '"}->children[count ($this->{"items_' . $ref . '"}->children) - 1];' . "\n";
//					$conf .= '$this->{"items_' . $id . '"}->parent =& $this->{"items_' . $ref . '"};' . "\n";
//					$conf .= '$this->{"items_' . $id . '"}->colours = $this->colours;' . "\n\n";
					$conf .= '$this->addItem ("' . $id . '", "' . $title . '", "' . $ref . '");' . "\n\n";
				}
			}
		}
*/

		$conf .= CLOSE_TAG;
		return $conf;
	}

	/**
	 * Renders the menu.  $mode can be 'html' or 'collapsible'.
	 * $tplt can be a single template or an array of templates.  If $recursive
	 * is set to 0 or false, it will only render a single level of the tree.
	 * Note: Do not set $recursive to true, rather set it to 1, so that it
	 * can be used to count the level of depth within the menu structure a
	 * given item is, and so it will use the proper template if $tplt is
	 * an array.
	 * 
	 * @access	public
	 * @param	string	$mode
	 * @param	mixed	$tplt
	 * @param	integer	$recursive
	 * @return	string
	 * 
	 */
	function display ($mode = 'html', $tplt = '', $recursive = 1) {
		/*
		ob_start ();
		echo '<pre>';
		print_r ($this->tree);
		echo '</pre>';
		$ret = ob_get_contents ();
		ob_end_clean ();
		return $ret;
		*/
		if ($mode == 'html') {
			$res = '<ul>';
		} else {
			$res = '<script language="JavaScript">

				function ' . $this->initFunc . '() {
					var width = 350, height = 22;
					var falsePic = "' . $this->closedPic . '";
					var truePic = "' . $this->openedPic . '";
					List_SITELLITE_EMPTY = new List(true, width, height, "' . $this->tree[0]->colours[0] . '");
					List_SITELLITE_EMPTY.setIndent(3);
					List_SITELLITE_EMPTY.collapsedImageURL = falsePic;
					List_SITELLITE_EMPTY.expandedImageURL = truePic;' . "\n";
		}
		foreach ($this->tree as $child) {
			$res .= $child->display ($mode, $tplt, $recursive);
		}
		if ($mode == 'html') {
			$res .= '</ul>';
		} else {
			$res .= '	List_SITELLITE_EMPTY.build(' . $this->xpos . ',' . $this->ypos . ');
				}

</script>' . "\n";
		}
		return $res;
	}

	/**
	 * Generates a breadcrumb trail from the start of the tree to the
	 * specified item, returned as an array of template-rendered items.  If
	 * $home is true, add a 'home' link by calling the homeLink() method.
	 * 
	 * @access	public
	 * @param	string	$id
	 * @param	string	$tplt
	 * @param	boolean	$home
	 * @return	array
	 * 
	 */
	function trail ($id, $tplt = '', $home = 'index', $separator = ' &gt; ') {
		if (empty ($tplt)) {
			if (! is_object ($this->{'items_' . $id})) {
				return array ();
			}
			return $this->{'items_' . $id}->trail ();
		} else {
			if (! is_object ($this->{'items_' . $id})) {
				return '';
			}

			global $simple, $intl;
			$t = array ();

			if ($home) {
				$t[] = $this->homeLink ($tplt, $home);
			}

			$trail = $this->{'items_' . $id}->trail ();
			$last = array_pop ($trail);

			foreach ($trail as $item) {
				if ($item->id != $home) {
					$t[] = $simple->fill ($tplt, $item);
				}
			}

			array_push ($t, $last->title);

			return join ($separator, $t);
		}
	}

	/**
	 * Returns the HTML code to include the necessary JavaScript files
	 * for the collapsible display mode.
	 * 
	 * @access	public
	 * @return	string
	 * 
	 */
	function includeJavaScript () {
		if (! $this->includedJs) {
			$this->includedJs = true;
			return '<script language="JavaScript1.2" SRC="' . $this->jsDir . '/ua.js"></script>
<script language="JavaScript1.2" SRC="' . $this->jsDir . '/xbDOM.js"></script>
<script language="JavaScript1.2" SRC="' . $this->jsDir . '/xbStyle.js"></script>
<script language="JavaScript1.2" SRC="' . $this->jsDir . '/xbCollapsibleLists.js"></script>' . "\n";
		}
	}

	/**
	 * Returns a 'home' link.  Called by the trail() method to add an
	 * optional 'home' link to breadcrumb trails.  The home link id is always
	 * 'index', and the title is always 'Home', which can be changed or translated
	 * if a global $intl saf.I18n object is found.
	 * 
	 * @access	public
	 * @param	string	$tplt
	 * @return	string
	 * 
	 */
	function homeLink ($tplt, $home = 'index') {
		global $intl, $simple;

		if (is_bool ($home)) {
			$home = 'index';
		}

		$item = array (
			'id' => $home,
			'title' => 'Home',
		);

		if (is_object ($intl)) {
			$item['title'] = $intl->get ($item['title']);
		}
		return $simple->fill ($tplt, $item);
	}

	/**
	 * Returns a section of the item tree, rendered as an XHTML-compliant
	 * unordered list.  $open allows you to specify the id of a single item in
	 * the section which can be 'opened' to either one more level or entirely (if
	 * $recursive is set to true).  This makes it possible to display a section
	 * such as a product list and while the visitor is viewing a specific product,
	 * to also show sub-links pertaining to that product.  $skip specifies an
	 * optional item to ignore (ie. in a "related links" context, ignore the
	 * current item).
	 * 
	 * @access	public
	 * @param	string	$id
	 * @param	string	$tplt
	 * @param	string	$open
	 * @param	boolean	$recursive
	 * @param	string	$skip
	 * @return	string
	 * 
	 */
	function section ($id = '', $tplt = '', $open = '', $recursive = false, $skip = '') {
		if (empty ($id)) {
			// top level
			global $simple;
			$res = '<ul>';
			foreach ($this->tree as $item) {
				if (! empty ($open) && $item->id == $open) {
					if (! $recursive) {
						$r = $item->display ('html', $tplt, 0);
						$r = str_replace ('</li>', '<ul></ul></li>', $r);
						foreach ($item->children as $child) {
							$r = str_replace ('</ul></li>', $child->display ('html', $tplt, 0) . '</ul></li>', $r);
						}
						$res .= $r;
					} else {
						$res .= $item->display ('html', $tplt, 1);
					}
				} elseif ($item->id != $skip) {
					$res .= $item->display ('html', $tplt, 0);
				}
			}
		} elseif (! is_object ($this->{'items_' . $id})) {
			return '';
		} else {
			// inner level
			// top level
			global $simple;
			$res = '<ul>';
			foreach ($this->{'items_' . $id}->children as $item) {
				if (! empty ($open) && $item->id == $open) {
					if (! $recursive) {
						$r = $item->display ('html', $tplt, 0);
						$r = str_replace ('</li>', '<ul></ul></li>', $r);
						foreach ($item->children as $child) {
							$r = str_replace ('</ul></li>', $child->display ('html', $tplt, 0) . '</ul></li>', $r);
						}
						$res .= $r;
					} else {
						$res .= $item->display ('html', $tplt, 1);
					}
				} elseif ($recursive && empty ($open) && $item->id != $skip) {
					$res .= $item->display ('html', $tplt, 1);
				} elseif ($item->id != $skip) {
					$res .= $item->display ('html', $tplt, 0);
				}
			}
		}
		return $res . '</ul>';
	}

	/**
	 * Returns the number of child items of the specified item.
	 * If $id is not specified, returns the number of top-level items.
	 * 
	 * @access	public
	 * @param	string	$id
	 * @return	integer
	 * 
	 */
	function countChildren ($id = false) {
		if ($id && is_object ($this->{'items_' . $id})) {
			return count ($this->{'items_' . $id}->children);
		} elseif ($id == false) {
			return count ($this->tree);
		}
		return 0;
	}

	/**
	 * Returns the children of the specified item.
	 * If $id is not specified, returns the top-level items.
	 * 
	 * @access	public
	 * @param	string	$id
	 * @return	array
	 * 
	 */
	function getChildren ($id = false) {
		if ($id && is_object ($this->{'items_' . $id})) {
			return $this->{'items_' . $id}->children;
		} elseif ($id == false) {
			return $this->tree;
		}
		return array ();
	}

	/**
	 * Clears the item tree.
	 * 
	 * @access	public
	 * 
	 */
	function clear () {
		foreach (get_object_vars ($this) as $key => $val) {
			if (is_object ($val) && preg_match ('/^items_.*$/', $key)) {
				unset ($this->{$key});
			}
		}
		$this->tree = array ();
	}

	/**
	 * Retrieves a list of all of the items denoted as section roots.
	 *
	 * @access	public
	 *
	 */
	function getSections ($item = false) {
		$sections = array ();
		foreach ($this->getChildren ($item) as $child) {
			if ($child->is_section == 'yes') {
				$sections[$child->id] = $child->title;
			}
			$add = $this->getSections ($child->id);
			if (count ($add) > 0) {
				foreach ($add as $id => $title) {
					$sections[$id] = $title;
				}
			}
		}
		return $sections;
	}
}

/**
 * Alias of $GLOBALS['menu']->getSections().
 *
 * @access	public
 *
 */
function menu_get_sections ($item = false) {
	loader_box ('sitellite/nav/init');
	return $GLOBALS['menu']->getSections ($item);
}

/**
 * Determines whether the specified $child id is a child of the specified
 * $parent id.
 *
 * @access	public
 *
 */
function menu_is_child_of ($child, $parent) {
	loader_box ('sitellite/nav/init');
	$trail = $GLOBALS['menu']->trail ($child);
	foreach ($trail as $item) {
		if ($item->id == $parent) {
			return true;
		}
	}
	return false;
}

?>