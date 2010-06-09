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
// This is the Database source driver package for saf.Session.
//


/**
	 * This is the Database source driver package for saf.Session.
	 * 
	 * New in 1.2:
	 * - Added the getRole(), getTeam(), and isDisabled() methods.
	 * - Added an $encryptionMethod property that allows the method of password
	 *   encryption to be changed.
	 * - Added the $rolecolumn, $teamcolumn, and $disabledcolumn properties.
	 * 
	 * <code>
	 * <?php
	 * 
	 * $s = new SessionSource_Database;
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
	 * @version	1.2, 2003-04-23, $Id: Database.php,v 1.4 2007/10/06 00:06:30 lux Exp $
	 * @access	public
	 * 
	 */

$GLOBALS['loader']->import ('saf.Session.Source');

class SessionSource_Database extends SessionSource {
	

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
	var $usernamecolumn = 'username';

	/**
	 * The name of the password column in the database.
	 * Defaults to 'password'.
	 * 
	 * @access	public
	 * 
	 */
	var $passwordcolumn = 'password';

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
			if ($this->sessObj->useID) {
				$res = db_fetch (
					sprintf ('select * from %s where %s = ? and (%s IS NULL or %s NOT LIKE "PENDING:%%")',
						$this->tablename,
						$this->usernamecolumn,
						$this->sessionidcolumn,
						$this->sessionidcolumn
					),
					$username
				);
			} else {
				$res = db_fetch (
					sprintf ('select * from %s where %s = ?',
						$this->tablename,
						$this->usernamecolumn
					),
					$username
				);
			}
			if (! is_object ($res)) {
				$this->error = db_error ();
				return false;
			}

			$this->resultObj =& $res;

