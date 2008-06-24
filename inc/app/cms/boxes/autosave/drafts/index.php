<?php

loader_import ('saf.MailForm.Autosave');
$a = new Autosave ();
$list = $a->retrieve_all ();
if (count ($list) > 0) {
	loader_import ('cms.Filters');
	page_title (intl_get ('Drafts'));
	echo template_simple ('autosave_drafts.spt', $list);
} else {
	echo '<p>' . intl_get ('No drafts.') . '</p>';
}

?>