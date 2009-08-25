<?php

loader_import ('ui.Filters');

function ui_comments_author ($username) {
	$result = db_single ('SELECT firstname, lastname
		FROM sitellite_user WHERE username=?', $username);
	if ($result) {
		return '<a href="' . site_prefix () . '/sitemember-profile-action?user=' . $username . '">' . $result->firstname . ' ' . $result->lastname . '</a>';
	}
	else {
		return intl_get ('anonymous');
	}
}

class Comments {

function Comments ($parameters) {
	$this->group    = $parameters['group'];
	$this->item     = $parameters['item'];
	$this->anon     = $parameters['anon'];
	$this->approve  = $parameters['approve'];
	$this->readonly = $parameters['readonly'];

	$this->admin    = session_admin ();
	$this->addform  = '';

	if ($this->anon == 'no' && ! session_valid ()) {
		$this->login = true;
	}
	else {
		$this->login = false;
	}
}

function get () {
	$query = 'SELECT * FROM ui_comment WHERE `group`=? AND item=?';
	$params = array ($this->group, $this->item);

	if (! $this->admin) {
		$query .= ' AND approved=1';
	}
	$query .= ' ORDER BY date';
	
	$comments = db_fetch_array ($query, $params);
	$this->comments = array ();
	foreach ($comments as $c) {
		$c->admin = $this->admin;
		$this->comments[] = template_simple ('comment-item.spt', $c);
	}
}

function createAddForm ($username) {
	$this->user = $username;
	switch ($this->approve) {
		case 'yes':
			$this->approved = 1;
			break;
		case 'user':
			if (session_valid ()) {
				$this->approved = 1;
			}
			else {
				$this->approved = 0;
			}
			break;
		case 'no':
		default:
			$this->approved = 0;
	}
	$this->addform = template_simple ('comment-add.spt', $this);
}


}

?>
