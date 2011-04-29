<?php

global $cgi;

if (! @is_writeable ('inc/html/' . $cgi->path)) {
	page_title (intl_get ('An Error Occurred'));
	echo '<p>' . intl_get ('Your template is not writeable by Sitellite.  Please adjust your server settings to continue.') . '</p>';
	return;
}

loader_import('saf.File');
loader_import('sitetemplate.Filters');

class SitetemplateEditForm extends MailForm {
	function SitetemplateEditForm () {
		parent::MailForm ();

		global $cgi;

		$this->parseSettings ('inc/app/sitetemplate/forms/edit/settings.php');

		if (@file_exists ('inc/app/xed/lib/Widget/Linker.php')) {
			$this->link_chooser = true;
		}

		list ($set, $tpl) = explode ('/', $cgi->path);
		list ($mode, $name, $ext) = preg_split ('|\.|', basename ($cgi->path));

		if (@file_exists ('inc/html/' . $set . '/config.ini.php')) {
			$info = parse_ini_file ('inc/html/' . $set . '/config.ini.php');
			if (isset ($info['set_name'])) {
				$sname = $info['set_name'];
			} else {
				$sname = $set;
			}
		} else {
			$sname = $set;
		}

		session_set ('imagechooser_path', site_prefix () . '/inc/html/' . $set . '/pix');

		page_title (intl_get ('Editing Template') . ': ' . $sname . ' / ' . strtoupper ($mode) . ' / ' . ucfirst ($name));

		$set = str_replace ('/' . $mode . '.' . $name . '.' . $ext, '', $cgi->path);

		$this->widgets['edit_buttons']->data = array ('mode' => strtoupper ($mode), 'name' => ucfirst ($name), 'link_chooser' => $this->link_chooser);

		$this->widgets['body']->setValue (join ('', file ('inc/html/' . $cgi->path)));

		$this->widgets['submit_buttons']->data = array ('set' => $set);
	}

	function onSubmit ($vals) {
		if (! file_overwrite ('inc/html/' . $vals['path'], $vals['body'])) {
			page_title (intl_get ('An Error Occurred'));
			echo '<p>' . intl_get ('The file was unable to be saved.  Please verify your server settings before trying again.') . '</p>';
			return;
		}

		list ($set, $tpl) = explode ('/', $vals['path']);

		session_set ('sitellite_alert', intl_get ('Template Saved'));
		header ('Location: ' . site_prefix () . '/index/sitetemplate-templateselect-action?set_name=' . $set);
		exit;
	}
}

?>
