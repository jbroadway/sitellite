<?php
require('midi.class.php');

$p=$_POST;

if (isset($_FILES['mid_upload'])){
	$tmpFile=$_FILES['mid_upload']['tmp_name'];
	if (!is_dir('tmp')) mkdir('tmp');
	srand ((double)microtime()*1000000);
	$file='tmp/~'.rand().'.mid';
  copy($tmpFile,$file) or die('problems uploading file');
	@chmod($file,0666);
}elseif (isset($p['file'])) $file=$p['file'];

?>

<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<title>Manipulate MIDI file</title>
<style>
body {font-family:arial;font-size:11px;margin:5px;}
input {font-family:arial;font-size:11px}
select {font-family:arial;font-size:11px}
option {font-family:arial;font-size:11px}
</style>
</head>
<body>
<form enctype="multipart/form-data" action="manipulate.php" method="POST">
<input type="hidden" name="MAX_FILE_SIZE" value="1048576"><!-- 1 MB -->
MIDI file (*.mid) to upload: <input type="file" name="mid_upload">
<input type="submit" value=" send ">
</form>
<hr>
<?php
if (isset($file)){
	$plug = isset($p['plug'])?$p['plug']:'wm';
	$midi = new Midi();     
	$midi->importMid($file);

	$tc = $midi->getTrackCount();
?>
<form action="manipulate.php" method="POST">
<input type="hidden" name="file" value="<?php echo isset($file)?$file:''?>">
<input type="radio" name="plug" value="bk"<?php echo $plug=='bk'?' checked':''?>>Beatnik
<input type="radio" name="plug" value="qt"<?php echo $plug=='qt'?' checked':''?>>QuickTime
<input type="radio" name="plug" value="wm"<?php echo $plug=='wm'?' checked':''?>>Windows Media
<input type="radio" name="plug" value=""<?php echo $plug==''?' checked':''?>>other (default Player)
<br><br>
<input type="checkbox" name="up"<?php echo isset($p['up'])?' checked':''?>>transpose up (1 octave)
<input type="checkbox" name="down"<?php echo isset($p['down'])?' checked':''?>>transpose down (1 octave)
<br><br>
<input type="checkbox" name="double"<?php echo isset($p['double'])?' checked':''?>>double tempo
<input type="checkbox" name="half"<?php echo isset($p['half'])?' checked':''?>>half tempo
<br><br>
<input type="checkbox" name="delete"<?php echo isset($p['delete'])?' checked':''?>>delete track 
<select name="delTrackNum"><?php for ($i=0;$i<$tc;$i++) echo "<option value=\"$i\"".(isset($p['delTrackNum'])&&$i==$p['delTrackNum']?' selected':'').">$i</option>\n";?></select>
<input type="checkbox" name="solo"<?php echo isset($p['solo'])?' checked':''?>>solo track 
<select name="soloTrackNum"><?php for ($i=0;$i<$tc;$i++) echo "<option value=\"$i\"".(isset($p['soloTrackNum'])&&$i==$p['soloTrackNum']?' selected':'').">$i</option>\n";?></select>
<br><br>
<input type="checkbox" name="insert"<?php echo isset($p['insert'])?' checked':''?>>insert MIDI messages (3 handclaps at start)
<br><br>
<input type="checkbox" name="show"<?php echo isset($p['show'])?' checked':''?>>show MIDI result as Text
<br><br>
<input type="submit" value=" PLAY! ">
</form>
<?php

	$new='tmp/~'.rand().'.mid';

	if (isset($p['up']))          $midi->transpose(12);
	if (isset($p['down']))        $midi->transpose(-12);
	if (isset($p['double'])) 			$midi->setTempo($midi->getTempo()/2);
	if (isset($p['half'])) 				$midi->setTempo($midi->getTempo()*2);
	if (isset($p['solo']))        $midi->soloTrack($p['soloTrackNum']);
	if (isset($p['delete']))      $midi->deleteTrack($p['delTrackNum']);
	if (isset($p['insert'])){
		      											$midi->insertMsg(0,"0 On ch=10 n=39 v=127");
		      											$midi->insertMsg(0,"120 On ch=10 n=39 v=127");
		      											$midi->insertMsg(0,"240 On ch=10 n=39 v=127");
	}
	$midi->saveMidFile($new);
	$midi->playMidFile($new,1,1,0,$plug);
	
	if (isset($p['show'])) echo '<hr>'.nl2br($midi->getTxt());
}
?>
</body>
</html>