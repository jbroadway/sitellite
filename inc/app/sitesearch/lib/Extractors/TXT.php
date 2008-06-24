<?php

class TXT_Extractor extends SiteSearch_Extractor {
	var $mime = 'text/plain';

	function process ($text) {
		return $text;
	}
}

?>