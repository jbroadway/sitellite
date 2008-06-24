<?php

class SiteBlog {
	function categories () {
		$res = db_pairs ('select id, title from siteblog_category order by title asc');
		$cats = array ('' => '- ALL -');
		foreach ($res as $id => $title) {
			$cats[$id] = $title;
		}
		return $cats;
	}
}

?>