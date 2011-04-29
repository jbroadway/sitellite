<?php
// Issue #187 dropdown menu

// Functions for the dropdown menu.

/**
 * This function creates the menu. 
 */
function create_menu($list, $page, $submenu_disabled) {
    $menu = array();
    
    // Check if the submenu is enabled
   	if (!$submenu_disabled) {
	    foreach ($list as $item) {
	        $menu[] = create_menu_item($item, $page);
    	}
    } else {
	    foreach ($list as $item) {
	        $menu[] = create_menu_item_nosubitems($item, $page);
    	}
    }

	// Make the last menu item get an other class
    $last = sizeof($menu) -1;
    $menu[$last]['last'] = "true";
    
    return $menu;
}

/**
 * This function creates the menuitems 
 * it should not be used somewhere else
 * than from the create_menu function
 */
function create_menu_item($item, $page) {

    // create leaf
    $menu = array(
                'id' => $item->id,
                'title' => $item->title,
                );

    // check if active
    if ($page->id == $item->id || $item->id == page_get_section ()) {
        $menu['active'] = 1;
    }
    
	// check for subnodes
	$children = $item->children;
	if (!empty($children)) {
	    $submenuitems = array();
	    foreach ($children as $child) {
	        // create subnodes
	        $submenuitems[] = create_menu_item($child, $page);
	    }
	    $menu['submenu'] = $submenuitems;
    }
    // return array with menus
    return $menu;
}


/**
 * This function creates the menuitems 
 * without any subitems.
 * it should not be used somewhere else
 * than from the create_menu function
 */
function create_menu_item_nosubitems($item, $page) {

    // create leaf
    $menu = array(
                'id' => $item->id,
                'title' => $item->title,
                );

    // check if active
    if ($page->id == $item->id || $item->id == page_get_section ()) {
        $menu['active'] = 1;
    }
    
    // return array with menus
    return $menu;
}

?>
