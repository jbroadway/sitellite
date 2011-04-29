<?php

/**
 * @package Session
 */
class SessionAcl {
	var $access = array (); // access levels from conf
	var $status = array (); // statuses from conf
	var $roles = array (); // roles from conf
	var $teams = array (); // teams from conf
	var $resources = array (); // resources from conf
	var $user = array (); // user info (name, role, team)
	var $prefs = array (); // preferences from conf
	var $_prefs = array (); // users preferences

	//var $resource = array (); // resource info (name, access, status) -- unused ???

	var $path; // path to conf files (default is inc/conf/auth)

	function SessionAcl ($user = false, $role = 'anonymous', $team = 'core', $teams = false) {
		if (! is_array ($teams)) {
			$teams = array ();
		}

		$this->user = array (
			'name' => $user,
			'role' => $role,
			'team' => $team,
			'teams' => $teams,
		);

		// this struct seems unused...
		// was this intended as a default to allowed() ???
		// or does it store the resource data from a previous allowed() call ???
		/*$this->resource = array (
			'name' => 'documents',
			'access' => 'public',
			'status' => 'approved',
		);*/

		$this->roles['anonymous'] = array (
			'name' => 'anonymous',
			'admin' => false,
			'disabled' => false,
			'allow' => array (
				'resources' => array (),
				'access' => array (
					'public' => 'r',
				),
				'status' => array (
					'approved' => 'r',
				),
			),
		);
	}

	// roles/access levels/statuses/teams/resources are all defined in the
	// config.  this way, it eases the burden of complexity from the driver
	// layer and underlying service (ldap, database, etc).  the user and role
	// are all that are required off the driver to perform fine-grained
	// access control.  the team is optional.
	function init ($path = 'inc/conf/auth') {
		$this->path = $path;
		$this->access = array_keys (parse_ini_file ($path . '/access/index.php'));
		$this->status = array_keys (parse_ini_file ($path . '/status/index.php'));
		$this->resources = array_keys (parse_ini_file ($path . '/resources/index.php'));
		$this->teams = parse_ini_file ($path . '/teams/index.php', true);
		$d = dir ($path . '/roles');
		while (false !== ($file = $d->read ())) {
			if (strpos ($file, '.') === 0 || is_dir ($path . '/roles/' . $file)) {
				continue;
			}
			$role = @parse_ini_file ($path . '/roles/' . $file, true);
			if (! $role) {
				continue;
			}
			$name = $role['role']['name'];
			$this->roles[$name] = $role['role'];
			$this->roles[$name]['allow'] = array ();
			foreach ($role as $section => $values) {
				if (preg_match ('/^allow:(.+)$/', $section, $regs)) {
					$this->roles[$name]['allow'][$regs[1]] = $values;
				}
				if (isset ($this->roles[$name]['allow']['status']['approved'])) {
					$this->roles[$name]['allow']['status']['parallel'] = $this->roles[$name]['allow']['status']['approved'];
				}
			}
		}

		$this->prefs = parse_ini_file ($path . '/preferences/index.php', true);
		foreach ($this->prefs as $key => $prefs) {
			$this->_prefs[$key] = $prefs['default_value'];
		}
	}

	// initializes user preferences
	function initPrefs () {
		if ($this->user['name'] && $this->isAdmin ()) {
			$res = db_fetch ('select * from sitellite_prefs where username = ?', $this->user['name']);
			if (! $res) {
				foreach ($this->prefs as $key => $prefs) {
					$this->_prefs[$key] = $prefs['default_value'];
				}
				return;
			} elseif (is_object ($res)) {
				$res = array ($res);
			}
			foreach ($res as $row) {
				$this->_prefs[$row->pref] = $row->value;
			}
		}
	}

	function verify ($userDisabled = false) {
		// user-level disabling is determined upon a driver-specific setting
		$ok = true;
		if ($userDisabled) {
			$ok = false;
		}

		// check here that the role in fact exists, if not then set it to 'anonymous'
		if (! is_array ($this->roles[$this->user['role']])) {
			$this->user['role'] = 'anonymous';
		}

		$role = $this->roles[$this->user['role']];
		$team = $this->teams[$this->user['team']];
		if ($role['disabled']) {
			$ok = false;
		}
		if ($team['disabled']) {
			$ok = false;
		}
		if (! $ok) {
			$this->user['role'] = 'anonymous';
			$this->user['team'] = 'core';
		}
		return $ok;
	}

