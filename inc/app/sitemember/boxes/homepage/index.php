<?php

$user = $parameters['user'];
$self = false;
if (! $user) {
	if (session_valid ()) {
		$user = session_username ();
		$self = true;
	} else {
		header ('Location: ' . site_prefix () . '/index/sitemember-app');
		exit;
	}
} elseif (session_username () == $user) {
	$self = true;
}

$page = db_single ('select * from sitellite_homepage where user = ?', $user);
if (! $page) {
	if ($self) {
		$page = new StdClass;
		$page->user = $user;
		$page->title = intl_get ('Your Homepage');
		$page->template = appconf ('homepage_default_template');
		$page->body = intl_get ('Edit your homepage to change this text.');
	} else {
		$page = new StdClass;
		$page->user = $user;
		$page->title = $user . '\'s ' . intl_get ('Homepage');
		$page->template = appconf ('homepage_default_template');
		$page->body = intl_get ('This user has not yet created a home page.');
	}
}

if ($context == 'action') {
	page_title ($page->title);
	if (! empty ($page->template)) {
		page_template ($page->template);
	}
	$default_template = appconf ('homepage_template');
	if ($default_template) {
		page_template ($default_template);
	}
}

if ($self && session_allowed ('sitellite_homepage', 'w', 'resource')) {
	echo '<p><a href="' . site_prefix () . '/index/sitemember-homepage-form?user='
		. $user . '">' . intl_get ('Edit Homepage') . '</a></p>';
}

if ($context != 'action') {
	echo '<p><strong>' . intl_get ('Title') . ': ' . $page->title . '</strong></p>';
}

$GLOBALS['page']->body_parts = preg_split ('|<hr[^>]*>|is', $page->body);
echo $page->body;

?>