<?php

function help_get_id ($file) {
	return str_replace ('.html', '', basename ($file));
}

function help_get_title ($body, $id) {
	if (preg_match ('/<h[1-6][^>]*>([^<]+)<\/h[1-6]>/i', $body, $regs)) {
		return $regs[1];
	}
	return $id;
}

function help_extract_info ($file, $query, $hits) {
	$data = @join ('', @file ($file));
	$res = new StdClass;

	// id is helpfile
	$res->id = help_get_id ($file);

	// title is first header
	$res->title = help_get_title ($data, $res->id);

	$data = strip_tags ($data);

	// hits
	$res->hits = $hits;

	// description is paragraph containing query
	if (preg_match ('/[\r\n]+(.*?)(' . preg_quote ($query[0], '/') . ')([^\r\n]*)[\r\n]+/i', $data, $regs)) {
		$res->description = help_highlight ($regs[1] . $regs[2] . $regs[3], $query);
	} else {
		$res->description = intl_get ('No summary available.');
	}

	return $res;
}

function help_search ($appname, $query, $lang) {
	loader_import ('saf.File');
	loader_import ('saf.File.Directory');

	$query = help_split_query ($query);
	//info ($query);

	$files = Dir::find ('*.html', 'inc/app/' . $appname . '/docs/' . $lang);
	if (count ($files) == 0) {
		return false; // no help files
	}

	$results = array ();

	foreach ($files as $file) {
		if (strstr ($file, '/.')) {
			continue;
		}
		if ($hits = File::contains ($query, false, $file, true, 'strip_tags')) {
			$results[] = help_extract_info ($file, $query, $hits);
		}
	}

	// sort by hits now
	for ($i = 0; $i < count ($results); $i++) {
		for ($j = $i + 1; $j < count ($results); $j++) {
			if ($results[$j]->hits > $results[$i]->hits) {
				$tmp = $results[$j];
				$results[$j] = $results[$i];
				$results[$i] = $tmp;
			}
		}
	}

	return $results;
}

function help_split_query ($query) {
	$pieces = array ();
	$res = preg_split ('/("|[ ]+)/', $query, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	$open = false;
	foreach ($res as $piece) {
		//echo '--' . htmlentities ($piece) . '--' . BR;
		if ($open && $piece == '"') {
			$open = false;
		} elseif ($open) {
			$pieces[count ($pieces) - 1] .= $piece;
		} elseif ($piece == '"') {
			$open = true;
			$pieces[] = '';
		} elseif (preg_match ('/[a-zA-Z0-9_\'\.\/"-]/', $piece)) {
			$pieces[] = $piece;
		}
	}
	return $pieces;
}

function help_highlight ($string, $queries) {
	$string = strip_tags ($string);
	foreach ($queries as $query) {
		$string = preg_replace ('/(' . preg_quote ($query, '/') . ')/i', '<strong class=\'highlighted\'>\1</strong>', $string);
	}
	return $string;
}

function help_get_pages ($appname, $lang) {
	loader_import ('saf.File.Directory');

	$files = Dir::find ('*.html', 'inc/app/' . $appname . '/docs/' . $lang);
	if (count ($files) == 0) {
		return array (); // no help files
	}

	foreach ($files as $k => $v) {
		if (strstr ($v, '/.')) {
			unset ($files[$k]);
		}
	}

	sort ($files);

	return $files;
}

function help_get_previous ($appname, $lang, $current, $files) {
	// get rid of index.html
	$key = array_search ('inc/app/' . $appname . '/docs/' . $lang . '/index.html', $files);
	if ($key !== false && $key !== null) {
		unset ($files[$key]);
	}

	// find current file
	$fullname = 'inc/app/' . $appname . '/docs/' . $lang . '/' . $current . '.html';
	$key = array_search ($fullname, $files);

	if ($key !== false && $key !== null) {
		$key--;
		if (! isset ($files[$key])) {
			return false;
		}

		// get id and title of previous page
		$data = @join ('', @file ($files[$key]));
		$id = help_get_id ($files[$key]);
		return array ('id' => $id, 'title' => help_get_title ($data, $id));
	}
	return false;
}

function help_get_next ($appname, $lang, $current, $files) {
	// get rid of index.html
	$key = array_search ('inc/app/' . $appname . '/docs/' . $lang . '/index.html', $files);
	if ($key !== false && $key !== null) {
		unset ($files[$key]);
	}

	// find current file
	$fullname = 'inc/app/' . $appname . '/docs/' . $lang . '/' . $current . '.html';
	$key = array_search ($fullname, $files);
	if ($key !== false && $key !== null) {
		$key++;
		if (! isset ($files[$key])) {
			return false;
		}

		// get id and title of next page
		$data = @join ('', @file ($files[$key]));
		$id = help_get_id ($files[$key]);
		return array ('id' => $id, 'title' => help_get_title ($data, $id));
	}
	return false;
}

function help_get_apps () {
	$out = array ();

	$dh = opendir ('inc/app');
	if (! $dh) {
		return array ('cms' => intl_get ('Sitellite Content Manager'));
	}

	while (false !== ($file = readdir ($dh))) {
		if (strpos ($file, '.') === 0 || ! @is_dir ('inc/app/' . $file)) {
			continue;
		}
		// look for docs/en directory
		if (@is_dir ('inc/app/' . $file . '/docs/en')) {
			if (! @file_exists ('inc/app/' . $file . '/conf/config.ini.php')) {
				$name = ucfirst ($file);
			} else {
				$data = parse_ini_file ('inc/app/' . $file . '/conf/config.ini.php');
				$name = $data['app_name'];
				if (empty ($name)) {
					$name = ucfirst ($file);
				}
			}
			$out[$file] = $name;
		}
	}

	closedir ($dh);

	asort ($out);
	return $out;
}

function help_get_langs ($appname) {
	if (@file_exists ('inc/app/' . $appname . '/docs/languages.php')) {
		$res = parse_ini_file ('inc/app/' . $appname . '/docs/languages.php');
		if (! $res['en']) {
			return array_merge (array ('en' => 'English'), $res);
		}
		return $res;
	}
	return array ('en' => 'English');
}

?>