<?php

if (! session_admin ()) {
	page_title ( 'SiteSearch - Login' );

	global $cgi;

	if (isset ($cgi->username)) {
		echo '<p>Invalid password.  Please try again.</p>';
	} else {
		echo '<p>Please enter your username and password to enter.</p>';
	}

	echo template_simple ('<form method="post" action="{site/prefix}/index/sitellite-user-login-action">
		<input type="hidden" name="goto" value="sitesearch-stats-index-action" />
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

page_title ('SiteSearch');

loader_import ('sitesearch.Filters');
loader_import ('sitesearch.Logger');
loader_import ('sitesearch.SiteSearch');
$logger = new SiteSearchLogger;

// indexing info

/*
$data = $logger->getCurrentIndex ();
if (! $data) {
	echo template_simple ('stats_first.spt');
	return;
}
*/

$search = new SiteSearch;

$data->total = $search->numDocs ();

/*
$data->counts = unserialize ($data->counts);
$data->total = 0;
if (! is_array ($data->counts)) {
	$data->counts = array ();
}
foreach ($data->counts as $c) {
	$data->total += $c;
}
*/

// top 10 searches

global $cgi;

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

$data->top = $logger->getTopSearches (10, $start, $end);

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

$data->ttl = $logger->getTotalSearches ($year, $month);
$data->ttl_total = 0;
$days = 0;
foreach ($data->ttl as $day) {
	$data->ttl_total += $day->total;
	$days++;
}
if ($days > 0) {
	$data->ttl_avg = ceil ($data->ttl_total / $days);
} else {
	$data->ttl_avg = 0;
}

$searcher = new SiteSearch;
$data->uptime = @$searcher->uptime ();

echo template_simple ('stats_index.spt', $data);

?>