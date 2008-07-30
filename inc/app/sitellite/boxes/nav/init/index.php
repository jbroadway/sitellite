<?php

// Menu Initializations, called automatically by the built-in sitellite/nav/*
// boxes, or must be called manually (sitellite/nav/init) before accessing the
// global $menu object for custom menu boxes.

if (! isset ($GLOBALS['menu'])) {
	global $cgi, $conf;

	loader_import ('saf.GUI.Menu');

	$GLOBALS['menu'] = new Menu ('sitellite_page', 'id', 'if(nav_title != "", nav_title, title)', 'below_page', 'include', 'is_section', 'template');

	global $menu;

	$menu->sitelliteAllowed = true;
	$menu->sortcolumn = 'sort_weight';
	$menu->sortorder = 'desc';
	$menu->cache = $conf['Cache']['menucaching'];
	$menu->getTree ();
}

?>