			if (call_user_func ($this->encryptionMethod, $password, $res->{$this->passwordcolumn})) {
				// good

				if ($this->sessObj->useID) {
					$id = md5 (uniqid(mt_rand(),1));
					while (! db_execute (
						sprintf (
							'update %s set %s = ?, %s = ? where %s = ?',
							$this->tablename,
							$this->sessionidcolumn,
							$this->timeoutcolumn,
							$this->usernamecolumn
						),
						$id,
						date ('Y-m-d H:i:s', time() + $this->sessObj->timeout),
						$username
					)) {
						$id = md5 (uniqid (mt_rand (), 1));
					}
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
				$res = db_fetch (
					sprintf (
						'select * from %s where %s = ? and %s > now() and (%s IS NULL or %s NOT LIKE "PENDING:%%")',
						$this->tablename,
						$this->sessionidcolumn,
						$this->timeoutcolumn,
						$this->sessionidcolumn,
						$this->sessionidcolumn
					),
					$id
				);
			} else {
				$res = db_fetch (
					sprintf (
						'select * from %s where %s = ? and (%s IS NULL or %s NOT LIKE "PENDING:%%")',
						$this->tablename,
						$this->sessionidcolumn,
						$this->sessionidcolumn,
						$this->sessionidcolumn
					),
					$id
				);
			}

			if (! is_object ($res)) {
				$this->error = db_error ();
				return false;
			}

			$this->resultObj =& $res;
			$this->sessObj->username = $res->{$this->usernamecolumn};

			if ($this->sessObj->timeout > 0) {
				// reset server-side timeout
				// this is an "if" so as to allow for automatic logins
				$r = db_execute (
					sprintf (
						'update %s set %s = ? where %s = ?',
						$this->tablename,
						$this->timeoutcolumn,
						$this->sessionidcolumn
					),
					date ('Y-m-d H:i:s', time() + $this->sessObj->timeout),
					$id
				);
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
		if ($this->sessObj->timeout > 0) {
			return db_execute (
				sprintf (
					'update %s set %s = NULL, %s = ? where %s = ?',
					$this->tablename,
					$this->sessionidcolumn,
					$this->timeoutcolumn,
					$this->usernamecolumn
				),
				"00000000000000",
				$this->sessObj->username
			);
		} else {
			return db_execute (
				sprintf (
					'update %s set %s = NULL where %s = ?',
					$this->tablename,
					$this->sessionidcolumn,
					$this->usernamecolumn
				),
				$this->sessObj->username
			);
		}
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
		$res = $this->resultObj->{$this->teamscolumn};
		if (! $res) {
			return array ();
		}
		return unserialize ($res);
	}

	/**
	 * Returns the whether or not the current user account
	 * is disabled.
	 * 
	 * @access	public
	 * 
	 */
	function isDisabled () {
		$res = $this->resultObj->{$this->disabledcolumn};
		if (! $res) {
			return false;
		} elseif ($res === true || $res == 'yes') {
			return true;
		}
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
		return db_shift (
			sprintf (
				'select count(*) from %s where %s = ? and %s = ?',
				$this->tablename,
				$this->usernamecolumn,
				$this->sessionidcolumn
			),
			$user,
			$key
		);
	}

	/**
	 * Adds a new user.
	 *
	 * @access  public
	 *
	 */
	function add ($data) {
		list ($one, $two, $bind) = $this->_add ($data);
		$res = db_execute (
			sprintf (
				'insert into %s (%s) values (%s)',
				$this->tablename,
				$one,
				$two
			),
			$bind
		);
		if (! $res) {
			$this->error = db_error ();
			return false;
		}
		return true;
	}

	/**
	 * Joins the list of data into a piece of SQL.
	 *
	 * @access  private
	 *
	 */
	function _add ($data) {
		$one = '';
		$two = '';
		$bind = array ();
		$join = '';
		foreach ($data as $key => $value) {
			if ($key == $this->teamscolumn) {
				$value = serialize ($value);
			}
			$one .= $join . $key;
			$two .= $join . '?';
			$bind[] = $value;
			$join = ', ';
		}
		return array ($one, $two, $bind);
	}

	/**
	 * Updates the data of the specified user.
	 *
	 * @access  public
	 *
	 */
	function update ($data, $user) {
		$res = db_execute (
			sprintf (
				'update %s set %s where %s = ?',
				$this->tablename,
				$this->_update ($data),
				$this->usernamecolumn
			),
			$user
		);
		if (! $res) {
			$this->error = db_error ();
			return false;
		}
		return true;
	}

	/**
	 * Joins the list of data into a piece of SQL.
	 *
	 * @access  private
	 *
	 */
	function _update ($data) {
		$out = '';
		$op = '';
		foreach ($data as $key => $value) {
			if ($key == $this->teamscolumn) {
				$value = serialize ($value);
			}
			$out .= $op . $key . ' = ' . db_quote ($value);
			$op = ', ';
		}
		return $out;
	}

	/**
	 * Removes the specified user.
	 *
	 * @access  public
	 *
	 */
	function delete ($user) {
		$res = db_execute (
			sprintf (
				'delete from %s where %s = ?',
				$this->tablename,
				$this->usernamecolumn
			),
			$user
		);
		if (! $res) {
			$this->error = db_error ();
			return false;
		}
		return true;
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
	 * Retrieves the total number of active (ie. currently logged in) users.
	 *
	 * @access  public
	 *
	 */
	function getActive () {
		$sql = 'select count(*) from ' . $this->tablename . ' where ' . $this->sessionidcolumn . ' is not null and ' . $this->timeoutcolumn . ' >= ?';
		$bind = array (date ('Y-m-d H:i:s', time() - $this->sessObj->timeout));

		return db_shift ($sql, $bind);
	}

	/**
	 * Retrieves a list of users.
	 *
	 * @access  public
	 *
	 */
	function getList ($offset, $limit, $order, $ascdesc, $role, $team, $name, $disabled, $public, $teams) {
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

		if ($disabled) {
			$sql .= $pre . 'disabled = ?';
			$bind[] = $disabled;
			$pre = ' and ';
		}

		if ($public) {
			$sql .= $pre . 'public = ?';
			$bind[] = $public;
			$pre = ' and ';
		}

		if ($teams) {
			$sql .= $pre . '(teams like ' . db_quote ('%' . $teams . '%') . ' or teams = ?)';
			$bind[] = 'a:1:{s:3:"all";s:2:"rw";}';
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