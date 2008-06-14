<?php

loader_import ('ext.textile');

echo textile ($parameters['text']);

if ($box['context'] == 'action') {
	exit;
}

?>