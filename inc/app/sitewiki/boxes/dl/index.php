<?php

$obj = db_single (
	'select * from sitewiki_file where page_id = ? and id = ?',
	$parameters['page'],
	$parameters['file']
);

if (! $obj) {
	die ('File not found!');
}

header ('Content-Type: ' . mime ($obj->name));
header ('Content-Disposition: inline; filename=' . $obj->name);
header ('Content-Length: ' . filesize ('inc/app/sitewiki/data/' . $obj->page_id . '_' . $obj->id));
readfile ('inc/app/sitewiki/data/' . $obj->page_id . '_' . $obj->id);

exit;

?>