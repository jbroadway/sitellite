<?php

if (@file_exists ('inc/app/cms/conf/collections/' . $parameters['table'] . '.php')) {
	page_title (intl_get ('Error'));
	echo '<p>' . intl_get ('This collection file already exists. Please choose another database table.') . '</p>';
	echo '<p><a href="' . site_prefix () . '/index/myadm-app">' . intl_get ('Back') . '</a></p>';
	return;
}

if (! @is_writeable ('inc/app/cms/conf/collections')) {
	page_title (intl_get ('Error'));
	echo '<p>' . intl_get ('The collection folder is not writeable. Please check your server folder permissions and try again.') . '</p>';
	echo '<p><a href="' . site_prefix () . '/index/myadm-app">' . intl_get ('Back') . '</a></p>';
	return;
}

loader_import ('saf.MailForm');

function myadm_get_apps () {
	loader_import ('saf.File.Directory');
	$dir = new Dir (getcwd () . '/inc/app');
	$list = array ('' => '- ' . intl_get ('SELECT') . ' -');
	if (! $dir->handle) {
		return $list;
	}
	$files = $dir->read_all ();
	foreach ($files as $file) {
		if (strpos ($file, '.') === 0 || $file == 'CVS') {
			continue;
		} elseif (@is_dir (getcwd () . '/inc/app/' . $file)) {
			$info = ini_parse (getcwd () . '/inc/app/' . $file . '/conf/config.ini.php', false);
			if (isset ($info['app_name'])) {
				$list[$file] = $info['app_name'];
			} else {
				$list[$file] = ucfirst ($file);
			}
		}
	}
	return $list;
}

function myadm_default_type ($field) {
	$typemap = array (
		'(var)?char\(([0-9]+)\)'	=> 'text',
		'text'						=> 'textarea',
		'mediumtext'				=> 'xed.Widget.Xeditor',
		'longtext'					=> 'xed.Widget.Xeditor',
		'date'						=> 'calendar',
		'time'						=> 'time',
		'datetime'					=> 'datetime',
		'timestamp\([0-9]+\)'		=> 'datetime',
		'enum\((.+)\)'				=> 'select',
		'set\((.+)\)'				=> 'multiple',
		'int\(([0-9]+)\)'			=> 'text',
		'blob'						=> 'textarea',
		'.*'						=> 'text',
	);
	foreach ($typemap as $regex => $type) {
		if (preg_match ('/^' . $regex . '$/i', $field->Type)) {
			return $type;
		}
	}
	return 'text';
}

function myadm_widget_types () {
	return array (
		'boxchooser.Widget.Boxchooser' => intl_get ('Box Browser'),
		'calendar' => intl_get ('Calendar'),
		'checkbox' => intl_get ('Checkboxes'),
		'date' => intl_get ('Date'),
		'datetime' => intl_get ('Date/Time'),
		'datetimeinterval' => intl_get ('Date/Time (at intervals)'),
		'selector' => intl_get ('Dynamic Selector'),
		'filechooser.Widget.Filechooser' => intl_get ('File Browser'),
		'file' => intl_get ('File Upload'),
		'cms.Widget.Folder' => intl_get ('Folders'),
		'hidden' => intl_get ('Hidden'),
		'imagechooser' => intl_get ('Image Browser'),
		'cms.Widget.Keywords' => intl_get ('Keywords'),
		'tinyarea' => intl_get ('Mini Editor'),
		'pagebrowser.Widget.Pagebrowser' => intl_get ('Page Browser'),
		'cms.Widget.Position' => intl_get ('Page Locations'),
		'password' => intl_get ('Password'),
		'info' => intl_get ('Read-Only'),
		'select' => intl_get ('Select'),
		'multiple' => intl_get ('Select Multiple'),
		'access' => intl_get ('Sitellite Access Level'),
		'owner' => intl_get ('Sitellite Owner'),
		'status' => intl_get ('Sitellite Status'),
		'team' => intl_get ('Sitellite Team'),
		'cms.Widget.Templates' => intl_get ('Sitellite Templates'),
		'text' => intl_get ('Text'),
		'textarea' => intl_get ('Text Box'),
		'time' => intl_get ('Time'),
		'timeinterval' => intl_get ('Time (at intervals)'),
		'xed.Widget.Xeditor' => intl_get ('WYSIWYG Editor'),
	);
}

