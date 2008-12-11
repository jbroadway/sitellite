<?php

global $cgi;

if (! @is_writeable ('inc/html/' . $cgi->set_name)) {
	page_title (intl_get ('An Error Occurred'));
	echo '<p>' . intl_get ('Your template is not writeable by Sitellite.  Please adjust your server settings to continue.') . '</p>';
	return;
}

loader_import('saf.File');
loader_import('sitetemplate.Filters');

class SitetemplateNewtplForm extends MailForm {
	function SitetemplateNewtplForm () {
		parent::MailForm ();

		global $cgi;
		
		$this->parseSettings ('inc/app/sitetemplate/forms/newtpl/settings.php');

		if (@file_exists ('inc/app/xed/lib/Widget/Linker.php')) {
			$this->link_chooser = true;
		}

		$mode = 'html';
		$name = 'new file';
		$set = $cgi->set_name;
		$sname = $set;

		session_set ('imagechooser_path', site_prefix () . '/inc/html/' . $set . '/pix');
		
		if (@file_exists ('inc/html/' . $set . '/config.ini.php')) {
			$info = parse_ini_file ('inc/html/' . $set . '/config.ini.php');
			if (isset ($info['set_name'])) {
				$sname = $info['set_name'];
			}
		}

		page_title (intl_get ('Editing New Template in') . ': ' . $sname);

		$set = str_replace ('/' . $mode . '.' . $name . '.' . $ext, '', $cgi->path);

		//$this->widgets['edit_buttons']->data = array ('mode' => strtoupper ($mode), 'name' => ucfirst ($name), 'link_chooser' => $this->link_chooser);

		//$this->widgets['body']->setValue (join ('', file ('inc/html/' . $sname)));

		$this->widgets['submit_buttons']->data = array ('set' => $set);
		
		$this->widgets['path']->setValue ($set);
	}

	function onSubmit ($vals) {
		$vals['output_mode'] = strtolower ($vals['output_mode']);
		$vals['name'] = strtolower ($vals['name']);
		
		//make sure that file doesnt exit
		if ( file_exists ('inc/html/' . $vals['set_name'] . '/' . $vals['output_mode'] . '.' . $vals['name'] . '.tpl') ) {
			echo '<p>' . intl_get ('A file with that name already exists. Choose a different template name.') . '</p>';
			echo '<p>' . intl_get ('Go <a href=javascript:history.back()>back</a> to choose a different file name.') . '</p>';
			
		}
		
		if (! file_overwrite ('inc/html/' . $vals['set_name'] . '/' . $vals['output_mode'] . '.' . $vals['name'] . '.tpl', $vals['body'])) {
			page_title (intl_get ('An Error Occurred'));
			echo '<p>' . intl_get ('The file was unable to be saved.  Please verify your server settings before trying again.') . '</p>';
			return;
		}

		umask (0000);
		chmod ('inc/html/' . $vals['set_name'] . '/' . $vals['output_mode'] . '.' . $vals['name'] . '.tpl', 0777);

		list ($set, $tpl) = explode ('/', $vals['path']);
		echo $set . ' ' . $tpl;
		page_title ('Template Saved');
		echo '<p><a href="' . site_prefix () . '/index/sitetemplate-templateselect-action?set_name=' . $vals['set_name'] . '">' . intl_get ('Return to template set') . '</a></p>';
	}
}

?>