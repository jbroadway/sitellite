<?php

global $cgi;

page_title ('Form Submission: ' . $cgi->_key);

echo template_simple (
	'util_custom_details.spt',
	db_single ('select * from sitellite_form_submission where id = ?', $cgi->_key)
);

?>