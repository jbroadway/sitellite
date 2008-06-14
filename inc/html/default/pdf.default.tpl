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

		<h2>Contact Simian Systems</h2>

		<ul>
			<li>Online at <a href="http://www.simian.ca/">www.simian.ca</a></li>

			<li>By phone at 250-714-0440</li>
			<li>Or visit us in person at:<br /><br />
				1071 Corydon<br />
				Winnipeg, Manitoba<br />
				R3M 0X3
			</li>
		</ul>

		<h2>Copyright Notice</h2>

		<p>Copyright <ch:copy /> <xt:var name="php: date ('Y')" /> Simian Systems Inc.</p>

</td>
</tr>
</table>

</body>
</html>
</xt:tpl>