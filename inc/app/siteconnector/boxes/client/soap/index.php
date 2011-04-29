<?php

loader_import ('siteconnector.SOAP.Client');

page_title ('SiteConnector Test');

$client = new SiteConnector_SOAP_Client (
	site_url () . '/index/siteconnector-app/api.soap/service.test?wsdl',
	true,
	false,
	array ('user' => 'wsc_test', 'pass' => 'wsc_test')
);

$object =& $client->getObject ();
if (! $object) {
	echo '<h2>Error</h2>';
	echo '<p>' . $client->error . '</p>';
	return;
}

$greetings = $object->hello ('Lux');
if ($error = siteconnector_error ($greetings)) {
	echo '<h2>Error</h2>';
	echo '<p>' . $error . '</p>';
	return;
} else {
	echo '<h2>Greetings</h2>';
	echo '<p>' . $greetings . '</p>';
}

$time = $object->ts ();
if ($error = siteconnector_error ($time)) {
	echo '<h2>Error</h2>';
	echo '<p>' . $error . '</p>';
	return;
} else {
	echo '<h2>Time</h2>';
	echo '<p>' . date ('F j, Y - g:i A', $time) . '</p>';
}

$player = $object->lastPick (array ('Ron', 'Lux', 'Josh', 'Ruby', 'Oliver'));
if ($error = siteconnector_error ($player)) {
	echo '<h2>Error</h2>';
	echo '<p>' . $error . '</p>';
	return;
} else {
	echo '<h2>Player</h2>';
	echo '<p>' . $player . '</p>';
}

$test = $object->testError ();
if ($error = siteconnector_error ($test)) {
	echo '<h2>Error</h2>';
	echo '<p>' . $error . '</p>';
} else {
	echo '<h2>Huh?  This should have been an error...</h2>';
	echo '<p>' . $test . '</p>';
}

?>