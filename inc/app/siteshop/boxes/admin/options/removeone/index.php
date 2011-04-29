<?php

global $cgi;

db_execute ('delete from siteshop_product_option where option_id = ?', $cgi->id);
db_execute ('delete from siteshop_option where id = ?', $cgi->id);

header ('Location: ' . site_prefix() . '/index/siteshop-admin-options-action');

exit;

?>