	/**
	 * Specifies whether the user is allowed to access the requested
	 * resource.  $resource may be a string, or an object or associative array
	 * with the properties name, sitellite_access, sitellite_status, and
	 * optionally, sitellite_team.  Valid $access values are r, w, and rw
	 * (read, write, and read/write).  Valid $type values are resource,
	 * access, status, and team.
	 *
	 * @access	public
	 * @param	mixed	$resource
	 * @param	string	$access
	 * @param	string	$type
	 * @return	boolean
	 * 
	 */
	function allowed ($resource = 'documents', $access = 'rw', $type = 'resource') {
		if (is_object ($resource)) {
			$level = $resource->sitellite_access;
			$status = $resource->sitellite_status;
			$team = $resource->sitellite_team;
			$resource = $resource->name;
		} elseif (is_array ($resource)) {
			$level = $resource['sitellite_access'];
			$status = $resource['sitellite_status'];
			$team = $resource['sitellite_team'];
			$resource = $resource['name'];
		} elseif ($type == 'access') {
			$level = $resource;
			unset ($resource);
		} elseif ($type == 'status') {
			$status = $resource;
			unset ($resource);
		} elseif ($type == 'team') {
			$team = $resource;
			unset ($resource);
		}
		$role = $this->roles[$this->user['role']];

		if (! isset ($role['allow']['resources']['all'])) {
			$role['allow']['resources']['all'] = '';
		}
		if (! isset ($role['allow']['access']['all'])) {
			$role['allow']['access']['all'] = '';
		}
		if (! isset ($role['allow']['status']['all'])) {
			$role['allow']['status']['all'] = '';
		}
		if (! isset ($this->user['teams']['all'])) {
			$this->user['teams']['all'] = '';
		}

		if (isset ($resource) && ! $this->_test ($access, $role['allow']['resources'][$resource], $role['allow']['resources']['all'])) {
			// no good on resource
			return false;
		}
		if (isset ($level) && ! $this->_test ($access, $role['allow']['access'][$level], $role['allow']['access']['all'])) {
			// no good on access level
			return false;
		}
		if (isset ($status) && ! $this->_test ($access, $role['allow']['status'][$status], $role['allow']['status']['all'])) {
			// no good on status
			return false;
		}
		//info ($team, true);
		if (isset ($team) && ! empty ($team) && $team != session_team () && ! $this->_test ($access, $this->user['teams'][$team], $this->user['teams']['all'])) {
			// no good on team
			return false;
		}
		return true;
	}

	/**
	 * Checks the specified permissions against the resource value and the all value.
	 *
	 * @access	private
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	boolean
	 *
	 */
	function _test ($check, $value, $all) {
		switch ($check) {
			case 'r':
				if (strpos ($value, $check) !== false || strpos ($all, $check) !== false) {
					return true;
				}
				break;
			case 'w':
				if (strpos ($value, $check) !== false || strpos ($all, $check) !== false) {
					return true;
				}
				break;
			case 'rw':
				if (strpos ($value, $check) !== false || strpos ($all, $check) !== false) {
					return true;
				}
				if (($value == 'r' && $all == 'w') || ($value == 'w' && $all == 'r')) {
					return true;
				}
				break;
		}
		return false;
	}

	/**
	 * Returns a piece of SQL that can be slipped into the WHERE clause of
	 * a query to check for proper permissions.
	 * 
	 * @access	public
	 * @return	string
	 * 
	 */
	function allowedSql () {
		$role = $this->roles[$this->user['role']];

		$sql = '';
		$op = '';

		if (! isset ($role['allow']['status']['all'])) {
			$sql .= ' sitellite_status in("' . join ('", "', array_keys ($role['allow']['status'])) . '") ';
			$op = 'and';
		}
		if (! isset ($role['allow']['access']['all'])) {
			$sql .= $op . ' sitellite_access in("' . join ('", "', array_keys ($role['allow']['access'])) . '") ';
		}
		/*
		if (! isset ($this->user['teams']['all']) || empty ($this->user['teams']['all'])) {
			$teams = array_keys ($this->user['teams']);
			if (! in_array ($this->user['team'], $teams)) {
				$teams[] = $this->user['team'];
			}
			$sql .= $op . ' sitellite_team in("", "' . join ('", "', $teams) . '") ';
		}
		*/

		if (empty ($sql)) {
			// use this to avoid breaking the AND clause in sql with this output appended
			return ' 1 = 1 ';
		}

		return $sql;
	}

