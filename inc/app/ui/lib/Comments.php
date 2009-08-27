<?php

loader_import ('ui.Filters');

class Comments {

var $name = '';
var $email = '';
var $website = '';

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
		if (! empty ($c->website)) {
			$c->name = '<a href="' . $c->website . '" rel="nofollow">' . $c->name .
				'</a>';
		}
		$this->comments[] = template_simple ('comment-item.spt', $c);
	}
}

function createAddForm () {
	if (appconf ('verify_session')) {
		@session_start ();
		$_SESSION['mf_verify_session'] = 'mf_verified';
	}

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
	if (session_valid ()) {
		$name = db_single ('SELECT firstname, lastname, email, website
			FROM sitellite_user WHERE username=?',
			session_username ());
		$this->name    = $name->firstname . ' ' . $name->lastname;
		$this->email   = $name->email;
		$this->website = $name->website;
	}
	$this->addform = template_simple ('comment-add.spt', $this);
}


}

?>
