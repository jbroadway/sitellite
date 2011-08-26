<html>
	<head>
	<title>SITELLITE content server 5</title>
	<link rel="stylesheet" type="text/css" href="${site/prefix}/inc/html/admin/site.css" />
	<xt:var name="makeJavascript" />
	</head>
	<body onload="${onload}" onunload="${onunload}" onfocus="${onfocus}" onblur="${onblur}" onclick="${onclick}">
<a name="top"> </a>

<div class="main">

<!-- content table -->
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #fff">
    <tr valign="top">
      <td class="content" colspan="2" style="padding: 0px; padding-left: 25px; padding-right: 25px">
      	<!-- p><ch:nbsp /></p -->

		<!-- span xt:condition="php: session_admin ()" -->
			<!-- a href="/index" xt:attributes="href php: site_prefix () . '/index'">Web Site Home</a> <ch:nbsp /> <ch:nbsp /> <ch:nbsp / -->
			<!-- xt:box name="cms/buttons/home" />
			<xt:box name="cms/buttons/logout" />
			<br clear="all" /><hr / -->
		<!-- /span -->

		<h1 xt:condition="php: not empty (object.title)" xt:content="title">Title</h1>

        <table border="0" cellpadding="0" cellspacing="0" width="100%" class="navbar">
	<tr>
		<td>
		<a href="${site/prefix}/index/sitemailer2-newsletters-action">Newsletters</a>
		<a href="${site/prefix}/index/sitemailer2-subscribers-action">Subscribers</a>
		<a href="${site/prefix}/index/sitemailer2-drafts-action">Drafts</a>
		<a href="${site/prefix}/index/sitemailer2-templates-action">Templates</a>
        <a href="${site/prefix}/index/sitemailer2-campaigns-action">Campaigns</a>
		<a href="${site/prefix}/index/sitemailer2-stats-action" >Stats</a>
		<a href="${site/prefix}/index/sitemailer2-settings-form" class="current">Settings</a>
		<a href="${site/prefix}/index/help-app?appname=sitemailer2">Help</a>
		</td>
	</tr>
</table>

<br clear="both" />
        
      	<p xt:replace="body">body here</p>

        <p align="right" foo="bar"><a href="#top">[ top ]</a></p>

        <hr />
      </td>
    </tr>
  </table>

</div>

<p align="left" style="padding: 25px; padding-top: 0px">
	Copyright <ch:copy /> <xt:var name="php: date ('Y')" />, <a href="http://www.simian.ca/">SIMIAN systems Inc.</a><br />
	All rights reserved.<br />
	<a href="http://www.sitellite.org/index/license" target="_blank"><xt:intl>Click here to read the software license agreement.</xt:intl></a>
</p>

</body>
</html>