	/**
	 * Returns a piece of SQL that can be slipped into the WHERE clause of
	 * a query to check for proper permissions, but that only returns items
	 * with a status of "approved".  This being separated from allowedSql()
	 * allows you to display drafts in private (ie. administrative) lists
	 * of items in your code, but by using this method instead on public-facing
	 * pages, you can be sure that they will only see actually approved
	 * documents, and will still be granted access based on their access
	 * privileges.
	 *
	 * This method also ignores the sitellite_team value, which allowedSql()
	 * does not (providing editing restrictions based on teams).
	 * 
	 * @access	public
	 * @return	string
	 * 
	 */
	function approvedSql () {
		$role = $this->roles[$this->user['role']];

		$sql = '';
		$op = '';

		if (! isset ($role['allow']['status']['all'])) {
			$sql .= ' sitellite_status in ("' . join ('", "', array_keys ($role['allow']['status'])) . '") ';
			$op = 'and';
		}

		if (! isset ($role['allow']['access']['all'])) {
			$sql .= $op . ' sitellite_access in("' . join ('", "', array_keys ($role['allow']['access'])) . '") ';
		}

		return $sql;
	}

	/**
	 * Returns an array of allowed access levels for the current user.
	 * If the user is allowed to access all levels, this method returns
	 * an array containing a single item "all".
	 *
	 * @access	public
	 * @return	array
	 *
	 */
	function allowedAccessList () {
		$role = $this->roles[$this->user['role']];

		if (! isset ($role['allow']['access']['all'])) {
			return array_keys ($role['allow']['access']);
		}
		return array ('all');
	}

	/**
	 * Returns an array of allowed statuses for the current user. If the
	 * user is allowed to access all statuses, this method returns an array
	 * containing a single item "all".  If the user is not an admin user,
	 * it will return a single value "approved", because that is the only
	 * status non-admins can access.
	 *
	 * @access	public
	 * @return	array
	 *
	 */
	function allowedStatusList () {
		//if (! $this->isAdmin ()) {
		//	return array ('approved');
		//} else {
			$role = $this->roles[$this->user['role']];

			if (! isset ($role['allow']['status']['all'])) {
				return array_keys ($role['allow']['status']);
			}
			return array ('all');
		//}
	}

	/**
	 * Returns an array of allowed teams for the current user. If the
	 * user is not an administrator, in which case teams are not relevant,
	 * or if the user is allowed to access all teams, this method returns
	 * an array containing a single item "all".  This is true unless the
	 * $list parameter is set to true, in which case a list of all the
	 * teams is returned instead.
	 *
	 * @access	public
	 * @param	boolean
	 * @return	array
	 *
	 */
	function allowedTeamsList ($list = false) {
		$team_list = array_keys ($this->teams);
		// $teams = array_keys ($this->user['teams']);
		$teams = array ();
		foreach ($this->user['teams'] as $k=>$v) {
			if (! empty ($v)) {
				$teams[] = $k;
			}	
		}	
		if (! in_array ($this->user['team'], $teams)) {
			$teams[] = $this->user['team'];
		}
		if (! $this->isAdmin () || in_array ('all', $teams)) {
			if ($list) {
				return $team_list;
			} else {
				return array ('all');
			}
		}
		return $teams;
	}

	/**
	 * Determines whether the current user belongs to an administrative role.
	 *
	 * @access	public
	 * @return	boolean
	 *
	 */
	function isAdmin () {
		$role = $this->roles[$this->user['role']];
		if ($role['admin']) {
			return true;
		}
		return false;
	}

	/**
	 * Returns the value of the specified preference setting.
	 *
	 * @access	public
	 * @param	string
	 * @return	mixed
	 *
	 */
	function pref ($name) {
		return $this->_prefs[$name];
	}

