<?php

page_title (intl_get ('Bookmarks'));

$res = db_fetch ('select * from xed_bookmarks order by name asc');

echo template_simple ('bookmarks.spt', array ('list' => $res));

?>