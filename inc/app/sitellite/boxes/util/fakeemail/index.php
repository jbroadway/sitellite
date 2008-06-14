<?php

loader_import ('saf.Misc.FakeEmail');

echo FakeEmail::doAll ($parameters['address']);

if ($box['context'] == 'action') {
	exit;
}

?>