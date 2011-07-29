<?php

global $cgi;

if ($cgi->appname == 'GLOBAL') {
	$file_index = 'inc/lang/index.php';
	$file_lang = 'inc/lang/' . $cgi->lang . '.php';
	$file_langs = 'inc/lang/languages.php';
} elseif ($cgi->appname == 'SAF') {
	$file_index = 'inc/lang/saf.php';
	$file_lang = 'inc/lang/' . $cgi->lang . '.php';
	$file_langs = 'inc/lang/languages.php';
} else {
	$file_index = 'inc/app/' . $cgi->appname . '/lang/index.php';
	$file_lang = 'inc/app/' . $cgi->appname . '/lang/' . $cgi->lang . '.php';
	$file_langs = 'inc/lang/languages.php';
}

$index = unserialize (@join ('', @file ($file_index)));
if (! is_array ($index)) {
	page_title (intl_get ('No Index'));
	echo template_simple ('translation_noindex.spt', $cgi);
	return;
} elseif (empty ($index)) {
	page_title (intl_get ('Empty Index'));
	echo template_simple ('translation_emptyindex.spt', $cgi);
	return;
}

if (! isset ($cgi->offset)) {
	$cgi->offset = 0;
	$parameters['offset'] = $cgi->offset;
}

$limit = 20;

if (@file_exists ($file_lang)) {
	if (! isset ($this->lang_hash[$cgi->lang])) {
		$this->lang_hash[$cgi->lang] = array ();
	}
	include ($file_lang);
} else {
	$this->lang_hash[$cgi->lang] = array ();
}

if ($parameters['save'] == 'yes') {
	// save changes
	foreach ($cgi->keys as $k => $v) {
		if (! empty ($cgi->values[$k])) {
			$this->lang_hash[$cgi->lang][$v] = $cgi->values[$k];
		} elseif (! empty ($this->lang_hash[$cgi->lang][$v])) {
			unset ($this->lang_hash[$cgi->lang][$v]);
		}
	}

	if (strstr ($cgi->lang, '-')) {
		list ($code, $locale) = split ('-', $cgi->lang);
	} else {
		$code = $cgi->lang;
		$locale = '';
	}

	// rewrite file
	global $intl;
	if (! $intl->writeLanguage (
		$file_lang,
		$this->lang_hash[$cgi->lang],
		$code,
		$locale
	)) {
		page_title (intl_get ('An Error Occurred'));
		echo $intl->error;
		return;
	}
}

if ($parameters['home'] == 'yes') {
	header ('Location: ' . site_prefix () . '/index/multilingual-translation-action?appname=' . $cgi->appname);
	exit;
}

$parameters['screen_total'] = ceil (count ($index) / $limit);
$parameters['screen_num'] = ($cgi->offset / $limit) + 1;
$parameters['more'] = (($cgi->offset + $limit) < count ($index)) ? true : false;

$show = array_slice ($index, $cgi->offset, $limit);

foreach ($show as $k => $v) {
	$show[$k]['current'] = $this->lang_hash[$cgi->lang][stripslashes ($k)];
}

header ('Content-Type: text/html; charset=' . $cgi->charset);

$langs = ini_parse ($file_langs);

page_title (intl_get ('Editing Language') . ': ' . $langs[$cgi->lang]['name']);

//page_add_script (site_prefix () . '/js/jquery-1.3.2.min.js');
page_add_script (site_prefix () . '/js/jquery.translate-1.2.6.min.js');

template_simple_register ('show', $show);
echo template_simple ('translation_strings.spt', $parameters);

//info ($show, true);

?>
