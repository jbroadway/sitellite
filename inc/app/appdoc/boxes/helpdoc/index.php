<?php

global $cgi;

if (empty ($cgi->appname) || strstr ($cgi->appname, '..') || ! @is_dir ('inc/app/' . $cgi->appname)) {
	header ('Location: ' . site_prefix () . '/index/appdoc-app');
	exit;
}

if (empty ($cgi->lang)) {
	$cgi->lang = 'en';
}

$info = ini_parse (getcwd () . '/inc/app/' . $cgi->appname . '/conf/config.ini.php', false);

page_title (intl_get ('Help Files') . ': ' . $info['app_name']);

if (! @is_dir ('inc/app/' . $cgi->appname . '/docs/' . $cgi->lang)) {
	loader_import ('saf.File.Directory');

	$res = Dir::build ('inc/app/' . $cgi->appname . '/docs/' . $cgi->lang, 0777);

	if (! $res) {
		echo '<p>' . intl_get ('Failed to create directory') . ': docs/' . $cgi->lang . '</p>';
		echo '<p><a href="javascript: history.go (-1)">' . intl_get ('Back') . '</a></p>';
		return;
	}
}

loader_import ('help.Help');

$data = array (
	'appname' => $cgi->appname,
	'lang' => $cgi->lang,
	'files' => array (),
	'langs' => help_get_langs ($cgi->appname),
);

$files = help_get_pages ($cgi->appname, $cgi->lang);
if (! is_array ($files)) {
	$files = array ();
}

foreach ($files as $file) {
	$id = help_get_id ($file);
	$body = @join ('', @file ($file));
	$word_count = count (preg_split ('/\W+/s', strip_tags ($body), -1, PREG_SPLIT_NO_EMPTY));
	$data['files'][basename ($file)] = array (
		'id' => $id,
		'title' => help_get_title ($body, $id),
		'words' => $word_count
	);
}

echo template_simple ('helpdoc.spt', $data);

?>