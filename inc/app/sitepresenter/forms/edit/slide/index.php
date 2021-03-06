<?php
//
// +----------------------------------------------------------------------+
// | Sitellite Content Management System                                  |
// +----------------------------------------------------------------------+
// | Copyright (c) 2010 Sitellite.org Community                           |
// +----------------------------------------------------------------------+
// | This software is released under the GNU GPL License.                 |
// | Please see the accompanying file docs/LICENSE for licensing details. |
// |                                                                      |
// | You should have received a copy of the GNU GPL License               |
// | along with this program; if not, visit www.sitellite.org.            |
// | The license text is also available at the following web site         |
// | address: <http://www.sitellite.org/index/license                     |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <john.luxford@gmail.com>                       |
// +----------------------------------------------------------------------+
//
// resolved tickets:
// #174 CMS cancel.
//

global $cgi;

class SitepresenterEditSlideForm extends MailForm {
	function SitepresenterEditSlideForm () {
		parent::MailForm ();
		
		$this->parseSettings ('inc/app/sitepresenter/forms/edit/slide/settings.php');
		
		global $page, $cgi;
		
		page_add_script ('
			function cms_cancel (f) {
				onbeforeunload_form_submitted = true;
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
		$this->widgets['submit_button']->buttons[0]->extra = 'onclick="onbeforeunload_form_submitted = true;"';
		$this->widgets['submit_button']->buttons[1]->extra = 'onclick="return cms_cancel (this.form)"';

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