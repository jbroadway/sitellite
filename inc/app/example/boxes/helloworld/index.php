<?php

/*
 * Hello World Example
 *
 * This example illustrates the basic multi-lingual capabilities of Sitellite.
 */

// set the page title to the text 'Hello World'
// note that intl_get() automatically translates the text based on the
// user's preferred languages
page_title (intl_get ('Hello World'));

// insert into breadcrumb navigation
page_id ('helloworld');
page_below ('examples');

// set default language to 'fr' then rebuild 'example' app index
global $intl;
$intl->language = 'fr';
$intl->getIndex ();

// now we output the same text, but it will appear this time in French
echo '<p>' . intl_get ('Hello World') . '</p>';

// let's reset the language settings now, so the rest of the site continues
// in the default language
$intl->language = 'en';

?>