<?php

db_execute (
	'delete from siteblog_comment where ip = ?', $parameters['ip']
);

db_execute (
	'insert into siteblog_banned values (?)', $parameters['ip']
);

page_title (intl_get ('IP Address Banned') . ': ' . $parameters['ip']);

echo '<p><a href="#" onclick="history.go (-1)">Back</a></p>';

?>