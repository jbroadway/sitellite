<?php

if (! appconf ('submissions')) {
	header ('Location: ' . site_prefix () . '/index/news-app');
	exit;
}

class NewsSubmissionsForm extends MailForm {
	function NewsSubmissionsForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/news/forms/submissions/settings.php');

		if (session_valid ()) {
			$this->widgets['author']->setDefault (session_username ());
		}

		$list = array ();
		foreach (db_fetch_array ('select * from sitellite_news_category') as $cat) {
			$list[$cat->name] = intl_get ($cat->name);
		}
		$this->widgets['category']->setValues ($list);

		page_title (intl_get ('Submit A Story'));

		if (! appconf ('comments_security')) {
			unset ($this->widgets['security_test']);
		}
	}

	function onSubmit ($vals) {
		// 1. add author if necessary
		if (! db_shift ('select * from sitellite_news_author where name = ?', $vals['author'])) {
			db_execute ('insert into sitellite_news_author (name) values (?)', $vals['author']);
		}

		// 2. submit story as 'draft'
		loader_import ('cms.Versioning.Rex');
		$rex = new Rex ('sitellite_news');
		$res = $rex->create (
			array (
				'title' => $vals['title'],
				'author' => $vals['author'],
				'category' => $vals['category'],
				'summary' => $vals['summary'],
				'body' => $vals['body'],
				'date' => date ('Y-m-d'),
				'sitellite_status' => 'draft',
				'sitellite_access' => 'public',
			),
			'Story submission.'
		);

		$vals['id'] = $res;

		// 3. email notification
		@mail (appconf ('submissions'), 'News Submission Notice', template_simple ('submission_email.spt', $vals));

		// 4. thank you screen
		page_title (intl_get ('Thank You!'));
		echo template_simple ('submissions.spt');
	}
}

?>