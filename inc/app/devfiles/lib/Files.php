<?php

loader_import ('saf.Database.Generic');
loader_import ('saf.File.Store');

class Files extends Generic {
	var $store;

	function Files () {
		parent::Generic ('devfiles_file', 'id');
		$this->store = new FileStore ('inc/app/devfiles/data');
		$this->store->autoInit = true;
	}

	function getApps () {
		$res = db_fetch ('select appname, count(*) as file_count from devfiles_file group by appname');
		if (! $res) {
			$this->error = db_error ();
			$res = array ();
		} elseif (is_object ($res)) {
			$res = array ($res);
		}
		return $res;
	}

	function getType ($ext) {
		if (@file_exists ('inc/app/devfiles/pix/icons/' . $ext . '.gif')) {
			return $ext;
		}
		return 'default';
	}

	function getPath ($file, $appname) {
		return $this->store->getPath ($appname . '-' . $file);
	}

	function verifyType ($type) {
		$allowed = appconf ('allowed');
		if (count ($allowed) > 0) {
			if (! in_array ($type, $allowed)) {
				return false;
			}
		}

		$not = appconf ('not_allowed');
		if (count ($not) > 0) {
			if (in_array ($type, $not)) {
				return false;
			}
		}

		return true;
	}

	function add ($file, $appname, $user, $name, $size) {
		$info = pathinfo ($name);

		/*if (empty ($name)) {
			$name = $info['basename'];
		} elseif (! strstr ($name, $info['extension'])) {
			$name = $name . '.' . $info['extension'];
		}*/

		$struct = array (
			'name' => $user,
			'file' => $name,
			'type' => $this->getType ($info['extension']),
			'size' => $size,
			'appname' => $appname,
		);

		// move file
		if ($this->store->exists ($appname . '-' . $name)) {
			$this->error = 'File already exists!  Please choose another name';
			return false;
		}

		if (! $this->store->move ($appname . '-' . $name, $file, true)) {
			$this->error = $this->store->error;
			return false;
		}

		// add to database
		$res = parent::add ($struct);
		if (! $res) {
			return false;
		}

		return $this->getPath ($name, $appname);
	}

	function remove ($file, $appname, $id) {
		if (! $this->store->remove ($appname . '-' . $file)) {
			$this->error = $this->store->error;
			return false;
		}
		return parent::remove ($id);
	}
}

?>