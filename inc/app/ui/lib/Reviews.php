<?php

loader_import ('ui.Filters');
loader_import ('ui.Widget.Rating');

class Reviews {

function Reviews ($parameters) {
	$this->user     = $parameters['user'];
	$this->group    = $parameters['group'];
	$this->item     = $parameters['item'];
	$this->anon     = $parameters['anon'];
        $this->approve  = $parameters['approve'];
	$this->readonly = $parameters['readonly'];
	$this->nstars   = $parameters['nstars'];

	$this->admin    = session_admin ();
	$this->addform  = '';

	if ($this->anon == 'no' && ! session_valid ()) {
		$this->login = true;
	}
	else {
		$this->login = false;
	}
}

function getRatingValues ($nstars) {
	static $values = null;
	global $parameters;

	if (!is_array ($values)) {
		switch ($nstars == 5) {
			case 5:
				$values = array (intl_get ('Cancel'),
						intl_get ('Poor'),
						intl_get ('Nothing special'),
						intl_get ('Okay'),
						intl_get ('Pretty cool'),
						intl_get ('Awesome!'));
				break;
			default:
				$values = range (0, $nstars, 1);
		}
	}

	return $values;
}

function getRatingWidget ($user, $rating, $values) {
	$r = new MF_Widget_rating ('rating_' . $user);
	$r->setValue ($rating);
	$r->setValues ($values);
	$r->starOptions['disabled'] = 'true';
	return $r->display (false);
}

function get () {
	$query = 'SELECT ui_review.*, rating, website, firstname, lastname, email
		 FROM ui_review, ui_rating
		LEFT JOIN sitellite_user
			ON username = user
		 WHERE ui_review.`group`=? AND ui_review.item=? AND
			ui_review.user = ui_rating.user AND
			ui_review.`group` = ui_rating.`group` AND
			ui_review.item = ui_rating.item';
	$params = array ($this->group, $this->item);
        if (! $this->admin) {
                $query .= ' AND approved=1';
        }
        $query .= ' ORDER BY date';

	
	$reviews = db_fetch_array ($query, $params); 
	$this->reviews = array ();
	$this->mine = false;
	$remote_addr = strtr ($_SERVER['REMOTE_ADDR'], '.', '-');
	foreach ($reviews as $c) {

		if ($c->user == session_username () || $c->user == $remote_addr) {
			$this->mine = true;
		}

		$c->admin = $this->admin;
		if ($c->firstname || $c->lastname) {
			$c->name = trim ($c->firstname . ' ' . $c->lastname);
		}
		else {
			$c->name = $c->user;
		}
		if (! empty ($c->website)) {
			$c->name = '<a href="' . $c->website . '" rel="nofollow">' .
				 $c->name . '</a>';
		}
		$c->ratingwidget = $this->getRatingWidget ($c->user,
			$c->rating, $this->getRatingValues ($this->nstars));
		$this->comments[] = template_simple ('review-item.spt', $c);
	}
}

function createAddForm () {
	if (appconf ('verify_session')) {
		@session_start ();
		$_SESSION['mf_verify_session'] = 'mf_verified';
	}
	if ($this->mine) {
		return;
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
	$name = db_single ('SELECT firstname, lastname, email, website,
		comment, rating FROM sitellite_user
		LEFT JOIN ui_rating ON username = ui_rating.user
		LEFT JOIN ui_review ON ui_review.user = ui_rating.user AND
			ui_review.item = ui_rating.item AND
			ui_review.`group` = ui_rating.`group`
		WHERE username = ? AND
			ui_review.`group`=? AND
			ui_review.item=?',
		session_username (), $this->group, $this->item);
	$this->name    = trim ($name->firstname . ' ' . $name->lastname);
	$this->email   = $name->email;
	$this->website = $name->website;
	$this->id      = $this->item;
	$this->comment = $name->comment;

	$r = new MF_Widget_rating ('rating');
	$r->setValue ($name->rating);
	$r->setValues ($this->getRatingValues ($this->nstars));
	$r->starOptions['cancelShow'] = 'false';
	$this->ratingwidget = $r->display (false);
	$this->addform = template_simple ('review-add.spt', $this);
}


}

?>