	/**
	 * Alters the value of the specified preference setting, in the current
	 * session AND in the database.  Returns false on failure to update.
	 * Returns the previous value on success.
	 *
	 * @access	public
	 * @param	string
	 * @param	mixed
	 * @return	mixed
	 *
	 */
	function prefSet ($name, $value) {
		$old = $this->_prefs[$name];
		$this->_prefs[$name] = $value;

		$res = db_single ('select * from sitellite_prefs where username = ? and pref = ?', $this->user['name'], $name);
		if (is_object ($res)) {
			$res = db_execute ('update sitellite_prefs set value = ? where username = ? and pref = ?', $value, $this->user['name'], $name);
			if (! $res) {
				$this->error = db_error ();
				$this->_prefs[$name] = $old;
				return false;
			}
		} else {
			$res = db_execute ('insert into sitellite_prefs (id, username, pref, value) values (null, ?, ?, ?)', $this->user['name'], $name, $value);
			if (! $res) {
				$this->error = db_error ();
				$this->_prefs[$name] = $old;
				return false;
			}
		}
		return $old;
	}

	/**
	 * Determines whether the specified resource name exists.
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 *
	 */
	function isResource ($name) {
		return in_array ($name, $this->resources);
	}

	/**
	 * Returns the list of roles which are admins.
	 *
	 * @return	array
	 *
	 */
	function adminRoles () {
		$roles = array ();
		foreach ($this->roles as $k => $v) {
			if ($v['admin']) {
				$roles[] = $k;
			}
		}
		return $roles;
	}
}

/**
 * Alias of SessionAcl->isAdmin().
 */
function session_admin () {
	return $GLOBALS['session']->acl->isAdmin ();
}

/**
 * Alias of SessionAcl->allowed().
 */
function session_allowed ($resource = 'documents', $access = 'rw', $type = 'resource') {
	return $GLOBALS['session']->acl->allowed ($resource, $access, $type);
}

/**
 * Alias of SessionAcl->allowedSql().
 */
function session_allowed_sql () {
	return $GLOBALS['session']->acl->allowedSql ();
}

/**
 * Alias of SessionAcl->approvedSql().
 */
function session_approved_sql () {
	return $GLOBALS['session']->acl->approvedSql ();
}

/**
 * Alias of SessionAcl->allowedAccessList().
 */
function session_allowed_access_list () {
	return $GLOBALS['session']->acl->allowedAccessList ();
}

/**
 * Alias of SessionAcl->allowedStatusList().
 */
function session_allowed_status_list () {
	return $GLOBALS['session']->acl->allowedStatusList ();
}

/**
 * Alias of SessionAcl->allowedTeamsList().
 */
function session_allowed_teams_list ($list = false) {
	return $GLOBALS['session']->acl->allowedTeamsList ($list);
}

/**
 * Alias of SessionAcl->pref().
 */
function session_pref ($name) {
	return $GLOBALS['session']->acl->pref ($name);
}

/**
 * Alias of SessionAcl->prefs.
 */
function session_pref_list () {
	return $GLOBALS['session']->acl->prefs;
}

/**
 * Alias of SessionAcl->prefSet().
 */
function session_pref_set ($name, $value) {
	return $GLOBALS['session']->acl->prefSet ($name, $value);
}

/**
 * Alias of SessionAcl->status.
 */
function session_get_statuses () {
	return $GLOBALS['session']->acl->status;
}

/**
 * Alias of SessionAcl->access.
 */
function session_get_access_levels () {
	return $GLOBALS['session']->acl->access;
}

/**
 * Alias of SessionAcl->resources.
 */
function session_get_resources () {
	return $GLOBALS['session']->acl->resources;
}

/**
 * Retrieves a list of all teams.
 */
function session_get_teams () {
	return array_keys ($GLOBALS['session']->acl->teams);
}

/**
 * Retrieves a list of all roles.
 */
function session_get_roles () {
	return array_keys ($GLOBALS['session']->acl->roles);
}

/**
 * Alias of SessionAcl->user['role'].
 */
function session_role () {
	return $GLOBALS['session']->acl->user['role'];
}

/**
 * Alias of SessionAcl->user['team'].
 */
function session_team () {
	return $GLOBALS['session']->acl->user['team'];
}

function session_is_resource ($name) {
	return $GLOBALS['session']->acl->isResource ($name);
}

function session_admin_roles () {
	return $GLOBALS['session']->acl->adminRoles ();
}

?>
