<?php

global $cgi;

if (! @is_writeable ('inc/html/' . $cgi->set_name)) {
	page_title (intl_get ('An Error Occurred'));
	echo '<p>' . intl_get ('Your template set is not writeable by Sitellite.  Please adjust your server settings to continue.') . '</p>';
	return;
}

loader_import('saf.File');
loader_import('sitetemplate.Filters');

class SitetemplateNewcssForm extends MailForm {
	function SitetemplateNewcssForm () {
		parent::MailForm ();

		global $cgi;
		
		$this->parseSettings ('inc/app/sitetemplate/forms/newcss/settings.php');

		$mode = 'html';
		$name = 'new file';
		$set = $cgi->set_name;
		$sname = $set;
		
		
		if (@file_exists ('inc/html/' . $set . '/config.ini.php')) {
			$info = parse_ini_file ('inc/html/' . $set . '/config.ini.php');
			if (isset ($info['set_name'])) {
				$sname = $info['set_name'];
			}
		}

		page_title (intl_get ('Editing New Style in') . ': ' . $sname);

		$set = str_replace ('/' . $mode . '.' . $name . '.' . $ext, '', $cgi->path);

		//$this->widgets['edit_buttons']->data = array ('mode' => strtoupper ($mode), 'name' => ucfirst ($name));

		//$this->widgets['body']->setValue (join ('', file ('inc/html/' . $sname)));

		$this->widgets['submit_buttons']->data = array ('set' => $set);
		
		$this->widgets['path']->setValue ($set);
	}

	function onSubmit ($vals) {
		$vals['name'] = strtolower ($vals['name']);
		
		//make sure that file doesnt exit
		if (file_exists ('inc/html/' . $vals['set_name'] . '/' . $vals['name'] . '.css')) {	
			echo '<p>' . intl_get ('A file with that name already exists. Choose a different file name.') . '</p>';
			echo '<p>' . intl_get ('Go <a href=javascript:history.back()>back</a> to choose a different file name.') . '</p>';
		}

		if (preg_match ('/\.css$/i', $vals['name'])) {
			$ext = '';
		} else {
			$ext = '.css';
		}

		if (! file_overwrite ('inc/html/' . $vals['set_name'] . '/' . $vals['name'] . $ext, $vals['body'])) {
			page_title (intl_get ('An Error Occurred'));
			echo '<p>' . intl_get ('The file was unable to be saved.  Please verify your server settings before trying again.') . '</p>';
			return;
		}

		umask (0000);
		chmod ('inc/html/' . $vals['set_name'] . '/' . $vals['name'] . $ext, 0777);

		list ($set, $tpl) = explode ('/', $vals['path']);
		echo $set . ' ' . $tpl;
		page_title ('File Saved');
		echo '<p><a href="' . site_prefix () . '/index/sitetemplate-templateselect-action?set_name=' . $vals['set_name'] . '">' . intl_get ('Return to template set') . '</a></p>';
	}
}

?>