<?php

loader_import ('siteevent.Event');

if (! isset ($parameters['limit'])) {
	$parameters['limit'] = 10;
}

$e = new SiteEvent_Event;

$list = $e->getUpcoming ($parameters['limit'], $parameters['category']);
$list2 = array ();

$ids = array ();

foreach (array_keys ($list) as $k) {
	if (in_array ($list[$k]->id, $ids)) {
		continue;
	}
	$item =& $list[$k];

	$item->title = (! empty ($item->short_title)) ? $item->short_title : $item->title;
	if ($item->recurring == 'no' || $item->recurring == 'daily') {
		if ($item->until_date > $item->date) {
			list ($y, $m, $d) = split ('-', $item->date);
			list ($yy, $mm, $dd) = split ('-', $item->until_date);
			$item->date = strftime ('%b %e', mktime (5, 0, 0, $m, $d, $y)) . ' - ' . strftime ('%b %e', mktime (5, 0, 0, $mm, $dd, $yy));
		} else {
			list ($y, $m, $d) = split ('-', $item->date);
			$item->date = strftime ('%b %e', mktime (5, 0, 0, $m, $d, $y));
		}
		$ids[] = $item->id;
	} else {
		list ($y, $m, $d) = split ('-', $item->date);
		$item->date = strftime ('%b %e', mktime (5, 0, 0, $m, $d, $y));
	}

	$list2[] = $item;
}

echo template_simple (
	'sidebar.spt',
	array (
		'list' => $list2,
        'location' => $parameters['location']
	)
);

?>