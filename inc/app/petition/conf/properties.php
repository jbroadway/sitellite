<?php

// Make index page a list of petitions or just the current one
// Options: 'list' or 'current'
appconf_set ('show', 'current');

appconf_set ('provinces', array (
	'' => '- SELECT -',
	'AB' => 'Alberta',
	'BC' => 'British Columbia',
	'MB' => 'Manitoba',
	'NB' => 'New Brunswick',
	'NF' => 'Newfoundland/Labrador',
	'NS' => 'Nova Scotia',
	'NV' => 'Nunavut',
	'NT' => 'Northwest Territories',
	'ON' => 'Ontario',
	'PE' => 'Prince Edward Island',
	'QC' => 'Quebec',
	'SK' => 'Saskatchewan',
	'YK' => 'Yukon Territory',
));

loader_import ('petition.Objects');
loader_import ('petition.Filters');

?>