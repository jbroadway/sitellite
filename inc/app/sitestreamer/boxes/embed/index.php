<?php

if (preg_match ('/\.flv$/i', $parameters['file'])) {
	loader_import ('sitestreamer.flash');
	echo sitestreamer_flash ($parameters['file'], $parameters['width'], $parameters['height']);
	return;
}

echo '<embed src="' . site_prefix () . '/index/cms-filesystem-action?file=' . $parameters['file'] . '" width="' . $parameters['width'] . '" height="' . $parameters['height'] . '" />';

?>