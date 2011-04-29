<?php
//
// +----------------------------------------------------------------------+
// | Sitellite Content Management System                                  |
// +----------------------------------------------------------------------+
// | Copyright (c) 2010 Sitellite.org Community                           |
// +----------------------------------------------------------------------+
// | This software is released under the GNU GPL License.                 |
// | Please see the accompanying file docs/LICENSE for licensing details. |
// |                                                                      |
// | You should have received a copy of the GNU GPL License               |
// | along with this program; if not, visit www.sitellite.org.            |
// | The license text is also available at the following web site         |
// | address: <http://www.sitellite.org/index/license                     |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <john.luxford@gmail.com>                       |
// +----------------------------------------------------------------------+
//
// resolved tickets:
// #177 Pagination.
//

global $cgi;

if (empty ($cgi->offset)) {
	$cgi->offset = 0;
}

if (! isset ($cgi->show_types)) {
	$cgi->show_types = 'yes';
}

if (! isset ($cgi->show_domains)) {
	$cgi->show_domains = 'yes';
}

if ($parameters['ctype'] == 'all') {
	unset ($parameters['ctype']);
}

if ($parameters['domains'] == 'all') {
	unset ($parameters['domains']);
}

if ($cgi->multiple == 'yes') {
	$multiple = true;
} else {
	$multiple = false;
}

$sitesearch_allowed = array ();
$sitesearch_highlight = array ();

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
		$sitesearch_highlight[$config['Collection']['name']] = $config['Collection']['name'];
		if (isset ($config['Collection']['sitesearch_highlight']) && ! $config['Collection']['sitesearch_highlight']) {
			unset ($sitesearch_highlight[$config['Collection']['name']]);
		}
	}
}
$folders = ini_parse ('inc/app/sitesearch/conf/folders.ini.php');
$domains = array (site_domain () => site_domain ());
foreach ($folders as $name => $folder) {
	if (isset ($folder['domain'])) {
		$domains[$folder['domain']] = $folder['domain'];
	} else {
		$sitesearch_allowed[] = $name;
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

	if (is_array ($parameters['domains'])) {
		$sites = $parameters['domains'];
	} elseif (! empty ($parameters['domains'])) {
		$sites = explode (',', $parameters['domains']);
	} else {
		$sites = array ('all');
	}

	if (count ($collections) > 0) {
		$res = @$searcher->query ($parameters['query'], appconf ('limit'), $cgi->offset, $collections, $sites);
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
			foreach ($folders as $name => $folder) {
				if (! isset ($folder['domain'])) {
					$types[$name] = $name;
				}
			}
			asort ($types);
		}
	}
} else {
	$show_types = false;
	$types = array ();
}

if ($cgi->show_domains == 'yes') {
	$show_domains = true;
} else {
	$show_domains = false;
}

$enc = urlencode ($cgi->query);

foreach (array_keys ($results) as $key) {
	if (! in_array ($results[$key]['ctype'], $sitesearch_highlight)) {
		continue;
	}
	if (strstr ($results[$key]['url'], '?')) {
		$results[$key]['url'] .= '&highlight=' . $enc;
	} else {
		$results[$key]['url'] .= '?highlight=' . $enc;
	}
}

loader_import ('saf.GUI.Pager');
// Start: SEMIAS #177 Pagination.
//	Not sure a fix is needed here.
$pg = new Pager ($cgi->offset, appconf ('limit'));
if ($parameters['multiple'] == 'yes') {
	$t = '';
	if (is_array ($parameters['ctype'])) {
		foreach ($parameters['ctype'] as $ct) {
			$t .= '&ctype[]=' . urlencode ($ct);
		}
	}
	$d = '';
	if (is_array ($parameters['domains'])) {
		foreach ($parameters['domains'] as $ds) {
			$d .= '&domains[]=' . $ds;
		}
	}
} else {
	$t = '&ctype=' . urlencode ($parameters['ctype']);
	$d = '&domains=' . urlencode ($parameters['domains']);
}
$pg->setUrl (
	site_current () . '?query=%s' . $t . $d . '&show_types=%s&multiple=%s&show_domains=%s',
	$enc,
	$parameters['show_types'],
	$parameters['multiple'],
	$parameters['show_domains']
);
$pg->total = $total;
$pg->setData ($results);
$pg->update ();
// END: SEMIAS
page_id ('search');
page_title ('SiteSearch - Results For "' . $cgi->query . '"');
template_simple_register ('cgi', $cgi);
template_simple_register ('pager', $pg);
echo template_simple (
	'stats_search.spt',
	array (
		'show_types' => $show_types,
		'show_domains' => $show_domains,
		'types' => $types,
		'domains' => $domains,
		'multiple' => $multiple,
		'syntax' => $res['metadata']['syntax'],
	)
);

?>