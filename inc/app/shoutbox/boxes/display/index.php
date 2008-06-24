<?php

loader_import ('shoutbox.Filters');

$messages = db_fetch_array (
    'select * from shoutbox order by posted_on desc'
);

echo template_simple ('display.spt', $messages);

?>