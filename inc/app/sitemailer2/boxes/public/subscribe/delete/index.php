<?php

global $cgi;

foreach ($cgi->_key as $id) {
	db_execute ('delete from sitemailer2_recipient where id = ?', $id);
	db_execute ('delete from sitemailer2_recipient_in_newsletter where recipient = ?', $id);
}

if (count ($cgi->_key) > 1) {
	$msg = 'subsdel';
} else {
	$msg = 'subdel';
}
header ('Location: ' . site_prefix () . '/index/sitemailer2-subscribers-action?_msg=' . $msg);

exit;

?>