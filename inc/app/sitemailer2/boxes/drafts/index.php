<?php

loader_import ('sitemailer2.Filters');

page_title ('SiteMailer 2');

$data = array ();

$res = db_fetch_array ('select * from sitemailer2_message where status = "draft" order by date desc');

foreach (array_keys ($res) as $k) {
	$n = array ();
	foreach (db_shift_array ('select newsletter from sitemailer2_message_newsletter where message = ?', $res[$k]->id) as $id) {
		$n[] = db_shift ('select name from sitemailer2_newsletter where id = ?', $id);
	}
	$res[$k]->newsletter = join (', ', $n);
}

$data['list'] =& $res;

global $cgi;

$msg_list = appconf ('msg');

if (isset ($msg_list[$cgi->_msg])) {
	page_onload ('alert (\'' . $msg_list[$cgi->_msg] . '\')');
}

echo template_simple ('drafts.spt', $data);

?>