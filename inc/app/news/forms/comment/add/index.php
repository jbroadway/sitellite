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
// #198 Allow for HTML mailing templates.

class NewsCommentAddForm extends MailForm {
	function NewsCommentAddForm () {
		parent::MailForm ();

		$this->parseSettings ('inc/app/news/forms/comment/add/settings.php');

		page_title (intl_get ('Add Comment'));

		if (session_valid ()) {
			$this->widgets['user_id']->setDefault (session_username ());
		}

		global $cgi;

		page_add_script ('
			function news_cancel (f) {
				window.location.href = "' . site_prefix () . '/index/news-app/story.' . $cgi->story_id . '";
				return false;
			}
		');

		$this->widgets['submit_button']->buttons[1]->extra = 'onclick="return news_cancel (this.form)"';

		if (! appconf ('comments_security')) {
			unset ($this->widgets['security_test']);
		}
	}

	function onSubmit ($vals) {
		loader_import ('news.Comment');

		$c = new NewsComment;

		$vals['ts'] = date ('Y-m-d H:i:s');
		unset ($vals['submit_button']);

		unset ($vals['security_test']);

		$c->add ($vals);

		$ce = appconf ('comments_email');
		if ($ce) {
			loader_import ('news.Functions');
//START: SEMIAS. #198 Allow for HTML mailing templates.
//-----------------------------------------------
//			@mail ($ce, intl_get ('News Comment Notice'), template_simple ('comment_email.spt', $vals), 'From: ' . 'news@' . site_domain ());
//-----------------------------------------------
            site_mail (
                $ce,
                intl_get ('News Comment Notice'),
                template_simple ('comment_email.spt', $vals),
                'From: ' . 'news@' . site_domain (),
                array ("Is_HTML" => true)
            );
//END: SEMIAS.
		}

		page_title (intl_get ('Comment Added'));
		echo template_simple ('comment_added.spt', $vals);
	}
}

?>