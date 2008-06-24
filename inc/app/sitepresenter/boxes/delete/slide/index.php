<?php

db_execute (
	'delete from sitepresenter_slide where id = ? and presentation = ?',
	$parameters['slide'],
	$parameters['presentation']
);

header ('Location: ' . site_prefix () . '/index/sitepresenter-slides-action?id=' . $parameters['presentation']);

exit;

?>