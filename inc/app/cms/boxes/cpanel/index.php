<?php

echo loader_box ('cms/nav');

loader_import ('saf.HTML');

echo template_simple (CMS_JS_ALERT_MESSAGE, $GLOBALS['cgi']);

if (false && @is_dir (site_docroot () . '/inc/app/sitetracker')) {

echo html::table (
	html::tr (
		html::td (

			html::h1 (intl_get ('Inbox')) .
			loader_box ('cms/messages/inbox') .

			html::hr () .

			html::h1 (intl_get ('Bookmarks')) .
			loader_box ('cms/bookmarks')

		, array ('valign' => 'top', 'width' => '66%', 'style' => 'padding-right: 10px'))
		. html::td (

			loader_box ('sitetracker/stats/summary')

		, array ('valign' => 'top', 'width' => '33%', 'style' => 'padding-left: 10px'))
	), array ('border' => 0, 'cellpadding' => 0, 'cellspacing' => 0, 'width' => '100%')
);

} else {

echo html::table (
	html::tr (
		html::td (

			html::h1 (intl_get ('Inbox')) .
			loader_box ('cms/messages/inbox') .

			//html::hr () .

			html::h1 (intl_get ('Auto-Saved Edits')) .
			loader_box ('cms/autosave/drafts')

		, array ('valign' => 'top', 'width' => '66%', 'style' => 'padding-right: 10px'))
		. html::td (

			html::h1 (intl_get ('Bookmarks')) .
			loader_box ('cms/bookmarks')

		, array ('valign' => 'top', 'width' => '33%', 'style' => 'padding-left: 10px'))
	), array ('border' => 0, 'cellpadding' => 0, 'cellspacing' => 0, 'width' => '100%')
);

}

?>