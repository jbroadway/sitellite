<?php

loader_import ('saf.File.Directory');

$parameters['images'] = Dir::find ('*.jpg', 'pix/' . $parameters['folder']);

header ('Content-Type: text/xml');
echo template_simple ('xml.spt', $parameters);
exit;

?>