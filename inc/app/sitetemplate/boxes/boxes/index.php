<?php

loader_import ('sitetemplate.Functions');

page_title (intl_get ('Boxes'));

// make a list of apps

$apps = sitetemplate_get_apps ();

echo template_simple ('boxes.spt', array ('apps' => $apps));

?>