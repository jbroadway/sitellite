<?php

// import functions
loader_import ('saf.Misc.RPC');
loader_import ('sitellite.smiley.Smiley');

// set the page title
page_title ('Shoutbox');

if ($box['context'] == 'action') {
    page_add_script (site_prefix () . '/js/rpc.js');
    page_add_style (site_prefix () . '/inc/app/shoutbox/html/shoutbox.css');
} else {
    echo '<script type="text/javascript" src="/js/rpc.js"></script>';
    echo '<link rel="stylesheet" type="text/css" href="/inc/app/shoutbox/html/shoutbox.css" />';
}

// get latest x messages
$limitshout = appconf("limit_shoutbox");
$messages = db_fetch_array (
    'select * from shoutbox order by posted_on desc limit '.$limitshout
);

// get refresh time and height from config and register for use in template
template_simple_register("refreshtime",appconf("refresh_time"));
// template_simple_register("height",appconf("height"));

// save latest received id
$lastid = $messages[0]->id;
if(!is_numeric($lastid)) {
 $lastid = 0;
}

template_simple_register("lastid",$lastid);

// display the shoutbox
echo template_simple ('shout.spt', $messages);

?>
