<?php

loader_box ('sitellite/nav/init');

global $page, $menu;

$template = "<a href='{site/prefix}/index/{id}'>{title}</a>";

if (! $page->below_page) {
	$below = '';
} else {
	$below = $page->below_page;
}

echo $menu->section ($below, $template, '', $false);

?>