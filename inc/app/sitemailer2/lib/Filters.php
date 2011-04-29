<?php

loader_import ('saf.Date');

function sitemailer2_filter_template ($id) {
	return db_shift ('select title from sitemailer2_template where id = ?', $id);
}

function sitemailer2_filter_last_sent ($date) {
	if (! $date) {
		return 'None';
	}
	return Date::timestamp (
		$date,
		array (
			'today' => '\T\o\d\a\y - g:i A',
			'yesterday' => '\Y\e\s\t\e\r\d\a\y - g:i A',
			'tomorrow' => '\T\o\m\o\r\r\o\w - g:i A',
			'this week' => 'l, F j, Y - g:i A',
			'other' => 'F j, Y - g:i A',
		)
	);
}

function sitemailer2_filter_date ($date) {
    
	return Date::timestamp (
		$date,
		array (
			'today' => '\T\o\d\a\y',
			'yesterday' => '\Y\e\s\t\e\r\d\a\y',
			'tomorrow' => '\T\o\m\o\r\r\o\w',
			'this week' => 'M j, Y',
			'other' => 'M j, Y',
		)
	);
}

function sitemailer2_filter_org_link ($item) {
	if (strlen ($item->organization) > 20) {
		$item->organization = substr ($item->organization, 0, 17) . '...';
	}
	return '<a href="' . $item->website . '" target="_blank">' . $item->organization . '</a>';
}

function sitemailer2_filter_newsletter ($id) {
	return db_shift ('select name from sitemailer2_newsletter where id = ?', $id);
}

function sitemailer2_filter_newsletters ($id) {

    //get the newsletters this recipient belongs to
    $nls = db_pairs ('select distinct t1.newsletter, t2.name from sitemailer2_recipient_in_newsletter as t1, sitemailer2_newsletter as t2 where t1.recipient = ? and t2.id = t1.newsletter', $id->id);
    
    $nls = implode (", ", $nls);
    
    return $nls;
}

?>