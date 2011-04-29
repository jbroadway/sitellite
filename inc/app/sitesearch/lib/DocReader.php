<?php

/**
 * Parses a given HTML file for easy extraction of the title, description,
 * keywords, and body data.
 */

function docreader_get_data ($file) {
	return @join ('', @file ($file));
}

function docreader_get_title ($doc) {
	if (preg_match ('|<h([1-6])[^>]*>(.*?)</h\1>|is', $doc, $regs)) {
		$title = preg_replace ('|[\r\n\t ]+|s', ' ', trim (strip_tags ($regs[2])));
	} elseif (preg_match ('|<title[^>]*>(.*)</title>|is', $doc, $regs)) {
		$title = preg_replace ('|[\r\n\t ]+|s', ' ', trim (strip_tags ($regs[1])));
	}
	if (! isset ($title)) {
		return 'Untitled';
	}
	if (strlen ($title) > 80) {
		return substr ($title, 64) . '...';
	}
	return $title;
}

function docreader_get_description ($doc) {
	if (preg_match ('|<meta name="description" content="([^"]*?)" />|is', $doc, $regs)) {
		return preg_replace ('|[\r\n\t ]+|s', ' ', trim (strip_tags ($regs[1])));
	}
	return substr (docreader_get_body ($doc), 0, 255) . '...';
}

function docreader_get_keywords ($doc) {
	if (preg_match ('|<meta name="keywords" content="([^"]*?)" />|is', $doc, $regs)) {
		return preg_replace ('|[\r\n\t ]+|s', ' ', trim (strip_tags ($regs[1])));
	}
	return '';
}

function docreader_get_body ($doc) {
	if (preg_match ('|<body[^>]*>(.*)</body>|is', $doc, $regs)) {
		return preg_replace ('|[\r\n\t ]+|s', ' ', trim (strip_tags ($regs[1])));
	} else {
		return preg_replace ('|[\r\n\t ]+|s', ' ', trim (strip_tags ($doc)));
	}
}

?>