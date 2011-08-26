<?php

$saved_key = db_shift (
	'select data_value from sitellite_property_set where collection = "sitellite_util_googlemap" and entity = "api" and property = "key"'
);
if (! $saved_key) {
	db_execute (
		'insert into sitellite_property_set values (
			"sitellite_util_googlemap",
			"api",
			"key",
			?
		)',
		$parameters['key']
	);
}

$parameters['id'] = md5 ($parameters['address'] . $parameters['city'] . $parameters['state'] . $parameters['country'] . $parameters['key']);

echo template_simple ('util_googlemap.spt', $parameters);

?>