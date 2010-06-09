<?php

$msg = db_single ('select * from sitemailer2_message where id = ?', $parameters['id']);
if (! $msg) {
	die ('Unknown message');
}

loader_import ('saf.Date');

$data = array (
	'body' => $msg->mbody,
	'date' => Date::format ($msg->date, 'F jS, Y'),
	'email' => '',
	'firstname' => '',
	'fullname' => '',
	'lastname' => '',
	'organization' => '',
	'tracker' => '',
	'unsubscribe' => '',
	'website' => '',
);

$t = db_single ('select * from sitemailer2_template where id = ?', $msg->template);

$t->body = str_replace ('{body}', '{filter none}{body}{end filter}', $t->body);

echo template_simple ($t->body, $data);

exit;

?>