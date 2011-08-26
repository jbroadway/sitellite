<?php

global $cgi;

if (empty ($cgi->appname) || strstr ($cgi->appname, '..') || ! @is_dir ('inc/app/' . $cgi->appname)) {
	header ('Location: ' . site_prefix () . '/index/appdoc-app');
	exit;
}

if (empty ($cgi->lang) || strstr ($cgi->lang, '..') || ! @is_dir ('inc/app/' . $cgi->appname . '/docs/' . $cgi->lang)) {
	header ('Location: ' . site_prefix () . '/index/appdoc-helpdoc-action?appname=' . $cgi->appname);
	exit;
}

if (! is_array ($cgi->_key)) {
	if (strstr ($cgi->_key, '..')) {
		continue;
	}
	unlink (site_docroot () . '/inc/app/' . $cgi->appname . '/docs/' . $cgi->lang . '/' . $cgi->_key);
} else {
	foreach ($cgi->_key as $file) {
		if (strstr ($file, '..')) {
			continue;
		}
		unlink (site_docroot () . '/inc/app/' . $cgi->appname . '/docs/' . $cgi->lang . '/' . $file);
	}
}

header ('Location: ' . site_prefix () . '/index/appdoc-helpdoc-action?appname=' . $cgi->appname . '&lang=' . $cgi->lang);
exit;

?>