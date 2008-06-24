<?php

global $cgi;

loader_import ('cms.Workflow.Lock');

lock_init ();

if ($cgi->unlock == 1) {
	lock_remove ('sitewiki_page', $cgi->page);

	if ($cgi->ret) {
		header ('Location: ' . $cgi->ret);
	} else {
		header ('Location: ' . site_prefix () . '/index/sitewiki-app/show.' . $cgi->page);
	}
	exit;
}

page_title (intl_get ('Editing') . ' ' . $cgi->page);

class SitewikiEditForm extends MailForm {
	var $editable = true;

	function SitewikiEditForm () {
		parent::MailForm (__FILE__);

		$level = 0;
		if (session_valid ()) {
			$level++;
		}
		if (session_admin ()) {
			$level++;
		}

		global $cgi;
		$res = db_fetch (
			'select * from sitewiki_page where id = ?',
			$cgi->page
		);

		if (! $res) {
			$this->widgets['submit_button']->buttons[1]->extra = 'onclick="window.location.href = \'' . site_prefix () . '/index/sitewiki-edit-form?page=' . $cgi->page . '&unlock=1&ret=' . urlencode ($_SERVER['HTTP_REFERER']) . '\'; return false"';
			if ($level >= appconf ('default_edit_level')) {
				$this->new_page = true;
			} else {
				echo template_simple ('not_visible.spt');
				$this->editable = false;
				return;
			}
			$this->widgets['view_level']->setValue (appconf ('default_view_level'));
			$this->widgets['edit_level']->setValue (appconf ('default_edit_level'));
		} else {
			$this->widgets['submit_button']->buttons[1]->extra = 'onclick="window.location.href = \'' . site_prefix () . '/index/sitewiki-edit-form?page=' . $cgi->page . '&unlock=1\'; return false"';
			if ($level < $res->edit_level) {
				echo template_simple ('not_visible.spt');
				$this->editable = false;
				return;
			} else {
				$this->widgets['body']->setValue ($res->body);
				$this->widgets['view_level']->setValue ($res->view_level);
				$this->widgets['edit_level']->setValue ($res->edit_level);
			}
		}

		if (! appconf ('security_test')) {
			unset ($this->widgets['security_test']);
		}

		if (! session_valid ()) {
			unset ($this->widgets['files']);
			unset ($this->widgets['file_1']);
			unset ($this->widgets['file_2']);
			unset ($this->widgets['file_3']);
		}
	}

	function show () {
		if (! $this->editable) {
			return;
		}
		return parent::show ();
	}

	function onSubmit ($vals) {
		loader_import ('cms.Versioning.Rex');

		$rex = new Rex ('sitewiki_page');

		unset ($vals['editing']);
		unset ($vals['instructions']);
		unset ($vals['security_test']);
		unset ($vals['submit_button']);
		$vals['id'] = $vals['page'];
		unset ($vals['page']);

		if ($this->new_page) {
			$vals['created_on'] = date ('Y-m-d H:i:s');
			$vals['updated_on'] = date ('Y-m-d H:i:s');
			$vals['owner'] = session_username ();
			if (! $vals['owner']) {
				$vals['owner'] = 'anonymous';
			}
			$vals2 = $vals;
			unset ($vals2['files']);
			unset ($vals2['file_1']);
			unset ($vals2['file_2']);
			unset ($vals2['file_3']);
			$res = $rex->create ($vals2, 'Page created.');
		} else {
			$vals['updated_on'] = date ('Y-m-d H:i:s');
			$vals2 = $vals;
			unset ($vals2['files']);
			unset ($vals2['file_1']);
			unset ($vals2['file_2']);
			unset ($vals2['file_3']);
			$method = $rex->determineAction ($vals['id']);
			$res = $rex->{$method} ($vals['id'], $vals2);
		}

		if (session_valid ()) {
			// handle files
			$types = preg_split ('/, ?/', appconf ('allowed_file_types'));
			if (is_object ($vals['file_1'])) {
				$info = pathinfo ($vals['file_1']->name);
				if (in_array (strtolower ($info['extension']), $types)) {
					db_execute (
						'insert into sitewiki_file values (null, ?, ?, now(), ?)',
						$vals['id'],
						$vals['file_1']->name,
						session_username ()
					);
					$file_id = db_lastid ();
					$vals['file_1']->move ('inc/app/sitewiki/data', $vals['id'] . '_' . $file_id);
				}
			}
			if (is_object ($vals['file_2'])) {
				$info = pathinfo ($vals['file_2']->name);
				if (in_array (strtolower ($info['extension']), $types)) {
					db_execute (
						'insert into sitewiki_file values (null, ?, ?, now(), ?)',
						$vals['id'],
						$vals['file_2']->name,
						session_username ()
					);
					$file_id = db_lastid ();
					$vals['file_2']->move ('inc/app/sitewiki/data', $vals['id'] . '_' . $file_id);
				}
			}
			if (is_object ($vals['file_3'])) {
				$info = pathinfo ($vals['file_3']->name);
				if (in_array (strtolower ($info['extension']), $types)) {
					db_execute (
						'insert into sitewiki_file values (null, ?, ?, now(), ?)',
						$vals['id'],
						$vals['file_3']->name,
						session_username ()
					);
					$file_id = db_lastid ();
					$vals['file_3']->move ('inc/app/sitewiki/data', $vals['id'] . '_' . $file_id);
				}
			}
		}

		lock_remove ('sitewiki_page', $vals['id']);

		header ('Location: ' . site_prefix () . '/index/sitewiki-app/show.' . $vals['id']);
		exit;
	}
}

$form = new SitewikiEditForm ();
if ($form->editable && ! isset ($cgi->editing)) {

	if (! session_valid ()) {
		global $session;
		$session->username = '';
	}

	if (lock_exists ('sitewiki_page', $cgi->page)) {
		if ($cgi->break_lock == 1) {
			lock_remove ('sitewiki_page', $cgi->page);
			if (! session_valid ()) {
				$session->username = 'anonymous';
			}
			lock_add ('sitewiki_page', $cgi->page);
		} else {
			$info = lock_info ('sitewiki_page', $cgi->page);

			loader_import ('saf.Date');
			$now = time ();
			$then = Date::toUnix ($info->expires) - 3600;
			$diff = $now - $then;
			$info->min = round ($diff / 60);

			echo template_simple ('locked.spt', $info);
			return;
		}
	} else {
		if (! session_valid ()) {
			$session->username = 'anonymous';
		}
		lock_add ('sitewiki_page', $cgi->page);
	}
}
echo $form->run ();

?>