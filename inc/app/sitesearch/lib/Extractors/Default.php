<?php

class Default_Extractor extends SiteSearch_Extractor {
	var $mime = 'application/x-octet-stream';

	function process ($text) {
		return $text;
	}
}

?>