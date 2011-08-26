<?php

echo "<font face=\"Verdana\" size=\"2\">";
echo "<b>TAR Archive Class</b><br><br>\n\n";

function text2html($html,$repeat='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;') {
	$html = htmlentities($html);
	$html = str_replace("\n","<br>",$html);
	$html = str_replace("\t","$repeat",$html);

	return $html;
}

// Include TAR Class
include("tar.class.php");

// Creating a NEW Tar file
	$tar = new tar();
	$tar->addFile("exmaple.php");
	$tar->addFile("example2.php");
	$tar->addFile("tar.class.php");
	$tar->toTar("new.tar",FALSE);		// Normal TAR
	// $tar->toFile("new.tgz",TRUE);	// Gzipped TAR
	unset($tar);

// Appending 2 tar files together, saving in gzipped format (Gzipping requires zlib)
	$tar = new tar();
	$tar->openTAR("my.tar",FALSE);
	$tar->appendTar("another.tar",FALSE);
	$tar->toTar("combined.tgz",TRUE);
	unset($tar);

// Removing 2 files from the new.tar file created above
	$tar = new tar();
	$tar->openTar("new.tar",FALSE);
	$tar->removeFile("example.php");
	$tar->removeFile("example2.php");
	$tar->saveTar();			// Saves to currently open TAR file (In this case, new.tar)
	unset($tar);

// Check if a TAR file contains a specific file
	$tar = new tar();
	$tar->openTar("new.tar",FALSE);
	if($tar->containsFile("tar.class.php"))
		echo "This tar file contains a file named 'tar.class.php'!<br>\n";
	else
		echo "This tar file does NOT contain a file named 'tar.class.php'!<br>\n";

	// There is no need to save our tar file since we did not edit it, so delete our tar class
	unset($tar);

// Get information about a file in a TAR file
	$tar = new tar();
	$tar->openTar("new.tar");	// If second argument is not present, default is FALSE
	$information = $tar->getFile("tar.class.php");
	echo "<br>\n<b>Information about tar.class.php!</b><br>\n";
	foreach($information as $key => $value) {
		echo "&nbsp;&nbsp;&nbsp;&nbsp;$key = " . text2html($value) . "<br>\n";
	}
	echo "<br>\n";

echo "</font>";

?>