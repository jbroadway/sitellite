<?php

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

loader_box ('sitellite/nav/init');

// import the global $menu and $page objects
global $menu, $page;

template_simple_register ('page', $page);

// the template to display a single menu link.
$template = "<a href=\"{site/prefix}/index/{id}\"{if obj.id eq page.id} class=\"current\"{end if}{if obj.id ne page.id and sitellite_nav_list_in_trail (obj.id)} class=\"parent\"{end if}>{title}</a>\n";

// retrieve the breadcrumb trail to the currently active page.
if ($page->below_page) {
	if (! is_object ($menu->{'items_' . $page->id})) {
		$menu->addItem ($page->id, $page->title, $page->below_page);
	}
	$trail_as_objects = $menu->{'items_' . $page->id}->trail ();
	$trail = array ();
	foreach ($trail_as_objects as $obj) {
		$trail[] = $obj->id;
	}
} else {
	$trail = array ($page->id);
}

// here we define a few helper functions that will render the menu for us.

/**
 * A simple tab maker, to keep the indenting of the menu output pretty.
 * Outputs directly and returns nothing.
 *
 * @param integer number of tabs to display
 */
function sitellite_nav_list_walker_tab ($level = 0) {
	echo str_pad ('', $level, "\t");
}

$GLOBALS['_sitellite_nav_trail'] =& $trail;

/**
 * A function to test whether the current page is a parent of the current
 * page.
 *
 * @param integer page id to test
 */
function sitellite_nav_list_in_trail ($id) {
	if (in_array ($id, $GLOBALS['_sitellite_nav_trail'])) {
		return true;
	}
	return false;
}

/**
 * A recursive function that renders the navigation as an unordered list.
 * Outputs directly and returns nothing.  You may use output buffering
 * to control its output after the fact, but that is likely not necessary
 * for most uses.
 *
 * @param array reference to the current level of the tree
 * @param array trail of items to the active page
 * @param string the template to use to display an individual item
 * @param integer the current depth into the nav tree
 */
function sitellite_nav_list_walker (&$list, $trail, $template, $level = 0) {
	if (! is_array ($list) || count ($list) == 0) {
		return;
	}

	global $page;

	if ($level == 0 || $level == 1) { // top level, always show all

		echo "<ul>\n";
		foreach ($list as $key => $item) {
			if ($item->id == $page->id && $page->include == 'no') {
				continue;
			}
			echo "\t<li>" . template_simple ($template, $item);
			if (in_array ($item->id, $trail)) {
				sitellite_nav_list_walker ($list[$key]->children, $trail, $template, $level + 1);
			}
			echo "\t</li>\n";
		}
		echo "</ul>\n";

	} elseif ($level == count ($trail)) { // active level, show all plus children

		echo sitellite_nav_list_walker_tab ($level) . "<ul>\n";
		foreach ($list as $key => $item) {
			if ($item->id == $page->id && $page->include == 'no') {
				continue;
			}
			echo sitellite_nav_list_walker_tab ($level) . "\t<li>" . template_simple ($template, $item);
			if (in_array ($item->id, $trail)) {
				sitellite_nav_list_walker ($list[$key]->children, $trail, $template, $level + 1);
			}
			echo sitellite_nav_list_walker_tab ($level) . "\t</li>\n";
		}
		echo sitellite_nav_list_walker_tab ($level) . "</ul>\n";

	} elseif ($level >= count ($trail)) { // child level, show all

		echo sitellite_nav_list_walker_tab ($level) . "<ul>\n";
		foreach ($list as $key => $item) {
			echo sitellite_nav_list_walker_tab ($level) . "\t<li>" . template_simple ($template, $item) . "</li>\n";
		}
		echo sitellite_nav_list_walker_tab ($level) . "</ul>\n";

	} else { // show only item from $trail[$level], this is not an active level

		echo sitellite_nav_list_walker_tab ($level) . "<ul>\n";
		foreach ($list as $key => $item) {
			if (in_array ($item->id, $trail)) {
				echo sitellite_nav_list_walker_tab ($level) . "\t<li>" . template_simple ($template, $list[$key]);
				sitellite_nav_list_walker ($list[$key]->children, $trail, $template, $level + 1);
				echo sitellite_nav_list_walker_tab ($level) . "\t</li>\n";
				break;
			}
		}
		echo sitellite_nav_list_walker_tab ($level) . "</ul>\n";

	}
}

// call the walker, which generates the menu
sitellite_nav_list_walker ($menu->tree, $trail, $template);

?>
