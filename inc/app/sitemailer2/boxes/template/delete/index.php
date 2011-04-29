<?php

global $cgi;

foreach ($cgi->delete as $id) {
	db_execute ('delete from sitemailer2_template where id = ?', $id);
}

if (count ($cgi->delete) > 1) {
	$msg = 'tpldeletes';
} else {
	$msg = 'tpldeleted';
}
header ('Location: ' . site_prefix () . '/index/sitemailer2-templates-action?msg=' . $msg);

exit;

?>
