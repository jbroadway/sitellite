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
// #175 Webfiles - showing PDF files.
// #197 File Icons - File Display.

// BEGIN KEEPOUT CHECKING
if (! defined ('SAF_VERSION')) {
    // Add these lines to the very top of any file you don't want people to
    // be able to access directly.
    header ('HTTP/1.1 404 Not Found');
    echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n"
        . "<html><head>\n<title>404 Not Found</title>\n</head><body>\n<h1>Not Found</h1>\n"
        . "The requested URL " . $PHP_SELF . " was not found on this server.<p>\n<hr>\n"
        . $_SERVER['SERVER_SIGNATURE'] . "</body></html>";
    exit;
}
// END KEEPOUT CHECKING

// this block allows us to make requests in this form:
// http://www.example.com/cms-filesystem/path/to/file.txt
if (empty ($parameters['file'])) {
	$exploded = explode ('/cms-filesystem-action/', $_SERVER['REQUEST_URI']);
	if (count ($exploded) >= 2) {
		$parameters['file'] = urldecode (array_pop ($exploded));
		if (strpos ($parameters['file'], '?') !== false) {
			list ($parameters['file'], $extra) = explode ('?', $parameters['file']);
		}
	}
}

if (empty ($parameters['file'])) {
	// show a bulleted list of options for the specified path
	// (useful for implementing a "downloads" feature on a site).

	if (! isset ($parameters['path'])) {
		$parameters['path'] = '';
	}

	if ($parameters['show-extensions'] == 'yes') {
		$show = true;
	} else {
		$show = false;
	}

	if (session_admin ()) {
		$acl = session_allowed_sql ();
	} else {
		$acl = session_approved_sql ();
	}

	$res = db_fetch_array (
		'select name, display_title, extension
		from sitellite_filesystem
		where
			path = ? and
			' . $acl . 'order by display_title',
		$parameters['path']
	);

	if (count ($res) == 0) {
		return;
	}

	if (! empty ($parameters['path'])) {
		$parameters['path'] .= '/';
	}

	foreach (array_keys ($res) as $k) {
		if (empty ($res[$k]->name)) {
			// skip dot-files (empty files will only have extensions
			unset ($res[$k]);
			continue;
		}
		if (empty ($res[$k]->display_title)) {
			$res[$k]->display_title = $res[$k]->name;
		}
	}

	// show an auto-discovery rss link to this directory listing as well
	page_add_link (
		'alternate',
		'application/rss+xml',
		'http://' . site_domain () . site_prefix () . '/index/cms-filesystem-rss-action?path=' . urlencode (trim ($parameters['path'], '/'))
	);
#START: SEMIAS. #197 File Icons - File Display.
	echo template_simple (
		'filesystem_list.spt',
		array (
			'path' => $parameters['path'],
			'list' => $res,
			'show' => $show,
		)
	);

	return;
}
#END: SEMIAS.
if (strpos ($parameters['file'], '/') === 0) {
	$parameters['file'] = substr ($parameters['file'], 1);
}

$parameters['file'] = strtolower ($parameters['file']);

$info = pathinfo ($parameters['file']);
if ($info['dirname'] == '.') {
	$info['dirname'] = '';
}
if (! $info['extension']) {
	$info['extension'] = '';
}
$info['basename'] = preg_replace ('/\.' . preg_quote ($info['extension'], '/') . '$/', '', $info['basename']);

if (session_admin ()) {
	$acl = session_allowed_sql ();
} else {
	$acl = session_approved_sql ();
}

$res = db_shift (
	'select name from sitellite_filesystem
	where
		path = ? and
		name = ? and
		extension = ? and
		' . $acl,
	$info['dirname'],
	$info['basename'],
	$info['extension']
);

if (! $res) {
	header ('Location: ' . site_prefix () . '/index');
	exit;
}

if ($parameters['rid']) {
	// retrieve a specific revision (only if current file's permissions pass though)
	loader_import ('cms.Versioning.Rex');
	$r = new Rex ('sitellite_filesystem');
	$revision = $r->getRevision ($parameters['file'], $parameters['rid'], true);

	header ('Cache-control: private');
	header ('Content-Type: ' . str_replace ('|[,;].*$|i', '', mime ($parameters['file'])));
	header ('Content-Disposition: inline; filename="' . basename ($parameters['file']) . '"');
	header ('Content-Length: ' . $revision->filesize);
	echo $revision->body;
	exit;
}

set_time_limit (0);

if (isset ($_SERVER['HTTP_RANGE'])) {
	$size = filesize ('inc/data/' . $parameters['file']);
	if (preg_match ('/^bytes=(\d+)-(\d*)$/', $_SERVER['HTTP_RANGE'], $matches)) {
		$from = $matches[1];
		$to = $matches[2];
		if (empty ($to)) {
			$to = $size - 1;
		}
		$csize = $to - $from + 1;
		$bufsize = 20000;

		// give 'em the file
		header ('HTTP/1.1 206 Partial Content');
		header ("Content-Range: $from-$to/$size");
		header ("Content-Length: $csize");
		header ('Content-Type: application/force-download');
		header ('Content-Type: ' . str_replace ('|[,;].*$|i', '', mime ('inc/data/' . $parameters['file'])));

		if ($fh = fopen ('inc/data/' . $parameters['file'], 'rb')) {
			fseek ($fh, $from);
			$cur_pos = ftell ($fh);
			while ($cur_pos !== false && ftell ($fh) + $bufsize < $to + 1) {
				$buffer = fread ($fh, $bufsize);
				echo $buffer;
				$cur_pos = ftell ($fh);
			}

			$buffer = fread ($fh, $to + 1 - $cur_pos);
			echo $buffer;

			fclose ($fh);
		} else {
			header ('HTTP/1.1 404 Not Found');
		}
	} else {
		header ('HTTP/1.1 500 Internal Server Error');
	}
} else {
	// give 'em the file
	header ('Cache-control: private');
	header ('Content-Type: ' . str_replace ('|[,;].*$|i', '', mime ('inc/data/' . $parameters['file'])));
// Start: SEMIAS #175 Webfiles - showing PDF files.
// --------------------
//	header ('Content-Disposition: inline; filename="' . basename ($parameters['file']) . '"');
// --------------------
	header ('Content-Disposition: attachment; filename="' . basename ($parameters['file']) . '"');
// END: SEMIAS
	if (conf ('Server', 'xsendfile') == 'lighttpd') {
		header ('X-Sendfile: ' . site_docroot () . '/inc/data/' . $parameters['file']);
	} elseif (conf ('Server', 'xsendfile') == 'nginx') {
		header ('X-Accel-Redirect: ' . site_prefix () . '/inc/data/' . $parameters['file']);
	} else {
		header ('Content-Length: ' . filesize ('inc/data/' . $parameters['file']));
		readfile ('inc/data/' . $parameters['file']);
        session_destroy();
        exit;
	}
}

db_execute (
	'insert into sitellite_filesystem_download values (?, now(), ?)',
	$parameters['file'],
	$_SERVER['REMOTE_ADDR']
);

exit;

?>