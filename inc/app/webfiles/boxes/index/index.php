<?php

loader_import ('webfiles.Server');

$webfiles = new HTTP_WebDAV_Server_Webfiles;

$webfiles->ServeRequest ();

exit;

?>