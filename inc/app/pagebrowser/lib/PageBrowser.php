<?php

class PageBrowser {
	function getSections ($limit) {
		return db_fetch_array ('select id, if(nav_title != "", nav_title, if(title != "", title, id)) as title from sitellite_page where is_section = "yes" order by title asc');

/*
		global $menu;
		$out = array ();
		foreach ($menu->getSections () as $id => $title) {
			$out[] = (object) array ('id' => $id, 'title' => $title);
		}
		return $out;
*/
	}

	function getChildren ($id, $limit) {
		$children = db_fetch_array ('select id, if(nav_title != "", nav_title, if(title != "", title, id)) as title from sitellite_page where below_page = ? order by title asc', $id);
		foreach (array_keys ($children) as $i) {
			$children[$i]->children = db_shift ('select count(*) from sitellite_page where below_page = ?', $children[$i]->id);
		}
		return $children;

/*
		global $menu;
		if (empty ($id)) {
			$page = false;
		} else {
			$page = $id;
		}
		$out = array ();
		foreach ($menu->getChildren ($page) as $item) {
			$out[] = (object) array ('id' => $item->id, 'title' => $item->title, 'children' => $menu->countChildren ($item->id));
		}
		return $out;
*/
	}

	function getTrail ($id, $limit, $incl_self = false) {
		if (! $id || $id == '') {
			return array ();
		}

		$trail = array ();
		if ($incl_self) {
			$trail[] = (object) array ('id' => $id, 'title' => $this->getTitle ($id));
		}
		$parent = db_shift ('select below_page from sitellite_page where id = ?', $id);
		while ($parent) {
			$next = db_single ('select id, if(nav_title != "", nav_title, if(title != "", title, id)) as title, below_page from sitellite_page where id = ?', $parent);
			if (is_object ($next)) {
				$trail[] = (object) array ('id' => $next->id, 'title' => $next->title);
				$parent = $next->below_page;
			} else {
				$parent = false;
			}
		}
		$trail = array_reverse ($trail);

		$out = array ((object) array ('id' => '', 'title' => intl_get ('Root')));
		foreach ($trail as $item) {
			//info ($item);
			if (! $incl_self && $item->id == $id) {
				continue;
			}
			$out[] = (object) array ('id' => $item->id, 'title' => $item->title);
		}
		//exit;
		return $out;

/*
		if (! $id || $id == '') {
			return array ();
		}
		global $menu;
		$out = array ((object) array ('id' => '', 'title' => intl_get ('Root')));
		foreach ($menu->trail ($id) as $item) {
			//info ($item);
			if (! $incl_self && $item->id == $id) {
				continue;
			}
			$out[] = (object) array ('id' => $item->id, 'title' => $item->title);
		}
		//exit;
		return $out;
*/
	}

	function getTitle ($id) {
		if (! $id || $id == '') {
			return 'Root';
		}
		return db_shift ('select if(nav_title != "", nav_title, if(title != "", title, id)) from sitellite_page where id = ?', $id);
	}

	function setCurrent ($id, $limit) {
		return array (
			$this->getTitle ($id),
			$this->getChildren ($id, $limit),
			$this->getTrail ($id, $limit),
		);
	}
}

?>