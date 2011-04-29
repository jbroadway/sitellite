<?php

// BEGIN CLI KEEPOUT CHECKING
if (php_sapi_name () !== 'cli') {
    // Add these lines to the very top of any file you don't want people to
    // be able to access directly.
    header ('HTTP/1.1 404 Not Found');
    echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n"
        . "<html><head>\n<title>404 Not Found</title>\n</head><body>\n<h1>Not Found</h1>\n"
        . "The requested URL " . $PHP_SELF . " was not found on this server.<p>\n<hr>\n"
        . $_SERVER['SERVER_SIGNATURE'] . "</body></html>";
    exit;
}
// END CLI KEEPOUT CHECKING

// sitesearch scheduler task

loader_import ('sitesearch.SiteSearch');
loader_import ('sitesearch.Logger');
loader_import ('sitesearch.Extractor');
loader_import ('sitesearch.Functions');
loader_import ('cms.Versioning.Rex');

$search = new SiteSearch;

$collections = Rex::getCollections ();

$default_domain = conf ('Site', 'domain');

$mtime = time ();
$counts = array ();

foreach ($collections as $collection) {
	$rex = new Rex ($collection);
	if (! $rex->collection || ! $rex->info['Collection']['sitesearch_url']) {
		continue;
	}

	$counts[$collection] = 0;

	$item_list = $rex->getList (array ());
	if (! is_array ($item_list)) {
		continue;
	}
	while (count ($item_list) > 0) {
		// index item
		$item = array_shift ($item_list);
		$item = $rex->getCurrent ($item->{$rex->key});
		if (! $item) {
			continue;
		}

		if (isset ($rex->info['Collection']['sitesearch_include_field'])) {
			if ($item->{$rex->info['Collection']['sitesearch_include_field']} == 'no') {
				continue;
			}
		}

		if (! isset ($item->sitellite_access)) {
			$item->sitellite_access = 'public';
		}

		if (! isset ($item->sitellite_status)) {
			$item->sitellite_status = 'approved';
		}

		if (! isset ($item->sitellite_status)) {
			$item->sitellite_team = 'none';
		}

		if ($collection != 'sitellite_filesystem') {
			$item->{$rex->body} = extractor_run ($item->{$rex->body}, 'HTML');
		}

		if (! isset ($rex->info['Collection']['summary_field']) || empty ($rex->info['Collection']['summary_field'])) {
			$description = substr (strip_tags ($item->{$rex->body}), 0, 128) . '...';
		} else {
			$description = $item->{$rex->info['Collection']['summary_field']};
		}

		if (! isset ($rex->info['Collection']['keywords_field'])) {
			$keywords = '';
		} elseif (strpos ($rex->info['Collection']['keywords_field'], ',') !== false) {
			$op = '';
			foreach (preg_split ('/, ?/', $rex->info['Collection']['keywords_field']) as $f) {
				$keywords .= $op . $item->{$f};
				$op = ', ';
			}
		} else {
			$keywords = $item->{$rex->info['Collection']['keywords_field']};
		}

		$data = array (
			'title' => $item->{$rex->title},
			'url' => site_prefix () . '/index/' . sprintf ($rex->info['Collection']['sitesearch_url'], $item->{$rex->key}),
			'description' => $description,
			'keywords' => $keywords,
			'body' => $item->{$rex->body},
			'access' => $item->sitellite_access,
			'status' => $item->sitellite_status,
			'team' => $item->sitellite_team,
			'ctype' => $collection,
			'mtime' => (string) $mtime,
			'domain' => $default_domain,
		);

		if ($collection == 'sitellite_filesystem') {
			$new = extractor_run ('inc/data/' . $item->{$rex->key});
			if (! $new) {
				$data['body'] = $data['description'];
			} else {
				$data['body'] = $new;
			}
			unset ($new);
		}

		$counts[$collection]++;

		$res = $search->addDocument ($data);
		if (! $res) {
			echo 'Error adding document: ' . $search->error . NEWLINE;
			echo 'Document: ' . $data['ctype'] . '/' . $item->{$rex->key} . NEWLINE;
			echo 'Document URL: ' . $data['url'] . NEWLINE;
			return;
		}

		unset ($data);
	}
}

