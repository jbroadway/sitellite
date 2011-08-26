<?php

page_add_script (site_prefix () . '/inc/app/monoslideshow/html/swfobject.js');

//header ('Content-Type: text/plain');
echo template_simple ('embed.spt', $parameters);
//exit;

?>