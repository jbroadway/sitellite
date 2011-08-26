<?php

loader_import ('saf.Database.Generic');

class NewsComment extends Generic {
	function NewsComment () {
		parent::Generic ('sitellite_news_comment', 'id', 'story_id');
	}
}

?>