<?php

loader_import('digger.Filters');
loader_import('digger.Functions');

global $cgi;

if (! $cgi->offset) {
    $cgi->offset = 0;
}

if ($cgi->category) {
    $cat = ' and category = ' . db_quote($cgi->category);
} else {
    $cat = '';
}

$limit = appconf('limit');

$q = db_query(
	'SELECT * FROM digger_linkstory
	WHERE status = "enabled" ' . $cat . '
	ORDER BY posted_on desc, score desc'
);

if ($q->execute()) {
    $total = $q->rows();
    $res = $q->fetch($cgi->offset, $limit);
    $q->free();
} else {
    die($q->error());
}

// has voted?
foreach(array_keys($res) as $k) {
    if (digger_has_voted($res[$k]->id)) {
        $res[$k]->voted = 'style="display: none"';
    }
}

// pager
loader_import('saf.GUI.Pager');

$pg = new Pager($cgi->offset, $limit, $total);
$pg->setUrl(site_prefix() . '/index/digger-app?category=' . $cgi->category);
$pg->getInfo();

// output
if (! empty($cgi->category)) {
    page_title(appconf('digger_title') . ' - ' . db_shift('select category from digger_category where id = ?', $cgi->category));
} else {
    page_title(appconf('digger_title'));
}

template_simple_register('pager', $pg);
echo template_simple('index.spt',
	array(
		'category' => $cgi->category,
		'results' => $res,
		'banned_score' => appconf('ban_threshold'),
	)
);

?>