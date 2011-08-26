<?php

loader_import ('cms.Versioning.Rex');

function multilingual_filter_datetime ($ts) {
	return date ('F j, Y - g:iA', strtotime ($ts));
}

function multilingual_filter_title ($c, $id) {
	$r = new Rex ($c);
	$cur = $r->getCurrent ($id);
	return $cur->{$r->info['Collection']['title_field']};
}

function multilingual_filter_lang ($lang) {
	$langs = intl_get_langs ();
	return $langs[$lang];
}

function multilingual_get_langs () {
	$langs = intl_get_langs ();
	// remove default language
	unset ($langs[intl_default_lang ()]);
	return $langs;
}

function multilingual_sort ($a, $b) {
	if ($a->title == $b->title) {
		return 0;
	}
	return ($a->title < $b->title) ? -1 : 1;
}

?>