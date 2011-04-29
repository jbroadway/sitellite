<?php

loader_import ('saf.Database.Generic');
loader_import ('saf.Database.PropertySet');

class SiteLinks_Item extends Generic {
	/**
	 * Constructor method.
	 */
	function SiteLinks_Item () {
		parent::Generic ('sitelinks_item', 'id');
		$this->usePermissions = true;
		if (session_admin ()) {
			$this->perms = session_allowed_sql ();
		} elseif (session_valid ()) {
			$this->perms = '((' . session_approved_sql () . ') or user_id = "' . session_username () . '")';
		} else {
			$this->perms = session_approved_sql ();
		}
		//die ($this->perms);
	}

	function get ($id) {
		$item = db_single ('select * from sitelinks_item where id = ? and ' . $this->perms, $id);
		if (! $item) {
			return $item;
		}

		$ps = new PropertySet ('sitelinks_item', $id);
		foreach ($ps->get () as $k => $v) {
			$item->{$k} = $v;
		}

		$item->views = $this->views ($id);
		$item->hits = $this->hits ($id);
		$rating = $this->rating ($id);
		$item->votes = $rating->votes;
		$item->rating = $rating->rating;
		return $item;
	}

	/**
	 * Returns the number of views of the specified item.
	 */
	function views ($item) {
		return db_shift ('select count(*) from sitelinks_view where item_id = ?', $item);
	}

	/**
	 * Returns the number of hits of the specified item.
	 */
	function hits ($item) {
		return db_shift ('select count(*) from sitelinks_hit where item_id = ?', $item);
	}

	/**
	 * Returns an object with the properties $obj->rating and $obj->votes,
	 * where the rating is the average of all the votes for this item,
	 * and votes is the number of votes placed.
	 */
	function rating ($item) {
		$res = db_single (
			'select avg(rating) as rating, count(*) as votes from sitelinks_rating where item_id = ?',
			$item
		);
		$res->rating = number_format ($res->rating, 2);
		return $res;
	}

	/**
	 * Increases the number of views of the specified item.
	 */
	function addView ($item) {
		$res = db_shift (
			'select count(*) from sitelinks_view where item_id = ? and ts > date_sub(now(), interval 1 day) and ip = ? and ua = ?',
			$item,
			$_SERVER['REMOTE_ADDR'],
			$_SERVER['HTTP_USER_AGENT']
		);
		if ($res > 0) {
			return false;
		}

		$res = db_execute (
			'insert into sitelinks_view (id, item_id, ts, ip, ua) values (null, ?, now(), ?, ?)',
			$item,
			$_SERVER['REMOTE_ADDR'],
			$_SERVER['HTTP_USER_AGENT']
		);
		if (! $res) {
			$this->error = db_error ();
			return false;
		}
		return true;
	}

	/**
	 * Increases the number of hits (aka "click throughs") of the specified item.
	 */
	function addHit ($item) {
		$res = db_shift (
			'select count(*) from sitelinks_hit where item_id = ? and ts > date_sub(now(), interval 1 day) and ip = ? and ua = ?',
			$item,
			$_SERVER['REMOTE_ADDR'],
			$_SERVER['HTTP_USER_AGENT']
		);
		if ($res > 0) {
			return false;
		}

		$res = db_execute (
			'insert into sitelinks_hit (id, item_id, ts, ip, ua) values (null, ?, now(), ?, ?)',
			$item,
			$_SERVER['REMOTE_ADDR'],
			$_SERVER['HTTP_USER_AGENT']
		);
		if (! $res) {
			$this->error = db_error ();
			return false;
		}
		return true;
	}

	/**
	 * Determines whether the current visitor has voted for the specified item within the
	 * past month.
	 */
	function hasVoted ($item) {
		return db_shift (
			'select count(*) from sitelinks_rating where item_id = ? and ip = ? and ua = ? and ts > date_sub(now(), interval 1 month)',
			$item,
			$_SERVER['REMOTE_ADDR'],
			$_SERVER['HTTP_USER_AGENT']
		);
	}

	/**
	 * Makes a rating on the specified item.  $rating should be from 0 to 5.
	 */
	function addRating ($item, $rating) {
		if ($this->hasVoted ($item)) {
			return false;
		}

		$res = db_execute (
			'insert into sitelinks_rating (id, item_id, rating, ts, ip, ua) values (null, ?, ?, now(), ?, ?)',
			$item,
			$rating,
			$_SERVER['REMOTE_ADDR'],
			$_SERVER['HTTP_USER_AGENT']
		);
		if (! $res) {
			$this->error = db_error ();
			return false;
		}

		$r = $this->rating ($item);
		loader_import ('cms.Versioning.Rex');
		$rex = new Rex ('sitelinks_item');
		if (! $rex->collection) {
			$this->error = 'SiteLinks collection is not installed.  Please copy install/sitelinks_item.php into inc/app/cms/conf/collections.';
			return false;
		}
		$rex->modify ($item, array ('user_rating' => $r->rating));

		return true;
	}

