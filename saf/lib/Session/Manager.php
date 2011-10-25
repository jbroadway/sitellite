<?php

loader_import ('saf.File');

/**
 * @package Session
 */
class SessionManager {
	function SessionManager () {
		$this->user = new SessionManager_User;
		$this->role = new SessionManager_Role;
		$this->team = new SessionManager_Team;
		$this->resource = new SessionManager_Resource;
		$this->access = new SessionManager_Access;
		$this->status = new SessionManager_Status;
		$this->pref = new SessionManager_Pref;
	}
}

/**
 * @package Session
 */
class SessionManager_User {
	/* Format: Dependent on Session Source driver */

	/**
	 * Number of users.
	 */
	var $total;

	/**
	 * Error message, if an error occurs.
	 */
	var $error;

	/**
	 * Returns a list of users.
	 *
	 * @return array
	 */
	function getList ($offset = false, $limit = false, $order, $ascdesc, $role = false, $team = false, $name = false, $disabled = false, $public = false, $teams = false) {
		$res = session_user_get_list ($offset, $limit, $order, $ascdesc, $role, $team, $name, $disabled, $public, $teams);
		if (! $res) {
			$this->error = session_user_error ();
			return false;
		}
		$this->total = session_user_total ();
		return $res;
	}

	/**
	 * Adds a user to the system.
	 *
	 * @param array hash
	 * @return boolean
	 */
	function add ($data) {
		$res = session_user_add ($data);
		if (! $res) {
			$this->error = session_user_error ();
		}
		return $res;
	}

	/**
	 * Modifies a user in the system.
	 *
	 * @param string
	 * @param array hash
	 * @return boolean
	 */
	function edit ($user, $data) {
		$res = session_user_edit ($user, $data);
		if (! $res) {
			$this->error = session_user_error ();
		}
		return $res;
	}

	/**
	 * Deletes a user from the system.
	 *
	 * @param string
	 * @return boolean
	 */
	function delete ($user) {
		$res = session_user_delete ($user);
		if (! $res) {
			$this->error = session_user_error ();
		}
		return $res;
	}

