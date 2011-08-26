<?php

class Petition extends Generic {
	function Petition ($id = false) {
		parent::Generic ('petition', 'id');
		$this->usePermissions = true;

		if (is_array ($id)) {
			$newkey = $this->add ($id);
			if (is_numeric ($newkey)) {
				$this->setCurrent ($this->get ($newkey));
			} else {
				$this->setCurrent ($this->get ($id['id']));
			}
		} elseif (is_object ($id)) {
			$this->setCurrent ($id);
		} elseif ($id) {
			$this->setCurrent ($this->get ($id));
		}


		$this->_cascade['Signature'] = 'petition_id';
	}

	function &setSignature (&$o) {
		if (is_subclass_of ($o, 'Generic')) {
			$k = $o->pkey ();
			$o->_current->petition_id = $this->pkey ();
		} else {
			$k = $o->id;
		}
		if (! db_execute (
			'update petition_signature set petition_id = ? where id = ?',
			$this->pkey (),
			$k
		)) {
			$this->error = db_error ();
			return false;
		}
		return $o;
	}

	function unsetSignature (&$o) {
		if (is_subclass_of ($o, 'Generic')) {
			$k = $o->pkey ();
			$o->_current->petition_id = 0;
		} else {
			$k = $o->id;
		}
		if (! db_execute (
			'update petition_signature set petition_id = ? where id = ?',
			0,
			$k
		)) {
			$this->error = db_error ();
			return false;
		}
		return true;
	}

	function getSignatures ($id = false) {
		if (! $id) {
			$id = $this->val ('id');
		} elseif (is_object ($id)) {
			$id = $id->id;
		}

		return db_fetch_array (
			'select * from petition_signature
			where petition_id = ?',
			$id
		);
	}
}

class Signature extends Generic {
	function Signature ($id = false) {
		parent::Generic ('petition_signature', 'id');
		

		if (is_array ($id)) {
			$newkey = $this->add ($id);
			if (is_numeric ($newkey)) {
				$this->setCurrent ($this->get ($newkey));
			} else {
				$this->setCurrent ($this->get ($id['id']));
			}
		} elseif (is_object ($id)) {
			$this->setCurrent ($id);
		} elseif ($id) {
			$this->setCurrent ($this->get ($id));
		}

		// Signature cascade
	}

	function &setPetition (&$o) {
		if (is_subclass_of ($o, 'Generic')) {
			$k = $o->pkey ();
		} else {
			$k = $o->id;
		}
		if (! db_execute (
			'update petition_signature set petition_id = ? where id = ?',
			$k,
			$this->pkey ()
		)) {
			$this->error = db_error ();
			return false;
		}
		$this->_current->petition_id = $k;
		return $o;
	}

	function unsetPetition (&$o) {
		if (is_subclass_of ($o, 'Generic')) {
			$k = $o->pkey ();
		} else {
			$k = $o->id;
		}
		if (! db_execute (
			'update petition_signature set petition_id = ? where id = ?',
			0,
			$this->pkey ()
		)) {
			$this->error = db_error ();
			return false;
		}
		$this->_current->petition_id = 0;
		return true;
	}

	function getPetition () {
		return db_single (
			'select * from petition
			where id = ?',
			$this->val ('petition_id')
		);
	}
}

?>