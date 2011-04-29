<?php

class SitepresenterEditSlideForm extends MailForm {
	function SitepresenterEditSlideForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/sitepresenter/forms/edit/slide/settings.php');
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

		$res = db_single ('select * from sitepresenter_slide where id = ?', $cgi->id);
		foreach (get_object_vars ($res) as $k => $v) {
			$this->widgets[$k]->setValue ($v);
		}
	}

	function onSubmit ($vals) {
		db_execute (
			'update sitepresenter_slide
				set title = ?, body = ?
				where id = ?',
			$vals['title'],
			$vals['body'],
			$vals['id']
		);

		header ('Location: ' . site_prefix () . '/index/sitepresenter-slides-action?id=' . $vals['presentation']);

		exit;
	}
}

?>