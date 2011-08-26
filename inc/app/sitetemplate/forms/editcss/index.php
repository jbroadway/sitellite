<?php

global $cgi;

if (! @is_writeable ('inc/html/' . $cgi->path)) {
	page_title (intl_get ('An Error Occurred'));
	echo '<p>' . intl_get ('Your stylesheet is not writeable by Sitellite.  Please adjust your server settings to continue.') . '</p>';
	return;
}

loader_import('saf.File');

class SitetemplateEditCSSForm extends MailForm {
	function SitetemplateEditCSSForm () {
		parent::MailForm ();

		global $cgi;
		//info($cgi);
		$this->parseSettings ('inc/app/sitetemplate/forms/editcss/settings.php');

		list ($set, $tpl) = explode ('/', $cgi->path);
		list ($name, $ext) = preg_split ('|\.|', basename ($cgi->path));

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

		page_title (intl_get ('Editing Style') . ': ' . $sname . ' / ' . strtoupper ($ext) . ' / ' . ucfirst ($name));

		$this->widgets['edit_buttons']->data = array ('name' => ucfirst ($name));
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

		page_title ('Stylesheet Saved');
		echo '<p><a href="' . site_prefix () . '/index/sitetemplate-templateselect-action?set_name=' . $set . '">' . intl_get ('Return to template set') . '</a></p>';
	}
}

?>