<?php

loader_import ('saf.Database.PropertySet');

class SitelinksPropertiesForm extends MailForm {
	function SitelinksPropertiesForm () {
		parent::MailForm ();

		page_title (intl_get ('Editing Extras'));

		global $cgi;

		// 1. read config and build widgets
		$this->type = db_shift ('select ctype from sitelinks_item where id = ?', $cgi->_key);
		if (empty ($this->type)) {
			$this->type = 'default';
		}

		ini_add_filter ('ini_filter_split_comma_single', array (
			'rule 0', 'rule 1', 'rule 2', 'rule 3', 'rule 4', 'rule 5', 'rule 6', 'rule 7', 'rule 8',
			'button 0', 'button 1', 'button 2', 'button 3', 'button 4', 'button 5', 'button 6', 'button 7', 'button 8',
		));
		$this->config = ini_parse ('inc/app/sitelinks/conf/types/' . $this->type . '.php');
		unset ($this->config['Type']);
		ini_clear ();

		foreach ($this->config as $name => $data) {
			$w =& $this->createWidget ($name, $data);
		}

		// 2. load values from ps
		$ps = new PropertySet ('sitelinks_item', $cgi->_key);

		foreach ($ps->get () as $k => $v) {
			if (isset ($this->widgets[$k])) {
				$this->widgets[$k]->setDefault ($v);
			}
		}

		// misc widgets
		$this->addWidget ('hidden', '_collection');
		$this->addWidget ('hidden', '_key');
		$this->addWidget ('hidden', '_return');

		$w =& $this->addWidget ('msubmit', 'submitButton');

		$b =& $w->getButton ();
		$b->setValues (intl_get ('Save'));

		$b =& $w->addButton ();
		$b->setValues (intl_get ('Cancel'));
		$b->extra = 'onclick="history.go (-1); return false"';
	}

	function onSubmit ($vals) {
		// 1. set values to ps
		$ps = new PropertySet ('sitelinks_item', $vals['_key']);

		// 2. send user back
		foreach (array_keys ($this->config) as $k) {
			$ps->set ($k, $vals[$k]);
		}

		if (! empty ($vals['_return'])) {
			header ('Location: ' . $vals['_return']);
			exit;
		}
		header ('Location: ' . site_prefix () . '/index/cms-browse-action?collection=sitelinks_item');
		exit;
	}
}

?>