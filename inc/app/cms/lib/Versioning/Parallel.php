<?php

class Parallel {
	var $page_id;
	var $parallel_id;
	var $revision_id;
	var $goal_url;
	var $total_views = 0;
	var $total_clicks = 0;
	var $campaign_start;
	var $error = false;

	function Parallel ($pg) {
		if (is_object ($pg)) {
			$this->page_id = $pg->id;
		} else {
			$this->page_id = $pg;
		}
		$sp = db_single ('select * from sitellite_parallel where page = ?', $this->page_id);
		if ($sp) {
			$this->parallel_id = $sp->id;
			$this->goal_url = $sp->goal;
		} else {
			db_execute ('insert into sitellite_parallel values (null, ?, "")', $this->page_id);
			$this->parallel_id = db_lastid ();
			$this->goal_url = '';
		}
	}

	function next () {
		global $cgi;

		$show = session_get ('parallel_id');
		if ($show && ! $cgi->parallel_show) {
			$cgi->parallel_show = $show;
		}

		if ($cgi->parallel_show) {
			$selected = 0;
			$list[$selected] = db_single ('select * from sitellite_page_sv where id = ? and sv_autoid = ?', $this->page_id, $cgi->parallel_show);
		} else {
			// determine which parallel version to serve
			$list = db_fetch_array ('select * from sitellite_page_sv where id = ? order by sv_revision desc limit 10', $this->page_id);
			$good = array ();
			foreach (array_keys ($list) as $k) {
				if ($list[$k]->sitellite_status != 'parallel') {
					break;
				}
				$good[] = $k;
			}
	
			$selected = array_rand ($good);
		}
		$this->revision_id = $list[$selected]->sv_autoid;

		if (! session_admin ()) {
			session_set ('parallel_id', $this->revision_id);
		}

		unset ($list[$selected]->sv_autoid);
		unset ($list[$selected]->sv_author);
		unset ($list[$selected]->sv_action);
		unset ($list[$selected]->sv_revision);
		unset ($list[$selected]->sv_changelog);
		unset ($list[$selected]->sv_deleted);
		unset ($list[$selected]->sv_current);

		// load javascript for goal tracking
		$this->load_script ();

		// track view
		$this->viewed ();

		return $list[$selected];
	}

	function viewed () {
		// track view
		if (! session_admin ()) {
			return db_execute (
				'insert into sitellite_parallel_view values (?, ?, now())',
				$this->parallel_id,
				$this->revision_id
			);
		}
	}

	function clicked ($revision_id) {
		// track click
		if (! session_admin ()) {
			return db_execute (
				'insert into sitellite_parallel_click values (?, ?, now())',
				$this->parallel_id,
				$revision_id
			);
		}
	}

	function load_script () {
		page_add_script (site_prefix () . '/js/rpc.js');
		page_add_script (site_prefix () . '/js/parallel.js');
		page_add_script ('
			var parallel_script_url = "' . site_prefix () . '/index/cms-parallel-rpc-action";
			var parallel_page_id = "' . $this->page_id . '";
			var parallel_revision_id = "' . $this->revision_id . '";
			var parallel_goal_url = "' . $this->goal_url . '";
		');
	}

	function set_goal ($goal) {
		db_execute (
			'update sitellite_parallel set goal = ? where id = ?',
			$goal,
			$this->parallel_id
		);
		$this->goal_url = $goal;
	}

	function get_stats () {
		$stats = array ();
		$list = db_fetch_array ('select * from sitellite_page_sv where id = ? order by sv_revision desc limit 10', $this->page_id);
		$good = array ();
		foreach (array_keys ($list) as $k) {
			if ($list[$k]->sitellite_status != 'parallel') {
				break;
			}
			$good[] = $k;
		}

		$highest_id = 0;
		$oldest_id = 0;
		$highest = 0;
		$oldest = false;

		$good = array_reverse ($good);

		foreach ($good as $k) {
			$stats[$list[$k]->sv_autoid] = array (
				'description' => $list[$k]->sv_changelog,
				'ts' => $list[$k]->sv_revision,
				'viewed' => db_shift ('select count(*) from sitellite_parallel_view where parallel_id = ? and revision_id = ?', $this->parallel_id, $list[$k]->sv_autoid),
				'clicked' => db_shift ('select count(*) from sitellite_parallel_click where parallel_id = ? and revision_id = ?', $this->parallel_id, $list[$k]->sv_autoid),
				'highest' => false,
				'oldest' => false,
			);
			if ($stats[$list[$k]->sv_autoid]['viewed'] > 0) {
				$stats[$list[$k]->sv_autoid]['ratio'] = ($stats[$list[$k]->sv_autoid]['clicked'] / $stats[$list[$k]->sv_autoid]['viewed']) * 100;
			} else {
				$stats[$list[$k]->sv_autoid]['ratio'] = 0;
			}
			if ($stats[$list[$k]->sv_autoid]['ratio'] > $highest) {
				$highest = $stats[$list[$k]->sv_autoid]['ratio'];
				$highest_id = $list[$k]->sv_autoid;
			}
			if ($oldest == false || $stats[$list[$k]->sv_autoid]['ts'] < $oldest) {
				$oldest = $stats[$list[$k]->sv_autoid]['ts'];
				$oldest_id = $list[$k]->sv_autoid;
			}
			$this->total_views += $stats[$list[$k]->sv_autoid]['viewed'];
			$this->total_clicks += $stats[$list[$k]->sv_autoid]['clicked'];
		}
		if ($highest_id) {
			$stats[$highest_id]['highest'] = true;
		}
		$stats[$oldest_id]['oldest'] = true;
		$this->campaign_start = $stats[$oldest_id]['ts'];
		return $stats;
	}

	function clear_series () {
		db_execute ('delete from sitellite_parallel where id = ?', $this->parallel_id);
		db_execute ('delete from sitellite_parallel_view where parallel_id = ?', $this->parallel_id);
		db_execute ('delete from sitellite_parallel_click where parallel_id = ?', $this->parallel_id);
	}

	function approve ($revision_id = false) {
		if (! $revision_id) {
			$revision_id = $this->revision_id;
		}

		loader_import ('cms.Versioning.Rex');

		$rex = new Rex ('sitellite_page');

		$info = db_single (
			'select * from sitellite_page_sv where id = ? and sv_autoid = ?',
			$this->page_id,
			$revision_id
		);

		unset ($info->sv_autoid);
		unset ($info->sv_author);
		unset ($info->sv_action);
		unset ($info->sv_revision);
		unset ($info->sv_changelog);
		unset ($info->sv_deleted);
		unset ($info->sv_current);

		$method = $rex->determineAction ($this->page_id, 'approved');
		if (! $method) {
			$this->error = $rex->error;
			return false;
		}

		$info = (array) $info;
		$info['sitellite_status'] = 'approved';

		$res = $rex->{$method} ($this->page_id, $info, intl_get ('Approved from parallel testing campaign'));
		if (! $res) {
			$this->error = $rex->error;
			return false;
		}

		$this->clear_series ();

		return true;
	}
}

?>