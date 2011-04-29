<?php

// your app begins here

if (session_admin ()) {
	$allowed = session_allowed_sql ();
} else {
	$allowed = session_approved_sql ();
}

if ($box['context'] == 'action') {
	$limit = '';
} elseif (is_numeric ($parameters['limit']) && $parameters['limit'] > 0) {
	$limit = 'limit ' . $parameters['limit'];
} else {
	$limit = 'limit 5';
}

if ($box['context'] == 'action') {

	if (! isset ($parameters['category']) || $parameters['category'] == '') {
		page_title (intl_get ('Presentations'));
		$parameters['category'] = '';
	} else {
		page_title (intl_get ('Presentations') . ': ' . $parameters['category']);
	}

	$presentations = db_fetch_array (
		'select * from sitepresenter_presentation where category = ? and ' . $allowed . ' order by ts desc ' . $limit,
		$parameters['category']
	);
} else {

	if (! isset ($parameters['category']) || $parameters['category'] == '') {
		$presentations = db_fetch_array (
			'select id, title, ts, sitellite_status, sitellite_access, sitellite_team from sitepresenter_presentation where ' . $allowed . ' order by ts desc ' . $limit
		);
	} else {
		$presentations = db_fetch_array (
			'select id, title, ts, sitellite_status, sitellite_access, sitellite_team from sitepresenter_presentation where category = ? and ' . $allowed . ' order by ts desc ' . $limit,
			$parameters['category']
		);
	}
}

loader_import ('saf.Date');

foreach (array_keys ($presentations) as $key) {
	$presentations[$key]->fmdate = Date::format ($presentations[$key]->ts, 'M j, Y');
}

$action = ($box['context'] == 'action') ? true : false;

if ($action) {
	$categories = db_shift_array (
		'select distinct category from sitepresenter_presentation where category != "" order by category asc'
	);
	if (count ($categories) == 0) {
		$categories = false;
	}
} else {
	$categories = false;
}

loader_import ('sitepresenter.Filters');

echo template_simple ('list.spt', array ('action' => $action, 'list' => $presentations, 'categories' => $categories, 'category' => $parameters['category']));

?>