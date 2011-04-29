<?php

loader_import ('imagechooser.Functions');

class ImagechooserAdminUpdateForm extends MailForm {
	function ImagechooserAdminUpdateForm () {
		parent::MailForm ();
		global $cgi;
		page_title (intl_get ('Updating file') . ': ' . $cgi->location . '/' . $cgi->src);
		$this->parseSettings ('inc/app/imagechooser/forms/admin/update/settings.php');


		$this->widgets['submit_button']->buttons[1]->extra = 'onclick="window.close()"';

	}

	function onSubmit ($vals) {
		
		move_uploaded_file ($vals['new_file']->tmp_name,
			site_docroot () . $vals['location'] . '/' . $vals['src']);		

		echo <<<EOT
<script language="javascript" type="text/javascript">
<!--
window.close ();
// -->
</script>
EOT;

		exit;
	}
}

?>
