<html>
<head>
<title>{$title}</title>
<link rel="stylesheet" type="text/css" href="{$subdir}media/style.css">
</head>
<body>

<table border="0" cellspacing="0" cellpadding="0" height="48" width="100%">
  <tr>
    <td class="header_top">
      <span class="header_logo"><a href="http://www.sitellite.org/"><img src="{$subdir}media/sitellite.gif" alt="Sitellite Community Web Site" title="Sitellite Community Web Site" border="0" /></a></span>
      Sitellite Application Framework
    </td>
  </tr>
  <tr><td class="header_line"><img src="{$subdir}media/empty.png" width="1" height="1" border="0" alt=""  /></td></tr>
  <tr>
    <td class="header_menu">
          <!-- Package: {$package}
          &nbsp; &nbsp; &nbsp; &nbsp; -->
  		  <a href="{$subdir}classtrees_{$package}.html" class="menu">Class Tree</a>
  		  &nbsp; &nbsp; &nbsp; &nbsp;
		  <a href="{$subdir}elementindex_{$package}.html" class="menu">Index</a>
		  &nbsp; &nbsp; &nbsp; &nbsp;
		  <a href="{$subdir}elementindex.html" class="menu">All Elements</a>
    </td>
  </tr>
  <tr><td class="header_line"><img src="{$subdir}media/empty.png" width="1" height="1" border="0" alt=""  /></td></tr>
</table>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="2" class="breadcrumb">
      You are here: <a href="/docs/">SAF</a>
      {if $hasel}
        {if $package != 'saf'}
          / <a href="/docs/li_{$package}.html">{$package}</a>
        {/if}
        / {$class_name}
      {/if}
      {if ! $hasel}
        {if $package != 'saf'}
          / {$package}
        {/if}
      {/if}
    </td>
  </tr>
  <tr valign="top">
    <td width="175" class="menu">
{if count($ric)}
	<div id="ric">
		{section name=ric loop=$ric}
			<p><a href="{$subdir}{$ric[ric].file}">{$ric[ric].name}</a></p>
		{/section}
	</div>
{/if}
{if $hastodos}
	<div id="todolist">
			<p><a href="{$subdir}{$todolink}">Todo List</a></p>
	</div>
{/if}

<!-- h2>Search</h2>
<form method="post" action="/index/sitesearch-app">
<p>
  <input type="text" name="query" />
  <input type="hidden" name="ctype" value="SAF Docs" />
  <input type="hidden" name="show_types" value="yes" />
  <input type="hidden" name="domains" value="www.sitellite.org" />
  <input type="hidden" name="show_domains" value="yes" />
  <input type="submit" value="Go" />
</p>
</form -->

      <h2>Packages</h2>
      <p>{section name=packagelist loop=$packageindex}
        <a href="{$subdir}{$packageindex[packagelist].link}">{$packageindex[packagelist].title}</a><br />
      {/section}</p>
{if $tutorials}
		<h2>Tutorials/Manuals</h2>
		<p>{if $tutorials.pkg}
			<strong>Package-level:</strong>
			{section name=ext loop=$tutorials.pkg}
				{$tutorials.pkg[ext]}
			{/section}
		{/if}
		{if $tutorials.cls}
			<strong>Class-level:</strong>
			{section name=ext loop=$tutorials.cls}
				{$tutorials.cls[ext]}
			{/section}
		{/if}
		{if $tutorials.proc}
			<strong>Procedural-level:</strong>
			{section name=ext loop=$tutorials.proc}
				{$tutorials.proc[ext]}
			{/section}
		{/if}</p>
{/if}
      {if !$noleftindex}{assign var="noleftindex" value=false}{/if}
      {if !$noleftindex}

      {if $compiledclassindex}
      <h2>Classes</h2>
      <p>{eval var=$compiledclassindex}</p>
      {/if}

      {if $compiledfileindex}
      <h2>Files</h2>
      <p>{eval var=$compiledfileindex}</p>
      {/if}

      {/if}
    </td>
    <td>
      <table cellpadding="10" cellspacing="0" width="100%" border="0"><tr><td valign="top">

{if !$hasel}{assign var="hasel" value=false}{/if}
{if $hasel}

<div style="float: right">
<script type="text/javascript"><!--
google_ad_client = "pub-0776261361164405";
google_alternate_ad_url = "http://www.sitellite.org/adsense/125x125.html";
google_ad_width = 125;
google_ad_height = 125;
google_ad_format = "125x125_as";
google_ad_channel ="5965840350";
google_color_border = "A9B7C4";
google_color_bg = "FFFFFF";
google_color_link = "000000";
google_color_url = "EE9911";
google_color_text = "000000";
//--></script>
<script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</div>

<h1>{$eltype|capitalize}: {$class_name}</h1>
Source Location: {$source_location}<br /><br clear="all" />
{/if}