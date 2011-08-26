<?php

global $cgi;
?>
<style type="text/css">

.tablesizer {
	margin-top: 25px;
	margin-bottom: 25px;
	border: 0px none;
}

.tablesizer td {
	border: 0px none;
	background-color: #fff;
	width: 22px;
	height: 22px;
	padding: 0px;
	text-align: center;
	vertical-align: middle;
}

.tablesizer a {
	display: block;
	width: 22px;
	height: 22px;
	text-decoration: none;
	border: 0px none;
	border-right: 1px solid #666;
	border-bottom: 1px solid #666;
	background-color: #fff;
}

.tablesizer a:hover {
	text-decoration: none;
	color: #333;
}

</style>

<script language="javascript" type="text/javascript">

function tablesizer_select (x, y) {
	n = x + 'x' + y;
	if (opener && ! opener.closed) {
		opener.document.getElementById ('<?php echo $cgi->ifname; ?>').xed_insert_table ('<?php echo $cgi->ifname; ?>', n);
	} else {
		alert ('Hey, where did my parent go?');
	}
	window.close ();
	return false;
}

function tablesizer_highlight (xx, yy) {
	for (x = 1; x <= xx; x++) {
		for (y = 1; y <= yy; y++) {
			document.getElementById ('tablesizer-' + x + 'x' + y).style.backgroundColor = '#a9b7c4';
		}
	}
}

function tablesizer_off (xx, yy) {
	for (x = 1; x <= xx; x++) {
		for (y = 1; y <= yy; y++) {
			document.getElementById ('tablesizer-' + x + 'x' + y).style.backgroundColor = '#fff';
		}
	}
}

</script>

