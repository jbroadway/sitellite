<?php
$p = $_POST;
$tt = isset($p['tt'])?$p['tt']:0;
$file=(isset($_FILES['mid_upload'])&&$_FILES['mid_upload']['tmp_name']!='')?$_FILES['mid_upload']['tmp_name']:'';//(isset($p['file'])?$p['file']:'');
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<title>Midi2Text</title>
<style>
body {font-family:arial;font-size:11px;margin:5px;}
input {font-family:arial;font-size:11px}
</style>
</head>
<body>

<form enctype="multipart/form-data" action="mid2txt.php" method="POST" onsubmit="if (this.mid_upload.value==''){alert('Please choose a mid-file to upload!');return false}">
<input type="hidden" name="MAX_FILE_SIZE" value="1048576"><!-- 1 MB -->
MIDI file (*.mid) to upload: <input type="file" name="mid_upload">
<br>
TimestampType:
<input type="radio" name="tt" value="0"<?php if ($tt==0) echo ' checked'?>> Absolute
<input type="radio" name="tt" value="1"<?php if ($tt==1) echo ' checked'?>> Delta
<br><br>
<input type="submit" value=" send ">
</form>
<?php
if ($file!=''){
	require('midi.class.php');
	
	$midi = new Midi();
	$midi->importMid($file);
	
	echo 'File: '.$_FILES['mid_upload']['name'];
	echo '<hr><pre>';
	echo $midi->getTxt($tt);
	echo '</pre>';
}
?>
</body>
</html>