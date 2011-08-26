<?php

class XedExampleForm extends MailForm {
	function XedExampleForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/xed/forms/spellchecker/settings.php');
	}
	function onSubmit ($vals) {
		page_onload (false);
		page_onclick (false);
		page_onfocus (false);

		echo '<ul><li><a href="#rendered">Rendered HTML</a></li><li><a href="#source">HTML Source</a></li><li><a href="xed-example-form">Back</a></li></ul>';
		echo '<a name="rendered"></a><h2>Rendered HTML:</h2><div style="border: #369 1px dashed; padding: 10px; width: 600px">';
		echo $vals['xeditor'];
		echo '<br clear="all" /></div><p><a href="#top">[ top ]</a></p><a name="source"></a><h2>HTML Source:</h2><div style="border: #369 1px dashed">';
		echo '<pre>' . htmlentities ($vals['xeditor']) . '</pre></div>';
	}
}

page_title ('Xed Example Form');
$form = new XedExampleForm ();
echo $form->run ();

?>