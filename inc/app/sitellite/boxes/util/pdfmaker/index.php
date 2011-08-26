<?php

loader_import ('ext.htmldoc');
htmldoc_send_pdf ($parameters['url'], $parameters['options'], false);
exit;

?>