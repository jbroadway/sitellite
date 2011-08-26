<?php

/*while (ob_get_level ()) {
	ob_end_clean ();
}
ob_implicit_flush ();*/

loader_import ('xed.Cleaners');

if (! empty ($parameters['data'])) {
	$original = $parameters['data'];
	$parameters['data'] = the_cleaners ($parameters['data']);
} else {
	$original = '';
}

if (! empty ($parameters['ifname'])) {
	// called as an rpc service, return the cleaned data via saf.Misc.RPC
	//loader_import ('saf.Misc.RPC');
	//echo rpc_response ($parameters['ifname'], $parameters['data']);
	$parameters['data'] = the_cleaners ($parameters[$parameters['ifname']]);
	$parameters['data'] = preg_replace ("/(\r\n|\n\r|\r|\n)/", "'\n\t\t+ '\\n", addslashes ($parameters['data']));
	$parameters['data'] = str_replace ('</script>', '</\' + \'script>', $parameters['data']);
	page_template ('dialog');
	page_title (intl_get ('Document has been cleaned.'));
	page_add_script (template_simple ('clean_reply.spt', $parameters));
	echo '<p><a href="#" onclick="window.close ()">' . intl_get ('Close Window') . '</a></p>';

	return;
} elseif ($parameters['ws'] == 'true') {
	// called as a web service, return the cleaned data only
	echo $parameters['data'];
	exit;
}

echo '<h1>Cleaners Test</h1>
<form method="post">
<p>Please enter some HTML to clean:</p>
<p><textarea name="data" cols="100" rows="20">' . htmlentities_compat ($original) . '</textarea></p>
<p><input type="submit" value="Clean!" /></p>';

if (! empty ($parameters['data'])) {

echo '<hr />
<p>I have washed your HTML, sir:</p>
<p><textarea name="original" cols="100" rows="20">' . htmlentities_compat ($parameters['data']) . '</textarea></p>';

}

echo '</form>';

exit;

?>