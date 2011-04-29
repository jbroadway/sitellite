<?php

function sitepoll_filter_title ($title) {
	if (strlen ($title) > 30) {
		return substr ($title, 0, 27) . '...';
	}
	return $title;
}

function sitepoll_virtual_votes (&$obj) {
	return db_shift (
		'select count(*) from sitepoll_vote where poll = ?',
		$obj->id
	);
}

function sitepoll_virtual_enable_comments (&$obj) {
	if ($obj->enable_comments == 'no') {
		return intl_get ('Disabled');
	}
	return db_shift (
		'select count(*) from sitepoll_comment where poll = ?',
		$obj->id
	);
}

function sitepoll_filter_date ($date) {
	loader_import ('saf.Date');
	return Date::format ($date, 'F jS, Y');
}

function sitepoll_filter_shortdate ($date) {
	loader_import ('saf.Date');
	return Date::format ($date, 'M jS');
}

function sitepoll_filter_date_time ($date) {
	loader_import ('saf.Date');
	return Date::timestamp ($date, 'F jS, Y - g:i A');
}

function sitepoll_filter_comment_body ($body) {
	return preg_replace (
		'|(http://[^\r\n\t ]+)|is',
		'<a href="\1" target="_blank">\1</a>',
		str_replace (
			NEWLINE,
			'<br />' . NEWLINE,
			htmlentities_compat ($body)
		)
	);
}

?>