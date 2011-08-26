<?php

page_title ('SiteMailer 2');

$data = array ();

$data['subscribers'] = db_pairs ('select status, count(*) from sitemailer2_recipient group by status asc');
if (! isset ($data['subscribers']['active'])) {
	$data['subscribers']['active'] = '0';
}
if (! isset ($data['subscribers']['disabled'])) {
	$data['subscribers']['disabled'] = '0';
}
if (! isset ($data['subscribers']['unverified'])) {
	$data['subscribers']['unverified'] = '0';
}

echo template_simple ('stats.spt', $data);

?>