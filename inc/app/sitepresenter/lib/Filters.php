<?php

function sitepresenter_virtual_views ($row) {
	return db_shift (
		'select count(*) from sitepresenter_view where presentation = ?',
		$row->id
	);
}

function sitepresenter_virtual_slides ($row) {
	return db_shift (
		'select count(*) from sitepresenter_slide where presentation = ?',
		$row->id
	);
}

function sitepresenter_virtual_run ($row) {
	return template_simple (
		'<a href="{site/prefix}/index/sitepresenter-presentation-action?id={id}" target="_blank"><img
			src="{site/prefix}/inc/app/sitepresenter/pix/run.gif" alt="{intl Run}" title="{intl Run}" border="0" /></a>
		
		<a href="{site/prefix}/index/sitepresenter-slides-action?id={id}"><img
			src="{site/prefix}/inc/app/sitepresenter/pix/slides.gif" alt="{intl Edit Slides}" title="{intl Edit Slides}" border="0" /></a>',
		$row
	);
}

function sitepresenter_filter_link_title ($t) {
	return strtolower (
		preg_replace (
			'/[^a-zA-Z0-9]+/',
			'-',
			$t
		)
	);
}

?>