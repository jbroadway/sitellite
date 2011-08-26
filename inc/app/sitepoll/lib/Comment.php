<?php

loader_import ('saf.Database.Generic');

class SitepollComment extends Generic {
	function SitepollComment () {
		parent::Generic ('sitepoll_comment', 'id', 'poll');
	}
}

?>