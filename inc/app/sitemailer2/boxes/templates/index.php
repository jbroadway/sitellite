<?php

loader_import ('sitemailer2.Filters');

page_title ('SiteMailer 2');

$data = array ();

$res = db_fetch_array (
	'select * from sitemailer2_template order by title asc'
);

foreach (array_keys ($res) as $key) {
	$res[$key]->subscribers = db_shift ('select count(*) from sitemailer2_subscriber_category where category = ?', $res[$key]->id);
	$res[$key]->last_sent = db_shift ('select date from sitemailer2_message where newsletter = ? order by date desc limit 1', $res[$key]->id);
	if (! $res[$key]->subscribers) {
		$res[$key]->subscribers = '&#048;';
	}
}

$data['list'] =& $res;

global $cgi;

$msg_list = appconf ('msg');

if (isset ($msg_list[$cgi->msg])) {
	page_onload ('alert (\'' . $msg_list[$cgi->msg] . '\')');
}

echo template_simple ('templates.spt', $data);

?>