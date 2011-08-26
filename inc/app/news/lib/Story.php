<?php

loader_import ('saf.Database.Generic');
loader_import ('saf.Date');

class NewsStory extends Generic {
	function NewsStory () {
		parent::Generic ('sitellite_news', 'id');
		$this->usePermissions = true;
		$this->multilingual = true;
	}

	function getCategories () {
		$res1 = array ('' => '- SELECT -');
		$res2 = assocify (db_shift_array ('select distinct name from sitellite_news_category where name != "" order by name asc'));
		return array_merge ($res1, $res2);
	}

	function allHeadlines () {
		$s = new NewsStory;
		$s->orderBy ('date desc, rank desc, id desc');
		$res = $s->find (array ());
		$out = array ();
		foreach ($res as $story) {
			if (strlen ($story->title) > 33) {
				$story->title = substr ($story->title, 0, 30) . '...';
			}
			$out[$story->id] = intl_shortdate ($story->date) . ' - ' . $story->title;
		}
		return $out;
	}
}

?>