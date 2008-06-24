<?php

if ($box['context'] == 'action') {
	page_title (appconf ('title'));
}

echo template_simple (
	'glossary.spt',
	db_fetch_array ('select * from siteglossary_term order by word asc')
);

?>