<?php

loader_import ('saf.Database.PropertySet');
$ps = new PropertySet ('mailform', 'snippet');
echo $ps->get ($parameters['code']);

?>