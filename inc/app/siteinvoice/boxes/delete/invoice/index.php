<?php

global $cgi;

foreach ($cgi->_key as $key) {
	db_execute ('delete from siteinvoice_invoice where id = ?', $key);
	unlink ('inc/app/siteinvoice/data/' . $key . '.pdf');
}

page_title ('SiteInvoice - Invoices Deleted');

echo '<p><a href="' . site_prefix () . '/index/siteinvoice-app">Continue</a></p>';

?>
