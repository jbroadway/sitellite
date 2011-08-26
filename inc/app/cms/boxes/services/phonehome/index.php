<?php

@file (
	sprintf (
		'http://www.sitellite.org/home.php?d=%s&v=%s',
		urlencode (site_domain ()),
		urlencode (SITELLITE_VERSION)
	)
);

?>