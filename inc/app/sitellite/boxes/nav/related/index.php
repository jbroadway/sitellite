<?php

loader_box ('sitellite/nav/init');

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

// import any object we need from the global namespace
global $page, $menu;

// include our box settings file
$box = parse_ini_file ('inc/app/sitellite/boxes/nav/related/settings.php', true);

// box logic begins here

$template = "<a href='{site/prefix}/index/{id}'>{title}</a>";

ob_start ();

if (! empty ($page->below_page)) {

	if (is_object ($menu->{'items_' . $page->id}) && count ($menu->{'items_' . $page->id}->children) > 0) {
		// show section links of the page below current with the current page open
		// show related links
		//printf ("<h3 class=\"sidenav\">%s</h3>\n<p class=\"sidenav\">\n", 'Related Links');
		echo $menu->section ($page->below_page, $template, $page->id, false);
	} elseif (count ($menu->{'items_' . $page->below_page}->children) > 1) {
		// show related links
		//printf ("<h3 class=\"sidenav\">%s</h3>\n<p class=\"sidenav\">\n", 'Related Links');
		echo $menu->section ($page->below_page, $template, '', false, $page->id);
	}

} elseif ($page->id != 'index') {

	// show section links
	if (is_object ($menu->{'items_' . $page->id}) && count ($menu->{'items_' . $page->id}->children) > 0) {
		//printf ("<h3 class=\"sidenav\">%s</h3>\n<p class=\"sidenav\">\n", 'Section Links');
		echo $menu->section ($page->id, $template, '', false);
	}

}

$out = ob_get_contents ();
ob_end_clean ();

if (! empty ($out)) {
	//echo '<h2>Related Links</h2>';
	echo $out;
}

?>