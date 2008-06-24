<?php

class XML_Extractor extends SiteSearch_Extractor {
	var $mime = 'text/xml';

	function process ($text) {
		return strip_tags ($text);
	}
}

?>