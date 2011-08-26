<?php

loader_import ('cms.Versioning.Parallel');

$p = new Parallel ($parameters['id']);

if (isset ($parameters['goal_url'])) {
	$p->set_goal ($parameters['goal_url']);
}

function parallel_filter_height ($value, $total, $new_base) {
	$div_by = $total / $new_base;
	if ($div_by > 0) {
		$ret = ceil ($value / $div_by);
		if ($ret > 250) {
			return 250;
		}
		if ($ret < 16) {
			return 16;
		}
		return $ret;
	}
	return 16;
}

if (empty ($p->goal_url)) {
	page_title (intl_get ('Parallel Page') . ': ' . $parameters['id']);
	echo template_simple ('parallel_goal.spt', $parameters);
} else {
	page_title (intl_get ('Parallel Stats') . ': ' . $parameters['id']);
	$parameters['stats'] = $p->get_stats ();
	if ($p->total_views == 0) {
		echo template_simple ('parallel_nostats.spt');
		return;
	}
	foreach ($parameters['stats'] as $k => $v) {
		$parameters['stats'][$k]['click_height'] = parallel_filter_height ($v['clicked'], $p->total_clicks, 150);
		$parameters['stats'][$k]['view_height'] = parallel_filter_height ($v['viewed'], $p->total_views, 450);
	}
	$parameters['total_views'] = $p->total_views;
	$parameters['total_clicks'] = $p->total_clicks;

	loader_import ('saf.Date');

	$parameters['duration'] = round ((time () - strtotime ($p->campaign_start)) / 86400, 1);
	$parameters['goal'] = $p->goal_url;
	$parameters['versions'] = count ($parameters['stats']);

	page_add_style (
		template_simple (
			'parallel_css.spt',
			$parameters
		)
	);
	echo template_simple ('parallel_stats.spt', $parameters);
	//info ($stats, true);
}

?>