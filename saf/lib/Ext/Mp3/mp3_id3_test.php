<?
require( "mp3_id3_utils.php" );

// now some example

$start = time();

$id1 = mp3_id( "test.mp3" );
if($id1==-1) {
  echo "File not exists or invalid header.<br>";
} else {
  reset($id1);
  echo "<Table border=1 celpadding=1 cellspacing=1>";
  while (list ($key, $val) = each ($id1)) {
    echo "<tr><td><font size=1 face=tahoma>$key </td><td><font size=1 face=tahoma>$val</td></tr>";
  }
  echo "</table>";
  if($id1==0) echo "File doesn't have an ID3 tag.";
}

echo "<br>Time to take the info: ".(time()-$start). " seconds.<br>";
// author: sejba@geocities.com

// This was modified by Luca.

// Now we set a ID3 TAG

$id2["title"] = "Is a song";
$id2["comment"] = "The Comment";
$id2["track"] = 20;

// If you're using this in a web server, remember that the server proccess must have read/write perms in test.mp3!!!

if ( set_id3( "test.mp3", $id2 ) ) 
  echo "OK!<br>";
else
  echo "There was an error!<br>";

$start = time();

$id3 = mp3_id( "test.mp3" );
if($id3==-1) {
  echo "File not exists or invalid header.<br>";
} else {
  reset($id3);
  echo "<Table border=1 celpadding=1 cellspacing=1>";
  while (list ($key, $val) = each ($id3)) {
    echo "<tr><td><font size=1 face=tahoma>$key </td><td><font size=1 face=tahoma>$val</td></tr>";
  }
  echo "</table>";
  if($id3==0) echo "File doesn't have an ID3 tag.";
}

echo "<br>Time to take the info: ".(time()-$start). " seconds.<br>";

// Returns test file to the original values.
set_id3( "test.mp3", $id1 );

?>