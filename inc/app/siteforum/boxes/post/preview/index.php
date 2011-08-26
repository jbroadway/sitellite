<?php

page_title (intl_get ('Post Preview'));

loader_import ('siteforum.Filters');

$parameters['date'] = date ('Y-m-d H:i:s');

$parameters['sig'] = db_shift ('select sig from sitellite_user where username = ?', session_username ());

echo template_simple ('preview.spt', $parameters);

if (appconf ('template')) {
	page_template (appconf ('template'));
}

?>