	/**
	 * Generate a form for adding items to this list.
	 *
	 * @return object saf.MailForm object
	 */
	function &getAddForm () {
		loader_import ('saf.MailForm');

		$form = new MailForm;
		$form->action = site_prefix () . '/index/usradm-add-user-action';
		$form->error_mode = 'all';



		loader_import ('ext.phpsniff');

		$sniffer = new phpSniff;
		$form->_browser = $sniffer->property ('browser');



		$form->addWidget ('hidden', '_list');



		page_add_script ('
			formhelp_prepend = \'<table border="0" cellpadding="0"><tr><td width="12" valign="top"><img src="' . site_prefix () . '/inc/app/cms/pix/arrow-10px.gif" alt="" border="0" /></td><td valign="top">\';
			formhelp_append = \'</td></tr></table>\';
		');



		$w =&  $form->addWidget ('tab', 'tab1');
		$w->title = intl_get ('Account');



		//$w =& $form->addWidget ('section', 'section1');
		//$w->title = intl_get ('Basic Information (Required)');



		$w =& $form->addWidget ('text', '_username');
		$w->alt = intl_get ('Username');
		$w->addRule ('not empty', intl_get ('Username must not be empty.'));
		$w->addRule ('unique "sitellite_user/username"', intl_get ('Username already in use.'));
		$w->extra = 'maxlength="48"';



		$w =& $form->addWidget ('password', 'passwd');
		$w->alt = intl_get ('Password');
		$w->addRule ('not empty', intl_get ('Password must not be empty.'));
		$w->ignoreEmpty = false;



		$w =& $form->addWidget ('password', 'password_verify');
		$w->alt = intl_get ('Verify Password');
		$w->addRule ('equals "passwd"', intl_get ('Passwords do not match.'));
		$w->ignoreEmpty = false;



		$w =& $form->addWidget ('text', 'firstname');
		$w->alt = intl_get ('First Name');
		//$w->addRule ('not empty', intl_get ('First name must not be empty.'));
		$w->extra = 'maxlength="32"';



		$w =& $form->addWidget ('text', 'lastname');
		$w->alt = intl_get ('Last Name');
		//$w->addRule ('not empty', intl_get ('Last name must not be empty.'));
		$w->extra = 'maxlength="32"';



		$w =& $form->addWidget ('text', 'email');
		$w->alt = intl_get ('Email');
		//$w->addRule ('not empty', intl_get ('Email must not be empty.'));
		//$w->addRule ('contains "@"', intl_get ('Email does not appear to be valid.'));
		$w->extra = 'maxlength="42"';



		$snm =& session_get_manager ();



		$list = assocify (array_keys ($snm->role->getList ()));
		unset ($list['anonymous']);
		unset ($list['']);
		$w =& $form->addWidget ('select', 'role');
		$w->alt = intl_get ('Role');
		$w->setValues ($list);
		$w->extra = 'id="role"';



		$w =& $form->addWidget ('select', 'team');
		$w->alt = intl_get ('Team');
		$w->setValues (assocify (array_keys ($snm->team->getList ())));
		$w->extra = 'id="team"';



		$w =& $form->addWidget ('select', 'disabled');
		$w->alt = intl_get ('Disabled');
		$w->setValues (array ('yes' => 'Yes', 'no' => 'No'));
		$w->setValue ('no');
		$w->extra = 'id="disabled"';



		$w =& $form->addWidget ('select', 'public');
		$w->alt = intl_get ('Public');
		$w->setValues (array ('yes' => 'Yes', 'no' => 'No'));
		$w->setValue ('no');
		$w->extra = 'id="public"';



		$w =& $form->addWidget ('textarea', 'profile');
		$w->alt = intl_get ('Profile');
		$w->labelPosition = 'left';
		$w->rows = 5;
		$w->extra = 'id="profile"';



		$w =& $form->addWidget ('textarea', 'sig');
		$w->alt = intl_get ('Signature (for comments)');
		$w->labelPosition = 'left';
		$w->rows = 3;
		$w->extra = 'id="sig"';



		$w =&  $form->addWidget ('tab', 'tab2');
		$w->title = intl_get ('Contact');



		//$w =& $form->addWidget ('section', 'section2');
		//$w->title = intl_get ('Extended Information (Optional)');



		$w =& $form->addWidget ('text', 'company');
		$w->alt = intl_get ('Company');
		$w->extra = 'maxlength="48"';



		$w =& $form->addWidget ('text', 'position');
		$w->alt = intl_get ('Position');
		$w->extra = 'maxlength="48"';



		$w =& $form->addWidget ('text', 'website');
		$w->alt = intl_get ('Web Site');
		$w->setValue ('http://');
		$w->extra = 'maxlength="72"';



		$w =& $form->addWidget ('text', 'phone');
		$w->alt = intl_get ('Phone #');
		$w->extra = 'maxlength="24"';



		$w =& $form->addWidget ('text', 'cell');
		$w->alt = intl_get ('Cell #');
		$w->extra = 'maxlength="24"';



		$w =& $form->addWidget ('text', 'fax');
		$w->alt = intl_get ('Fax #');
		$w->extra = 'maxlength="24"';



		$w =& $form->addWidget ('text', 'sms_address');
		$w->alt = intl_get ('SMS #');
		$w->extra = 'maxlength="72"';



		$w =& $form->addWidget ('text', 'jabber_id');
		$w->alt = intl_get ('Jabber ID');
		$w->extra = 'maxlength="48"';



		$w =& $form->addWidget ('text', 'address1');
		$w->alt = intl_get ('Address');
		$w->extra = 'maxlength="72"';



		$w =& $form->addWidget ('text', 'address2');
		$w->alt = intl_get ('Address Line 2');
		$w->extra = 'maxlength="72"';



		$w =& $form->addWidget ('text', 'city');
		$w->alt = intl_get ('City');
		$w->extra = 'maxlength="48"';



		$w =& $form->addWidget ('text', 'province');
		$w->alt = intl_get ('Province/State');
		$w->extra = 'maxlength="48"';



		$w =& $form->addWidget ('text', 'postal_code');
		$w->alt = intl_get ('Postal/Zip Code');
		$w->extra = 'maxlength="16"';



		$w =& $form->addWidget ('text', 'country');
		$w->alt = intl_get ('Country');
		$w->extra = 'maxlength="48"';



		$w =&  $form->addWidget ('tab', 'tab3');
		$w->title = intl_get ('Access');



		$w =& $form->addWidget ('usradm.Widget.Allowedbox', 'teams');
		$w->alt = 'Allowed Teams';

		$w->headers[] = '&nbsp;';
		$w->headers[] = intl_get ('Read');
		$w->headers[] = intl_get ('Write');

		$b =& $w->addButton ('all', array ('r' => '', 'w' => ''));
		$b->alt = '<strong>All</strong>';
		$b->setValue (array ('r', 'w'));
		$b->extra = 'class="teams" onclick="teams_select_all (this)"';

		foreach (session_get_teams () as $value) {
			$b =& $w->addButton ($value, array ('r' => '', 'w' => ''));
			$b->alt = ucwords (str_replace ('_', ' ', $value));
			$b->extra = 'class="teams"';
		}



		$w =&  $form->addWidget ('tab', 'tab-end');



		$w =& $form->addWidget ('msubmit', 'submit_button');

		$b =& $w->getButton ();
		$b->setValues (intl_get ('Save'));

		$b =& $w->addButton ('cancel_button');
		$b->setValues (intl_get ('Cancel'));
		$b->extra = 'onclick="window.location.href = \'' . site_prefix () . '/index/usradm-browse-action?list=users\'; return false"';



		return $form;
	}

	/**
	 * Generate a form for editing items in this list.
	 *
	 * @return object saf.MailForm object
	 */
	function &getEditForm ($item) {
		loader_import ('saf.MailForm');

		$form = new MailForm;
		$form->action = site_prefix () . '/index/usradm-edit-user-action';
		$form->error_mode = 'all';



		loader_import ('ext.phpsniff');

		$sniffer = new phpSniff;
		$form->_browser = $sniffer->property ('browser');



		$user = session_user_get ($item);



		$form->addWidget ('hidden', '_list');



		page_add_script ('
			formhelp_prepend = \'<table border="0" cellpadding="0"><tr><td width="12" valign="top"><img src="' . site_prefix () . '/inc/app/cms/pix/arrow-10px.gif" alt="" border="0" /></td><td valign="top">\';
			formhelp_append = \'</td></tr></table>\';
		');



		$w =&  $form->addWidget ('tab', 'tab1');
		$w->title = intl_get ('Account');



		$w =& $form->addWidget ('info', '_key');
		$w->alt = intl_get ('Username');
		$w->extra = 'maxlength="48"';



		$w =& $form->addWidget ('password', 'passwd');
		$w->alt = intl_get ('Password');



		$w =& $form->addWidget ('password', 'password_verify');
		$w->alt = intl_get ('Verify Password');
		$w->addRule ('equals "passwd"', intl_get ('Passwords do not match.'));
		$w->ignoreEmpty = false;



		$w =& $form->addWidget ('text', 'firstname');
		$w->alt = intl_get ('First Name');
		//$w->addRule ('not empty', intl_get ('First name must not be empty.'));
		$w->setValue ($user->firstname);
		$w->extra = 'maxlength="32"';



		$w =& $form->addWidget ('text', 'lastname');
		$w->alt = intl_get ('Last Name');
		//$w->addRule ('not empty', intl_get ('Last name must not be empty.'));
		$w->setValue ($user->lastname);
		$w->extra = 'maxlength="32"';



		$w =& $form->addWidget ('text', 'email');
		$w->alt = intl_get ('Email');
		//$w->addRule ('not empty', intl_get ('Email must not be empty.'));
		//$w->addRule ('contains "@"', intl_get ('Email does not appear to be valid.'));
		$w->setValue ($user->email);
		$w->extra = 'maxlength="42"';



		$snm =& session_get_manager ();



		$list = assocify (array_keys ($snm->role->getList ()));
		unset ($list['anonymous']);
		unset ($list['']);
		$w =& $form->addWidget ('select', 'role');
		$w->alt = intl_get ('Role');
		$w->setValues ($list);
		$w->setValue ($user->role);
		$w->extra = 'id="role"';



		$w =& $form->addWidget ('select', 'team');
		$w->alt = intl_get ('Team');
		$w->setValues (assocify (array_keys ($snm->team->getList ())));
		$w->setValue ($user->team);
		$w->extra = 'id="team"';



		$w =& $form->addWidget ('select', 'disabled');
		$w->alt = intl_get ('Disabled');
		$w->setValues (array ('yes' => 'Yes', 'no' => 'No'));
		$w->setValue ($user->disabled);
		$w->extra = 'id="disabled"';



		$w =& $form->addWidget ('select', 'public');
		$w->alt = intl_get ('Public');
		$w->setValues (array ('yes' => 'Yes', 'no' => 'No'));
		$w->setValue ($user->public);
		$w->extra = 'id="public"';



		$w =& $form->addWidget ('textarea', 'profile');
		$w->alt = intl_get ('Profile');
		$w->setValue ($user->profile);
		$w->labelPosition = 'left';
		$w->rows = 5;
		$w->extra = 'id="profile"';



		$w =& $form->addWidget ('textarea', 'sig');
		$w->alt = intl_get ('Signature (for comments)');
		$w->setValue ($user->sig);
		$w->labelPosition = 'left';
		$w->rows = 3;
		$w->extra = 'id="sig"';



		$w =& $form->addWidget ('info', 'registered');
		$w->alt = intl_get ('Date Registered');
		$w->setValue (loader_call ('saf.Date', 'Date::timestamp', $user->registered, 'F jS, Y - g:i A'));



		$w =& $form->addWidget ('info', 'modified');
		$w->alt = intl_get ('Date Last Modified');
		$w->setValue (loader_call ('saf.Date', 'Date::timestamp', $user->modified, 'F jS, Y - g:i A'));



		$w =&  $form->addWidget ('tab', 'tab2');
		$w->title = intl_get ('Contact');



		$w =& $form->addWidget ('text', 'company');
		$w->alt = intl_get ('Company');
		$w->setValue ($user->company);
		$w->extra = 'maxlength="48"';



		$w =& $form->addWidget ('text', 'position');
		$w->alt = intl_get ('Position');
		$w->setValue ($user->position);
		$w->extra = 'maxlength="48"';



		$w =& $form->addWidget ('text', 'website');
		$w->alt = intl_get ('Web Site');
		if (! empty ($user->website)) {
			$w->setValue ($user->website);
		} else {
			$w->setValue ('http://');
		}
		$w->extra = 'maxlength="72"';



		$w =& $form->addWidget ('text', 'phone');
		$w->alt = intl_get ('Phone #');
		$w->setValue ($user->phone);
		$w->extra = 'maxlength="24"';



		$w =& $form->addWidget ('text', 'cell');
		$w->alt = intl_get ('Cell #');
		$w->setValue ($user->cell);
		$w->extra = 'maxlength="24"';



		$w =& $form->addWidget ('text', 'fax');
		$w->alt = intl_get ('Fax #');
		$w->setValue ($user->fax);
		$w->extra = 'maxlength="24"';



		$w =& $form->addWidget ('text', 'sms_address');
		$w->alt = intl_get ('SMS #');
		$w->setValue ($user->sms_address);
		$w->extra = 'maxlength="72"';



		$w =& $form->addWidget ('text', 'jabber_id');
		$w->alt = intl_get ('Jabber ID');
		$w->setValue ($user->jabber_id);
		$w->extra = 'maxlength="48"';



		$w =& $form->addWidget ('text', 'address1');
		$w->alt = intl_get ('Address');
		$w->setValue ($user->address1);
		$w->extra = 'maxlength="72"';



		$w =& $form->addWidget ('text', 'address2');
		$w->alt = intl_get ('Address Line 2');
		$w->setValue ($user->address2);
		$w->extra = 'maxlength="72"';



		$w =& $form->addWidget ('text', 'city');
		$w->alt = intl_get ('City');
		$w->setValue ($user->city);
		$w->extra = 'maxlength="48"';



		$w =& $form->addWidget ('text', 'province');
		$w->alt = intl_get ('Province/State');
		$w->setValue ($user->province);
		$w->extra = 'maxlength="48"';



		$w =& $form->addWidget ('text', 'postal_code');
		$w->alt = intl_get ('Postal/Zip Code');
		$w->setValue ($user->postal_code);
		$w->extra = 'maxlength="16"';



		$w =& $form->addWidget ('text', 'country');
		$w->alt = intl_get ('Country');
		$w->setValue ($user->country);
		$w->extra = 'maxlength="48"';



		$w =&  $form->addWidget ('tab', 'tab3');
		$w->title = intl_get ('Access');



		$w =& $form->addWidget ('usradm.Widget.Allowedbox', 'teams');
		$w->alt = 'Allowed Teams';

		$w->headers[] = '&nbsp;';
		$w->headers[] = intl_get ('Read');
		$w->headers[] = intl_get ('Write');

		$b =& $w->addButton ('all', array ('r' => '', 'w' => ''));
		$b->alt = '<strong>All</strong>';
		$b->extra = 'class="teams" onclick="teams_select_all (this)"';

		$teams = unserialize ($user->teams);
		if (isset ($teams['all'])) {
			$b->setValue (assocify (preg_split ('//', $teams['all'], -1, PREG_SPLIT_NO_EMPTY)));
		}
		foreach (session_get_teams () as $value) {
			$b =& $w->addButton ($value, array ('r' => '', 'w' => ''));
			$b->alt = ucwords (str_replace ('_', ' ', $value));
			$b->setValue (assocify (preg_split ('//', $teams[$value], -1, PREG_SPLIT_NO_EMPTY)));
			$b->extra = 'class="teams"';
		}



		$w =&  $form->addWidget ('tab', 'tab-end');



		$w =& $form->addWidget ('msubmit', 'submit_button');

		$b =& $w->getButton ();
		$b->setValues (intl_get ('Save'));

		$b =& $w->addButton ('cancel_button');
		$b->setValues (intl_get ('Cancel'));
		$b->extra = 'onclick="window.location.href = \'' . site_prefix () . '/index/usradm-browse-action?list=users\'; return false"';



		return $form;
	}
}

/**
 * @package Session
 */
class SessionManager_Role { // Lives in inc/conf/auth/roles/${name}.php
	/* INI Format:
	 * [role]
	 * name = master
	 * admin = yes
	 * disabled = no
	 * [allow:resources]
	 * all = rw
	 * [allow:access]
	 * all = rw
	 * [allow:status]
	 * all = rw
	 */

	/**
	 * Directory to store info.
	 */
	var $dir = 'inc/conf/auth/roles';

	/**
	 * Parsed data from file.
	 */
	var $data = array ();

	/**
	 * Error message, if an error occurs.
	 */
	var $error;

	/**
	 * Constructor method.
	 */
	function SessionManager_Role () {
		loader_import ('saf.File.Directory');
		$this->getData ();
	}

	/**
	 * Retrieves the data from  and stores it in $data.
	 */
	function getData () {
		foreach (Dir::find ('*.php', $this->dir, false) as $file) {
			if (strpos ($file, '.') === 0) {
				continue;
			}
			$inidata = ini_parse ($file, true);
			$this->data[$inidata['role']['name']] = $inidata;
		}
	}

	/**
	 * Returns an array of the data.
	 *
	 * @return array
	 */
	function getList () {
		return $this->data;
	}

	/**
	 * Adds an item to $data and rewrites the INI file.
	 *
	 * @param string
	 * @param boolean
	 * @return boolean
	 */
	function add ($name, $data) {
		if (strstr ($name, '..')) {
			$this->error = 'Invalid role name!';
			return false;
		}

		$this->data[$name] = $data;
		$r = file_overwrite ($this->dir . '/' . $name . '.php', ini_write ($this->data[$name]));
		if (! $r) {
			$this->error = 'Failed to write INI file!';
		}
		return $r;
	}

	/**
	 * Renames an item in $data and rewrites the INI file.
	 *
	 * @param string
	 * @param string
	 * @param array hash
	 * @return boolean
	 */
	function edit ($name, $newname, $data) {
		if (strstr ($name, '..')) {
			$this->error = 'Invalid role name!';
			return false;
		}
		if (strstr ($newname, '..')) {
			$this->error = 'Invalid role name!';
			return false;
		}

		unset ($this->data[$name]);
		$r = unlink ($this->dir . '/' . $name . '.php');
		if (! $r) {
			$this->error = 'Failed to remove INI file!';
		}

		$this->data[$newname] = $data;
		$r = file_overwrite ($this->dir . '/' . $newname . '.php', ini_write ($this->data[$newname]));
		if (! $r) {
			$this->error = 'Failed to write INI file!';
		}
		return $r;
	}

	/**
	 * Deletes an item from $data and rewrites the INI file.
	 *
	 * @param string
	 * @return boolean
	 */
	function delete ($name) {
		if (strstr ($name, '..')) {
			$this->error = 'Invalid role name!';
			return false;
		}

		unset ($this->data[$name]);
		$r = unlink ($this->dir . '/' . $name . '.php');
		if (! $r) {
			$this->error = 'Failed to remove INI file!';
		}
		return $r;
	}

	/**
	 * Generate a form for adding items to this list.
	 *
	 * @return object saf.MailForm object
	 */
	function &getAddForm () {
		loader_import ('saf.MailForm');

		$form = new MailForm;
		$form->action = site_prefix () . '/index/usradm-add-role-action';
		$form->error_mode = 'all';



		$form->addWidget ('hidden', '_list');



		$w =& $form->addWidget ('tab', 'tab1');
		$w->title = intl_get ('Edit');



		$w =& $form->addWidget ('text', 'name');
		$w->alt = intl_get ('Name');
		$w->addRule ('not empty', intl_get ('Role name must not be empty.'));
		$w->addRule ('regex "^[-a-zA-Z0-9_]+$"', intl_get ('Role same should only contains alphanumeric characters or underscores.'));
		$w->extra = 'maxlength="48"';



		$w =& $form->addWidget ('select', 'admin');
		$w->alt = intl_get ('Is admin?');
		$w->setValues (array ('yes' => 'Yes', 'no' => 'No'));
		$w->setValue ('no');
		$w->extra = 'id="admin"';



		$w =& $form->addWidget ('select', 'disabled');
		$w->alt = intl_get ('Disabled');
		$w->setValues (array ('yes' => 'Yes', 'no' => 'No'));
		$w->setValue ('no');
		$w->extra = 'id="disabled"';



		$w =& $form->addWidget ('tab', 'tab2');
		$w->title = intl_get ('Resources');



		$w =& $form->addWidget ('usradm.Widget.Allowedbox', 'resources');
		$w->alt = 'Allowed Resources';

		$w->headers[] = '&nbsp;';
		$w->headers[] = intl_get ('Read');
		$w->headers[] = intl_get ('Write');

		$b =& $w->addButton ('all', array ('r' => '', 'w' => ''));
		$b->alt = '<strong>All</strong>';
		$b->extra = 'class="resources" onclick="resources_select_all (this)"';

		loader_import ('usradm.Functions');

		$resources = array ();

		foreach (session_get_resources () as $value) {
			$resources[$value] = usradm_resource_name ($value);
		}

		asort ($resources);

		foreach ($resources as $key => $value) {
			$b =& $w->addButton ($key, array ('r' => '', 'w' => ''));
			$b->alt = $value;
			$b->extra = 'class="resources"';
		}



		$w =& $form->addWidget ('tab', 'tab3');
		$w->title = intl_get ('Access Levels');

		$w =& $form->addWidget ('usradm.Widget.Allowedbox', 'accesslevels');
		$w->alt = 'Allowed Access Levels';

		$w->headers[] = '&nbsp;';
		$w->headers[] = intl_get ('Read');
		$w->headers[] = intl_get ('Write');

		$b =& $w->addButton ('all', array ('r' => '', 'w' => ''));
		$b->alt = '<strong>All</strong>';
		$b->extra = 'class="access" onclick="access_select_all (this)"';

		foreach (session_get_access_levels () as $value) {
			$b =& $w->addButton ($value, array ('r' => '', 'w' => ''));
			$b->alt = ucwords (str_replace ('_', ' ', $value));
			$b->extra = 'class="access"';
		}



		$w =& $form->addWidget ('tab', 'tab4');
		$w->title = intl_get ('Statuses');



		$w =& $form->addWidget ('usradm.Widget.Allowedbox', 'statuses');
		$w->alt = 'Allowed Statuses';

		$w->headers[] = '&nbsp;';
		$w->headers[] = intl_get ('Read');
		$w->headers[] = intl_get ('Write');

		$b =& $w->addButton ('all', array ('r' => '', 'w' => ''));
		$b->alt = '<strong>All</strong>';
		$b->extra = 'class="status" onclick="status_select_all (this)"';

		foreach (session_get_statuses () as $value) {
			$b =& $w->addButton ($value, array ('r' => '', 'w' => ''));
			$b->alt = ucwords (str_replace ('_', ' ', $value));
			$b->extra = 'class="status"';
		}



		$w =& $form->addWidget ('tab', 'tab-end');



		$w =& $form->addWidget ('msubmit', 'submit_button');

		$b =& $w->getButton ();
		$b->setValues (intl_get ('Save'));

		$b =& $w->addButton ('cancel_button');
		$b->setValues (intl_get ('Cancel'));
		$b->extra = 'onclick="window.location.href = \'' . site_prefix () . '/index/usradm-browse-action?list=roles\'; return false"';

		return $form;
	}

	/**
	 * Generate a form for editing items in this list.
	 *
	 * @return object saf.MailForm object
	 */
	function &getEditForm ($item) {
		loader_import ('saf.MailForm');

		$form = new MailForm;
		$form->action = site_prefix () . '/index/usradm-edit-role-action';
		$form->error_mode = 'all';



		$form->addWidget ('hidden', '_list');
		$form->addWidget ('hidden', '_key');



		$w =& $form->addWidget ('tab', 'tab1');
		$w->title = intl_get ('Edit');



		$w =& $form->addWidget ('text', 'name');
		$w->alt = intl_get ('Name');
		$w->addRule ('not empty', intl_get ('Role name must not be empty.'));
		$w->addRule ('regex "^[-a-zA-Z0-9_]+$"', intl_get ('Role same should only contains alphanumeric characters or underscores.'));
		$w->setValue ($item);
		$w->extra = 'maxlength="48"';



		$w =& $form->addWidget ('select', 'admin');
		$w->alt = intl_get ('Is admin?');
		$w->setValues (array ('yes' => 'Yes', 'no' => 'No'));
		if ($this->data[$item]['role']['admin']) {
			$w->setValue ('yes');
		} else {
			$w->setValue ('no');
		}
		$w->extra = 'id="admin"';



		$w =& $form->addWidget ('select', 'disabled');
		$w->alt = intl_get ('Disabled');
		$w->setValues (array ('yes' => 'Yes', 'no' => 'No'));
		$w->setValue ('no');
		if ($this->data[$item]['role']['disabled']) {
			$w->setValue ('yes');
		} else {
			$w->setValue ('no');
		}
		$w->extra = 'id="disabled"';



		$w =& $form->addWidget ('tab', 'tab2');
		$w->title = intl_get ('Resources');



		$w =& $form->addWidget ('usradm.Widget.Allowedbox', 'resources');
		$w->alt = 'Allowed Resources';

		$w->headers[] = '&nbsp;';
		$w->headers[] = intl_get ('Read');
		$w->headers[] = intl_get ('Write');

		$b =& $w->addButton ('all', array ('r' => '', 'w' => ''));
		$b->alt = '<strong>All</strong>';
		$b->setValue (assocify (preg_split ('//', $this->data[$item]['allow:resources']['all'], -1, PREG_SPLIT_NO_EMPTY)));
		$b->extra = 'class="resources" onclick="resources_select_all (this)"';

		loader_import ('usradm.Functions');

		$resources = array ();

		foreach (session_get_resources () as $value) {
			$resources[$value] = usradm_resource_name ($value);
		}

		asort ($resources);

		foreach ($resources as $key => $value) {
			$b =& $w->addButton ($key, array ('r' => '', 'w' => ''));
			$b->alt = $value;
			$b->setValue (assocify (preg_split ('//', $this->data[$item]['allow:resources'][$key], -1, PREG_SPLIT_NO_EMPTY)));
			$b->extra = 'class="resources"';
		}

		//foreach (session_get_resources () as $value) {
		//	$b =& $w->addButton ($value, array ('r' => '', 'w' => ''));
		//	$b->alt = ucwords (str_replace ('_', ' ', $value));
		//	$b->setValue (assocify (preg_split ('//', $this->data[$item]['allow:resources'][$value], -1, PREG_SPLIT_NO_EMPTY)));
		//}



		$w =& $form->addWidget ('tab', 'tab3');
		$w->title = intl_get ('Access Levels');



		$w =& $form->addWidget ('usradm.Widget.Allowedbox', 'accesslevels');
		$w->alt = 'Allowed Access Levels';

		$w->headers[] = '&nbsp;';
		$w->headers[] = intl_get ('Read');
		$w->headers[] = intl_get ('Write');

		$b =& $w->addButton ('all', array ('r' => '', 'w' => ''));
		$b->alt = '<strong>All</strong>';
		$b->setValue (assocify (preg_split ('//', $this->data[$item]['allow:access']['all'], -1, PREG_SPLIT_NO_EMPTY)));
		$b->extra = 'class="access" onclick="access_select_all (this)"';

		foreach (session_get_access_levels () as $value) {
			$b =& $w->addButton ($value, array ('r' => '', 'w' => ''));
			$b->alt = ucwords (str_replace ('_', ' ', $value));
			$b->setValue (assocify (preg_split ('//', $this->data[$item]['allow:access'][$value], -1, PREG_SPLIT_NO_EMPTY)));
			$b->extra = 'class="access"';
		}



		$w =& $form->addWidget ('tab', 'tab4');
		$w->title = intl_get ('Statuses');



		$w =& $form->addWidget ('usradm.Widget.Allowedbox', 'statuses');
		$w->alt = 'Allowed Statuses';

		$w->headers[] = '&nbsp;';
		$w->headers[] = intl_get ('Read');
		$w->headers[] = intl_get ('Write');

		$b =& $w->addButton ('all', array ('r' => '', 'w' => ''));
		$b->alt = '<strong>All</strong>';
		$b->setValue (assocify (preg_split ('//', $this->data[$item]['allow:status']['all'], -1, PREG_SPLIT_NO_EMPTY)));
		$b->extra = 'class="status" onclick="status_select_all (this)"';

		foreach (session_get_statuses () as $value) {
			$b =& $w->addButton ($value, array ('r' => '', 'w' => ''));
			$b->alt = ucwords (str_replace ('_', ' ', $value));
			$b->setValue (assocify (preg_split ('//', $this->data[$item]['allow:status'][$value], -1, PREG_SPLIT_NO_EMPTY)));
			$b->extra = 'class="status"';
		}



		$w =& $form->addWidget ('tab', 'tab-end');



		$w =& $form->addWidget ('msubmit', 'submit_button');

		$b =& $w->getButton ();
		$b->setValues (intl_get ('Save'));

		$b =& $w->addButton ('cancel_button');
		$b->setValues (intl_get ('Cancel'));
		$b->extra = 'onclick="window.location.href = \'' . site_prefix () . '/index/usradm-browse-action?list=roles\'; return false"';

		return $form;
	}
}

/**
 * @package Session
 */
class SessionManager_Pref { // Lives in inc/conf/auth/prefs/index.php
	/* INI Format:
	 * [pref_name]
	 * alt = Display Name
	 * instructions = Instructions for site admins
	 * type = mailform type
	 * value 1 = first value
	 * value 2 = second value
	 * default_value = first value
	 */

	/**
	 * File to store info.
	 */
	var $file = 'inc/conf/auth/preferences/index.php';

	/**
	 * Parsed data from file.
	 */
	var $data = array ();

	/**
	 * Error message, if an error occurs.
	 */
	var $error;

	/**
	 * Constructor method.
	 */
	function SessionManager_Pref () {
		$this->getData ();
	}

	/**
	 * Retrieves the data from $file and stores it in $data.
	 */
	function getData () {
		$this->data = ini_parse ($this->file, true);
	}

	/**
	 * Returns an array of the data.
	 *
	 * @return array
	 */
	function getList () {
		return $this->data;
	}

	/**
	 * Adds an item to $data and rewrites the INI file.
	 *
	 * @param string
	 * @param boolean
	 * @return boolean
	 */
	function add ($name, $data) {
		$this->data[$name] = $data;
		$r = file_overwrite ($this->file, ini_write ($this->data));
		if (! $r) {
			$this->error = 'Failed to write INI file!';
		}
		return $r;
	}

	/**
	 * Renames an item in $data and rewrites the INI file.
	 *
	 * @param string
	 * @param string
	 * @param array hash
	 * @return boolean
	 */
	function edit ($name, $newname, $data) {
		unset ($this->data[$name]);
		$this->data[$newname] = $data;
		$r = file_overwrite ($this->file, ini_write ($this->data));
		if (! $r) {
			$this->error = 'Failed to write INI file!';
		}
		return $r;
	}

	/**
	 * Deletes an item from $data and rewrites the INI file.
	 *
	 * @param string
	 * @return boolean
	 */
	function delete ($name) {
		unset ($this->data[$name]);
		$r = file_overwrite ($this->file, ini_write ($this->data));
		if (! $r) {
			$this->error = 'Failed to write INI file!';
		}
		return $r;
	}

	/*
	 * Show as form fields:
	 * pref_name => text
	 * alt => text
	 * type => always 'select' (don't show)
	 * value n => textarea (one-per-line)
	 * values => text (in lieu of "value n" enter a function name that returns a value array)
	 * default_value => text
	 */

	/**
	 * Generate a form for adding items to this list.
	 *
	 * @return object saf.MailForm object
	 */
	function &getAddForm () {
		loader_import ('saf.MailForm');

		$form = new MailForm;
		$form->action = site_prefix () . '/index/usradm-add-pref-action';
		$form->error_mode = 'all';

		$form->addWidget ('hidden', '_list');

		$w =& $form->addWidget ('text', 'pref_name');
		$w->alt = intl_get ('Preference Name');
		$w->addRule ('not empty', intl_get ('Preference name may not be empty.'));
		$w->extra = 'maxlength="32"';

		$w =& $form->addWidget ('text', 'alt');
		$w->alt = intl_get ('Display Name');
		$w->addRule ('not empty', intl_get ('Display name may not be empty.'));

		$w =& $form->addWidget ('text', 'instructions');
		$w->alt = intl_get ('Instructions');
		$w->addRule ('not empty', intl_get ('Instructions may not be empty.'));
		$w->extra = 'size="40"';

		$w =& $form->addWidget ('textarea', 'value_list');
		$w->alt = intl_get ('Values (one-per-line)');
		$w->rows = 3;
		$w->labelPosition = 'left';

		$w =& $form->addWidget ('text', 'values');
		$w->alt = intl_get ('Retrieve values from function');

		$w =& $form->addWidget ('text', 'default_value');
		$w->alt = intl_get ('Default Value');

		$w =& $form->addWidget ('msubmit', 'submit_button');

		$b =& $w->getButton ();
		$b->setValues (intl_get ('Save'));

		$b =& $w->addButton ('cancel_button');
		$b->setValues (intl_get ('Cancel'));
		$b->extra = 'onclick="window.location.href = \'' . site_prefix () . '/index/usradm-browse-action?list=prefs\'; return false"';

		return $form;
	}

	/**
	 * Generate a form for editing items in this list.
	 *
	 * @return object saf.MailForm object
	 */
	function &getEditForm ($item) {
		loader_import ('saf.MailForm');

		$form = new MailForm;
		$form->action = site_prefix () . '/index/usradm-edit-pref-action';
		$form->error_mode = 'all';

		$form->addWidget ('hidden', '_list');
		$form->addWidget ('hidden', '_key');

		$w =& $form->addWidget ('text', 'pref_name');
		$w->alt = intl_get ('Preference Name');
		$w->addRule ('not empty', intl_get ('Preference name may not be empty.'));
		$w->setValue ($item);
		$w->extra = 'maxlength="32"';

		$w =& $form->addWidget ('text', 'alt');
		$w->alt = intl_get ('Display Name');
		$w->addRule ('not empty', intl_get ('Display name may not be empty.'));
		$w->setValue ($this->data[$item]['alt']);

		$w =& $form->addWidget ('text', 'instructions');
		$w->alt = intl_get ('Instructions');
		$w->addRule ('not empty', intl_get ('Instructions may not be empty.'));
		$w->extra = 'size="40"';
		$w->setValue ($this->data[$item]['instructions']);

		$w =& $form->addWidget ('textarea', 'value_list');
		$w->alt = intl_get ('Values (one-per-line)');
		$w->rows = 3;
		$w->labelPosition = 'left';
		$vals = '';
		foreach ($this->data[$item] as $k => $v) {
			if (strpos ($k, 'value ') === 0) {
				if ($v === '1') {
					$vals .= 'on' . NEWLINE;
				} elseif (! $v) {
					$vals .= 'off' . NEWLINE;
				} else {
					$vals .= $v . NEWLINE;
				}
			}
		}
		$w->setValue ($vals);

		$w =& $form->addWidget ('text', 'values');
		$w->alt = intl_get ('Retrieve values from function');
		$w->setValue ($this->data[$item]['values']);

		$w =& $form->addWidget ('text', 'default_value');
		$w->alt = intl_get ('Default Value');
		$v = $this->data[$item]['default_value'];
		if ($v === '1') {
			$w->setValue ('on');
		} else {
			$w->setValue ($v);
		}

		$w =& $form->addWidget ('msubmit', 'submit_button');

		$b =& $w->getButton ();
		$b->setValues (intl_get ('Save'));

		$b =& $w->addButton ('cancel_button');
		$b->setValues (intl_get ('Cancel'));
		$b->extra = 'onclick="window.location.href = \'' . site_prefix () . '/index/usradm-browse-action?list=prefs\'; return false"';

		return $form;
	}
}

/**
 * @package Session
 */
class SessionManager_Team { // Lives in inc/conf/auth/teams/index.php
	/* INI Format:
	 * [core]
	 * disaabled = no
	 * description = "This is the default team."
	 */

	/**
	 * File to store info.
	 */
	var $file = 'inc/conf/auth/teams/index.php';

	/**
	 * Parsed data from file.
	 */
	var $data = array ();

	/**
	 * Error message, if an error occurs.
	 */
	var $error;

	/**
	 * Constructor method.
	 */
	function SessionManager_Team () {
		$this->getData ();
	}

	/**
	 * Retrieves the data from $file and stores it in $data.
	 */
	function getData () {
		$this->data = ini_parse ($this->file, true);
	}

	/**
	 * Returns an array of the data.
	 *
	 * @return array
	 */
	function getList () {
		return $this->data;
	}

	/**
	 * Adds an item to $data and rewrites the INI file.
	 *
	 * @param string
	 * @param boolean
	 * @param string
	 * @return boolean
	 */
	function add ($name, $disabled, $description) {
		$this->data[$name] = array ('disabled' => $disabled, 'description' => $description);
		$r = file_overwrite ($this->file, ini_write ($this->data));
		if (! $r) {
			$this->error = 'Failed to write INI file!';
		}
		return $r;
	}

	/**
	 * Renames an item in $data and rewrites the INI file.
	 *
	 * @param string
	 * @param string
	 * @param boolean
	 * @param string
	 * @return boolean
	 */
	function edit ($name, $newname, $disabled, $description) {
		unset ($this->data[$name]);
		$this->data[$newname] = array ('disabled' => $disabled, 'description' => $description);
		$r = file_overwrite ($this->file, ini_write ($this->data));
		if (! $r) {
			$this->error = 'Failed to write INI file!';
		}
		return $r;
	}

	/**
	 * Deletes an item from $data and rewrites the INI file.
	 *
	 * @param string
	 * @return boolean
	 */
	function delete ($name) {
		unset ($this->data[$name]);
		$r = file_overwrite ($this->file, ini_write ($this->data));
		if (! $r) {
			$this->error = 'Failed to write INI file!';
		}
		return $r;
	}

	/**
	 * Generate a form for adding items to this list.
	 *
	 * @return object saf.MailForm object
	 */
	function &getAddForm () {
		loader_import ('saf.MailForm');

		$form = new MailForm;
		$form->action = site_prefix () . '/index/usradm-add-team-action';
		$form->error_mode = 'all';

		$form->addWidget ('hidden', '_list');

		$w =& $form->addWidget ('text', 'name');
		$w->alt = intl_get ('Name');
		$w->addRule ('not empty', intl_get ('Team name may not be empty.'));
		$w->extra = 'maxlength="48"';

		$w =& $form->addWidget ('select', 'disabled');
		$w->alt = intl_get ('Disabled');
		$w->setValues (array ('yes' => intl_get ('Yes'), 'no' => intl_get ('No')));
		$w->setValue ('no');

		$w =& $form->addWidget ('text', 'description');
		$w->alt = intl_get ('Description');
		$w->addRule ('not empty', intl_get ('Description may not be empty.'));
		$w->extra = 'size="40"';

		$w =& $form->addWidget ('msubmit', 'submit_button');

		$b =& $w->getButton ();
		$b->setValues (intl_get ('Save'));

		$b =& $w->addButton ('cancel_button');
		$b->setValues (intl_get ('Cancel'));
		$b->extra = 'onclick="window.location.href = \'' . site_prefix () . '/index/usradm-browse-action?list=teams\'; return false"';

		return $form;
	}

	/**
	 * Generate a form for editing items in this list.
	 *
	 * @return object saf.MailForm object
	 */
	function &getEditForm ($item) {
		loader_import ('saf.MailForm');

		$form = new MailForm;
		$form->action = site_prefix () . '/index/usradm-edit-team-action';
		$form->error_mode = 'all';

		$form->addWidget ('hidden', '_list');
		$form->addWidget ('hidden', '_key');

		$w =& $form->addWidget ('info', 'name');
		$w->alt = intl_get ('Name');
		$w->addRule ('not empty', intl_get ('Team name may not be empty.'));
		$w->setValue ($item);
		$w->extra = 'maxlength="48"';

		$w =& $form->addWidget ('select', 'disabled');
		$w->alt = intl_get ('Disabled');
		$w->setValues (array ('yes' => intl_get ('Yes'), 'no' => intl_get ('No')));
		if (! $this->data[$item]['disabled']) {
			$w->setValue ('no');
		} else {
			$w->setValue ('yes');
		}

		$w =& $form->addWidget ('text', 'description');
		$w->alt = intl_get ('Description');
		$w->addRule ('not empty', intl_get ('Description may not be empty.'));
		$w->extra = 'size="40"';
		$w->setValue ($this->data[$item]['description']);

		$w =& $form->addWidget ('msubmit', 'submit_button');

		$b =& $w->getButton ();
		$b->setValues (intl_get ('Save'));

		$b =& $w->addButton ('cancel_button');
		$b->setValues (intl_get ('Cancel'));
		$b->extra = 'onclick="window.location.href = \'' . site_prefix () . '/index/usradm-browse-action?list=teams\'; return false"';

		return $form;
	}
}

/**
 * Handles INI files of the format:
 *
 * value1=
 * value2=
 * value3=
 *
 * @package Session
 */
class SessionManager_Simple {
	/**
	 * File to store info.
	 */
	var $file;

	/**
	 * Parsed data from file.
	 */
	var $data = array ();

	/**
	 * Error message, if an error occurs.
	 */
	var $error;

	/**
	 * Constructor method.
	 */
	function SessionManager_Simple () {
		$this->getData ();
	}

	/**
	 * Retrieves the data from $file and stores it in $data.
	 */
	function getData () {
		$this->data = ini_parse ($this->file, false);
	}

	/**
	 * Returns an array of the data.
	 *
	 * @return array
	 */
	function getList () {
		return array_keys ($this->data);
	}

	/**
	 * Adds an item to $data and rewrites the INI file.
	 *
	 * @param string
	 * @return boolean
	 */
	function add ($name) {
		$this->data[$name] = '';
		$r = file_overwrite ($this->file, ini_write ($this->data));
		if (! $r) {
			$this->error = 'Failed to write INI file!';
		}
		return $r;
	}

	/**
	 * Renames an item in $data and rewrites the INI file.
	 *
	 * @param string
	 * @param string
	 * @return boolean
	 */
	function edit ($name, $newname) {
		unset ($this->data[$name]);
		$this->data[$newname] = '';
		$r = file_overwrite ($this->file, ini_write ($this->data));
		if (! $r) {
			$this->error = 'Failed to write INI file!';
		}
		return $r;
	}

	/**
	 * Deletes an item from $data and rewrites the INI file.
	 *
	 * @param string
	 * @return boolean
	 */
	function delete ($name) {
		unset ($this->data[$name]);
		$r = file_overwrite ($this->file, ini_write ($this->data));
		if (! $r) {
			$this->error = 'Failed to write INI file!';
		}
		return $r;
	}
}

/**
 * @package Session
 */
class SessionManager_Status extends SessionManager_Simple { // Lives in inc/conf/auth/status/index.php
	/* INI Format:
	 * approved=
	 */

	/**
	 * File to store status info.
	 */
	var $file = 'inc/conf/auth/status/index.php';
}

/**
 * @package Session
 */
class SessionManager_Access extends SessionManager_Simple { // Lives in inc/conf/auth/access/index.php
	/* INI Format:
	 * public=
	 */

	/**
	 * File to store access info.
	 */
	var $file = 'inc/conf/auth/access/index.php';
}

/**
 * @package Session
 */
class SessionManager_Resource extends SessionManager_Simple { // Lives in inc/conf/auth/resources/index.php
	/* INI Format:
	 * documents=
	 */

	/**
	 * File to store resource info.
	 */
	var $file = 'inc/conf/auth/resources/index.php';
}

?>
