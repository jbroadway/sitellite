<?php

function sitepublisher_rule_folder ($vals) {
	if (! @is_dir ($vals['folder'])) {
		return false;
	}
	if (! @is_writeable ($vals['folder'])) {
		return false;
	}
	return true;
}

class SitepublisherPublishForm extends MailForm {
	function SitepublisherPublishForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/sitepublisher/forms/publish/settings.php');
		$this->widgets['submit_button']->buttons[0]->extra = 'onclick="this.value = \'' . intl_get ('Please Wait...') . '\'"';
		$this->widgets['submit_button']->buttons[1]->extra = 'onclick="window.location.href = \'' . site_prefix () . '/index/cms-cpanel-action\'; return false"';
		page_title (intl_get ('SitePublisher'));

		loader_import ('saf.Database.PropertySet');
		$this->_ps = new PropertySet ('sitepublisher', 'defaults');

		$folder = $this->_ps->get ('folder');
		if (! empty ($folder)) {
			$this->widgets['folder']->setValue ($folder);
		}

		$extension = $this->_ps->get ('extension');
		if (! $extension) {
			$extension = 'html';
		}
		$this->widgets['extension']->setValue ($extension);
	}

	function onSubmit ($vals) {
		$this->_ps->set ('folder', $vals['folder']);
		$this->_ps->set ('extension', $vals['extension']);

		set_time_limit (0);
		chdir ('inc/app/sitepublisher/bin');
		$out = shell_exec ('./publish.sh ' . escapeshellarg (site_domain ()) . ' ' . escapeshellarg ($vals['folder']));
		if ($vals['extension'] != 'html') {
			$out .= shell_exec ('./rename.sh ' . escapeshellarg ($vals['folder']) . ' ' . escapeshellarg ($vals['extension']));
		}
		chdir ('../../../..');

		page_title (intl_get ('SitePublisher - Done'));
		echo '<p><a href="' . site_prefix () . '/index/cms-cpanel-action">' . intl_get ('Click here to continue.') . '</a></p>';
		echo '<h2>' . intl_get ('Publisher Output:') . '</h2>';
		echo '<pre style="border: 1px solid #999; background-color: #eee; padding: 10px">';
		echo htmlentities ($out);
		echo '</pre>';
	}
}

?>