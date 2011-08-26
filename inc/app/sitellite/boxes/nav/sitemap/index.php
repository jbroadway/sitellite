<?php
// resolved tickets:
// #194 site map.

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
page_add_style ('<style type="text/css">
ul#gray {
	list-style-type: none;
    list-style-image: none;
    text-decoration: none;
}
ul#gray>li>a {
	font-weight: bold;
	list-style-type: none;
    list-style-image: none;
}
#gray ul {
    list-style-image: none;
  	list-style-type: none;
    background: transparent;
}
.treeview-gray a:hover {
	list-style-type: none;
    list-style-image: none;
    color: #999;
}
.treeview-gray ul a:hover {
	list-style-type: none;
    list-style-image: none;
    color: #999;
}
.treeview-gray li {
	list-style-type: none;
    list-style-image: none;
    background: transparent;
    color: #999;
}
</style>');

loader_box ('sitellite/nav/init');

// import any object we need from the global namespace
global $page, $menu;

// box logic begins here

if ($parameters['recursive'] == 'no') {
	$recur = false;
} else {
	$recur = true;
}

//page_id ('sitemap');
if ($context == 'action') {
	page_title (intl_get ('Site Map'));
}

//page_add_script (site_prefix () . '/js/jquery-1.3.2.min.js');
page_add_script (site_prefix () . '/js/jquery.cookie.js');
page_add_script (site_prefix () . '/js/jquery-treeview/jquery.treeview.min.js');
page_add_style (site_prefix () . '/js/jquery-treeview/jquery.treeview.css');
page_add_script ('<script type="text/javascript">

$(function () {
	$("#gray").treeview ({
		animated: "medium",
		control: "#sidetreecontrol",
		persist: "location"
	});
});

</script>');



echo preg_replace ('/^<ul>/', '<ul id="gray" class="treeview-gray treeview">', $menu->display ('html', '<a href="{site/prefix}/index/{id}">{title}</a>', $recur));

?>