<?php

global $cgi, $session;

if (! empty ($cgi->sub2)) {
	header ('Location: ' . site_prefix () . '/index/cms-add-form?collection=myadm_report&sql_query='
		. urlencode ($cgi->sql));
	exit;
}

if ($cgi->sql) {

	$split = sql_split ($cgi->sql);

	foreach ($split as $q) {
		if (! is_array ($session->get ('history'))) {
			$session->append ('history', $q);
		} elseif (! in_array ($cgi->sql, $session->get ('history'))) {
			$session->append ('history', $q);
		}
	}
}

if ($cgi->history) {
	$cgi->sql = $cgi->history;
	$split = array ($cgi->sql);
}

if (! isset ($split)) {
	$split = array ();
}

page_title ( 'Database Manager - SQL Shell' );

echo template_simple ('<p><a href="{site/prefix}/index/myadm-app">Home</a></p>');

?>
<form method="post">
<p align="center">

<textarea name="sql" cols="60" rows="15"><?php

if (! empty ($cgi->sql)) {
	echo htmlentities ($cgi->sql);
}

?></textarea><br />

<select name="history">
	<option value="">Query History</option>
<?php

	foreach ($session->get ('history') as $option) {
		$show = htmlentities ($option);
		if (strlen ($show) > 72) {
			$show = substr ($show, 0, 69) . '...';
		}
		echo TAB . '<option value="' . htmlentities ($option) . '">' . $show . '</option>' . NEWLINE;
	}

?>
</select><br />

<input type="submit" name="sub1" value="Execute" />&nbsp;
<input type="submit" name="sub2" value="Create Report" />

</p>
</form>
<?php

foreach ($split as $sql) {

	if (! empty ($sql)) {
		$q = db_query ($sql);
		if ($q->execute ()) {
			$rows = $q->rows ();
			if ($rows > 0) {
				echo '<p><strong>' . $sql . '</strong></p>';
				echo '<a name="results"></a><p>' . $rows . ' rows</p>';
				echo '<table border="1" width="100%">';
				$headers = false;
				while ($row = $q->fetch ()) {
					if (! $headers) {
						echo '<tr>';
						foreach (array_keys (get_object_vars ($row)) as $value) {
							echo '<th>' . $value . '</th>';
						}
						echo '</tr>';
						$headers = true;
					}
					echo '<tr>';
					foreach (get_object_vars ($row) as $value) {
						if (empty ($value)) {
							echo '<td>&nbsp;</td>';
						} else {
							echo '<td>' . htmlentities ($value) . '</td>';
						}
					}
					echo '</tr>';
				}
				echo '</table>';
			} else {
				echo '<a name="results"></a><p>Query executed.</p>';
			}
		} else {
			echo '<a name="results"></a><p>Error: ' . $q->error () . '</p>';
		}
	}

}

//exit;

?>