<?php
session_start();
$save_dir = 'tmp/sess_'.session_id().'/';

//clean up
$sessions = array();
$handle = opendir (session_save_path());
while (false !== ($file = readdir ($handle)))
	if ($file!='.' && $file!='..') $sessions[] = $file;
closedir($handle);
$handle = opendir('tmp/');
while (false !== ($dir = readdir ($handle)))
	if ($dir!='.' && $dir!='..' && $dir!='CVS' && $dir!='.DS_Store' && !in_array($dir,$sessions)) rm("tmp/$dir/");
closedir($handle);

// removes non-empty dir
function rm($dir){
	$handle = opendir($dir);
	while (false !== ($file = readdir ($handle)))
		if ($file!='.' && $file!='..' && $file!='CVS') unlink("$dir/$file");
	closedir($handle);
	rmdir($dir);
}

if (!is_dir('tmp')) mkdir('tmp');
if (!is_dir($save_dir)) mkdir($save_dir);	

//---------------------------------------------
require('midi.class.php');

$midi = new Midi();

$instruments = $midi->getInstrumentList();
$drumset     = $midi->getDrumset();
$drumkit     = $midi->getDrumkitList();
$notes       = $midi->getNoteList();
//---------------------------------------------

$p=$_POST;$g=$_GET; //'cause I'm lazy

srand((double)microtime()*1000000);
$rand = rand();
$f = $rand.'.mid';
$file = $save_dir.$f;

if (isset($g['download'])){
	$file = $g['download'];
	$filename  = 'output.mid';
	$midi->downloadMidFile($file,$filename);
}

if (isset($p['publish'])){
	unset($p['plug']);
	if (isset($p['showTxt'])) unset($p['showTxt']);
	if (isset($p['showXml'])) unset($p['showXml']);
	
	$str = serialize($p);
	$m=fopen("mix/$rand.mix","wb");
	fwrite($m, $str);
	@fclose($m);
	echo "your mix has been published as $rand.mix!";
}

if (isset($g['mix'])){
	$mixfile = 'mix/'.$g['mix'];
	$m=fopen($mixfile,"r");
	$str = fread($m, filesize($mixfile));
	@fclose($m);
	$p = unserialize($str);
	$p['play'] = 1;
}

//DEFAULTS
$loop = isset($p['noloop'])?0:1;
$rep = isset($p['rep'])?$p['rep']:4;
$plug = isset($p['plug'])?$p['plug']:(isset($g['plug'])?$g['plug']:'wm');
$play = isset($p['play'])?1:0;
$bpm = isset($p['bpm'])?$p['bpm']:150;

$aktiv=array();
$inst=array();
$note=array();
$vol=array();

for ($k=1;$k<=8;$k++){
	$aktiv[$k] = isset($p["aktiv$k"])?1:0;
	$inst[$k] = isset($p["inst$k"])?$p["inst$k"]:0;
	$note[$k] = isset($p["note$k"])?$p["note$k"]:35;
	$vol[$k] = isset($p["vol$k"])?$p["vol$k"]:127;
}
//if (!isset($p['last'])) $aktiv[1]=1; //first call
?>

