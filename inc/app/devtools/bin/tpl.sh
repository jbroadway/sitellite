#!/bin/sh

ARGS=1
E_BADARGS=65

test $# -ne $ARGS && echo "Usage: `basename $0` SETNAME" && exit $E_BADARGS

mkdir $1
cd $1

mkdir pix

cat <<END > html.default.tpl
<xt:tpl version="1.0"><xt:doctype
	root="html"
	access="public"
	name="-//W3C//DTD XHTML 1.0 Transitional//EN"
	uri="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"
/>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title xt:content="string: Site Name - \${title}">Site Name</title>

	<xt:var name="makeMeta" />

	<link rel="stylesheet" type="text/css" href="\${site/prefix}/inc/html/$1/site.css" />

	<xt:var name="makeJavascript" />
</head>
<body onload="\${onload}" onunload="\${onunload}" onfocus="\${onfocus}" onblur="\${onblur}" onclick="\${onclick}"><a name="top"> </a>

<!-- wrapper -->
<div id="wrapper">

<!-- header -->
<div id="header">

	<h1>Site Name</h1>

</div>

<div id="breadcrumb">
	<xt:box name="sitellite/nav/breadcrumb">
		<p><a href="#">Home</a> / <a href="#">About Us</a> / Fake Page</p>
	</xt:box>
</div>

<table id="columns">
<tr>

<!-- left column -->
<td id="left">

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

	<xt:box name="cms/buttons" />

	<h1
		xt:content="title"
		xt:condition="php: not empty (object.title)">Fake Page</h1>

	<span xt:replace="body">
		<p></p>
	</span>

	<p align="right">
		<a href="#top"><xt:intl>Return to Top</xt:intl></a>
	</p>

</td>

<!-- right column -->
<td id="right">

	<xt:box name="sitellite/sidebar" position="right">
		<h1>Right Column</h1>
		<p>
			This is a sample sidebar element.  It will be replaced with actual content
			when the template is rendered by Sitellite.
		</p>
	</xt:box>

</td>

</tr>
</table>

<!-- footer -->
<div id="footer">

	<p>
		<xt:intl>Copyright</xt:intl>
		<xt:copy />
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
END

cat <<END > site.css
body {
	background-color: #fff;
	font: #444 12px Verdana, Helvetica, Arial, sans-serif;
	margin: 0px;
	padding: 0px;
}

td {
	font: #444 12px Verdana, Helvetica, Arial, sans-serif;
}

#wrapper {
	margin: 10px;
	padding: 10px;
	width: 800px;
}

#header {
	height: 40px;
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
END

cat <<END > config.ini.php
; <?php /* DO NOT ALTER THIS LINE, IT IS HERE FOR SECURITY REASONS

; This is your template set config file.  Most fields are optional, and those
; that are not so self explanatory have a comment above them.

; Only app_name among all these is actually required, but all are recommended.
set_name		= $1
description		= About $1...
author			= Me
copyright		= "Copyright (C) 2004, Me Inc."
license			= "http://www.opensource.org/licenses/index.php#PICK ONE!!!"
version			= "0.1 alpha \$Id\$"

; DO NOT ALTER THIS LINE, IT IS HERE FOR SECURITY REASONS */ ?>
END

cd ..

echo "Your template set is ready, sir."
