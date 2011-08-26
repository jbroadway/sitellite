<?php

if (! session_valid ()) {
	// must be called for a registered user
	header ('Location: /index/sitemailer2-public-action');
	exit;
}

$settings = @parse_ini_file ('inc/app/sitemailer2/conf/settings2.ini.php');

if ($settings['subscriber_registration'] == 'none') {
    //stop, this should not be called
    echo '<p>Error, this should not be called.</p>';
    return;
}

if ($box['context'] == 'action') {
	page_title (intl_get ('Newsletters'));
}

$u = session_user_get (session_username ());
$u->recipient_id = db_shift ('select id from sitemailer2_recipient where email = ?', $u->email);
if (! $u->recipient_id) {
	db_execute (
		'insert into sitemailer2_recipient values (null, ?, ?, ?, ?, ?, now(), "active")',
		$u->email,
		$u->firstname,
		$u->lastname,
		$u->company,
		$u->website
	);
	$u->recipient_id = db_lastid ();
}

if (isset ($parameters['submit_button']) && ! is_array ($parameters['newsletters'])) {
	$parameters['newsletters'] = array ();
}

$lists = db_fetch_array ('select id, name from sitemailer2_newsletter where public = "yes" order by name asc');

foreach ($lists as $k => $v) {
	$lists[$k]->subscribed = db_shift (
		'select count(*) from sitemailer2_recipient_in_newsletter where recipient = ? and newsletter = ?',
		$u->recipient_id,
		$v->id
	);
	if (isset ($parameters['submit_button'])) {
		if ($lists[$k]->subscribed && ! in_array ($v->id, $parameters['newsletters'])) {
			db_execute (
				'delete from sitemailer2_recipient_in_newsletter where recipient = ? and newsletter = ?',
				$u->recipient_id,
				$v->id
			);
			$lists[$k]->subscribed = 0;
		} elseif (! $lists[$k]->subscribed && in_array ($v->id, $parameters['newsletters'])) {
			db_execute (
				'insert into sitemailer2_recipient_in_newsletter values (?, ?, now(), "subscribed")',
				$u->recipient_id,
				$v->id
			);
			$lists[$k]->subscribed = 1;
		}
	}
}

//only require the email, which we should have now
echo template_simple ('public_checkboxes.spt',
	array (
		'lists' => $lists,
		'updated' => isset ($parameters['submit_button']),
	)
);

?>