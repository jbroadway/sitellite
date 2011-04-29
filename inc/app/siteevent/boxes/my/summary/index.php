<?php

if ($box['context'] == 'action') {
	page_title (intl_get ('Event Submissions'));
}

echo template_simple (
	'my_summary.spt',
	array (
		'events' => db_shift ('select count(*) from siteevent_event where sitellite_owner = ? and sitellite_status = ?', session_username (), 'approved'),
		'pending' => db_shift ('select count(*) from siteevent_event where sitellite_owner = ? and sitellite_status = ?', session_username (), 'draft'),
		'rejected' => db_shift ('select count(*) from siteevent_event where sitellite_owner = ? and sitellite_status = ?', session_username (), 'rejected'),
	)
);

?>