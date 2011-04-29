<?php
// COPY FROM TOP SOME PARTS ARE REMOVED
// Issue #187 dropdown menu 

// BEGIN KEEPOUT CHECKING
// Add these lines to the very top of any file you don't want people to
// be able to access directly.
if (! defined ('SAF_VERSION')) {
  header ('HTTP/1.1 404 Not Found');
  echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n"
		. "<html><head>\n<title>404 Not Found</title>\n</head><body>\n<h1>Not Found</h1>\n"
		. "The requested URL " . $PHP_SELF . " was not found on this server.<p>\n<hr>\n"
		. $_SERVER['SERVER_SIGNATURE'] . "</body></html>";
  exit;
}
// END KEEPOUT CHECKING



/**
 *       D R O P D O W N   M E N U 
 *
 * <xt:box name="sitellite/nav/dropdown" />
 * optional parameters: 
 *		- disabledropdown="true"
 *		- sort="reverse"
 *		- list="list with menu items (comma separated)"
 */

loader_box ('sitellite/nav/init');
loader_import ('sitellite.nav.Dropdown');

// import any object we need from the global namespace
global $menu, $page;

// add menu styles
page_add_style ( site_prefix () . '/inc/app/sitellite/html/nav/dropdown/menu.css' );

// Box logic begins here

// Check if the dropdown is disabled
$disabled = $parameters['disabledropdown'];
$disabled = ($disabled == "true" ? true:false);

// Check if the site menu is used or if a user appended a list
if (! isset ($parameters['list'])) {
	$list =& $menu->tree;
} else {
	$list = array ();
	foreach (preg_split ('/, ?/', $parameters['list']) as $title) {
        $list[] =& $menu->{'items_' . $title};
	}
}

// Change the sort order
if ($parameters['sort'] == 'reverse') {
	$list = array_reverse ($list);
}

// Create the menu
$menuitems = create_menu($list, $page, $disabled);

// Show the template
echo template_simple ('nav/dropdown/menu.spt', array('list' => $menuitems));
?>
