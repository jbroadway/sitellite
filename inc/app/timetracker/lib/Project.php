<?php

loader_import ('saf.Database.Generic');

class TimeTrackerProject extends Generic {
	function TimeTrackerProject () {
		parent::Generic ('timetracker_project', 'id');
	}
}

?>