<?php
$p = $_POST;
$tt = isset($p['tt'])?$p['tt']:0;
$xm = isset($p['xm'])?$p['xm']:0;
$file=(isset($_FILES['mid_upload'])&&$_FILES['mid_upload']['tmp_name']!='')?$_FILES['mid_upload']['tmp_name']:'';

If ($file!=''){
	require('midi.class.php');
	
	$midi = new Midi();
	$midi->importMid($file);
	$xml = $midi->getXml($tt);
	
	$fn = $_FILES['mid_upload']['name'];
	
	$a = explode('.',$fn);
	array_pop($a);
	array_push($a,"xml");
	$xn = implode('.',$a);
	//$xn = str_replace('.mid','.xml',$fn);
		
	if ($xm==1){
		header('Content-Type: application/octetstream');
		header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
		header('Content-Disposition: attachment; filename="'.$xn.'"');
		header('Pragma: no-cache');
		echo $xml;
		exit();
	}
}
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<title>Midi2Xml</title>
<style>
body {font-family:arial;font-size:11px;margin:5px;}
input {font-family:arial;font-size:11px}
</style>
</head>
<body>

<form enctype="multipart/form-data" action="mid2xml.php" method="POST" onsubmit="if (this.mid_upload.value==''){alert('Please choose a mid-file to upload!');return false}">
<input type="hidden" name="MAX_FILE_SIZE" value="1048576"><!-- 1 MB -->
MIDI file (*.mid) to upload: <input type="file" name="mid_upload">
<br><br>
TimestampType:<br>
<input type="radio" name="tt" value="0"<?php if ($tt==0) echo ' checked'?>> Absolute<br>
<input type="radio" name="tt" value="1"<?php if ($tt==1) echo ' checked'?>> Delta
<br><br>
<input type="radio" name="xm" value="0"<?php if ($xm==0) echo ' checked'?>> Display XML<br>
<input type="radio" name="xm" value="1"<?php if ($xm==1) echo ' checked'?>> Download XML
<br><br>
<input type="submit" value=" send ">
</form>
<?php
if ($file!=''&&$xm==0){
	echo 'File: '.$fn;
	echo '<hr><pre>';	
	echo htmlspecialchars($xml);
	echo '</pre>';
}
?>
</body>
</html>