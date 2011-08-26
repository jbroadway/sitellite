<?php

if (! session_allowed ('sitellite_homepage', 'w', 'resource')) {
	header ('Location: ' . site_prefix () . '/index/sitemember-app');
	exit;
}

class SitememberHomepageForm extends MailForm {
	function SitememberHomepageForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/sitemember/forms/homepage/settings.php');
		page_title (intl_get ('Editing') . ' ' . session_username () . '\'s ' . intl_get ('Homepage'));

		$res = db_single ('select * from sitellite_homepage where user = ?', session_username ());
		if (is_object ($res)) {
			$this->widgets['title']->setValue ($res->title);
			$this->widgets['template']->setValue ($res->template);
			$this->widgets['body']->setValue ($res->body);
		}
	}

	function onSubmit ($vals) {
		if (! db_shift ('select count(*) from sitellite_homepage where user = ?', session_username ())) {
			if (! db_execute (
				'insert into sitellite_homepage (user, title, template, body) values (?, ?, ?, ?)',
				session_username (), $vals['title'], $vals['template'], $vals['body']
			)) {
				page_title (intl_get ('An Error Occurred'));
				echo '<p>' . intl_get ('Error') . ': ' . db_error () . '</p>';
				return;
			}
		} else {
			if (! db_execute (
				'update sitellite_homepage set title = ?, template = ?, body = ? where user = ?',
				$vals['title'], $vals['template'], $vals['body'], session_username ()
			)) {
				page_title (intl_get ('An Error Occurred'));
				echo '<p>' . intl_get ('Error') . ': ' . db_error () . '</p>';
				return;
			}
		}

		page_title (intl_get ('Changes Saved'));
		echo '<p><a href="' . site_prefix () . '/index/sitemember-app">' . intl_get ('Return to member home.') . '</a></p>';
	}
}

?>