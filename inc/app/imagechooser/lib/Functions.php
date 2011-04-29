<?php

/**
 * Counts the number of uses/links to the specified image in Web Pages.
 */
function imagechooser_count ($image) {
	return db_shift (
		'select count(*) from sitellite_page where body like ?',
		'%' . $image . '%'
	);
}

/**
 * Returns a list of pages linking to the specified image.
 */
function imagechooser_links ($image) {
	return db_pairs (
		'select id, if(title != "", title, id) from sitellite_page where body like ? order by id asc',
		'%' . $image . '%'
	);
}

/**
 * Updates all pages that reference the specified image.
 */
function imagechooser_update_pages ($image, $new) {
	$links = imagechooser_links ($image);
	if (count ($links) > 0) {
		loader_import ('cms.Versioning.Rex');
		$rex = new Rex ('sitellite_page');
		foreach ($links as $id => $title) {
			$c = $rex->getCurrent ($id);
			if (is_object ($c)) {
				$c->body = str_replace ($image, $new, $c->body);
				$method = $rex->determineAction ($id, $c->sitellite_status);
				$rex->{$method} ($id, (array) $c, 'An image in this page was renamed, updating link.');
			}
		}
	}
}

/**
 * Renames an image.  Make sure to call imagechooser_update_pages () to
 * update any web page links to it as well.
 */
function imagechooser_rename ($image, $new) {
	return rename (ltrim ($image, '/'), ltrim ($new, '/'));
}

?>