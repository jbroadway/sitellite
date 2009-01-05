<?php

if (! session_admin ()) {
	page_title (intl_get ('Translations - Login'));

	global $cgi;

	if (isset ($cgi->username)) {
		echo '<p>Invalid password.  Please try again.</p>';
	} else {
		echo '<p>Please enter your username and password to enter.</p>';
	}

	echo template_simple ('<form method="post" action="{site/prefix}/index/multilingual-app">
		<table cellpadding="5" border="0">
			<tr>
				<td>Username</td>
				<td><input type="text" name="username" /></td>
			</tr>
			<tr>
				<td>Password</td>
				<td><input type="password" name="password" /></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" value="Enter" /></td>
			</tr>
		</table>
		</form>'
	);

	return;
}

global $cgi;

loader_import ('multilingual.Translation');
loader_import ('multilingual.Filters');

$collections = Rex::getCollections ();
$parameters['collections'] = array ();
foreach ($collections as $collection) {
	$r = new Rex ($collection);
	if (isset ($r->info['Collection']['translate']) && $r->info['Collection']['translate'] == false) {
		// don't translate this collection
		continue;
	}
	if (isset ($r->info['Collection']['app']) && ! loader_app_enabled ($r->info['Collection']['app'])) {
		// app is disabled
		continue;
	}
	$parameters['collections'][$collection] = $r->info['Collection']['display'];
}

if ((! $cgi->_status || $cgi->_status == 'untranslated') && ! $cgi->_collection) {
	$cgi->_collection = 'sitellite_page';
}
$parameters['collection'] = $parameters['collections'][$cgi->_collection];
$parameters['_collection'] = $cgi->_collection;

$parameters['statuses'] = array (
	'incomplete' => intl_get ('Incomplete'),
	'expired' => intl_get ('Expired'),
	'untranslated' => intl_get ('Not Translated'),
	'translated' => intl_get ('Complete'),
);

$tr = new Translation ($cgi->_collection, $cgi->_lang);

switch ($cgi->_status) {
	case 'incomplete':
		$list = $tr->getUnfinished ($cgi->_collection);
		break;
	case 'expired':
		$list = $tr->getExpired ($cgi->_collection);
		break;
	case 'translated':
		$list = $tr->getApproved ($cgi->_collection);
		break;
	case 'untranslated':
	default:
		$list = $tr->getUntranslated ($cgi->_collection);
		$cgi->_status = 'untranslated';
		break;
}
$parameters['_status'] = $cgi->_status;

/*$teams = session_allowed_teams_list ();
if ($teams[0] == 'all') {
	$teams = assocify (session_get_teams ());
	foreach ($teams as $k => $v) {
		$teams[$k] = ucwords ($v);
	}
}*/

if (! is_array ($list)) {
	$list = array ();
}

$parameters['total'] = count ($list);
$parameters['list'] = array ();
if ($cgi->_status == 'untranslated') {
	$r = new Rex ($cgi->_collection);
	foreach ($list as $item) {
		$parameters['list'][$item->{$r->key}] = $tr->get ($item->{$r->key});
		if ($parameters['list'][$item->{$r->key}]->collection) {
			$parameters['list'][$item->{$r->key}]->title = multilingual_filter_title ($parameters['list'][$item->{$r->key}]->collection, $item->{$r->key});
		} else {
			$parameters['list'][$item->{$r->key}]->title = multilingual_filter_title ($cgi->_collection, $item->{$r->key});
		}
	}
	uasort ($parameters['list'], 'multilingual_sort');
} else {
	foreach ($list as $item) {
		$parameters['list'][$item->id] = $tr->getByID ($item->id);
		$r = new Rex ($parameters['list'][$item->id]->collection);
		$parameters['list'][$item->id]->title = multilingual_filter_title ($parameters['list'][$item->id]->collection, $parameters['list'][$item->id]->pkey);
		$parameters['list'][$item->id]->_collection = $parameters['list'][$item->id]->collection;
		$parameters['list'][$item->id]->collection = $parameters['collections'][$parameters['list'][$item->id]->collection];
	}
	uasort ($parameters['list'], 'multilingual_sort');
}

page_title (intl_get ('Translations'));

$r = new Rex (false);
$r->addFacet ('status', array (
	'display' => intl_get ('Translation Status'),
	'type' => 'select',
	'values' => $parameters['statuses'],
	'all' => false,
));
if ($cgi->_status == 'untranslated') {
	$r->addFacet ('collection', array (
		'display' => intl_get ('Content Type'),
		'type' => 'select',
		'values' => $parameters['collections'],
		'all' => false,
	));
} else {
	$r->addFacet ('collection', array (
		'display' => intl_get ('Content Type'),
		'type' => 'select',
		'values' => $parameters['collections'],
	));
}
/*$r->addFacet ('team', array (
	'display' => intl_get ('Owned by Team'),
	'type' => 'select',
	'values' => $teams,
));*/
$r->addFacet ('lang', array (
	'display' => intl_get ('Language'),
	'type' => 'select',
	'values' => multilingual_get_langs (),
));
$parameters['facets'] = $r->renderFacets ();

echo template_simple ('index.spt', $parameters);

//info ($parameters);

?>