<?php

loader_import ('saf.Date');

function siteinvoice_filter_client_id ($client_id) {
	return template_simple (
		'<a href="mailto:{contact_email}" title="{contact_name}, {contact_phone}">{name}</a>',
		db_single ('select * from siteinvoice_client where id = ?', $client_id)
	);
}

function siteinvoice_filter_sent_on ($sent_on) {
	return Date::format ($sent_on, 'F j, Y');
}

?>