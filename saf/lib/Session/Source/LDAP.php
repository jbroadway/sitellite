<?php
//
// +----------------------------------------------------------------------+
// | Sitellite Content Management System                                  |
// +----------------------------------------------------------------------+
// | Copyright (c) 2010 Sitellite.org Community                           |
// +----------------------------------------------------------------------+
// | This software is released under the GNU GPL License.                 |
// | Please see the accompanying file docs/LICENSE for licensing details. |
// |                                                                      |
// | You should have received a copy of the GNU GPL License               |
// | along with this program; if not, visit www.sitellite.org.            |
// | The license text is also available at the following web site         |
// | address: <http://www.sitellite.org/index/license                     |
// +----------------------------------------------------------------------+
// | Authors: John Luxford <john.luxford@gmail.com>                       |
// +----------------------------------------------------------------------+
//
// This is the LDAP source driver package for saf.Session.
//


/**
	 * This is the LDAP source driver package for saf.Session, which lets
	 * you connect to an LDAP server to authenticate users.  Note that this
	 * driver requires that PHP be compiled with LDAP support.
	 * 
	 * <code>
	 * Settings in inc/sessions/sitellite/settings.php:
	 * [Source 2]
	 *
	 * driver        = LDAP
	 * host          = localhost
	 * port          = 389
	 * auth          = user
	 * dn            = "ou=department,o=company"
	 *
	 * map username  = cn
	 * map sessionid = session_id
	 * map timeout   = expires
	 *
	 * Implementation code (executed by saf.Session automatically):
	 * <?php
	 * 
	 * $s = new SessionSource_LDAP;
	 * 
	 * $s->setProperties (array (
	 * 	'foo'	=> 'bar',
	 * ));
	 * 
	 * if ($s->authorize ($user, $pass, $id)) {
	 * 	// in
	 * } else {
	 * 	// out
	 * }
	 * 
	 * ? >
	 * </code>
	 * 
	 * @package	Session
	 * @author	John Luxford <john.luxford@gmail.com>
	 * @license	http://www.sitellite.org/index/license	GNU GPL License
	 * @access	public
	 * 
	 */

loader_import ('saf.Session.Source');
loader_import ('saf.Database.LDAP');
loader_import ('saf.Database.PropertySet');

class SessionSource_LDAP extends SessionSource {
	

	/**
	 * The name of the database table that contains the users.
	 * Defaults to 'sitellite_user'.
	 * 
	 * @access	public
	 * 
	 */
	var $tablename = 'sitellite_user';

	/**
	 * The name of the username column in the database.
	 * Defaults to 'username'.
	 * 
	 * @access	public
	 * 
	 */
	var $usernamecolumn = 'uid';

	/**
	 * The name of the password column in the database.
	 * Defaults to 'password'.
	 * 
	 * @access	public
	 * 
	 */
	var $passwordcolumn = 'userPassword';

	/**
	 * The name of the session id column in the database.
	 * Defaults to 'session_id'.
	 * 
	 * @access	public
	 * 
	 */
	var $sessionidcolumn = 'session_id';

	/**
	 * The name of the timeout column in the database.
	 * Defaults to 'expires'.
	 * 
	 * @access	public
	 * 
	 */
	var $timeoutcolumn = 'expires';

	/**
	 * The name of the role column in the database.
	 * Defaults to 'role'.
	 * 
	 * @access	public
	 * 
	 */
	var $rolecolumn = 'role';

	/**
	 * The name of the team column in the database.
	 * Defaults to 'team'.
	 * 
	 * @access	public
	 * 
	 */
	var $teamcolumn = 'team';

	/**
	 * The name of the teams column in the database.
	 * Defaults to 'team'.
	 * 
	 * @access	public
	 * 
	 */
	var $teamscolumn = 'teams';

	/**
	 * The name of the disabled column in the database.
	 * Defaults to 'disabled'.
	 * 
	 * @access	public
	 * 
	 */
	var $disabledcolumn = 'disabled';

	/**
	 * The name of the public column in the database.
	 * Defaults to 'public'.
	 * 
	 * @access	public
	 * 
	 */
	var $publiccolumn = 'public';

	/**
	 * The method to use to compare the password to the
	 * encrypted copy from the source.  Defaults to
	 * 'better_crypt_compare', which uses a modification of the
	 * crypt() function.  $encryptionMethod must be any valid
	 * value that can be passed as a first parameter to the
	 * call_user_func() PHP function.  The specified function
	 * must accept the challenging password as a first parameter,
	 * and the source password as a second. This makes it easy
	 * to write alternate encryption methods, such as MD5.
	 * 
	 * @access	public
	 * 
	 */
	var $encryptionMethod = 'better_crypt_compare';

	var $auth = 'rdn';

	var $readOnly = true;

	var $groupMembership = 'groupMembership';

