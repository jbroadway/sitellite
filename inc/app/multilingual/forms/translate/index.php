<?php

loader_import ('multilingual.Translation');
loader_import ('multilingual.Filters');

global $cgi;

if (! isset ($cgi->_lang)) {
	page_title (intl_get ('Select Language'));
	echo template_simple ('select_lang.spt', multilingual_get_langs ());
	return;
}

if (isset ($rex->info['Collection']['translate'])) {
	list ($call, $name) = explode (':', $rex->info['Collection']['translate']);
	if ($call == 'box') {
		echo loader_box ($name);
	} elseif ($call == 'form') {
		echo loader_form ($name);
	} else {
		echo loader_form ($call);
	}
	return;

} else {

class MultilingualTranslateForm extends MailForm {
	function MultilingualTranslateForm () {
		parent::MailForm ();

		$this->autosave = true;

		global $page, $cgi, $intl;

		$intl->language = $cgi->_lang;
		$intl->charset = $intl->languages[$intl->language]['charset'];

		$this->extra = 'id="multilingual-translate-form"';

		$w =& $this->addWidget ('template', '_header');
		$this->lang = multilingual_filter_lang ($cgi->_lang);
		$this->reflang = multilingual_filter_lang (intl_default_lang ());
		$w->template = '<tr><th colspan="2" width="50%">{intl Language}: {lang}</th><th width="50%">{intl Reference}: {reflang}</th></tr>';

		// get copy from repository
		loader_import ('cms.Versioning.Rex');
		$rex = new Rex ($cgi->_collection);
		$doc = $rex->getCurrent ($cgi->_key);
		$widgets = $rex->getStruct ();
		if (! $widgets) {
			$widgets = array ();
		}

		// edit widgets go here
		$this->widgets = array_merge ($this->widgets, $widgets);
		foreach ($this->widgets as $k => $v) {
			if (in_array ($k, array ($rex->key, 'sitellite_status', 'sitellite_access', 'sitellite_startdate', 'sitellite_expirydate', 'sitellite_owner', 'sitellite_team'))) {
				unset ($this->widgets[$k]);
				continue;
			}
			if ($v->name != '_header' && ! in_array ($v->type, array ('text', 'textarea', 'xeditor'))) {
				unset ($this->widgets[$k]);
				continue;
			}
			if (strtolower (get_class ($this->widgets[$k])) == 'mf_widget_xeditor') {
				$this->extra = 'onsubmit="xed_copy_value (this, \'' . $k . '\')"';
			}
			if (isset ($doc->{$k})) {
				$this->widgets[$k]->reference = $doc->{$k};
			}
		}

		$w =& $this->addWidget ('hidden', '_key');
		$w =& $this->addWidget ('hidden', '_collection');
		$w =& $this->addWidget ('hidden', '_lang');

		$w =& $this->addWidget ('status', '_status');
		$w->alt = intl_get ('Translation Status');
		$w->setValue ('draft');
		$w->reference = '';

		// submit buttons
		$w =& $this->addWidget ('msubmit', 'submit_button');
		$w->reference = '';
		$b =& $w->getButton ();
		$b->setValues (intl_get ('Save'));
		$b->extra = 'onclick="onbeforeunload_form_submitted = true"';

		$b =& $w->addButton ('submit_button', intl_get ('Cancel'));
		$b->extra = 'onclick="return cms_cancel (this.form)"';

		$this->error_mode = 'all';

		if ($rex->info['Collection']['singular']) {
			page_title (intl_get ('Translating') . ' ' . $rex->info['Collection']['singular'] . ': ' . $doc->{$rex->key});
		} else {
			page_title (intl_get ('Translating Item') . ': ' . $doc->{$rex->key});
		}

		$tr = new Translation ($cgi->_collection, $cgi->_lang);
		$curr = $tr->get ($cgi->_key);
		if ($curr) {
			foreach ($curr->data as $k => $v) {
				if (isset ($this->widgets[$k])) {
					$this->widgets[$k]->setDefault ($v);
				}
			}
			$this->widgets['_status']->setDefault ($curr->sitellite_status);
		}
	}

	function onSubmit ($vals) {
		$collection = $vals['_collection'];
		$status = $vals['_status'];
		$key = $vals['_key'];
		$lang = $vals['_lang'];

		unset ($vals['_collection']);
		unset ($vals['_status']);
		unset ($vals['_key']);
		unset ($vals['_lang']);
		unset ($vals['_header']);
		unset ($vals['submit_button']);

		foreach ($vals as $k => $v) {
			if (empty ($v)) {
				unset ($vals[$k]);
			}
		}

		$tr = new Translation ($collection, $lang);
		$id = $tr->getID ($key);
		if (! $id) {
			$tr->add ($key, $status, $vals);
		} else {
			$tr->save ($key, $status, $vals);
		}

		session_set ('sitellite_alert', intl_get ('Translation Saved'));
		header ('Location: ' . site_prefix () . '/index/multilingual-app');
		exit;
	}
}

echo CMS_JS_CANCEL;
$form = new MultilingualTranslateForm;
echo $form->run ();

}

?>
