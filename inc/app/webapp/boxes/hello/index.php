<?php

if (empty ($parameters['person'])) {
	$parameters['person'] = 'Unknown';
}

echo 'Hello ' . $parameters['person'];
exit;

?>