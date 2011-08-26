<?php

global $cgi;

foreach ($cgi->_key as $key) {
	db_execute ('delete from siteinvoice_client where id = ?', $key);
}

page_title ('SiteInvoice - Clients Deleted');

echo '<p><a href="' . site_prefix () . '/index/siteinvoice-clients-action">Continue</a></p>';

?>
