<?php

loader_import ('sitesearch.Filters');

global $cgi;

if (! isset ($cgi->limit)) {
	$cgi->limit = 10;
}

if (! isset ($cgi->offset)) {
	if (isset ($cgi->osoffset)) {
		$cgi->offset = ($cgi->osoffset - 1) * $cgi->limit;
	} else {
		$cgi->offset = 0;
	}
}

$settings = @parse_ini_file ('inc/app/sitesearch/conf/server.ini.php');

$sitesearch_allowed = array ();

loader_import ('saf.File.Directory');

foreach (Dir::fetch ('inc/app/cms/conf/collections') as $file) {
	if (strpos ($file, '.') === 0 || @is_dir ('inc/app/cms/conf/collections/' . $file)) {
		continue;
	}
	$config = ini_parse ('inc/app/cms/conf/collections/' . $file);
	if (isset ($config['Collection']['sitesearch_url'])) {
		if (isset ($config['Collection']['sitesearch_access']) && session_allowed ($config['Collection']['sitesearch_access'], 'r', 'access')) {
			$sitesearch_allowed[] = $config['Collection']['name'];
		}
	}
}

if (! empty ($parameters['query'])) {

	loader_import ('sitesearch.SiteSearch');

	$searcher = new SiteSearch;

	if (is_array ($parameters['ctype'])) {
		$collections = $parameters['ctype'];
		foreach ($collections as $k => $ctype) {
			if (! in_array ($ctype, $sitesearch_allowed)) {
				unset ($collections[$k]);
			}
		}
	} elseif (! empty ($parameters['ctype'])) {
		$collections = explode (',', $parameters['ctype']);
		foreach ($collections as $k => $ctype) {
			if (! in_array ($ctype, $sitesearch_allowed)) {
				unset ($collections[$k]);
			}
		}
	} else {
		$collections = $sitesearch_allowed;
	}

	if (is_array ($parameters['domain'])) {
		$domains = $parameters['domain'];
	} elseif (! empty ($parameters['domain'])) {
		$domains = explode (',', $parameters['domain']);
	} else {
		$domains = array ('all');
	}

	if (count ($collections) > 0) {
		$res = @$searcher->query ($parameters['query'], $cgi->limit, $cgi->offset, $collections, $domains);
	} else {
		$res = array ();
	}

	if (! $res) {
		echo '<p class="sitesearch-error">' . $searcher->error . '</p>';
		$results = array ();
		$total = 0;
	} elseif (is_array ($res['rows'])) {
		$results = $res['rows'];
		$total = $res['metadata']['hits'];
	} else {
		$results = array ();
		$total = 0;
	}

} else {
	$total = 0;
	$results = array ();
}

loader_import ('sitesearch.Logger');
$logger = new SiteSearchLogger;

if ($cgi->offset == 0) {
	$logger->logSearch ($cgi->query, $total);
}

if ($cgi->show_types == 'yes') {
	$show_types = true;

	$data = $logger->getCurrentIndex ();
	if (! $data) {
		$types = array ();
	} else {
		$counts = unserialize ($data->counts);
		if (! is_array ($counts)) {
			$types = array ();
		} else {
			loader_import ('sitesearch.Filters');
			$types = array ();
			foreach ($counts as $k => $c) {
				if (in_array ($k, $sitesearch_allowed)) {
					$types[$k] = sitesearch_filter_ctype ($k);
				}
			}
			asort ($types);
		}
	}
} else {
	$show_types = false;
}

$enc = urlencode ($cgi->query);

foreach (array_keys ($results) as $key) {
	if (strstr ($results[$key]['url'], '?')) {
		$results[$key]['url'] .= '&highlight=' . $enc;
	} else {
		$results[$key]['url'] .= '?highlight=' . $enc;
	}
}

header ('Content-Type: text/xml');
echo template_simple (
	'rss.spt',
	array (
		'list' => $results,
		'rss_title' => appconf ('rss_title'),
		'rss_description' => appconf ('rss_description'),
		'rss_date' => date ('Y-m-d\TH:i:s') . sitesearch_timezone (date ('Z')),
		'total' => $total,
		'limit' => $cgi->limit,
		'offset' => ($cgi->offset / $cgi->limit) + 1,
	)
);

exit;

?>