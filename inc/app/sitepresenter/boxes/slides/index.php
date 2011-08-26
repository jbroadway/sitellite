<?php

$pres = db_single (
	'select * from sitepresenter_presentation where id = ?',
	$parameters['id']
);

page_title (intl_get ('Editing Slides for Presentation') . ': ' . $pres->title);

$pres->slides = db_fetch_array (
	'select * from sitepresenter_slide where presentation = ? order by number asc',
	$parameters['id']
);

echo template_simple ('slides.spt', $pres);

?>