	function SessionSource_LDAP () {
		parent::SessionSource ();
	}

	/**
	 * Authorizes the user against the database.
	 * 
	 * @access	public
	 * @param	string	$username
	 * @param	string	$password
	 * @param	string	$id
	 * @return	boolean
	 * 
	 */
	function authorize ($username, $password, $id) {
		if (! empty ($username) && ! empty ($password)) {
			// retrieve old password and compare

			if (! is_array ($this->dn)) {
				$this->dn = array ($this->dn);
			}
			$success = false;
			foreach ($this->dn as $current_dn) {
				if ($this->auth == 'user') {
					$current_rdn = $this->usernamecolumn . '=' . $username . ',' . $current_dn;
					$this->password = $password;
				}
				if ($this->sessObj->useID) {
					$this->_ldap = new LDAP ($this->host, $this->port, $current_rdn, $this->password, $this->secure);
					if (! $this->_ldap->connection) {
						$this->error = $this->_ldap->error;
						continue;
					}
					if (! $this->_ldap->search ($current_dn, '(' . $this->usernamecolumn . '=' . $username . ')', array ())) {
						$this->error = $this->_ldap->error;
						continue;
					}
					$res = $this->_ldap->fetch ();
				} else {
					$this->_ldap = new LDAP ($this->host, $this->port, $current_rdn, $this->password, $this->secure);
					if (! $this->_ldap->connection) {
						$this->error = $this->_ldap->error;
						continue;
					}
					if (! $this->_ldap->search ($current_dn, '(' . $this->usernamecolumn . '=' . $username . ')', array ())) {
						$this->error = $this->_ldap->error;
						continue;
					}
					$res = $this->_ldap->fetch ();
				}
				if (! is_object ($res)) {
					$this->error = $this->_ldap->error;
					continue;
				} else {
					$success = true;
					break;
				}
			}

			if (! $success) {
				return false;
			}

			$this->resultObj =& $res;

			//info ($username);
			//info ($password);
			//info ($id);
			//info ($res);

			if ($this->auth == 'user' || call_user_func ($this->encryptionMethod, $password, str_replace ('{CRYPT}', '', $res->{$this->passwordcolumn}))) {
				// good

				if ($this->sessObj->useID) {
					$id = md5 (uniqid(mt_rand(),1));
					while (db_shift (
						'select count(*) from sitellite_property_set where collection = ? and entity = ?',
						'sitellite_session_source_ldap',
						$id
					)) {
						$id = md5 (uniqid (mt_rand (), 1));
					}
					$this->_ps = new PropertySet ('sitellite_session_source_ldap', $id);
					$res->session_id = $id;
					if ($this->sessObj->timeout > 0) {
						$res->expires = time () + $this->sessObj->timeout;
					}

					foreach ($this->set as $k => $v) {
						$res->{$k} = $v;
					}

					//$res->team = false;
					if (is_array ($res->{$this->groupMembership})) {
						foreach ($res->{$this->groupMembership} as $grp) {
							$grp = array_shift (explode (',', strtolower ($grp)));
							$grp = str_replace ('cn=', '', $grp);
							if (preg_match ('/^sitellite_tm_(.+)$/', $grp, $regs)) {
								if ($res->team !== false) {
									$res->allowedTeams[$regs[1]] = 'rw';
								} else {
									$res->team = $regs[1];
									$res->allowedTeams = array ($regs[1] => 'rw');
								}
							} elseif (preg_match ('/^sitellite_(.+)$/', $grp, $regs)) {
								$res->role = $regs[1];
							}
						}
					}

					$this->_ps->set ('info', serialize ($res));
					return $id;
				} else {
					return true;
				}

			} else {
				return false;
			}

		} elseif ($this->sessObj->useID && ! empty ($id)) {
			// retrieve by session identifier

			if ($this->sessObj->timeout > 0) {
				$this->_ps = new PropertySet ('sitellite_session_source_ldap', $id);
				$res = unserialize ($this->_ps->get ('info'));
			} else {
				$this->_ps = new PropertySet ('sitellite_session_source_ldap', $id);
				$res = unserialize ($this->_ps->get ('info'));
			}

			if (! is_object ($res)) {
				$this->error = $this->_ps->error;
				return false;
			}

			$this->resultObj =& $res;
			$this->sessObj->username = $res->{$this->usernamecolumn};

			if ($this->sessObj->timeout > 0) {
				// reset server-side timeout
				// this is an "if" so as to allow for automatic logins
				$res->expires = time () + $this->sessObj->timeout;
				$this->_ps->set ('info', serialize ($res));
			}

			return $this->resultObj->{$this->usernamecolumn};

		} else {

			return false;

		}
	}

	/**
	 * Closes the session with the source.  In this case, explicitly
	 * removes the user's session info from the database.
	 * 
	 * @access	public
	 * 
	 */
	function close () {
		return $this->_ps->delete ();
	}

