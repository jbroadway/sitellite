<?php
require_once('FormFilters.php');
?>
<!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title> Form Filter Example </title>
</head>
<body>
<h1>Form Filter Example</h1>
<p>This example demonstrates how PEAR::XML_SaxFilters <i>might</i> be used with PEAR::XML_HTMLSax to filter form posts which are allowed to contain a limited subset of HTML. Note that this example is not checking the HTML submitted for well formedness but rather limiting the allowed tags to a limited subset and preventing the use of attributes for XSS.</p>
<hr>
<table>
<tr valign="top">
<td>
Enter something below<br />
<form action="<?php echo ($_SERVER['PHP_SELF']); ?>" method="post">
<textarea name="message" cols="40" rows="5"></textarea><br />
<input type="submit" name="submit" value=" Post "><br />
</form>
</td>
<td>
<b>Allowed Tags</b>
<ul>
<li>&lt;a href="http://www.php.net"&gt;PHP&lt;/a&gt;</li>
<li>&lt;b /&gt;, &lt;strong /&gt; = &lt;strong /&gt;</li>
<li>&lt;i /&gt;, &lt;em /&gt; = &lt;em /&gt;</li>
<li>&lt;br /&gt;</li>
<li>&lt;p /&gt;</li>
<li>&lt;code /&gt;</li>
<li>&lt;blockquote /&gt;</li>
<li>&lt;ul /&gt; &lt;ol /&gt;, &lt;li /&gt;</li>
</ul>
</td>
</tr>
</table>
<hr>
<h2>Output</h2>
<?php
if ( isset($_POST['submit']) ) {
    $filteredText = filterForm($_POST['message']);
?>
<table width="450">
<tr>
<td>
<?php echo ( $filteredText ); ?>
</td>
</tr>
</table>
<hr>
<h2>Raw Output</h2>
<code>
<?php echo ( htmlspecialchars($filteredText) ); ?>
</code>
<?php
}
?>
</body>
</html>