<xt:tpl version="1.0">
<xt:note>HEADER LEFT "Sitellite Example Website"</xt:note>
<xt:note>FOOTER LEFT "$HEADING"</xt:note>
<xt:note>FOOTER RIGHT "$PAGE"</xt:note>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="/inc/html/default/site.css"
		xt:attributes="href string: ${site/prefix}/inc/html/default/site.css"
	/>
</head>
<body><a name="top"> </a>

<table>
<tr>
<td id="centre">

	<h1
		xt:content="title"
		xt:condition="php: not empty (object.title)">Page Title</h1>

	<span xt:replace="body">
		<p>Page body</p>
	</span>

		<xt:note>NEW PAGE</xt:note>

		<h2>Need help?</h2>

		<p>Visit us online at <a href="http://www.sitellite.org/">www.sitellite.org</a></p>

		<h2>Copyright Notice</h2>

		<p>Copyright <ch:copy /> <xt:var name="php: date ('Y')" /> Sitellite.org Community</p>

</td>
</tr>
</table>

</body>
</html>
</xt:tpl>