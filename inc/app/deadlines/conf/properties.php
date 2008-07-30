<?php

page_add_style ('

/* SimpleCal calendar style */

table.simplecal {
	background-color: #fff;
}

table.simplecal a {
	font-weight: bold;
	color: #369 !important;
}

table.simplecal a:hover {
	text-decoration: underline;
}

table.simplecal td.previous-month {
	border: 1px solid #ddd;
	border-right: 0px none;
	text-align: left;
	padding-left: 3px;
}

table.simplecal td.previous-month:hover {
	/*border: 1px solid #ccc;*/
}

table.simplecal td.next-month {
	border: 1px solid #ddd;
	border-left: 0px none;
	text-align: right;
	padding-right: 3px;
}

table.simplecal td.next-month:hover {
	/*border: 1px solid #ccc;*/
}

table.simplecal td.current-month {
	font-weight: bold;
	color: #000;
	font-size: 16px;
	height: 40px;
	vertical-align: middle;
	border-top: 1px solid #ddd;
	border-bottom: 1px solid #ddd;
}

table.simplecal tr.day-headings td {
	color: #000;
	background-color: #cde;
	font-weight: bold;
	width: 20px;
	padding: 3px;
	/* padding-bottom: 0px; */
}

table.simplecal td.day {
	background-color: #fff;
	border: 1px solid #ddd;
	color: #000;
	padding-bottom: 0px;
	height: 75px;
	width: 14%;
}

table.simplecal td.weekend-day {
	background-color: #eee;
	border: 1px solid #ddd;
	color: #000;
	padding-bottom: 0px;
	height: 75px;
	width: 14%;
}

table.simplecal td.inactive {
	border: 1px solid #eee;
	padding-bottom: 0px;
	width: 14%;
	height: 75px;
}

table.simplecal td.current-day {
	color: #000;
	border: 1px solid #cde;
	background-color: #cde;
	padding-bottom: 0px;
	width: 14%;
	height: 75px;
}

table.simplecal span.day-date {
	height: 100%;
	width: 20px;
	vertical-align: top;
	display: block;
	float: left;
	font-weight: bold;
}

table.simplecal a.link {
	font-weight: normal;
}

table.simplecal a.link:hover {
	text-decoration: underline;
}

table.simplecal a.link-important {
	color: #b00 !important;
	font-weight: bold;
}

table.simplecal a.link-important:hover {
	text-decoration: underline;
}

');

?>