class MyadmCollectionForm extends MailForm {
	function MyadmCollectionForm () {
		parent::MailForm ();

		global $cgi;

		// get collection name info
		if (strpos ($cgi->table, '_') > 0) {
			list ($app, $name) = explode ('_', $cgi->table, 2);
			if (! @is_dir ('inc/app/' . $app)) {
				$app = false; // no app by that name
			}
			$name = ucwords (str_replace ('_', ' ', $name));
			$pleural = $name . 's';
		} else {
			$app = $cgi->table;
			$name = ucwords ($cgi->table);
			$pleural = $name . 's';
		}

		// get general table info
		$dbtable = db_table ($cgi->table);
		$dbtable->getInfo ();
		//info ($dbtable->info);
		$fields = array ();
		$fields_empty = array ('' => '- ' . intl_get ('SELECT') . ' -');
		$title = false;
		$summary = false;
		$keywords = false;
		$body = false;
		$sort_by = false;
		foreach ($dbtable->info as $field) {
			$fields[$field->Field] = $field->Field;
			$fields_empty[$field->Field] = $field->Field;
			if (! $title && $field->Key != 'PRI' && preg_match ('/^(varchar|char)/i', $field->Type)) {
				$title = $field->Field;
			} elseif (! $summary && ($field->Field == 'description' || $field->Field == 'summary')) {
				$summary = $field->Field;
			} elseif (! $keywords && ($field->Field == 'keywords')) {
				$keywords = $field->Field;
			} elseif (! $body && ($field->Field == 'body' || $field->Field == 'content' || $field->Type == 'text' || $field->Type == 'mediumtext' || $field->Type == 'longtext')) {
				$body = $field->Field;
			} elseif (! $sort_by && (preg_match ('/^(date|timestamp)/i', $field->Type))) {
				$sort_by = $field->Field;
			}
		}
		ksort ($fields);
		ksort ($fields_empty);

		// determine default browse fields
		$browse_fields = array ();
		if ($title) {
			$browse_fields[] = $title;
		}
		if ($sort_by) {
			$browse_fields[] = $sort_by;
		}
		foreach ($dbtable->info as $field) {
			if ($field->Field == $dbtable->getPkey ()) {
				continue;
			}
			if (in_array ($field->Field, $browse_fields)) {
				continue;
			}
			if (! preg_match ('/^(enum|int|tinyint|decimal|char|varchar|date|time)/i', $field->Type)) {
				continue;
			}
			$browse_fields[] = $field->Field;
			if (count ($browse_fields) > 6) {
				break;
			}
		}

		// get array of resources
		loader_import ('usradm.Functions');
		$resources = array ('' => '- ' . intl_get ('SELECT') . ' -');
		foreach (session_get_resources () as $resource) {
			$resources[$resource] = usradm_resource_name ($resource);
		}

		$w =&$this->addWidget ('hidden', 'table');

		$w =& $this->addWidget ('tab', 'tab1');
		$w->title = intl_get ('General');

		$w =& $this->addWidget ('text', 'collection_name');
		$w->alt = intl_get ('Collection name');
		if ($pleural) {
			$w->setDefault ($pleural);
		}
		$w->addRule ('not empty', intl_get ('You must enter a collection name.'));

		$w =& $this->addWidget ('text', 'collection_singular');
		$w->alt = intl_get ('Singular item name');
		if ($name) {
			$w->setDefault ($name);
		}
		$w->addRule ('not empty', intl_get ('You must enter a singular item name.'));

		$w =& $this->addWidget ('select', 'collection_app');
		$w->alt = intl_get ('Belongs to app');
		$w->setValues (myadm_get_apps ());
		if ($app) {
			$w->setDefault ($app);
		}

		$w =& $this->addWidget ('select', 'collection_is_versioned');
		$w->alt = intl_get ('Store a change history');
		$w->setValues (array ('1' => 'Yes', '0' => 'No'));

		$w =& $this->addWidget ('select', 'collection_translate');
		$w->alt = intl_get ('Enable translations');
		$w->setValues (array ('1' => 'Yes', '0' => 'No'));

		$w =& $this->addWidget ('select', 'collection_scheduler_skip');
		$w->alt = intl_get ('Skip in scheduled tasks');
		$w->setValues (array ('0' => 'No', '1' => 'Yes'));

		$w =& $this->addWidget ('select', 'collection_visible');
		$w->alt = intl_get ('Make this a hidden collection');
		$w->setValues (array ('1' => 'No', '0' => 'Yes'));

		$w =& $this->addWidget ('text', 'collection_sitesearch_url');
		$w->alt = intl_get ('Page URL for items');
		$w->prepend = '/index/';
		$w->append = ' (ex: myapp-item-action/id.%s)';

		$w =& $this->addWidget ('select', 'collection_sitesearch_access');
		$w->alt = intl_get ('SiteSearch access level');
		$w->setValues (assocify (session_get_access_levels ()));
		$w->setDefault ('public');

		$w =& $this->addWidget ('text', 'collection_add');
		$w->alt = intl_get ('Custom add item form');
		$w->append = ' (ex: form:myapp/add)';

		$w =& $this->addWidget ('text', 'collection_edit');
		$w->alt = intl_get ('Custom edit item form');
		$w->append = ' (ex: form:myapp/edit)';

		$w =& $this->addWidget ('text', 'collection_delete');
		$w->alt = intl_get ('Custom delete item action');
		$w->append = ' (ex: box:myapp/delete)';

		$w =& $this->addWidget ('section', 'section1');
		$w->title = intl_get ('Required Field Info');

		$w =& $this->addWidget ('select', 'collection_key_field');
		$w->alt = intl_get ('Primary key field');
		$w->setValues ($fields);
		$w->setDefault ($dbtable->getPkey ());

		$w =& $this->addWidget ('select', 'collection_title_field');
		$w->alt = intl_get ('Title field');
		$w->setValues ($fields);
		$w->setDefault ($title);

		$w =& $this->addWidget ('select', 'collection_summary_field');
		$w->alt = intl_get ('Summary field');
		$w->setValues ($fields_empty);
		$w->setDefault ($summary);

		$w =& $this->addWidget ('select', 'collection_keywords_field');
		$w->alt = intl_get ('Keywords field');
		$w->setValues ($fields_empty);
		$w->setDefault ($keywords);

		$w =& $this->addWidget ('select', 'collection_body_field');
		$w->alt = intl_get ('Body field');
		$w->setValues ($fields_empty);
		$w->setDefault ($body);

		$w =& $this->addWidget ('select', 'collection_order_by');
		$w->alt = intl_get ('Default sorting field');
		$w->setValues ($fields_empty);
		$w->setDefault ($sort_by);

		$w =& $this->addWidget ('select', 'collection_sorting_order');
		$w->alt = intl_get ('Default sorting order');
		$w->setValues (array ('desc' => intl_get ('Descending'), 'asc' => intl_get ('Ascending')));

		$w =& $this->addWidget ('tab', 'tab2');
		$w->title = intl_get ('Browse Cols');

		$w =& $this->addWidget ('section', 'col1');
		$w->title = intl_get ('Column 1');

		$w =& $this->addWidget ('select', 'browse1');
		$w->alt = intl_get ('Field');
		$w->setValues ($fields);
		$w->setDefault ($browse_fields[0]);

		$w =& $this->addWidget ('text', 'browse1_header');
		$w->alt = intl_get ('Header');
		$w->setDefault (ucwords (str_replace ('_', ' ', $browse_fields[0])));
		$w->addRule ('not empty', intl_get ('You must enter a header for your first browse column.'));

		$w =& $this->addWidget ('select', 'browse1_align');
		$w->alt = intl_get ('Align');
		$w->setValues (array ('left' => intl_get ('Left'), 'center' => intl_get ('Center'), 'right' => intl_get ('Right')));

		$w =& $this->addWidget ('text', 'browse1_width');
		$w->alt = intl_get ('Width');
		$w->extra = 'size="10"';
		$w->append = '%';

		$w =& $this->addWidget ('section', 'col2');
		$w->title = intl_get ('Column 2');

		$w =& $this->addWidget ('select', 'browse2');
		$w->alt = intl_get ('Field');
		$w->setValues ($fields_empty);
		$w->setDefault ($browse_fields[1]);

		$w =& $this->addWidget ('text', 'browse2_header');
		$w->alt = intl_get ('Header');
		$w->setDefault (ucwords (str_replace ('_', ' ', $browse_fields[1])));

		$w =& $this->addWidget ('select', 'browse2_align');
		$w->alt = intl_get ('Align');
		$w->setValues (array ('left' => intl_get ('Left'), 'center' => intl_get ('Center'), 'right' => intl_get ('Right')));

		$w =& $this->addWidget ('text', 'browse2_width');
		$w->alt = intl_get ('Width');
		$w->extra = 'size="10"';
		$w->append = '%';

		$w =& $this->addWidget ('section', 'col3');
		$w->title = intl_get ('Column 3');

		$w =& $this->addWidget ('select', 'browse3');
		$w->alt = intl_get ('Field');
		$w->setValues ($fields_empty);
		$w->setDefault ($browse_fields[2]);

		$w =& $this->addWidget ('text', 'browse3_header');
		$w->alt = intl_get ('Header');
		$w->setDefault (ucwords (str_replace ('_', ' ', $browse_fields[2])));

		$w =& $this->addWidget ('select', 'browse3_align');
		$w->alt = intl_get ('Align');
		$w->setValues (array ('left' => intl_get ('Left'), 'center' => intl_get ('Center'), 'right' => intl_get ('Right')));

		$w =& $this->addWidget ('text', 'browse3_width');
		$w->alt = intl_get ('Width');
		$w->extra = 'size="10"';
		$w->append = '%';

		$w =& $this->addWidget ('section', 'col4');
		$w->title = intl_get ('Column 4');

		$w =& $this->addWidget ('select', 'browse4');
		$w->alt = intl_get ('Field');
		$w->setValues ($fields_empty);
		$w->setDefault ($browse_fields[3]);

		$w =& $this->addWidget ('text', 'browse4_header');
		$w->alt = intl_get ('Header');
		$w->setDefault (ucwords (str_replace ('_', ' ', $browse_fields[3])));

		$w =& $this->addWidget ('select', 'browse4_align');
		$w->alt = intl_get ('Align');
		$w->setValues (array ('left' => intl_get ('Left'), 'center' => intl_get ('Center'), 'right' => intl_get ('Right')));

		$w =& $this->addWidget ('text', 'browse4_width');
		$w->alt = intl_get ('Width');
		$w->extra = 'size="10"';
		$w->append = '%';

		$w =& $this->addWidget ('section', 'col5');
		$w->title = intl_get ('Column 5');

		$w =& $this->addWidget ('select', 'browse5');
		$w->alt = intl_get ('Field');
		$w->setValues ($fields_empty);
		$w->setDefault ($browse_fields[4]);

		$w =& $this->addWidget ('text', 'browse5_header');
		$w->alt = intl_get ('Header');
		$w->setDefault (ucwords (str_replace ('_', ' ', $browse_fields[4])));

		$w =& $this->addWidget ('select', 'browse5_align');
		$w->alt = intl_get ('Align');
		$w->setValues (array ('left' => intl_get ('Left'), 'center' => intl_get ('Center'), 'right' => intl_get ('Right')));

		$w =& $this->addWidget ('text', 'browse5_width');
		$w->alt = intl_get ('Width');
		$w->extra = 'size="10"';
		$w->append = '%';

		$w =& $this->addWidget ('section', 'col6');
		$w->title = intl_get ('Column 6');

		$w =& $this->addWidget ('select', 'browse6');
		$w->alt = intl_get ('Field');
		$w->setValues ($fields_empty);
		$w->setDefault ($browse_fields[5]);

		$w =& $this->addWidget ('text', 'browse6_header');
		$w->alt = intl_get ('Header');
		$w->setDefault (ucwords (str_replace ('_', ' ', $browse_fields[5])));

		$w =& $this->addWidget ('select', 'browse6_align');
		$w->alt = intl_get ('Align');
		$w->setValues (array ('left' => intl_get ('Left'), 'center' => intl_get ('Center'), 'right' => intl_get ('Right')));

		$w =& $this->addWidget ('text', 'browse6_width');
		$w->alt = intl_get ('Width');
		$w->extra = 'size="10"';
		$w->append = '%';

		$w =& $this->addWidget ('tab', 'tab3');
		$w->title = intl_get ('Search Params');

		$w =& $this->addWidget ('section', 'param1');
		$w->title = intl_get ('Parameter 1');

		$w =& $this->addWidget ('select', 'param1_field');
		$w->alt = intl_get ('Field');
		$w->setValues ($fields_empty);

		$w =& $this->addWidget ('select', 'param1_type');
		$w->alt = intl_get ('Type');
		$w->setValues (assocify (array ('text', 'select', 'folder', 'filetype', 'join', 'range')));

		$w =& $this->addWidget ('text', 'param1_display');
		$w->alt = intl_get ('Display text');

		$w =& $this->addWidget ('text', 'param1_values');
		$w->alt = intl_get ('Values/fields');
		$w->extra = 'size="60"';
		$w->prepend = 'eval (';
		$w->append = ');';

		$w =& $this->addWidget ('section', 'param2');
		$w->title = intl_get ('Parameter 2');

		$w =& $this->addWidget ('select', 'param2_field');
		$w->alt = intl_get ('Field');
		$w->setValues ($fields_empty);

		$w =& $this->addWidget ('select', 'param2_type');
		$w->alt = intl_get ('Type');
		$w->setValues (assocify (array ('text', 'select', 'folder', 'filetype', 'join', 'range')));

		$w =& $this->addWidget ('text', 'param2_display');
		$w->alt = intl_get ('Display text');

		$w =& $this->addWidget ('text', 'param2_values');
		$w->alt = intl_get ('Values/fields');
		$w->extra = 'size="60"';
		$w->prepend = 'eval (';
		$w->append = ');';

		$w =& $this->addWidget ('section', 'param3');
		$w->title = intl_get ('Parameter 3');

		$w =& $this->addWidget ('select', 'param3_field');
		$w->alt = intl_get ('Field');
		$w->setValues ($fields_empty);

		$w =& $this->addWidget ('select', 'param3_type');
		$w->alt = intl_get ('Type');
		$w->setValues (assocify (array ('text', 'select', 'folder', 'filetype', 'join', 'range')));

		$w =& $this->addWidget ('text', 'param3_display');
		$w->alt = intl_get ('Display text');

		$w =& $this->addWidget ('text', 'param3_values');
		$w->alt = intl_get ('Values/fields');
		$w->extra = 'size="60"';
		$w->prepend = 'eval (';
		$w->append = ');';

		$w =& $this->addWidget ('tab', 'tab4');
		$w->title = intl_get ('Browse Links');

		$w =& $this->addWidget ('template', 'tab4_help');
		$w->template = '<tr><td colspan="2" id="tab4_help">{intl Browse links are links added to the browse screen that appear beside the Add Item link, such as an export or reports link.}</td></tr>';

		$w =& $this->addWidget ('section', 'link1');
		$w->title = intl_get ('Link 1');

		$w =& $this->addWidget ('text', 'link1_text');
		$w->alt = intl_get ('Text');

		$w =& $this->addWidget ('text', 'link1_url');
		$w->alt = intl_get ('Link URL');

		$w =& $this->addWidget ('select', 'link1_requires');
		$w->alt = intl_get ('Requires');
		$w->setValues (array ('r' => 'Read Access', 'rw' => 'Read/Write Access'));

		$w =& $this->addWidget ('select', 'link1_requires_resource');
		$w->alt = intl_get ('Requires resource');
		$w->setValues ($resources);

		$w =& $this->addWidget ('section', 'link2');
		$w->title = intl_get ('Link 2');

		$w =& $this->addWidget ('text', 'link2_text');
		$w->alt = intl_get ('Text');

		$w =& $this->addWidget ('text', 'link2_url');
		$w->alt = intl_get ('Link URL');

		$w =& $this->addWidget ('select', 'link2_requires');
		$w->alt = intl_get ('Requires');
		$w->setValues (array ('r' => 'Read Access', 'rw' => 'Read/Write Access'));

		$w =& $this->addWidget ('select', 'link2_requires_resource');
		$w->alt = intl_get ('Requires resource');
		$w->setValues ($resources);

		$w =& $this->addWidget ('section', 'link3');
		$w->title = intl_get ('Link 3');

		$w =& $this->addWidget ('text', 'link3_text');
		$w->alt = intl_get ('Text');

		$w =& $this->addWidget ('text', 'link3_url');
		$w->alt = intl_get ('Link URL');

		$w =& $this->addWidget ('select', 'link3_requires');
		$w->alt = intl_get ('Requires');
		$w->setValues (array ('r' => intl_get ('Read Access'), 'rw' => intl_get ('Read/Write Access')));

		$w =& $this->addWidget ('select', 'link3_requires_resource');
		$w->alt = intl_get ('Requires resource');
		$w->setValues ($resources);

		$w =& $this->addWidget ('tab', 'tab5');
		$w->title = intl_get ('Field Types');

		foreach ($dbtable->info as $k => $field) {
			$w =& $this->addWidget ('section', 'field' . ($k + 1) . '_' . $field->Field . '_section');
			$w->title = $field->Field . ' ' . $field->Type;

			$w =& $this->addWidget ('select', 'field' . ($k + 1) . '_' . $field->Field . '_type');
			$w->alt = intl_get ('Field type');
			$w->setValues (myadm_widget_types ());
			$w->setDefault (myadm_default_type ($field));

			$w =& $this->addWidget ('text', 'field' . ($k + 1) . '_' . $field->Field . '_alt');
			$w->alt = intl_get ('Display name');
			$w->setDefault (ucwords (str_replace ('_', ' ', $field->Field)));

			$w =& $this->addWidget ('text', 'field' . ($k + 1) . '_' . $field->Field . '_default');
			$w->alt = intl_get ('Default value');
			$w->setDefault ($field->Default);

			$w =& $this->addWidget ('text', 'field' . ($k + 1) . '_' . $field->Field . '_values');
			$w->alt = intl_get ('Set values');
			$w->extra = 'size="60"';
			$w->prepend = 'eval: ';

			$w =& $this->addWidget ('select', 'field' . ($k + 1) . '_' . $field->Field . '_nullable');
			$w->alt = intl_get ('Value can be null');
			$w->setValues (array ('0' => intl_get ('No'), '1' => intl_get ('Yes')));
			if ($field->Null != 'NO') {
				$w->setDefault ('1');
			}

			$w =& $this->addWidget ('text', 'field' . ($k + 1) . '_' . $field->Field . '_extra');
			$w->alt = intl_get ('Extra attributes');
			$w->extra = 'size="40"';

			$w =& $this->addWidget ('textarea', 'field' . ($k + 1) . '_' . $field->Field . '_rules');
			$w->alt = intl_get ('Validation rules');
			$w->labelPosition = 'left';
			$w->cols = 65;
			$w->rows = 3;
		}

		$w =& $this->addWidget ('tab', 'tab-end');

		$w =& $this->addWidget ('msubmit', 'submit_button');
		$b =& $w->getButton ();
		$b->setValues (intl_get ('Create'));
		$b =& $w->addButton ('submit_button', intl_get ('Cancel'));
		$b->extra = 'onclick="history.back (); return false"';

		page_title (intl_get ('Create a Collection for') . ' "' . $cgi->table . '"');

		$this->setValues ($cgi);
	}

