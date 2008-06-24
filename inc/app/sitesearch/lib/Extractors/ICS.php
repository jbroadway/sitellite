<?php

class ICS_Extractor extends SiteSearch_Extractor {
	var $mime = 'text/calendar';

	function process ($text) {
		return $text;
	}
}

?>