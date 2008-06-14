<?php

loader_import ('webpages.Functions');
loader_import ('saf.File.Directory');

class PageBrowser {
	function getSections ($limit) {
		$out = array ();
		return $out;
		foreach (webpages_get_folders () as $id => $title) {
			$out[] = (object) array ('id' => $id, 'title' => $title);
		}
		return $out;
	}

	function getChildren ($id, $limit) {
		/*
		if (session_admin ()) {
			$function = 'session_allowed_sql';
		} else {
			$function = 'session_approved_sql';
		}
		if (! $id) {
			$id = '';
		}
		return db_fetch_array ('select url as id, if(nav_title != "", nav_title, title) as title, 0 as children from webpages_page where folder_id = ? and ' . $function (), $id);
		*/
	}

	function getChildFolders ($id, $limit) {
		if (! empty ($id)) {
			$root = 'inc/data/';
		} else {
			$root = 'inc/data';
		}
		$d = new Dir ($root . $id);
		$list = $d->read_all ();
		$folders = array ();
		foreach ($list as $file) {
			if (strpos ($file, '.') === 0 || ! @is_dir ($root . $id . '/' . $file) || $file == 'CVS') {
				continue;
			}
			if (! empty ($id)) {
				$pref = $id . '/';
			} else {
				$pref = '';
			}
			$folders[] = (object) array ('id' => $pref . $file, 'name' => $file);
		}
		return $folders;
		/*
		if (session_admin ()) {
			$function = 'session_allowed_sql';
		} else {
			$function = 'session_approved_sql';
		}
		if (! $id) {
			$id = '';
		}
		return db_fetch_array ('select id, name from webpages_folder where parent_id = ? and ' . $function (), $id);
		*/
	}

	function getTrail ($id, $limit, $incl_self = false) {
		if (! $id || $id == '') {
			return array ();
		}

		$trail = explode ('/', $id);
		if (! $incl_self) {
			array_pop ($trail);
		}
		$folders = array ();

		for ($i = 0; $i < count ($trail); $i++) {
			$name = $trail[$i];
			$id = '';
			$sep = '';
			for ($j = 0; $j <= $i; $j++) {
				$id .= $sep . $trail[$j];
				$sep = '/';
			}
			$folders[] = (object) array ('id' => $id, 'title' => $name);
		}
		//$folders = array_reverse ($folders);
		array_unshift ($folders, (object) array ('id' => '', 'title' => 'root'));
		return $folders;

		//return webpages_trail ($id, $incl_self);

/*
		$url = trim (webpages_make_url ('', $id), '/');
		$trail = explode ('/', $url);

		$out = array ((object) array ('id' => 0, 'title' => intl_get ('Root')));
		foreach ($trail as $item) {
			//info ($item);
			if (! $incl_self && $item == $id) {
				continue;
			}
			$out[] = (object) array ('id' => $item, 'title' => $item);
		}
		//exit;
		return $out;
*/
	}

	function getTitle ($id) {
		if (! $id || $id == '') {
			return 'root';
		}
		return basename ($id);

//		return db_shift ('select name from webpages_folder where id = ?', $id);
//		return db_shift ('select if(nav_title != "", nav_title, title) from webpages_page where id = ?', $id);
	}

	function setCurrent ($id, $limit) {
		return array (
			$this->getTitle ($id),
			$this->getChildren ($id, $limit),
			$this->getTrail ($id, $limit),
			$this->getChildFolders ($id, $limit),
		);
	}

	function addFolder ($items, $path) {
		if (! $items) {
			return false;
		}
		$items = preg_split ('/, ?/', trim ($items));

		if (! empty ($path)) {
			$p = 'inc/data/' . $path . '/';
		} else {
			$p = 'inc/data/';
		}

		loader_import ('saf.File.Directory');

		foreach ($items as $item) {
			$item = strtolower ($item);
			$item = preg_replace ('/[^a-z0-9\._-]+/', '_', $item);
			if (! Dir::build ($p . $item, 0774)) {
				return false;
			}
		}

		return $this->setCurrent ($path, false);
	}

	function deleteFolder ($folder, $path) {
		if (! $folder) {
			return false;
		}

		if (! @rmdir ('inc/data/' . $folder)) {
			return false;
		}

		return $this->setCurrent ($path, false);
	}
}

?>