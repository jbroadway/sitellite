<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>{$title}</title>
	<link rel="stylesheet" type="text/css" id="layout" href="{$subdir}media/layout.css" media="screen">
	<link rel="stylesheet" type="text/css" href="{$subdir}media/style.css" media="all">
	<link rel="stylesheet" type="text/css" href="{$subdir}media/print.css" media="print">
</head>

<body topmargin="0" leftmargin="0" marginheight="0" marginwidth="0" bgcolor="#ffffff" text="#000000" link="#006600" alink="#cccc00" vlink="#003300">

<div id="header">
	<div id="header1">
		<div id="logo">
			<a href="http://pear.php.net/"><img src="{$subdir}media/pearsmall.gif" border="0" width="104" height="50" alt="PEAR"></a>
		</div>
		<h1 class="right">Documentation for: {$package}</h1>
		<div id="navlinks" class="right">
			<span id="docfiles">
{if count($ric)}
		{section name=ric loop=$ric}
			<a href="{$subdir}{$ric[ric].file}">{$ric[ric].name}</a> |
		{/section}
{/if}
{if $hastodos}
			 <a href="{$subdir}{$todolink}">Todo List</a>
{/if}
		</span>
		</div>
	</div>
	<div id="header2">
		<div id="indexes">
			[ <a href="{$subdir}classtrees_{$package}.html">Class Tree: {$package}</a> ]
			[ <a href="{$subdir}elementindex_{$package}.html">Index: {$package}</a> ]
			[ <a href="{$subdir}elementindex.html">All elements</a> ]
		</div>
		<form>
			View Package:
			<select class="package-selector" onchange="window.location=this[selectedIndex].value">
		{section name=packagelist loop=$packageindex}
		<option value="{$subdir}{$packageindex[packagelist].link}">{$packageindex[packagelist].title}</option>
		{/section}
			</select>
		</form>
	</div>
</div>


<div id="content">
	{if !$hasel}{assign var="hasel" value=false}{/if}
	{if $hasel}
	<h1>{$eltype|capitalize}: {$class_name}</h1>
	<p style="margin: 0px;">Source Location: {$source_location}</p>
	{/if}
