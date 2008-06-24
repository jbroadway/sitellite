<?php

class SitepresenterAddSlideForm extends MailForm {
	function SitepresenterAddSlideForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/sitepresenter/forms/add/slide/settings.php');
		page_add_script ('
			function cms_cancel (f) {
				if (arguments.length == 0) {
					window.location.href = "/index/cms-app";
				} else {
					if (f.elements["_return"] && f.elements["_return"].value.length > 0) {
						window.location.href = f.elements["_return"].value;
					} else {
						window.location.href = "/index/sitepresenter-app";
					}
				}
				return false;
			}
		');
		$this->widgets['submit_button']->buttons[1]->extra = 'onclick="return cms_cancel (this.form)"';

		global $cgi;

		page_title (
			intl_get ('Adding Slide to Presentation') . ': ' .
			db_shift ('select title from sitepresenter_presentation where id = ?', $cgi->presentation)
		);
	}

	function onSubmit ($vals) {
		$number = db_shift ('select number from sitepresenter_slide where presentation = ? order by number desc', $vals['presentation']);
		$number++;
		if ($number == 0) {
			$number = 1;
		}

		db_execute (
			'insert into sitepresenter_slide
				(id, title, presentation, number, body)
			values
				(null, ?, ?, ?, ?)',
			$vals['title'],
			$vals['presentation'],
			$number,
			$vals['body']
		);

		header ('Location: ' . site_prefix () . '/index/sitepresenter-slides-action?id=' . $vals['presentation']);

		exit;
	}
}

?>