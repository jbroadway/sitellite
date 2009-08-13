<?php

if (! isset ($parameters['set'])) {
	$parameters['set'] = 'keywords';	
}

if (! isset ($parameters['url'])) {
	$parameters['url'] = $GLOBALS['page']->id;
}

// Javascript popup!

echo template_simple ('<div class="add-tag-box"><a href="{site/prefix}/sitetag-edit-form?set={set}&url={url|urlencode}&title={page/title|urlencode}&description={page/description|urlencode}">{intl Edit tags}</a></div>', $parameters);

?>
