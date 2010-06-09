<?php

$section = page_get_section ();
if (! empty ($section)) {
	$sec = ' and section like ' . db_quote ('%' . $section . '%') . ' or section = ""';
} else {
	$sec = ' and section = ""';
}

if (! empty ($parameters['position'])) {
	$pos = ' and position = ' . db_quote ($parameters['position']);
} else {
	$pos = '';
}

if ($box['context'] == 'action') {
	$banner = db_single (
		'select * from sitebanner_ad where id = ?',
		$parameters['id']
	);
} else {
	$banners = db_shift_array (
		'select
			id
		from
			sitebanner_ad
		where
			(purchased = -1 or impressions < purchased)
				and active = "yes"'
		. $sec
		. $pos
	);

	if (count ($banners) == 0) {
		return;
	}
	$key = array_rand ($banners, 1);

	$banner = db_single (
		'select * from sitebanner_ad where id = ?',
		$banners[$key]
	);
}

if ($banner->client != session_username ()) {
	db_execute (
		'update sitebanner_ad set impressions = impressions + 1 where id = ?',
		$banners[$key]
	);

	db_execute (
		'insert into sitebanner_view
			(id, campaign, ip, ts, ua)
		values
			(null, ?, ?, now(), ?)',
		$banners[$key],
		$_SERVER['REMOTE_ADDR'],
		$_SERVER['HTTP_USER_AGENT']
	);
}

if ($banner->format == 'image') { // get image width/height
	$info = parse_url ($banner->file);
	if (strpos ($info['path'], '/') === 0) {
		$info['path'] = substr ($info['path'], 1);
	}
	$dimensions = getimagesize ($info['path']);
	$banner->width = $dimensions[0];
	$banner->height = $dimensions[1];
} elseif ($banner->format == 'adsense') { // display only one google ad per page
	if (defined ('SITEBANNER_ADSENSE')) {
		return;
	}
	//define ('SITEBANNER_ADSENSE', true);
}

if ($box['context'] == 'action') {
	echo template_simple ('display/' . $banner->format . '_action.spt', $banner);
	exit;
}

echo template_simple ('display/' . $banner->format . '.spt', $banner);

?>