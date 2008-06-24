<?php

loader_import ('saf.Database.PropertySet');

class SitegalleryEditForm extends MailForm {
	function SitegalleryEditForm () {
		parent::MailForm (__FILE__);
		page_title (intl_get ('Edit Album Description'));
		$multi = false;
		if (intl_lang () == intl_default_lang ()) {
			$ps1 = new PropertySet ('sitegallery', 'album_title');
			$ps2 = new PropertySet ('sitegallery', 'album_description');
			$ps3 = new PropertySet ('sitegallery', 'album_date');
		} else {
			$this->lang = intl_lang ();
			$this->reflang = intl_default_lang ();
			$multi = true;
			$ps1 = new PropertySet ('sitegallery', 'album_title_' . intl_lang ());
			$ps2 = new PropertySet ('sitegallery', 'album_description_' . intl_lang ());
			$ps3 = new PropertySet ('sitegallery', 'album_date');
			$ps4 = new PropertySet ('sitegallery', 'album_title');
			$ps5 = new PropertySet ('sitegallery', 'album_description');
		}
		global $cgi;
		$name = $ps1->get ($cgi->album);
		if (! empty ($name)) {
			$this->widgets['title']->setValue ($name);
		}
		$desc = $ps2->get ($cgi->album);
		if (! empty ($desc)) {
			$this->widgets['description']->setValue ($desc);
		}
		$date = $ps3->get ($cgi->album);
		if (! empty ($date)) {
			$this->widgets['date']->setValue ($date);
		}
		if ($multi) {
			$n = $ps4->get ($cgi->album);
			if (! empty ($n)) {
				$this->widgets['title_ref'] =& $this->widgets['title_ref']->changeType ('info');
				$this->widgets['title_ref']->setValue ($n);
				$this->widgets['title_ref']->htmlentities = false;
			}
			$n = $ps5->get ($cgi->album);
			if (! empty ($n)) {
				$this->widgets['description_ref'] =& $this->widgets['description_ref']->changeType ('info');
				$this->widgets['description_ref']->setValue (nl2br ($n));
				$this->widgets['description_ref']->alt = intl_get ('Reference Description');
				$this->widgets['description_ref']->htmlentities = false;
			}
			$langs = intl_get_langs ();
			$this->message = intl_get ('Language') . ': ' . $langs[intl_lang ()];
		}
	}

	function onSubmit ($vals) {
		if (intl_lang () == intl_default_lang ()) {
			$ps1 = new PropertySet ('sitegallery', 'album_title');
			$ps2 = new PropertySet ('sitegallery', 'album_description');
			$ps3 = new PropertySet ('sitegallery', 'album_date');
		} else {
			$ps1 = new PropertySet ('sitegallery', 'album_title_' . intl_lang ());
			$ps2 = new PropertySet ('sitegallery', 'album_description_' . intl_lang ());
			$ps3 = new PropertySet ('sitegallery', 'album_date');
		}
		$ps1->set ($vals['album'], $vals['title']);
		$ps2->set ($vals['album'], $vals['description']);
		$ps3->set ($vals['album'], $vals['date']);
		if (appconf ('page_alias')) {
			header ('Location: ' . site_prefix () . '/index/' . appconf ('page_alias'));
			exit;
		}
		header ('Location: ' . site_prefix () . '/index/sitegallery-app');
		exit;
	}
}

?>