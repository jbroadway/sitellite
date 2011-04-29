<?php

class HTM_Extractor extends SiteSearch_Extractor {
	var $mime = 'text/html';

	function process ($text) {
		return strip_tags ($text);
	}
}

?>