	/**
	 * Returns the role of the current user.
	 * 
	 * @access	public
	 * 
	 */
	function getRole () {
		$res = $this->resultObj->{$this->rolecolumn};
		if (! $res) {
			return 'anonymous';
		}
		return $res;
	}

	/**
	 * Returns the team of the current user.
	 * 
	 * @access	public
	 * 
	 */
	function getTeam () {
		$res = $this->resultObj->{$this->teamcolumn};
		if (! $res) {
			if (count ($this->resultObj->allowedTeams) > 0) {
				return array_shift (array_keys ($this->resultObj->allowedTeams));
			}
			return 'core';
		}
		return $res;
	}

	/**
	 * Returns the list of teams whose documents are accessible by the
	 * current user.
	 * 
	 * @access	public
	 * 
	 */
	function getTeams () {
		if (count ($this->resultObj->allowedTeams) > 0) {
			return $this->resultObj->allowedTeams;
		}
		return array ($this->resultObj->{$this->teamcolumn} => 'rw');
	}

	/**
	 * Returns the whether or not the current user account
	 * is disabled.
	 * 
	 * @access	public
	 * 
	 */
	function isDisabled () {
		return false;
	}

	/**
	 * Retrieves a user by their username.
	 *
	 * @access  public
	 *
	 */
	function getUser ($user) {
		return db_single ('select * from ' . $this->tablename . ' where ' . $this->usernamecolumn . ' = ?', $user);
	}

	/**
	 * Retrieves a user by their email address.
	 *
	 * @access  public
	 *
	 */
	function getUserByEmail ($email) {
		return db_shift ('select ' . $this->usernamecolumn . ' from ' . $this->tablename . ' where email = ?', $email);
	}

	/**
	 * Determines whether the specified verification key is valid.
	 *
	 * @access  public
	 *
	 */
	function isValidKey ($user, $key) {
		$this->error = 'LDAP driver is read-only.';
		return false;
	}

	/**
	 * Adds a new user.
	 *
	 * @access  public
	 *
	 */
	function add ($data) {
		$this->error = 'LDAP driver is read-only.';
		return false;
	}

	/**
	 * Updates the data of the specified user.
	 *
	 * @access  public
	 *
	 */
	function update ($data, $user) {
		$this->error = 'LDAP driver is read-only.';
		return false;
	}

	/**
	 * Removes the specified user.
	 *
	 * @access  public
	 *
	 */
	function delete ($user) {
		$this->error = 'LDAP driver is read-only.';
		return false;
	}

	/**
	 * Retrieves the total number of users.  $role and $team allow you to
	 * retrieve a total for specific roles and teams.
	 *
	 * @access  public
	 *
	 */
	function getTotal ($role, $team, $public) {
		$sql = 'select count(*) from ' . $this->tablename;
		$bind = array ();
		$pre = ' where ';

		if ($role) {
			$sql .= $pre . $this->rolecolumn . ' = ?';
			$bind[] = $role;
			$pre = ' and ';
		}

		if ($team) {
			$sql .= $pre . $this->teamcolumn . ' = ?';
			$bind[] = $team;
			$pre = ' and ';
		}

		if ($public) {
			$sql .= $pre . $this->publiccolumn . ' = ?';
			$bind[] = 'yes';
		}

		return db_shift ($sql, $bind);
	}

	/**
	 * Retrieves a list of users.
	 *
	 * @access  public
	 *
	 */
	function getList ($offset, $limit, $order, $ascdesc, $role, $team, $name) {
		$sql = 'select * from ' . $this->tablename;
		$bind = array ();
		$pre = ' where ';

		if ($role) {
			$sql .= $pre . $this->rolecolumn . ' = ?';
			$bind[] = $role;
			$pre = ' and ';
		}

		if ($team) {
			$sql .= $pre . $this->teamcolumn . ' = ?';
			$bind[] = $team;
			$pre = ' and ';
		}

		if ($name) {
			$sql .= $pre . '(lastname like ? or firstname like ? or username like ?)';
			$bind[] = '%' . $name . '%';
			$bind[] = '%' . $name . '%';
			$bind[] = '%' . $name . '%';
			$pre = ' and ';
		}

		if ($order) {
			$sql .= ' order by ' . $order;
			if ($ascdesc) {
				$sql .= ' ' . $ascdesc;
			}
		}

		$q = db_query ($sql);

		if ($q->execute ($bind)) {
			$this->total = $q->rows ();
			if ($offset == false && $limit == false) {
				$res = array ();
				while ($row = $q->fetch ()) {
					$res[] = $row;
				}
			} else {
				$res = $q->fetch ($offset, $limit);
			}
			$q->free ();
			return $res;
		} else {
			$this->error = $q->error ();
			return false;
		}
	}
}



?>