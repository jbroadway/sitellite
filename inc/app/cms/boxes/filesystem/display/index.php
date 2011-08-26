<?php

if (empty ($parameters['file']) ) {
    return;
}

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
$info['basename'] = preg_replace ('/>' . preg_quote ($info['extension'], '/') . '$/', '', $info['basename']);

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
    $info['filename'],
    $info['extension']
);

if (!$res) {
    return;
}

switch ($info['extension']) {
    case 'jpg':
    case 'gif':
    case 'jpeg':
    case 'png':
        $template = 'display/image.spt';
        break;
    case 'mp3':
    case 'wav':
        $template = 'display/audio.spt';
        break;
    case 'flv':
    case 'mp4':
		page_add_script ( site_prefix () . '/inc/app/cms/lib/flowplayer/flowplayer-3.1.1.min.js');
        $template = 'display/movie.spt';
        break;
    default:
        $template = 'display/download.spt';
        break;
}
echo template_simple ($template, $info);

?>
