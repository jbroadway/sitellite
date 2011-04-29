<?php

global $cgi;

foreach ($cgi->delete as $id) {
	db_execute ('delete from sitemailer2_newsletter where id = ?', $id);
	db_execute ('delete from sitemailer2_recipient_in_newsletter where newsletter = ?', $id);
	db_execute ('delete from sitemailer2_message where newsletter = ?', $id);
	db_execute ('delete from sitemailer2_message_sv where newsletter = ?', $id);
}

if (count ($cgi->delete) > 1) {
	$msg = 'deletes';
} else {
	$msg = 'deleted';
}
header ('Location: ' . site_prefix () . '/index/sitemailer2-app?msg=' . $msg);

exit;

?>