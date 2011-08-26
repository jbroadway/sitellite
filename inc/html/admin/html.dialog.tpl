<html>
	<head>
	<title><xt:var name="php: PRODUCT_NAME" /></title>
	<link rel="stylesheet" type="text/css" href="${site/prefix}/inc/html/admin/site.css" />
	</head>
	<body>
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

      	<p xt:replace="body">body here</p>

      </td>
    </tr>
  </table>

</div>

</body>
</html>