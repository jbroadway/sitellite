<?php

loader_import('saf.File');

// settings stuff
global $cgi;

if (empty ($cgi->format)) {
	$cgi->format = 'html';
}

$data = array (
	'location' => $cgi->location,
	'up' => false,
	'err' => false,
	'subfolders' => array (),
	'images' => array (),
);

// get all the data
page_title (intl_get ('Add a Template Set'));

$filename;

if (empty ($cgi->filename)) {
	$filename = 'New_Template_Set';
} else {
	$filename = $cgi->file;
}

if ($cgi->file) {
	if (@file_exists ('inc/html/' . $filename) && @is_dir ('inc/html/' . $filename)) {
		$data['err'] = intl_get ('The template set name you have chosen already exists.  Please choose another.');
	} elseif (preg_match ('/^.*[^a-zA-Z0-9\. _-]/', $filename)) {
		$data['err'] = intl_get ('Your file name contains invalid characters.  Allowed characters are') . ': A-Z 0-9 . _ - and space.';
	} else {
		//make the set
		
		make_set($cgi->file);
		//return to template select index
		header ('Location: ' . site_prefix () . '/index/sitetemplate-templateselect-action?set_name=' . $cgi->file);
	}
}

// show me the money
echo template_simple ('newtpl.spt', $data);

function make_set ($set_name) {
	
	if (! @is_writable ('inc/html')) {
		$data['err'] = 'The template folder is not writable, please contact the site admin.';

	} else {
		
		umask (0000);
		mkdir ('inc/html/' . $set_name, 0777);
		mkdir ('inc/html/' . $set_name . '/pix', 0777);
		
		//variables for all files written to
		$html_default_tpl; $site_css; $config_ini_php; $modes_php;
		
		//populate the variables
		$html_default_tpl = <<<END
<xt:tpl version="1.0"><xt:doctype
	root="html"
	access="public"
	name="-//W3C//DTD XHTML 1.0 Transitional//EN"
	uri="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"
/>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title xt:content="string: Site Name - \${head_title}">Site Name</title>

	<!-- note the space-slash (' /') at the end of the following tag.  this
	     tells the XML parser that this tag doesn't have a matching closing
	     tag.  this syntax is required to make your templates XHTML and XT
	     compatible -->
	<link rel="stylesheet" type="text/css" href="\${site/prefix}/inc/html/$set_name/site.css" />
	<script language="javascript" type="text/javascript" src="${site/prefix}/js/jquery-1.4.2.min.js"></script>
</head>
<body><a name="top"> </a>

<!-- wrapper -->
<div id="wrapper">

<!-- header -->
<div id="header">

	<h1>Site Name</h1>

</div>

<!-- the default navigation boxes are in the inc/app/sitellite/boxes/nav folder -->
<div id="breadcrumb">
	<xt:box name="sitellite/nav/breadcrumb">
		<p><a href="#">Home</a> / <a href="#">About Us</a> / Fake Page</p>
	</xt:box>
</div>

<table id="columns">
<tr>

<!-- left column -->
<td id="left">

	<!-- the sidebar box places a column of sidebars at this location.
	     note that you can put sample data between the xt:box tags to
	     make your templates previewable directly in the web browser,
	     or even in editors like Dreamweaver and Golive -->
	<xt:box name="sitellite/sidebar" position="left">
		<h1>Fake Menu</h1>
		<ul>
			<li><a href="#">About Us</a></li>
			<li><a href="#">Services</a></li>
			<li><a href="#">Support</a></li>
			<li><a href="#">Contact Us</a></li>
		</ul>
	</xt:box>

</td>

<!-- centre column -->
<td id="centre">

	<!-- this box inclusion embeds the page editing buttons into your pages -->
	<xt:box name="cms/buttons" />

	<!-- display the page title, if there is one -->
	<h1
		xt:content="title"
		xt:condition="php: not empty (object.title)">Fake Page</h1>

	<!-- replace this span tag with the page body contents -->
	<span xt:replace="body">
		<p></p>
	</span>

	<!-- the xt:intl tag marks its contents as translateable text -->
	<p align="right">
		<a href="#top"><xt:intl>Return to Top</xt:intl></a>
	</p>

</td>

<!-- right column -->
<td id="right">

	<xt:box name="sitellite/sidebar" position="right">
		<h1>Right Column</h1>
		<p>
			This is a sample sidebar element.  It will be replaced with
			actual content when the template is rendered by Sitellite.
		</p>
	</xt:box>

</td>

</tr>
</table>

<!-- footer -->
<div id="footer">

	<p>
		<xt:intl>Copyright</xt:intl>

		<!-- the ch: tags replace HTML entities such as 'nbsp' and 'copy',
		     since XT templates are XML documents which don't know about
		     HTML's special entities -->
		<ch:copy />

		<!-- you can include basic PHP statements in your templates via the
		     following syntax -->
		<span xt:replace="php: date ('Y')">2004</span>

		Site Name.<br />
		<xt:intl>All rights reserved.</xt:intl><br />
		<a href="http://www.sitellite.org/" target="_blank"><xt:intl>Powered by Sitellite CMS</xt:intl></a>
	</p>

</div>

</div>

</body>
</html>
</xt:tpl>
END;
		
		$site_css = <<<END
xt\:comment, xt\:note {
	display: none;
}

ch\:nbsp {
	padding: .5em;
}

body {
	background-color: #fff;
	color: #444;
	font: 12px Verdana, Helvetica, Arial, sans-serif;
	margin: 0px;
	padding: 0px;
}

td {
	color: #444;
	font: 12px Verdana, Helvetica, Arial, sans-serif;
}

#wrapper {
	margin: 10px;
	padding: 10px;
	width: 800px;
}

#header {
	height: 47px;
	padding: 10px;
	padding-bottom: 0px;
	margin: 0px;
	background-color: #a00;
}

