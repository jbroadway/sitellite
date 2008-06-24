<?php

loader_import ('saf.Database.Generic');

class SitePoll extends Generic {
	function SitePoll () {
		parent::Generic ('sitepoll_poll', 'id');
		//$this->usePermissions = true;
		//$this->multilingual = true;
	}
}

?>