$folders = ini_parse ('inc/app/sitesearch/conf/folders.ini.php');
if (count ($folders) > 0) {
	loader_import ('saf.File.Directory');

	foreach ($folders as $name => $info) {
		switch ($info['type']) {
			case 'site':
				// 1. wget the site
				shell_exec ('wget -m -w 2 -E -P tmp/ http://' . $info['domain'] . ' > /dev/null 2>&1');
				// 2. set $info['folder']
				$info['folder'] = 'tmp/' . $info['domain'];
				// 3. set $info['prefix'] as $info['domain']
				$info['prefix'] = 'http://' . $info['domain'];
				// 4. proceed with what's below
			case 'folder':
			default:
				$files = array ();
				$folders = array_merge (array ($info['folder']), Dir::getStruct ($info['folder']));
				foreach ($folders as $folder) {
					if (preg_match ('/CVS$/', $folder)) {
						continue;
					}
					$list = Dir::fetch ($folder);
					foreach ($list as $f) {
						if (strpos ($f, '.') === 0 || @is_dir ($folder . '/' . $f)) {
							continue;
						} elseif (preg_match ('/\.(jpg|png|gif|css|js)$/i', $f)) {
							continue;
						}
						$pref = str_replace ($info['folder'], '', $folder);
						if (! preg_match ('|/$|', $pref)) {
							$pref .= '/';
						}
						if ($info['type'] == 'site' && $info['sitellite']) {
							$files[$folder . '/' . $f] = $info['prefix'] . $pref . preg_replace ('/\.html$/i', '', $f);
						} else {
							$files[$folder . '/' . $f] = $info['prefix'] . $pref . $f;
						}
					}
				}
				break;
		}

		loader_import ('sitesearch.DocReader');
		$domain = (isset ($info['domain'])) ? $info['domain'] : $default_domain;
		if (isset ($info['domain'])) {
			$ctype = 'sitellite_page';
		} else {
			$ctype = $name;
			$counts[$ctype] = 0;
		}

		foreach ($files as $f => $url) {
			// get content type (ie. if pdf, don't parse as an html file)
			$finfo = pathinfo ($f);
			$fext = strtolower ($finfo['extension']);
			if (in_array ($fext, array ('html', 'htm'))) {
				// parse file
				$doc = docreader_get_data ($f);
				if (! $doc) {
					continue;
				}
				$title = docreader_get_title ($doc);
				if (! $title) {
					$title = 'Untitled';
				}
				$description = docreader_get_description ($doc);
				$keywords = docreader_get_keywords ($doc);
				$body = extractor_run (docreader_get_body ($doc), 'HTML');
				unset ($doc);
			} else {
				$body = extractor_run ($f);
				if (! $body) {
					$body = '';
				}
				$description = '';
				$keywords = '';
				$title = basename ($f);
			}

			$data = array (
				'title' => $title,
				'url' => $url,
				'description' => $description,
				'keywords' => $keywords,
				'body' => $body,
				'access' => 'public',
				'status' => 'approved',
				'team' => 'none',
				'ctype' => $ctype,
				'mtime' => (string) $mtime,
				'domain' => $domain,
			);

			// add file to index
			$counts[$ctype]++;

			$res = $search->addDocument ($data);
			if (! $res) {
				echo 'Error adding document: ' . $search->error . NEWLINE;
				echo 'Document URL: ' . $data['url'] . NEWLINE;
				return;
			}
		}

		if ($info['type'] == 'site') {
			Dir::rmdirRecursive ('tmp');
			$search->deleteExpired ((string) $mtime - 1, $info['domain']);
		}
	}
}

$search->deleteExpired ((string) $mtime - 1, $default_domain);

@chmod_recursive ($search->path, 0777);

$etime = time ();

// log our activities for big brother
$logger = new SiteSearchLogger;
$logger->logIndex ($mtime, $etime, $counts);

?>