#header h1 {
	font-variant: small-caps;
	font-size: 28px;
	color: #fff;
}

#breadcrumb {
	height: 20px;
}

#columns {
	border: 0px none;
}

#left {
	width: 160px;
	padding-right: 5px;
	vertical-align: top;
}

#centre {
	padding-left: 5px;
	padding-right: 5px;
	border-left: 1px solid #aaa;
	border-right: 1px solid #aaa;
	vertical-align: top;
}

#right {
	width: 170px;
	padding-left: 5px;
	vertical-align: top;
}

#footer {
	clear: both;
	margin-top: 5px;
	border-top: 1px solid #aaa;
}

a {
	color: #a00;
	text-decoration: none;
}

a:hover {
	text-decoration: underline;
}

h1 {
	font-size: 18px;
}

h2 {
	font-size: 14px;
}

h3 {
	font-weight: normal;
}

span.highlighted {
	background-color: #ff0;
	padding: 2px;
	padding-bottom: 0px;
}
END;

		$config_ini_php = <<<END
; <?php /* DO NOT ALTER THIS LINE, IT IS HERE FOR SECURITY REASONS

; This is your template set config file.  Most fields are optional, and those
; that are not so self explanatory have a comment above them.

; Only set_name among all these is actually required, but all are recommended.
set_name		= $set_name
description		= About $set_name ...
author			= Me
copyright		= "Copyright (C) 2004, Me Inc."
license			= "http://www.opensource.org/licenses/index.php#PICK ONE!!!"
version			= "0.1 alpha"

; DO NOT ALTER THIS LINE, IT IS HERE FOR SECURITY REASONS */ ?>
END;
							     
		$modes_php = <<<END
; <?php /*

[html]

content_type	= text/html
filter 1		= "final: cgi_rewrite_filter"
filter 2		= "body: saf.Misc.Search"
;filter 3		= "body: siteglossary.Terms"

; */ ?>
END;
							     
		//write the files
		file_overwrite ('inc/html/' . $set_name . '/html.default.tpl', $html_default_tpl);
		file_overwrite ('inc/html/' . $set_name . '/site.css', $site_css);
		file_overwrite ('inc/html/' . $set_name . '/config.ini.php', $config_ini_php);
		file_overwrite ('inc/html/' . $set_name . '/modes.php', $modes_php);
		
	}
}

?>