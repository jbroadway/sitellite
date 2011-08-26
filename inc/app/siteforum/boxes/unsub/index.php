<?php

db_execute (
	'delete from siteforum_subscribe where post_id = ? and user_id = ?',
	$parameters['post'],
	$parameters['user']
);

page_title (intl_get ('Unsubscription Successful'));
echo template_simple ('unsub.spt');

if (appconf ('template')) {
	page_template (appconf ('template'));
}

?>