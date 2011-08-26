<?php

$on = appconf ('list');
if (! $on) {
	header ('Location: ' . site_prefix () . '/index/sitemember-app');
	exit;
} elseif ($on != 'box:sitemember/list') {
	list ($type, $call) = split (':', $on);
	$func = 'loader_' . $type;
	echo $func (trim ($call), array (), $context);
	return;
}

//$total = session_user_get_total (false, false, false);
//$public = session_user_get_total (false, false, true);

$users = session_user_get_list (0, 0, false, false, false, false, false, false, false, false);
$total = count ($users);

// Case insensitive sort
function sitemember_usort ($a, $b) {
	if (ucfirst ($a->lastname) == ucfirst ($b->lastname)) {
		if (ucfirst ($a->firstname) == ucfirst ($b->firstname)) {
			return 0;
		}
		return (ucfirst ($a->firstname) < ucfirst ($b->firstname)) ? -1 : 1;
	}
	return (ucfirst ($a->lastname) < ucfirst ($b->lastname)) ? -1 : 1;
}

foreach (array_keys ($users) as $k) {
	if ($users[$k]->public == 'no') {
		unset ($users[$k]);
	}
}

$public = count ($users);

// Sort the complete memberlist
usort ($users, 'sitemember_usort');

$list = array ();
$letter = '';

// Ensure that A-Z are in the list (to not break the index at the top)
foreach (range ('A', 'Z') as $letter) {
	$list[$letter]['list'] = '';
}

// Add only public users to the list
foreach (array_keys ($users) as $k) {
	if ($users[$k]->public != 'no') {
		if (empty ($users[$k]->lastname)) {
			$letter = ucfirst ($users[$k]->username[0]);
		} else {
			$letter = ucfirst ($users[$k]->lastname[0]);
		}
		$list[$letter]['list'][] = $users[$k];
	}
	unset ($users[$k]);
}

if ($box['context'] == 'action') {
	page_title (sprintf (
		'%s (%s: %d, %s: %d)',
		intl_get ('Public Member List'),
		intl_get ('Public'),
		$public,
		intl_get ('Total'),
		$total
	));
}

echo template_simple (
	'list.spt',
	array (
		'list' => $list,
	)
);

?>