	function getCategories () {
		return db_fetch_array ('select category, count(*) as items from sitelinks_item where category != "" and ' . $this->perms . ' group by category asc');
	}

	function getTypes () {
		return db_fetch_array ('select ctype, count(*) as items from sitelinks_item where ctype != "" and ' . $this->perms . ' group by ctype asc');
	}

	function getCategory ($cat = false, $limit = false, $offset = false) {
		if (! $limit) {
			if ($cat) {
				$res = db_fetch_array (
					'select * from sitelinks_item where category = ? and ' . $this->perms . ' order by rank desc, user_rating desc, ts desc',
					$cat
				);
			} else {
				$res = db_fetch_array (
					'select * from sitelinks_item where ' . $this->perms . ' order by rank desc, user_rating desc, ts desc'
				);
			}
		} else {
			if ($cat) {
				$q = db_query (
					'select * from sitelinks_item where category = ? and ' . $this->perms . ' order by rank desc, user_rating desc, ts desc'
				);
				$b = array ($cat);
			} else {
				$q = db_query (
					'select * from sitelinks_item where ' . $this->perms . ' order by rank desc, user_rating desc, ts desc'
				);
				$b = array ();
			}

			if ($q->execute ($b)) {
				$res = $q->fetch ($offset, $limit);
				$this->total = $q->rows ();
				$q->free ();
			} else {
				$this->error = $q->error ();
				return false;
			}
		}

		$ps = new PropertySet ('sitelinks_item', false);
		foreach (array_keys ($res) as $key) {
			$ps->entity = $res[$key]->id;
			foreach ($ps->get () as $k => $v) {
				$res[$key]->{$k} = $v;
			}
		}

		return $res;
	}

	function getByType ($type = false, $limit = false, $offset = false) {
		if (! $limit) {
			if ($type) {
				$res = db_fetch_array (
					'select * from sitelinks_item where ctype = ? and ' . $this->perms . ' order by rank desc, user_rating desc, ts desc',
					$type
				);
			} else {
				$res = db_fetch_array (
					'select * from sitelinks_item where ' . $this->perms . ' order by rank desc, user_rating desc, ts desc'
				);
			}
		} else {
			if ($type) {
				$q = db_query (
					'select * from sitelinks_item where ctype = ? and ' . $this->perms . ' order by rank desc, user_rating desc, ts desc'
				);
				$b = array ($type);
			} else {
				$q = db_query (
					'select * from sitelinks_item where ' . $this->perms . ' order by rank desc, user_rating desc, ts desc'
				);
				$b = array ();
			}

			if ($q->execute ($b)) {
				$res = $q->fetch ($offset, $limit);
				$this->total = $q->rows ();
				$q->free ();
			} else {
				$this->error = $q->error ();
				return false;
			}
		}

		$ps = new PropertySet ('sitelinks_item', false);
		foreach (array_keys ($res) as $key) {
			$ps->entity = $res[$key]->id;
			foreach ($ps->get () as $k => $v) {
				$res[$key]->{$k} = $v;
			}
		}

		return $res;
	}

	function myLinks ($user = false) {
		if (! $user) {
			$user = session_username ();
		}

		$res = db_fetch_array (
			'select * from sitelinks_item where user_id = ? order by title asc',
			$user
		);

		$ps = new PropertySet ('sitelinks_item', false);
		foreach (array_keys ($res) as $key) {
			$ps->entity = $res[$key]->id;
			foreach ($ps->get () as $k => $v) {
				$res[$key]->{$k} = $v;
			}
		}

		return $res;
	}

	function getNewest ($limit = 10) {
		$res = db_fetch_array (
			'select * from sitelinks_item where ' . $this->perms . ' order by ts desc limit ' . $limit
		);

		$ps = new PropertySet ('sitelinks_item', false);
		foreach (array_keys ($res) as $key) {
			$ps->entity = $res[$key]->id;
			foreach ($ps->get () as $k => $v) {
				$res[$key]->{$k} = $v;
			}
		}

		return $res;
	}

	function getTop ($limit = 10) {
		$res = db_fetch_array (
			'select * from sitelinks_item where ' . $this->perms . ' order by user_rating desc limit ' . $limit
		);

		$ps = new PropertySet ('sitelinks_item', false);
		foreach (array_keys ($res) as $key) {
			$r = $this->rating ($res[$key]->id);
			$res[$key]->rating = $r->rating;
			$ps->entity = $res[$key]->id;
			foreach ($ps->get () as $k => $v) {
				$res[$key]->{$k} = $v;
			}
		}

		return $res;
	}

	function getRelated ($item) {
		$uid = db_shift ('select user_id from sitelinks_item where id = ?', $item);
		return db_fetch_array ('select id, title from sitelinks_item where user_id = ? and id != ? and ' . $this->perms . ' order by title asc limit 5', $uid, $item);
	}
}

?>