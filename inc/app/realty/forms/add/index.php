<?php

class RealtyAddForm extends MailForm {
	function RealtyAddForm () {
		parent::MailForm ();

		$w =& $this->addWidget ('hidden', 'collection');
		$w =& $this->addWidget ('hidden', '_return');

		global $cgi;
		loader_import ('cms.Versioning.Rex');

		$rex = new Rex ($cgi->collection);
		$widgets = $rex->getStruct ();
		if (! $widgets) {
			die ($rex->error);
		}

		$this->widgets = array_merge ($this->widgets, $widgets);

		foreach (array_keys ($this->widgets) as $k) {
			if (strtolower (get_class ($this->widgets[$k])) == 'mf_widget_xeditor') {
				$this->extra = 'onsubmit="xed_copy_value (this, \'' . $k . '\')"';
			}
		}

		if (isset ($rex->info['Collection']['singular'])) {
			page_title (intl_get ('Adding') . ' ' . $rex->info['Collection']['singular']);
		} else {
			page_title (intl_get ('Adding Item'));
		}

		$w =& $this->addWidget ('msubmit', 'submit_button');

		$b =& $w->getButton ();
		$b->setValues (intl_get ('Create'));

		$b =& $w->addButton ('submit_button', intl_get ('Cancel'));
		$b->extra = 'onclick="return cms_cancel (this.form)"';
	}

	function onSubmit ($vals) {
		loader_import ('cms.Versioning.Rex');

		$collection = $vals['collection'];
		unset ($vals['collection']);
		if (empty ($collection)) {
			$collection = 'sitellite_page';
		}

		$return = $vals['_return'];
		unset ($vals['_return']);

		$rex = new Rex ($collection); // default: database, database

		unset ($vals['submit_button']);

		$vals['photo1'] = '';
		$vals['photo2'] = '';
		$vals['photo3'] = '';
		$vals['photo4'] = '';
		$vals['photo5'] = '';
		$vals['photo6'] = '';
		$vals['photo7'] = '';
		$vals['photo8'] = '';

		$res = $rex->create ($vals);

		if (isset ($vals[$rex->key]) && $vals[$rex->key] != false) {
			$key = $vals[$rex->key];
		} elseif (! is_bool ($res)) {
			$key = $res;
		} else {
			$key = 'Unknown';
		}

		if (! $res) {
			die ($rex->error);
		} else {
			global $cgi;
			$alpha = range ('a', 'h');
			for ($i = 0; $i < 8; $i++) {
				$n = $i + 1;
				if (is_object ($cgi->{'photo' . $n})) {
					$cgi->{'photo' . $n}->move ('inc/app/realty/pix', $key . $alpha[$i] . '.jpg');
					db_execute ('update realty_listing set photo' . $n . ' = "/inc/app/realty/pix/' . $key . $alpha[$i] . '.jpg" where id = ' . $key);
				}
			}

			loader_import ('cms.Workflow');
			echo Workflow::trigger (
				'add',
				array (
					'collection' => $collection,
					'key' => $key,
					'data' => $vals,
					'changelog' => intl_get ('Item added.'),
					'message' => 'Collection: ' . $collection . ', Item: ' . $key,
				)
			);

			header ('Location: ' . site_prefix () . '/index/realty-details-action/id.' . $key);
			exit;
		}

	}
}

//echo CMS_JS_PREVIEW;
echo CMS_JS_CANCEL;

$form = new RealtyAddForm;
echo $form->run ();

?>