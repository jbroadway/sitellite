<?php

echo "<font face=\"Verdana\" size=\"2\">";
echo "<b>TAR Archive Class</b><br><br>\n\n";

// Include TAR Class
include("tar.class.php");


// Create instance of TAR class
$tar = new tar();


// Open an uncompressed tar file
if(!$tar->openTar("main.tar",FALSE))
	echo "<b>Could not open main.tar!</b><br>\n";
else
	echo "<b>Opened main.tar successfully!</b><br>\n";


// Append a compressed gzipped tar file
if(!$tar->appendTar("append.tgz",TRUE))
	echo "<b>Could not append append.tgz to opened tar file!</b><br>\n";
else
	echo "<b>Appended append.tgz successfully!</b><br>\n";


// List directories in the currently opened tar file(s)
echo "<b>Directories in " . $tar->filename . "</b><br>\n";
if($tar->numDirectories > 0) {
	foreach($tar->directories as $id => $information) {
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$information[directory]/$information[name]<br>\n";
	}
} else {
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;There are no directories described in this tar archive.<br>\n";
}
echo "<br>\n";


// List files in the currently opened tar file(s)
echo "<b>Files in " . $tar->filename . "</b><br>\n";
if($tar->numFiles > 0) {
	foreach($tar->files as $id => $information) {
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$information[directory]/$information[name]<br>\n";
	}
} else {
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;There are no files described in this tar archive.<br>\n";
}
echo "<br>\n";


// Check if a file exists in the tar file
if($tar->containsFile("fake.php"))
	echo "<b>This TAR Archive does contain a file called fake.php!</b><br>\n";
else
	echo "<b>This TAR Archive does not contain any files called fake.php!</b><br>\n";
echo "<br>\n";


// Add a file to the archive
if($tar->addFile("example.php"))
	echo "Added 'example.php' to archive!<br>\n";
else
	echo "Could not add 'example.php' to archive!<br>\n";
echo "<br>\n";


// Save changes to a NEW tar file
if(!$tar->toTar("test.tgz",TRUE))
	echo "Could not save Gzipped TAR Archive!<br>\n";
else
	echo "New Gzipped TAR File generated successfully!<br>\n";


// Save changes to currently opened tar file using existing filename and gzip method
// already set when loading (main.tar, no gzip in this example)
if(!$tar->saveTar())
	echo "Could not save TAR Archive!<br>\n";
else
	echo "New Regular TAR File generated successfully!<br>\n";


echo "</font>";

?>