	function onSubmit ($vals) {
		$table = $vals['table'];
		unset ($vals['table']);
		unset ($vals['tab1']);
		unset ($vals['tab2']);
		unset ($vals['tab3']);
		unset ($vals['tab4']);
		unset ($vals['tab5']);
		unset ($vals['tab-end']);
		unset ($vals['submit_button']);
		unset ($vals['section1']);
		unset ($vals['col1']);
		unset ($vals['col2']);
		unset ($vals['col3']);
		unset ($vals['col4']);
		unset ($vals['col5']);
		unset ($vals['col6']);
		unset ($vals['param1']);
		unset ($vals['param2']);
		unset ($vals['param3']);
		unset ($vals['tab4_help']);
		unset ($vals['link1']);
		unset ($vals['link2']);
		unset ($vals['link3']);

		$collection = array ('table' => $table, 'php_open' => '?php');
		$browse = array ();
		$params = array ();
		$links = array ();
		$hints = array ();
		foreach ($vals as $k => $v) {
			if (preg_match ('/^collection_(.+)$/', $k, $regs)) {
				$collection[$regs[1]] = $v;
				unset ($vals[$k]);
			} elseif (preg_match ('/^field[0-9]+_(.+)_section$/', $k)) {
				unset ($vals[$k]);
			} elseif (preg_match ('/^browse([0-9]+)$/', $k, $regs)) {
				unset ($vals[$k]);
				$browse[$regs[1]] = array ('field' => $v);
			} elseif (preg_match ('/^browse([0-9]+)_(.+)$/', $k, $regs)) {
				unset ($vals[$k]);
				$browse[$regs[1]][$regs[2]] = $v;
			} elseif (preg_match ('/^param([0-9]+)_(.+)$/', $k, $regs)) {
				unset ($vals[$k]);
				if (! is_array ($params[$regs[1]])) {
					$params[$regs[1]] = array ();
				}
				$params[$regs[1]][$regs[2]] = $v;
			} elseif (preg_match ('/^link([0-9]+)_(.+)$/', $k, $regs)) {
				unset ($vals[$k]);
				if (! is_array ($links[$regs[1]])) {
					$links[$regs[1]] = array ();
				}
				$links[$regs[1]][$regs[2]] = $v;
			} elseif (preg_match ('/^field([0-9]+)_(.+)_([^_]+)$/', $k, $regs)) {
				unset ($vals[$k]);
				if (! is_array ($hints[$regs[1]])) {
					$hints[$regs[1]] = array ('name' => $regs[2]);
				}
				if ($regs[3] == 'extra') {
					$v = str_replace ('"', '`', $v);
				} elseif ($regs[3] == 'rules') {
					$v = str_replace ('"', '`', $v);
					$v = preg_split ("/[\r\n]+/", $v, -1, PREG_SPLIT_NO_EMPTY);
				}
				$hints[$regs[1]][$regs[3]] = $v;
			}
		}
		$collection['browse'] = $browse;
		$collection['params'] = $params;
		$collection['links'] = $links;
		$collection['hints'] = $hints;

		$conf = template_simple ('collection_template.spt', $collection);

		// create version table if missing and is_versioned = true
		if ($collection['is_versioned'] == '1') {
			$tables = db_shift_array ('show tables');
			if (! in_array ($table . '_sv', $tables)) {
				// create the _sv table now
				$fields = db_fetch_array ('describe ' . $table);
				$create = 'create table ' . $table . "_sv (\n";
				$create .= "\tsv_autoid int(11) NOT NULL auto_increment primary key,\n";
				$create .= "\tsv_author varchar(48) NOT NULL default '',\n";
				$create .= "\tsv_action enum('created','modified','republished','replaced','restored','deleted','updated') NOT NULL default 'created',\n";
				$create .= "\tsv_revision datetime NOT NULL,\n";
				$create .= "\tsv_changelog text NOT NULL,\n";
				$create .= "\tsv_deleted enum('yes','no') NOT NULL default 'no',\n";
				$create .= "\tsv_current enum('yes','no') NOT NULL default 'yes',\n";
				$index = array ();
				$pkey_field = false;
				foreach ($fields as $k => $field) {
					$arr = (array) $field;
					$name = array_shift ($arr);
					$type = array_shift ($arr);
					$null = array_shift ($arr);
					$key = array_shift ($arr);
					$default = array_shift ($arr);
					$extra = array_shift ($arr);
		
					$create .= "\t" . $name . ' ' . $type;
					if ($null != 'YES') {
						$create .= ' not null';
					}
					if (! empty ($default)) {
						$create .= ' default "' . $default . '"';
					}
					if (! empty ($extra) && $extra != 'auto_increment') {
						// skip the auto_increment for _sv table
						$create .= ' ' . $extra;
					}
					switch ($key) {
						case 'PRI':
							//$create .= ' primary key'; // skip pkey for _sv table
							$pkey_field = $name;
							break;
						case 'MUL':
							$index[] = $name;
							break;
						default:
							break;
					}
					if ($k < count ($fields) - 1 || count ($index) > 0) {
						$create .= ',';
					}
					$create .= "\n";
				}
				//if (count ($index) > 0) {
				//	$create .= "\tindex (" . join (', ', $index) . ")\n";
				//}
				$create .= "\tindex (sv_author, sv_action, sv_revision, sv_deleted, sv_current),\n";
				$create .= "\tindex (" . $pkey_field . ")\n";
				$create .= ");\n\n";
				//echo $create;
				db_execute ($create);
			}
		}

		if (! @file_exists ('inc/app/cms/conf/collections/' . $table . '.php') && @is_writeable ('inc/app/cms/conf/collections')) {
			if (@file_put_contents ('inc/app/cms/conf/collections/' . $table . '.php', $conf)) {
				@chmod ('inc/app/cms/conf/collections/' . $table . '.php', 0777);
				session_set ('sitellite_alert', intl_get ('Your collection has been created.'));
				header ('Location: ' . site_prefix () . '/index/cms-browse-action?collection=' . $table);
				exit;
			}
		}

		page_title (intl_get ('Error'));
		echo '<p>' . intl_get ('The collection file was not able to be created. Please check your server folder permissions and try again.') . '</p>';
		echo '<p><a href="#" onclick="history.go (-1); return false">' . intl_get ('Back') . '</a></p>';
	}
}

$form = new MyadmCollectionForm;
$form->context = $box['context'];
echo $form->run ();

?>