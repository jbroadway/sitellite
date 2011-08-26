<html>
	<head>
	<title><xt:var name="php: PRODUCT_NAME" /></title>
	<link rel="stylesheet" type="text/css" href="${site/prefix}/inc/html/admin/site.css" />
	<script language="javascript" type="text/javascript" src="${site/prefix}/js/jquery-1.4.2.min.js"></script>
	</head>
	<body>
<a name="top"> </a>

<!--
<div style="text-align: left; width: 100%; background-color: #47a; color: #fff; padding: 5px">
	<table border="0" cellpadding="0" cellspacing="0" width="98%">
		<tr>
			<td valign="middle"><ch:nbsp /><ch:nbsp /><img src="${site/prefix}/inc/app/cms/pix/document.gif" alt="Document Logo" border="0" /></td>
			<td valign="bottom"><h1 style="margin: 5px; color: #fff; font: 24px Helvetica, Arial, sans-serif"><strong>SITELLITE</strong> content manager 4 <a href="javascript: window.close ()" xt:replace="php: ''">Close Preview Window</a></h1></td>
			<td align="right" width="50%" valign="middle" style="color: #fff; line-height: 15pt"><xt:intl>Search our help files</xt:intl>:<br /><form method="post" action="http://help.sitellite.net/index/search" target="content-window" style="display: inline"><input type="text" name="query" class="search-query" /> <input type="submit" value="Go" class="search-submit" /></form></td>
		</tr>
	</table>
</div>

<div style="text-align: right; width: 100%; background-color: #cde; border-top: 1px solid #fff; border-bottom: 1px solid #69c; color: #fff; height: 20px">
	<ch:nbsp />
</div>
-->

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

      	<p xt:replace="body">body here</p>

        <p align="right" id="return-to-top"><a href="#top">[ <xt:intl>top</xt:intl> ]</a></p>

        <hr />
      </td>
    </tr>
  </table>

</div>

<p align="left" style="padding: 25px; padding-top: 0px" id="footer">
	<xt:intl>Copyright</xt:intl> <ch:copy /> <xt:var name="php: date ('Y')" />, <a href="${php: PRODUCT_COPYRIGHT_WEBSITE}"><xt:var name="php: PRODUCT_COPYRIGHT" /></a><br />
	<xt:intl>All rights reserved.</xt:intl><br />
	<a href="${php: PRODUCT_LICENSE}" target="_blank"><xt:intl>Click here to read the software license agreement.</xt:intl></a>
</p>

<xt:box name="cms/alert" />

</body>
</html>
