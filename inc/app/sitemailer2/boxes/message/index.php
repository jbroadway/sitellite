<?php

global $cgi;

loader_import ('saf.Date');

$msg = db_single ('select * from sitemailer2_message where id = ?', $cgi->id);

$msg->template_body = db_shift ('select body from sitemailer2_template where id = ?', $msg->template);
if (! $msg->template_body) {
	$msg->template_body = '{body}';
}

$data = array (
	'body' => $msg->mbody,
	'date' => Date::format ($msg->date, 'F jS, Y'),
	'email' => 'joe@example.com',
	'firstname' => 'Joe',
	'fullname' => 'Joe Smith',
	'lastname' => 'Smith',
	'organization' => 'Example Inc.',
	'tracker' => '',
	'unsubscribe' => site_prefix () . '/index/sitemailer2-unsubscribe-action?email=joe@example.com',
	'website' => 'http://www.example.com/',
);

$msg->template_body = str_replace ('{body}', '{filter none}{body}{end filter}', $msg->template_body);

page_title ('SiteMailer 2 - View Message');

echo template_simple (
	'view_message.spt',
	array (
		'body' => template_simple ($msg->template_body, $data),
		'subject' => $msg->subject,
		'fromname' => $msg->fromname,
		'fromemail' => $msg->fromemail,
		'date' => $data['date'],
	)
);

?>