<table cellspacing="0" class="tablesizer">
	<tr>
		<td align="center" style="border-right: 1px solid #666">1</td>
		<td style="border-top: 1px solid #666"><a href="#" id="tablesizer-1x1" onclick="tablesizer_select (1, 1); return false" onmouseover="tablesizer_highlight (1, 1)" onmouseout="tablesizer_off (1, 1)">&nbsp;</a></td>
		<td style="border-top: 1px solid #666"><a href="#" id="tablesizer-1x2" onclick="tablesizer_select (1, 2); return false" onmouseover="tablesizer_highlight (1, 2)" onmouseout="tablesizer_off (1, 2)">&nbsp;</a></td>
		<td style="border-top: 1px solid #666"><a href="#" id="tablesizer-1x3" onclick="tablesizer_select (1, 3); return false" onmouseover="tablesizer_highlight (1, 3)" onmouseout="tablesizer_off (1, 3)">&nbsp;</a></td>
		<td style="border-top: 1px solid #666"><a href="#" id="tablesizer-1x4" onclick="tablesizer_select (1, 4); return false" onmouseover="tablesizer_highlight (1, 4)" onmouseout="tablesizer_off (1, 4)">&nbsp;</a></td>
		<td style="border-top: 1px solid #666"><a href="#" id="tablesizer-1x5" onclick="tablesizer_select (1, 5); return false" onmouseover="tablesizer_highlight (1, 5)" onmouseout="tablesizer_off (1, 5)">&nbsp;</a></td>
		<td style="border-top: 1px solid #666"><a href="#" id="tablesizer-1x6" onclick="tablesizer_select (1, 6); return false" onmouseover="tablesizer_highlight (1, 6)" onmouseout="tablesizer_off (1, 6)">&nbsp;</a></td>
		<td style="border-top: 1px solid #666"><a href="#" id="tablesizer-1x7" onclick="tablesizer_select (1, 7); return false" onmouseover="tablesizer_highlight (1, 7)" onmouseout="tablesizer_off (1, 7)">&nbsp;</a></td>
		<td style="border-top: 1px solid #666"><a href="#" id="tablesizer-1x8" onclick="tablesizer_select (1, 8); return false" onmouseover="tablesizer_highlight (1, 8)" onmouseout="tablesizer_off (1, 8)">&nbsp;</a></td>
		<td style="border-top: 1px solid #666"><a href="#" id="tablesizer-1x9" onclick="tablesizer_select (1, 9); return false" onmouseover="tablesizer_highlight (1, 9)" onmouseout="tablesizer_off (1, 9)">&nbsp;</a></td>
		<td style="border-top: 1px solid #666"><a href="#" id="tablesizer-1x10" onclick="tablesizer_select (1, 10); return false" onmouseover="tablesizer_highlight (1, 10)" onmouseout="tablesizer_off (1, 10)">&nbsp;</a></td>
	</tr>
	<tr>
		<td align="center" style="border-right: 1px solid #666">2</td>
		<td><a href="#" id="tablesizer-2x1" onclick="tablesizer_select (2, 1); return false" onmouseover="tablesizer_highlight (2, 1)" onmouseout="tablesizer_off (2, 1)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-2x2" onclick="tablesizer_select (2, 2); return false" onmouseover="tablesizer_highlight (2, 2)" onmouseout="tablesizer_off (2, 2)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-2x3" onclick="tablesizer_select (2, 3); return false" onmouseover="tablesizer_highlight (2, 3)" onmouseout="tablesizer_off (2, 3)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-2x4" onclick="tablesizer_select (2, 4); return false" onmouseover="tablesizer_highlight (2, 4)" onmouseout="tablesizer_off (2, 4)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-2x5" onclick="tablesizer_select (2, 5); return false" onmouseover="tablesizer_highlight (2, 5)" onmouseout="tablesizer_off (2, 5)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-2x6" onclick="tablesizer_select (2, 6); return false" onmouseover="tablesizer_highlight (2, 6)" onmouseout="tablesizer_off (2, 6)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-2x7" onclick="tablesizer_select (2, 7); return false" onmouseover="tablesizer_highlight (2, 7)" onmouseout="tablesizer_off (2, 7)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-2x8" onclick="tablesizer_select (2, 8); return false" onmouseover="tablesizer_highlight (2, 8)" onmouseout="tablesizer_off (2, 8)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-2x9" onclick="tablesizer_select (2, 9); return false" onmouseover="tablesizer_highlight (2, 9)" onmouseout="tablesizer_off (2, 9)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-2x10" onclick="tablesizer_select (2, 10); return false" onmouseover="tablesizer_highlight (2, 10)" onmouseout="tablesizer_off (2, 10)">&nbsp;</a></td>
	</tr>
	<tr>
		<td align="center" style="border-right: 1px solid #666">3</td>
		<td><a href="#" id="tablesizer-3x1" onclick="tablesizer_select (3, 1); return false" onmouseover="tablesizer_highlight (3, 1)" onmouseout="tablesizer_off (3, 1)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-3x2" onclick="tablesizer_select (3, 2); return false" onmouseover="tablesizer_highlight (3, 2)" onmouseout="tablesizer_off (3, 2)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-3x3" onclick="tablesizer_select (3, 3); return false" onmouseover="tablesizer_highlight (3, 3)" onmouseout="tablesizer_off (3, 3)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-3x4" onclick="tablesizer_select (3, 4); return false" onmouseover="tablesizer_highlight (3, 4)" onmouseout="tablesizer_off (3, 4)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-3x5" onclick="tablesizer_select (3, 5); return false" onmouseover="tablesizer_highlight (3, 5)" onmouseout="tablesizer_off (3, 5)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-3x6" onclick="tablesizer_select (3, 6); return false" onmouseover="tablesizer_highlight (3, 6)" onmouseout="tablesizer_off (3, 6)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-3x7" onclick="tablesizer_select (3, 7); return false" onmouseover="tablesizer_highlight (3, 7)" onmouseout="tablesizer_off (3, 7)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-3x8" onclick="tablesizer_select (3, 8); return false" onmouseover="tablesizer_highlight (3, 8)" onmouseout="tablesizer_off (3, 8)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-3x9" onclick="tablesizer_select (3, 9); return false" onmouseover="tablesizer_highlight (3, 9)" onmouseout="tablesizer_off (3, 9)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-3x10" onclick="tablesizer_select (3, 10); return false" onmouseover="tablesizer_highlight (3, 10)" onmouseout="tablesizer_off (3, 10)">&nbsp;</a></td>
	</tr>
	<tr>
		<td align="center" style="border-right: 1px solid #666">4</td>
		<td><a href="#" id="tablesizer-4x1" onclick="tablesizer_select (4, 1); return false" onmouseover="tablesizer_highlight (4, 1)" onmouseout="tablesizer_off (4, 1)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-4x2" onclick="tablesizer_select (4, 2); return false" onmouseover="tablesizer_highlight (4, 2)" onmouseout="tablesizer_off (4, 2)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-4x3" onclick="tablesizer_select (4, 3); return false" onmouseover="tablesizer_highlight (4, 3)" onmouseout="tablesizer_off (4, 3)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-4x4" onclick="tablesizer_select (4, 4); return false" onmouseover="tablesizer_highlight (4, 4)" onmouseout="tablesizer_off (4, 4)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-4x5" onclick="tablesizer_select (4, 5); return false" onmouseover="tablesizer_highlight (4, 5)" onmouseout="tablesizer_off (4, 5)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-4x6" onclick="tablesizer_select (4, 6); return false" onmouseover="tablesizer_highlight (4, 6)" onmouseout="tablesizer_off (4, 6)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-4x7" onclick="tablesizer_select (4, 7); return false" onmouseover="tablesizer_highlight (4, 7)" onmouseout="tablesizer_off (4, 7)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-4x8" onclick="tablesizer_select (4, 8); return false" onmouseover="tablesizer_highlight (4, 8)" onmouseout="tablesizer_off (4, 8)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-4x9" onclick="tablesizer_select (4, 9); return false" onmouseover="tablesizer_highlight (4, 9)" onmouseout="tablesizer_off (4, 9)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-4x10" onclick="tablesizer_select (4, 10); return false" onmouseover="tablesizer_highlight (4, 10)" onmouseout="tablesizer_off (4, 10)">&nbsp;</a></td>
	</tr>
	<tr>
		<td align="center" style="border-right: 1px solid #666">5</td>
		<td><a href="#" id="tablesizer-5x1" onclick="tablesizer_select (5, 1); return false" onmouseover="tablesizer_highlight (5, 1)" onmouseout="tablesizer_off (5, 1)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-5x2" onclick="tablesizer_select (5, 2); return false" onmouseover="tablesizer_highlight (5, 2)" onmouseout="tablesizer_off (5, 2)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-5x3" onclick="tablesizer_select (5, 3); return false" onmouseover="tablesizer_highlight (5, 3)" onmouseout="tablesizer_off (5, 3)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-5x4" onclick="tablesizer_select (5, 4); return false" onmouseover="tablesizer_highlight (5, 4)" onmouseout="tablesizer_off (5, 4)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-5x5" onclick="tablesizer_select (5, 5); return false" onmouseover="tablesizer_highlight (5, 5)" onmouseout="tablesizer_off (5, 5)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-5x6" onclick="tablesizer_select (5, 6); return false" onmouseover="tablesizer_highlight (5, 6)" onmouseout="tablesizer_off (5, 6)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-5x7" onclick="tablesizer_select (5, 7); return false" onmouseover="tablesizer_highlight (5, 7)" onmouseout="tablesizer_off (5, 7)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-5x8" onclick="tablesizer_select (5, 8); return false" onmouseover="tablesizer_highlight (5, 8)" onmouseout="tablesizer_off (5, 8)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-5x9" onclick="tablesizer_select (5, 9); return false" onmouseover="tablesizer_highlight (5, 9)" onmouseout="tablesizer_off (5, 9)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-5x10" onclick="tablesizer_select (5, 10); return false" onmouseover="tablesizer_highlight (5, 10)" onmouseout="tablesizer_off (5, 10)">&nbsp;</a></td>
	</tr>
	<tr>
		<td align="center" style="border-right: 1px solid #666">6</td>
		<td><a href="#" id="tablesizer-6x1" onclick="tablesizer_select (6, 1); return false" onmouseover="tablesizer_highlight (6, 1)" onmouseout="tablesizer_off (6, 1)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-6x2" onclick="tablesizer_select (6, 2); return false" onmouseover="tablesizer_highlight (6, 2)" onmouseout="tablesizer_off (6, 2)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-6x3" onclick="tablesizer_select (6, 3); return false" onmouseover="tablesizer_highlight (6, 3)" onmouseout="tablesizer_off (6, 3)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-6x4" onclick="tablesizer_select (6, 4); return false" onmouseover="tablesizer_highlight (6, 4)" onmouseout="tablesizer_off (6, 4)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-6x5" onclick="tablesizer_select (6, 5); return false" onmouseover="tablesizer_highlight (6, 5)" onmouseout="tablesizer_off (6, 5)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-6x6" onclick="tablesizer_select (6, 6); return false" onmouseover="tablesizer_highlight (6, 6)" onmouseout="tablesizer_off (6, 6)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-6x7" onclick="tablesizer_select (6, 7); return false" onmouseover="tablesizer_highlight (6, 7)" onmouseout="tablesizer_off (6, 7)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-6x8" onclick="tablesizer_select (6, 8); return false" onmouseover="tablesizer_highlight (6, 8)" onmouseout="tablesizer_off (6, 8)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-6x9" onclick="tablesizer_select (6, 9); return false" onmouseover="tablesizer_highlight (6, 9)" onmouseout="tablesizer_off (6, 9)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-6x10" onclick="tablesizer_select (6, 10); return false" onmouseover="tablesizer_highlight (6, 10)" onmouseout="tablesizer_off (6, 10)">&nbsp;</a></td>
	</tr>
	<tr>
		<td align="center" style="border-right: 1px solid #666">7</td>
		<td><a href="#" id="tablesizer-7x1" onclick="tablesizer_select (7, 1); return false" onmouseover="tablesizer_highlight (7, 1)" onmouseout="tablesizer_off (7, 1)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-7x2" onclick="tablesizer_select (7, 2); return false" onmouseover="tablesizer_highlight (7, 2)" onmouseout="tablesizer_off (7, 2)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-7x3" onclick="tablesizer_select (7, 3); return false" onmouseover="tablesizer_highlight (7, 3)" onmouseout="tablesizer_off (7, 3)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-7x4" onclick="tablesizer_select (7, 4); return false" onmouseover="tablesizer_highlight (7, 4)" onmouseout="tablesizer_off (7, 4)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-7x5" onclick="tablesizer_select (7, 5); return false" onmouseover="tablesizer_highlight (7, 5)" onmouseout="tablesizer_off (7, 5)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-7x6" onclick="tablesizer_select (7, 6); return false" onmouseover="tablesizer_highlight (7, 6)" onmouseout="tablesizer_off (7, 6)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-7x7" onclick="tablesizer_select (7, 7); return false" onmouseover="tablesizer_highlight (7, 7)" onmouseout="tablesizer_off (7, 7)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-7x8" onclick="tablesizer_select (7, 8); return false" onmouseover="tablesizer_highlight (7, 8)" onmouseout="tablesizer_off (7, 8)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-7x9" onclick="tablesizer_select (7, 9); return false" onmouseover="tablesizer_highlight (7, 9)" onmouseout="tablesizer_off (7, 9)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-7x10" onclick="tablesizer_select (7, 10); return false" onmouseover="tablesizer_highlight (7, 10)" onmouseout="tablesizer_off (7, 10)">&nbsp;</a></td>
	</tr>
	<tr>
		<td align="center" style="border-right: 1px solid #666">8</td>
		<td><a href="#" id="tablesizer-8x1" onclick="tablesizer_select (8, 1); return false" onmouseover="tablesizer_highlight (8, 1)" onmouseout="tablesizer_off (8, 1)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-8x2" onclick="tablesizer_select (8, 2); return false" onmouseover="tablesizer_highlight (8, 2)" onmouseout="tablesizer_off (8, 2)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-8x3" onclick="tablesizer_select (8, 3); return false" onmouseover="tablesizer_highlight (8, 3)" onmouseout="tablesizer_off (8, 3)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-8x4" onclick="tablesizer_select (8, 4); return false" onmouseover="tablesizer_highlight (8, 4)" onmouseout="tablesizer_off (8, 4)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-8x5" onclick="tablesizer_select (8, 5); return false" onmouseover="tablesizer_highlight (8, 5)" onmouseout="tablesizer_off (8, 5)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-8x6" onclick="tablesizer_select (8, 6); return false" onmouseover="tablesizer_highlight (8, 6)" onmouseout="tablesizer_off (8, 6)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-8x7" onclick="tablesizer_select (8, 7); return false" onmouseover="tablesizer_highlight (8, 7)" onmouseout="tablesizer_off (8, 7)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-8x8" onclick="tablesizer_select (8, 8); return false" onmouseover="tablesizer_highlight (8, 8)" onmouseout="tablesizer_off (8, 8)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-8x9" onclick="tablesizer_select (8, 9); return false" onmouseover="tablesizer_highlight (8, 9)" onmouseout="tablesizer_off (8, 9)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-8x10" onclick="tablesizer_select (8, 10); return false" onmouseover="tablesizer_highlight (8, 10)" onmouseout="tablesizer_off (8, 10)">&nbsp;</a></td>
	</tr>
	<tr>
		<td align="center" style="border-right: 1px solid #666">9</td>
		<td><a href="#" id="tablesizer-9x1" onclick="tablesizer_select (9, 1); return false" onmouseover="tablesizer_highlight (9, 1)" onmouseout="tablesizer_off (9, 1)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-9x2" onclick="tablesizer_select (9, 2); return false" onmouseover="tablesizer_highlight (9, 2)" onmouseout="tablesizer_off (9, 2)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-9x3" onclick="tablesizer_select (9, 3); return false" onmouseover="tablesizer_highlight (9, 3)" onmouseout="tablesizer_off (9, 3)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-9x4" onclick="tablesizer_select (9, 4); return false" onmouseover="tablesizer_highlight (9, 4)" onmouseout="tablesizer_off (9, 4)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-9x5" onclick="tablesizer_select (9, 5); return false" onmouseover="tablesizer_highlight (9, 5)" onmouseout="tablesizer_off (9, 5)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-9x6" onclick="tablesizer_select (9, 6); return false" onmouseover="tablesizer_highlight (9, 6)" onmouseout="tablesizer_off (9, 6)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-9x7" onclick="tablesizer_select (9, 7); return false" onmouseover="tablesizer_highlight (9, 7)" onmouseout="tablesizer_off (9, 7)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-9x8" onclick="tablesizer_select (9, 8); return false" onmouseover="tablesizer_highlight (9, 8)" onmouseout="tablesizer_off (9, 8)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-9x9" onclick="tablesizer_select (9, 9); return false" onmouseover="tablesizer_highlight (9, 9)" onmouseout="tablesizer_off (9, 9)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-9x10" onclick="tablesizer_select (9, 10); return false" onmouseover="tablesizer_highlight (9, 10)" onmouseout="tablesizer_off (9, 10)">&nbsp;</a></td>
	</tr>
	<tr>
		<td align="center" style="border-right: 1px solid #666">10</td>
		<td><a href="#" id="tablesizer-10x1" onclick="tablesizer_select (10, 1); return false" onmouseover="tablesizer_highlight (10, 1)" onmouseout="tablesizer_off (10, 1)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-10x2" onclick="tablesizer_select (10, 2); return false" onmouseover="tablesizer_highlight (10, 2)" onmouseout="tablesizer_off (10, 2)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-10x3" onclick="tablesizer_select (10, 3); return false" onmouseover="tablesizer_highlight (10, 3)" onmouseout="tablesizer_off (10, 3)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-10x4" onclick="tablesizer_select (10, 4); return false" onmouseover="tablesizer_highlight (10, 4)" onmouseout="tablesizer_off (10, 4)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-10x5" onclick="tablesizer_select (10, 5); return false" onmouseover="tablesizer_highlight (10, 5)" onmouseout="tablesizer_off (10, 5)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-10x6" onclick="tablesizer_select (10, 6); return false" onmouseover="tablesizer_highlight (10, 6)" onmouseout="tablesizer_off (10, 6)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-10x7" onclick="tablesizer_select (10, 7); return false" onmouseover="tablesizer_highlight (10, 7)" onmouseout="tablesizer_off (10, 7)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-10x8" onclick="tablesizer_select (10, 8); return false" onmouseover="tablesizer_highlight (10, 8)" onmouseout="tablesizer_off (10, 8)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-10x9" onclick="tablesizer_select (10, 9); return false" onmouseover="tablesizer_highlight (10, 9)" onmouseout="tablesizer_off (10, 9)">&nbsp;</a></td>
		<td><a href="#" id="tablesizer-10x10" onclick="tablesizer_select (10, 10); return false" onmouseover="tablesizer_highlight (10, 10)" onmouseout="tablesizer_off (10, 10)">&nbsp;</a></td>
	</tr>
	<tr>
		<td align="center">&nbsp;</td>
		<td align="center">1</td>
		<td align="center">2</td>
		<td align="center">3</td>
		<td align="center">4</td>
		<td align="center">5</td>
		<td align="center">6</td>
		<td align="center">7</td>
		<td align="center">8</td>
		<td align="center">9</td>
		<td align="center">10</td>
	</tr>
</table>
