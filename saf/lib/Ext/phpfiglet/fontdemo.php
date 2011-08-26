<?php
/*        _         _____  _       _       _
 *   ___ | |_  ___ |   __||_| ___ | | ___ | |_
 *  | . ||   || . ||   __|| || . || || -_||  _|
 *  |  _||_|_||  _||__|   |_||_  ||_||___||_|
 *  |_|       |_|            |___|
 *
 *  This script opens the supplied font directory and
 *  shows a line of text in every available font
 *
 */

require("phpfiglet_class.php");

$out = "";

$phpFiglet = new phpFiglet();

if ($handle = opendir('fonts')) {

	while (false !== ($file = readdir($handle)))
	{
		if ($file != "." && $file != "..") {
			if ($phpFiglet->loadFont("fonts/" . $file)) {
				$out .= $file . "\n";
				$out .= $phpFiglet->fetch("Hello World");
				$out .= "\n\n";
			}
		}
	}
	closedir($handle);
}
?>
<html>
<body>
<pre>
<?php print $out; ?>
<pre>
</body>
</html>