<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<title>Sequencer</title>
<style>
body {font-family:arial;font-size:11px;margin:5px;}
td {font-family:arial;font-size:11px}
select {font-family:arial;font-size:11px}
option {font-family:arial;font-size:11px}
input {font-family:arial;font-size:11px}
a,a:link,a:visited,a:active	{font-family:arial;font-size:11px;color:#000000;}
a:hover	{font-family:font-family:arial;font-size:11px;color:#666666;}
</style>
</head>
<body>
<form action="sequencer.php" method="POST" onsubmit="b=0;for(i=1;i<9;i++)b|=this['aktiv'+i].checked;if(b==0){alert('You have to activate at least one track!');return false};">
<input type="hidden" name="PHPSESSID" value="<?php echo session_id()?>">
<table border=0 cellpadding=2 cellspacing=0><tr><td>
<table border=0 cellpadding=2 cellspacing=0 bgcolor="#DADADA">

<!-- DRUMS -->
<tr bgcolor="#333333"><td>&nbsp;</td><td colspan=7 style="color:#FFFFFF"><b>Drum tracks</b></td></tr>
<tr bgcolor="#BBBBBB"><td align="center">on</td><td>instrument</td><td>drum kit</td><td>vol</td><td colspan=4>pattern</td></tr>
<?php
for ($k=1;$k<=4;$k++){
?>
<tr>
<td><input type="checkbox" name="aktiv<?php echo $k?>"<?php echo $aktiv[$k]?' checked':''?>></td>
<td>
<select name="note<?php echo $k?>">
<?php
	for ($i=0;$i<128;$i++)
		if (isset($drumset[$i]))
			echo '<OPTION value="'.$i.'"'.($note[$k]==$i?' selected':'').'>0'.$i.'&nbsp;&nbsp;'.$drumset[$i]."</OPTION>\n";
		else{
			$num = ($i<10)?"00$i":($i<100?"0$i":"$i");
			echo '<OPTION value="'.($i).'"'.($note[$k]==$i?' selected':'').'>'.$num."</OPTION>\n";
		};
?>
</select>
</td>
<td>
<select name="inst<?php echo $k?>">
<?php
	foreach ($drumkit as $key=>$val) echo "<option value=$key ".(($inst[$k]==$key)?' selected':'').">$val</option>\n";
?>
</select>
</td>
<td>
<select name="vol<?php echo $k?>">
<?php
	for ($i=127;$i>=0;$i--)
		echo "<OPTION value=\"$i\"".($vol[$k]==$i?' selected':'').">$i</OPTION>\n";
?>
</select>
</td>
<td bgcolor="#DADADA">
<?php
	for ($i=0;$i<16;$i++) {
		echo "<input type=\"checkbox\" name=\"n$k$i\"".(isset($p["n$k$i"])?' checked':'').">\n";
		if ($i<15&&$i%4==3) echo '</td><td'.($i%8==3?' bgcolor="#EEEEEE"':' bgcolor="#DADADA"').'>';
	}
}
?>
</td>
</tr>


<!-- INSTRUMENTS -->
<tr bgcolor="#333333"><td>&nbsp;</td><td colspan=7 style="color:#FFFFFF"><b>Instrument tracks</b></td></tr>
<tr bgcolor="#BBBBBB"><td align="center">on</td><td>instrument</td><td>note</td><td>vol</td><td colspan=4>pattern</td></tr>
<?php
for ($k=5;$k<=8;$k++){
?>
<tr>
<td><input type="checkbox" name="aktiv<?php echo $k?>"<?php echo $aktiv[$k]?' checked':''?>></td>
<td>
<select name="inst<?php echo $k?>">
<?php
	for ($i=0;$i<128;$i++){
		$num = ($i<10)?"00$i":($i<100?"0$i":"$i");
		echo '<OPTION value="'.($i).'"'.($inst[$k]==$i?' selected':'').'>'.$num.'&nbsp;&nbsp;'.$instruments[$i]."</OPTION>\n";
	}
?>
</select>
</td>
<td>
<select name="note<?php echo $k?>">
<?php
	for ($i=0;$i<128;$i++)
		echo '<OPTION value="'.($i).'"'.($note[$k]==$i?' selected':'').'>'.$notes[$i]."</OPTION>\n";
?>
</select>
</td>
<td>
<select name="vol<?php echo $k?>">
<?php
	for ($i=127;$i>=0;$i--)
		echo "<OPTION value=\"$i\"".($vol[$k]==$i?' selected':'').">$i</OPTION>\n";
?>
</select>
</td>
<td>
<?php
	for ($i=0;$i<16;$i++) {
		echo "<input type=\"checkbox\" name=\"n$k$i\"".(isset($p["n$k$i"])?' checked':'').">\n";
		if ($i<15&&$i%4==3) echo '</td><td'.($i%8==3?' bgcolor="#EEEEEE"':'').'>';
	}
}
?>
</td>
</tr>
<tr><td colspan=8 bgcolor="#FFFFFF">

<br><br>

<input type="text" name="bpm" size=3 value="<?php echo $bpm?>"> bpm<br>
<input type="text" name="rep" size=3 value="<?php echo $rep?>"> bar repetitions<br>
<input type="checkbox" name="noloop"<?php echo !$loop?' checked':''?>>don't loop<br>
<input type="radio" name="plug" value="bk"<?php echo $plug=='bk'?' checked':''?>>Beatnik
<input type="radio" name="plug" value="qt"<?php echo $plug=='qt'?' checked':''?>>QuickTime
<input type="radio" name="plug" value="wm"<?php echo $plug=='wm'?' checked':''?>>Windows Media
<input type="radio" name="plug" value=""<?php echo $plug==''?' checked':''?>>other (default Player)
<br>
<input type="checkbox" name="showTxt"<?php echo isset($p['showTxt'])?' checked':''?>>show MIDI result as Text<br>
<input type="checkbox" name="showXml"<?php echo isset($p['showXml'])?' checked':''?>>show MIDI result as XML
<br><br>
<input type="submit" name="play" value=" PLAY! ">&nbsp;&nbsp;
<input type="submit" name="publish" value="Publish">&nbsp;&nbsp;<br><br>
</form>

<?php

if ($play){
	$midi->open(480); //timebase=480, quarter note=120
	$midi->setBpm($bpm);
	
	for ($k=1;$k<=8;$k++) if ($aktiv[$k]){		
		$ch = ($k<5)?10:$k;
		$inst = $p["inst$k"];
		$n = $p["note$k"];
		$v = $p["vol$k"];
		$t = 0;
		$ts = 0;
		$tn = $midi->newTrack() - 1;
		
		$midi->addMsg($tn, "0 PrCh ch=$ch p=$inst");
		for ($r=0;$r<$rep;$r++){
			for ($i=0;$i<16;$i++){
				if ($ts == $t+120) $midi->addMsg($tn, "$ts Off ch=$ch n=$n v=127");
				$midi->addMsg($tn, "$ts Off ch=$ch n=$n v=127");
				if (isset($p["n$k$i"])){
					$t = $ts;
					$midi->addMsg($tn, "$t On ch=$ch n=$n v=$v");
				}
				$ts += 120;
			}
			$midi->addMsg($tn, "$ts Off ch=$ch n=$n v=127");
		}
		$midi->addMsg($tn, "$ts Meta TrkEnd");
	}	
	$midi->saveMidFile($file);
	$midi->playMidFile($file,1,1,$loop,$plug);
?>	
	<br><br>
	<input type="button" name="download" value="Save as SMF (*.mid)" onClick="self.location.href='sequencer.php?download=<?php echo urlencode($file)?>'">
<?php
}
?>
</td></tr></table>
</td>
<td width=10>&nbsp;</td>
<td valign="top">

<table width=140 border=0 cellpadding=2 cellspacing=0 bgcolor="#DADADA">
<tr bgcolor="#333333"><td colspan=7 style="color:#FFFFFF"><b>Published Mixes</b></td></tr>
<tr><td>
<?php
$handle=opendir ('mix');
while (false !== ($file = readdir ($handle)))
	if ($file!='.' && $file!='..') echo "<a href=\"sequencer.php?mix=$file&plug=$plug\">$file</a><br>\n";
closedir($handle);
?>
<br>
</td></tr></table>

</td></tr></table>
<?php 
if (isset($p['showTxt'])) echo '<hr><pre>'.$midi->getTxt().'</pre>';
if (isset($p['showXml'])) echo '<hr><pre>'.htmlspecialchars($midi->getXml()).'</pre>';
?>
</body>
</html>