<html>
<body>
<pre>
<?php
/*        _         _____  _       _       _
 *   ___ | |_  ___ |   __||_| ___ | | ___ | |_
 *  | . ||   || . ||   __|| || . || || -_||  _|
 *  |  _||_|_||  _||__|   |_||_  ||_||___||_|
 *  |_|       |_|            |___|
 *
 *  Usage example script
 *
 */

require("phpfiglet_class.php");

$phpFiglet = new phpFiglet();

if ($phpFiglet->loadFont("fonts/standard.flf")) {
	$phpFiglet->display("phpFiglet Rulez!");
} else {
	trigger_error("Could not load font file");
}
?>
<pre>
</body>
</html>