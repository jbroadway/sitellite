<?php

// You can set this to a single website domain name, or an array of up
// to 30 domains which will be searched.
appconf_set ('site', 'www.example.com');

// This is a list of prefixes you can filter out of page titles.  Useful
// for removing repetitive parts of titles in the search results, such
// as the name of the website being searched.
appconf_set ('titles', array (
	'Example Site -',
));

// This appid is simply a way for Yahoo to keep track of how popular a
// single application is.  Yahoo's rate limit of 5000 requests/day is
// based on IP address, not the appid.  Sites requiring more requests
// per day however must contact Yahoo for that at
// http://developer.yahoo.com/
appconf_set ('appid', 'sitesearch_sitellite_org');

?>