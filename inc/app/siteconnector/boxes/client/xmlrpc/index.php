<?php

loader_import ('siteconnector.XMLRPC.Client');

page_title ('SiteConnector Test');

$client = new SiteConnector_XMLRPC_Client (
	new HTTP_Request (
		site_url () . '/index/siteconnector-app/api.xmlrpc/service.test',
		array (
			'user' => 'wsc_test',
			'pass' => 'wsc_test',
		)
	)
);

$client->debug = true;

$greetings = $client->call ('test.hello', 'Lux');
if ($error = siteconnector_error ($greetings)) {
	echo '<h2>Error</h2>';
	echo '<p>' . $error . '</p>';
	return;
} else {
	echo '<h2>Greetings</h2>';
	echo '<p>' . $greetings . '</p>';
}

$time = $client->call ('test.ts');
if ($error = siteconnector_error ($time)) {
	echo '<h2>Error</h2>';
	echo '<p>' . $error . '</p>';
	return;
} else {
	echo '<h2>Time</h2>';
	echo '<p>' . date ('F j, Y - g:i A', $time) . '</p>';
}

$player = $client->call ('test.lastpick', array ('Ron', 'Lux', 'Josh', 'Ruby', 'Oliver'));
if ($error = siteconnector_error ($player)) {
	echo '<h2>Error</h2>';
	echo '<p>' . $error . '</p>';
	return;
} else {
	echo '<h2>Player</h2>';
	echo '<p>' . $player . '</p>';
}

$test = $client->call ('test.testerror');
if ($error = siteconnector_error ($test)) {
	echo '<h2>Error</h2>';
	echo '<p>' . $error . '</p>';
} else {
	echo '<h2>Huh?  This should have been an error...</h2>';
	echo '<p>' . $test . '</p>';
}

?>