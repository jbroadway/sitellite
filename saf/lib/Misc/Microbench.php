<?php

/**
 * Microbench Lightweight Benchmarking Utility
 *
 * Usage:
 *
 * microbench ('optional comment');
 * sleep (5);
 * microbench ('optional comment');
 * sleep (4);
 * microbench ('optional comment');
 * 
 * // display results
 * microbench_display ();
 *
 * @package Misc
 */
function microbench ($tag = '', $calculate = false) {
	static $time = array ();

	if (! $calculate) {
		if (empty ($tag)) {
			$time[] = microtime ();
		} else {
			$time[] = array ($tag, microtime ());
		}
		return;

	} else {
		$prev = false;
		$out = array ();
		$total = 0.0;

		foreach ($time as $marker) {
			if (is_array ($marker)) {
				$tag = array_shift ($marker);
				$marker = array_shift ($marker);
				if (empty ($tag)) {
					$tag = '';
				}
			} else {
				$tag = '';
			}
			if ($prev) {
				$m = explode (' ', $marker);
				$m = ((float) $m[0] + (float) $m[1]);

				$d = explode (' ', $prev);
				$d = ((float) $d[0] + (float) $d[1]);
				$diff = sprintf ('%.16f', $m - $d);

				$total = ((float) $total + (float) $diff);

				$out[] = array ('marker' => $marker, 'diff' => $diff, 'tag' => $tag);
			} else {
				$out[] = array ('marker' => $marker, 'diff' => false, 'tag' => $tag);
			}
			$prev = $marker;
		}

		foreach ($out as $key => $mark) {
			if (! $mark['diff']) {
				$out[$key]['percent'] = '0';
				continue;
			}
			$out[$key]['percent'] = sprintf ('%.2f', $mark['diff'] / $total) * 100;
		}

		return array (
			'total' => sprintf ('%.16f', $total),
			'marks' => $out,
		);
	}
}


/**
 * @package Misc
 */
function microbench_display () {
	echo template_simple (
		'<table border="1" cellpadding="5">
			<tr>
				<th>#</th>
				<th>Comment</th>
				<th>Marker</th>
				<th>Difference</th>
				<th>Percent</th>
			</tr>
		{loop obj[marks]}
			<tr>
				<td style="font: 14px courier new">{loop/_index}</td>
				<td style="font: 14px courier new">{loop/tag}</td>
				<td style="font: 14px courier new">{loop/marker}</td>
				<td style="font: 14px courier new">{loop/diff}</td>
				<td style="font: 14px courier new" align="right">{loop/percent}%</td>
			</tr>
		{end loop}
			<tr>
				<th align="right" colspan="3">Total Elapsed Time</th>
				<td colspan="2" style="font: 14px courier new">{total}</td>
			</tr>
		</table>',
		microbench ('', true)
	);
}

?>