<?php

$res = db_shift_array ('select distinct category from sitellite_news order by date desc limit 6');

$list = array ();
$sub = array ();
foreach ($res as $key) {
	$res = db_fetch_array ('select * from sitellite_news where category = ? order by date desc limit 3', $key);
	$list[$key] = array_shift ($res);
	$sub[$key] = $res;
}

loader_import ('news.Functions');

page_title (intl_get ('Latest Articles'));

echo template_simple ('overview.spt', array ('list' => $list, 'sub' => $sub));

?>