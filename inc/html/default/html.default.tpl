<xt:tpl version="1.0"><xt:doctype
	root="html"
	access="public"
	name="-//W3C//DTD XHTML 1.0 Transitional//EN"
	uri="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"
/>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<!--
		This tag sets the window title in the format "Sitellite Example Website - Page Title".
		Simply change the site name to your own to customize this line.
	-->
	<title xt:content="string: Sitellite 5.0 Example Website - ${head_title}">Site Name</title>

	<!--
		This tag replaces the CSS reference with a link that will work with Sitellite
		installations both in the document root and in sub-folders.
	-->
	<link rel="stylesheet" type="text/css" href="/inc/html/default/site.css"
		xt:attributes="href string: ${site/prefix}/inc/html/default/site.css"
	/>

	<!--
		This optional script automatically adds an external CSS class to external
		links as well as a file CSS class to links to files, which can then be styled
		separately from ordinary links (see example CSS for defaults).
	-->
	<script language="javascript" type="text/javascript" src="${site/prefix}/js/jquery-1.4.2.min.js"></script>
	<script language="javascript" src="/js/linkmapper-compressed.js"> </script>

	<!--
		You don't have to worry about meta tags because Sitellite inserts those into
		the template automatically for you.
	-->
</head>
<body><a name="top"> </a>

<!-- wrapper -->
<div id="wrapper">

<!-- header -->
<div id="header">

	<a href="/">Sitellite 5.0 Example Website</a>

</div>

<div id="menu">
	<!--
		Here we'll place our 'Email page' and 'Print page' links, which will appear
		on each page of the site.
	-->
	<div id="page-functions">
		<xt:register name="site" />
		<a href="http://digg.com/submit?phase=2[ch:amp]url=${php: urlencode (site.url . site.current)}[ch:amp]title=${php: urlencode (object.title)}" target="_blank"><img src="${site/prefix}/inc/html/default/pix/digg.gif" border="0" alt="Digg this page" title="Digg this page" /></a>
		[ch:nbsp][ch:nbsp]
		<a href="javascript: void window.open ('http://del.icio.us/post?url=${php: urlencode (site.url . site.current)}[ch:amp]title=${php: urlencode (object.title)}[ch:amp]notes=[ch:amp]v=4[ch:amp]noui[ch:amp]jump=close[ch:amp]src=sitellite${php SITELLITE_VERSION}', 'Bookmark', 'left=50,top=50,width=700,height=300,resize=yes')"><img src="${site/prefix}/inc/html/default/pix/bookmark.gif" border="0" alt="Bookmark with del.icio.us" title="Bookmark with del.icio.us" /></a>
		[ch:nbsp][ch:nbsp]
		<a href="javascript: void window.open ('/index/example-emailafriend-form?url=${site/current}', 'EmailAFriend', 'left=50,top=50,width=400,height=400,resize=yes')"><img src="${site/prefix}/inc/html/default/pix/email.gif" border="0" alt="Email page" title="Email page" /></a>
		[ch:nbsp][ch:nbsp]
		<a href="javascript: void window.print ()"><img src="${site/prefix}/inc/html/default/pix/print.gif" border="0" alt="Print page" title="Print page" /></a>
	</div>

	<!--
		The next line inserts a dynamically generated menu into the page.
		Notice how the tags allow for sample text that is replaced with the real
		thing when run through Sitellite.  This allows you to view your templates
		outside of Sitellite (ie. in Dreamweaver or in a web browser) and still
		see how they will look once inside Sitellite.
	-->
	<!--
        issue #187 new menu
        <xt:box name="sitellite/nav/top" separator=" // ">Home // About Us // Products</xt:box>
    -->
    <xt:box name="sitellite/nav/dropdown"
                 disabledropdown="no"
                 sort="no"
     />

</div>

<table id="columns" width="100%" cellpadding="0" cellspacing="0">
<tr>

<!-- left column -->
<td id="left">

	<!--
		This is how you create sidebars that flank the main page content.  Position
		names are arbitrary and can be added/removed under the "Properties" tab
		while editing any sidebar box.

		Sidebars are handy in that they allow you to create one or more 'columns'
		of content blocks, each of which is separately editable and some of which
		may even be dynamic calls to boxes such as the sitellite/nav boxes.
	-->
	<xt:box name="sitellite/sidebar" position="left">
		<h2>Left Column</h2>
		<p>This text will be replaced with the left sidebar content.</p>
	</xt:box>

</td>

<!-- centre column -->
<td id="centre">

	<!--
		This tag inserts the Sitellite editing buttons for the current page if the
		user is an administrator.
	-->
	<xt:box name="cms/buttons" />

	<!--
		Here we display the page title as an h1 tag, but only if there is a page
		title to show.  The xt:content tag replaces the contents of the h1 tag
		with the specified value.  The xt:condition tag evaluates whether to
		show the h1 tag at all.
	-->
	<h1
		xt:content="title"
		xt:condition="php: not empty (object.title)">Page Title</h1>

	<!--
		This span tag and its contents will all be replaced with the page body.
		This is done with the xt:replace attribute.
	-->
	<span xt:replace="body">
		<p>Page body</p>
	</span>

</td>

</tr>
</table>

<!-- footer -->
<div id="footer">

	<p style="float: left; width: 45%">
		<!--
			The ch:copy tag is replaced with its equivalent HTML entities.  The
			xt:var tag is a quick way of dynamically inserting the current date
			into the template.
		-->
		<xt:intl>Copyright</xt:intl> [ch:copy] <xt:var name="php: date ('Y')" /> Sitellite.org Community<br />
		<xt:intl>All rights reserved.</xt:intl><br /><br />
		<a href="http://www.sitellite.org/" class="product-credit"><xt:intl>Powered by Sitellite 5.0 Content Management System</xt:intl></a>
	</p>

	<p align="right" class="footer-links">
		<a href="http://www.sitellite.org/"><xt:intl>About Sitellite</xt:intl></a> // <a href="http://www.sitellite.org/index/community"><xt:intl>Sitellite Community</xt:intl></a> // <a href="http://www.sitellite.org/index/docs"><xt:intl>Documentation</xt:intl></a> // <a href="http://www.sitellite.org/index/support"><xt:intl>Support</xt:intl></a>
	</p>

</div>

</div>

</body>
</html>
</xt:tpl>
