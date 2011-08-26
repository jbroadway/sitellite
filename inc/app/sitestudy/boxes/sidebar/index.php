<?php

if ($parameters['limit'] && is_numeric ($parameters['limit'])) {
	$lim = ' limit ' . $parameters['limit'];
} else {
	$lim = '';
}

echo template_simple (
	'sidebar.spt',
	db_fetch_array (
		'select * from sitestudy_item order by sort_weight desc, id desc' . $lim
	)
);

?>