<?php

if (! session_admin ()) {
	page_title ( 'Web Services Connector - Login' );

	global $cgi;

	if (isset ($cgi->username)) {
		echo '<p>Invalid password.  Please try again.</p>';
	} else {
		echo '<p>Please enter your username and password to enter.</p>';
	}

	echo template_simple ('<form method="post" action="{site/prefix}/index/sitellite-user-login-action">
		<input type="hidden" name="goto" value="siteconnector-admin-action" />
		<table cellpadding="5" border="0">
			<tr>
				<td>Username</td>
				<td><input type="text" name="username" /></td>
			</tr>
			<tr>
				<td>Password</td>
				<td><input type="password" name="password" /></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" value="Enter" /></td>
			</tr>
		</table>
		</form>'
	);

	return;
}

page_title ('SiteConnector');

loader_import ('siteconnector.Filters');
loader_import ('siteconnector.Logger');

global $cgi;

$data = new StdClass;

$logger = new SiteConnector_Logger;

if (empty ($parameters['top_range'])) {
	$parameters['top_range'] = 'day';
}
$data->top_range = $parameters['top_range'];
$cgi->top_range = $data->top_range;

if (empty ($parameters['top_date'])) {
	$parameters['top_date'] = date ('Y-m-d');
}
$data->top_date = $parameters['top_date'];

list ($start, $end) = $logger->getTopRange ($data->top_range, $data->top_date);
$data->top_start = $start;
$data->top_end = $end;

list ($prev, $next) = $logger->getTopDates ($data->top_range, $data->top_date);
$data->top_prev = $prev;
$data->top_next = $next;

// top users

// top methods

// response codes

$data->top = $logger->getTopQueries (10, $start, $end);

$data->users = $logger->getTopUsers (10, $start, $end);

// total searches

if (empty ($parameters['ttl_date'])) {
	$parameters['ttl_date'] = date ('Y-m-d');
}
$data->ttl_date = $parameters['ttl_date'];

list ($year, $month) = $logger->getTotalRange ($data->ttl_date);
$data->ttl_year = $year;
$data->ttl_month = $month;

list ($prev, $next) = $logger->getTotalDates ($data->ttl_date);
$data->ttl_prev = $prev;
$data->ttl_next = $next;

$data->ttl = $logger->getTotalQueries ($year, $month);

if (empty ($parameters['ttl_date'])) {
	$parameters['ttl_date'] = date ('Y-m-d');
}
$data->ttl_date = $parameters['ttl_date'];

list ($year, $month) = $logger->getTotalRange ($data->ttl_date);
$data->ttl_year = $year;
$data->ttl_month = $month;

list ($prev, $next) = $logger->getTotalDates ($data->ttl_date);
$data->ttl_prev = $prev;
$data->ttl_next = $next;

$data->ttl = $logger->getTotalQueries ($year, $month);

$data->ttl_total = 0;
foreach (array_keys ($data->ttl) as $k) {
	$data->ttl_total += $data->ttl[$k]->total;
}
if (count ($data->ttl) == 0) {
	$data->ttl_avg = 0;
} else {
	$data->ttl_avg = ceil ($data->ttl_total / count ($data->ttl));
}

$data->codes = $logger->getTotalCodes ($year, $month);

$total = 0;
foreach (array_keys ($data->codes) as $k) {
	$total += $data->codes[$k]->total;
}

foreach (array_keys ($data->codes) as $k) {
	$data->codes[$k]->percent = number_format (($data->codes[$k]->total / $total) * 100, 0);
}

echo template_simple ('admin.spt', $data);

?>