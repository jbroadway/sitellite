<?php

loader_box ('sitellite/nav/init');

global $page, $menu;

$template = "<a href='{site/prefix}/index/{id}'>{title}</a>";

echo $menu->display ('html', $template, true);

?>