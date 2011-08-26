<?php

if (! $parameters['body']) {
	exit;
}

$res = db_execute (
	'insert into sitetemplate_to_be_validated (id, body) values (null, ?)',
	$parameters['body']
);

$id = db_lastid ();

//header ('Location: http://www.hermish.com/check_this.cfm?URLtest=' . urlencode (site_domain () . site_prefix () .
//'/index/sitetemplate-viewer-action?id=' . $res) . '&s508=1&viewSource=1');

echo template_simple ('s508.spt', array ('id' => $id));
exit;

?>
