<?php

if ($box['context'] == 'action') {
	page_title (intl_get ('Story Submissions'));
}

echo template_simple (
	'my_summary.spt',
	array (
		'stories' => db_shift ('select count(*) from sitellite_news where author = ? and sitellite_status = ?', session_username (), 'approved'),
		'pending' => db_shift ('select count(*) from sitellite_news where author = ? and sitellite_status = ?', session_username (), 'draft'),
		'rejected' => db_shift ('select count(*) from sitellite_news where author = ? and sitellite_status = ?', session_username (), 'rejected'),
	)
);

?>