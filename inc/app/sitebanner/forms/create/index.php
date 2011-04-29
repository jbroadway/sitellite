<?php

class SitebannerCreateForm extends MailForm {
	function SitebannerCreateForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/sitebanner/forms/create/settings.php');
		page_title (intl_get ('Create a New Banner'));
	}
	function onSubmit ($vals) {
		db_execute (
			'insert into sitebanner_ad
				(id, name, description, client, purchased, impressions, display_url, url, target, format, file, section, position, active)
			values
				(null, ?, ?, ?, 0, 0, ?, ?, ?, ?, ?, ?, ?, "no")',
			$vals['name'],
			$vals['description'],
			session_username (),
			$vals['display_url'],
			$vals['url'],
			'top',
			'external',
			$vals['file'],
			$vals['section'],
			$vals['position']
		);

		if (appconf ('email')) {
			@mail (appconf ('email'), 'SiteBanner Submission Notice', template_simple ('create_email.spt', $vals));
		}

		page_title (intl_get ('Banner Created'));
		echo template_simple ('create_reply.spt');
	}
}

?>