<?php

if ($parameters['multiple'] == 'yes') {
	$multiple = true;
} else {
	$multiple = false;
}

global $cgi;

if (isset ($cgi->ctype)) {
	$parameters['ctype'] = $cgi->ctype;
}

loader_import ('sitesearch.Logger');
$logger = new SiteSearchLogger;

if ($parameters['show_types'] == 'yes') {
	$show_types = true;

	$data = $logger->getCurrentIndex ();
	if (! $data) {
		$types = array ();
	} else {
		$counts = unserialize ($data->counts);
		if (! is_array ($counts)) {
			$types = array ();
		} else {
			loader_import ('sitesearch.Filters');
			$types = array ();
			foreach ($counts as $k => $c) {
				$types[$k] = sitesearch_filter_ctype ($k);
			}
			asort ($types);
		}
	}
} else {
	$show_types = false;
}

template_simple_register ('parameters', $parameters);
echo template_simple (
	'sidebar.spt',
	array (
		'show_types' => $show_types,
		'types' => $types,
		'multiple' => $multiple,
	)
);

?>