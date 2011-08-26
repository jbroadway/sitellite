<?php
	/* 
  	* MP3 ID3v2 Tag - A Class for reading MP3 ID3v2.x tags
    
	This code should read any ID3v2.2, ID3v2.3 and ID3v2.4 tag. 
	I have yet to present the tag info in a meaningful manner. 
	This can't write tag yet.
	This should read the MPEG frame too.
	This does try to deal with corrupt or not standard mp3 files, 
	but info could be not correct.
	
	!!!!THIS IS VERY BETA. DO NOT USE UNLESS YOU ENJOY THE RISK OF VERY BAD THINGS
	HAPPENING TO YOUR DATA.
	I really hope this doesn't mess up your MP3s but you
	are on your own if bad things happen.!!!!
	
	* Version 1.05
	*
	* By Daniel Martinez-Morales <danielATsociologiaDOTinfo>
	* 
	* Copyright 2002 (c) All Rights Reserved, All Responsibility Yours
	*
	* This code is released under the GNU LGPL Go read it over here:
	*
	* http://www.gnu.org/copyleft/lesser.txt
	* -----Traducción en castellano (no oficial)-----
	* http://gugs.sindominio.net/gnu-gpl/lgpl-es.html 
	* -----------------------------------------------
	*
	* 
	* To use this code:
	* include this class,
	*		include_once('id3v2.php');
	* create a id3v2 object only one time,
	*		$mp3 = new id3v2();
	* then you can get info using,
	*		$mp3->GetInfo($CompletPATHtotheMp3file);		
	* and show info.
	*		$mp3->ShowInfo();
	* Or you can use
			$mp3 = new id3v2();
	*		$mp3->myReaddir($CompletPATHtotheDir)
	* to scan a directory and its subdirectories.
	* But you can do
	*		$mp3 = new id3v2();
	*		$mp3->GetInfo($Path2file);
	*		$mp3->ShowInfo();
	*		$mp3->myReaddir($PatheDir);
	*		$mp3->GetInfo($Path2otherFile);
	*		$mp3->ShowInfo();
	*		...				
	*
	* Change Log:
	*	1.05:	First attempt at ID3v2 read support. (18-July-2002)
	*	
	*
	* TODO:
	*  Implement a ID3v2 writer
	*  Build a Mp3 manager using mysql.
	*	
	*/
	
class id3v2{
var $TargetSrcFile;
var $TargetNamFile;
var $mpegInfo;
var $id3v2Info;
var $conf;
var $jumps=3;

var $Codes=array();		//	defined in arrays.php
var $Tipus=array();		//	"	"		
var $FX=array();		//	"	"		
var $LookGenre=array();		//	"	"
var $HexPictureType=array();	//	"	"
var $LookHeaderFlags=array();	//	"	"
var $Emphasis=array();		//	"	"
var $ChannelMode=array();	//	"	"
var $Intensity=array();		//	"	"
var $LookAudioVersion=array();	//	"	"
var $LookLayerDescrip=array();	//	"	"
var $LookBitrateValues=array();	//	"	"
var $LookBitrateIndex=array();	//	"	"
var $LookFrameLen=array();	//	"	"
	
	function id3v2(){
	include("arrays.php");
	}

	function OpenFile($src){
		$this->TargetNamFile=$src;
		$this->TargetSrcFile=false;
		$this->mpegInfo=array();
		$this->id3v2Info=array();
		$this->id3v1Info=array();
		$this->conf=array();
		$this->TargetSrcFile = @fopen($this->TargetNamFile, 'rb');
		$FileSize=0;
		if ($this->TargetSrcFile) $FileSize=filesize($this->TargetNamFile);
		if ($FileSize<(100*1024)){
		return false;
		}
		else{
		$this->conf['FileSize']=$FileSize;
		$this->conf['DecodeTime']=$this->myMicrotime();
		return true;
		}
	}

	function CloseFile(){
		$this->conf['DecodeTime']=$this->myMicrotime()-$this->conf['DecodeTime'];
		fclose($this->TargetSrcFile);
	}

	function myBigEndian2IntSyn($byteword) {
		$intvalue = 0;
		$bytewordlen = strlen($byteword);
		for ($i=0;$i<$bytewordlen;$i++) {
		$intvalue = $intvalue | ((ord($byteword{$i})) & bindec('01111111')) << (($bytewordlen - 1 - $i) * 7);
		}
		return $intvalue;
	}
	
	function myHex2IntSyn($hex){
	$int = base_convert($hex, 16, 10);
	$int1 = floor($int/256) * 128 + ($int%256);
	$int2 = floor($int1/32768) * 16384 + ($int1%32768);
	$int = floor($int2/4194304) * 2097152 + ($int2%4194304);
	return $int;
	}
	

	function myBigEndian2Int($byteword) {
		$intvalue = 0;
		$bytewordlen = strlen($byteword);
		for ($i=0;$i<$bytewordlen;$i++) {
		$intvalue += ord($byteword{$i}) * pow(256, ($bytewordlen - 1 - $i));
		}
	return $intvalue;
	}

	function myStrBin2Bin($byteword) {
		$binvalue = "";
		$bytewordlen = strlen($byteword);
		for ($i=0;$i<$bytewordlen;$i++) {
			$binvalue .= str_pad(decbin(ord(substr($byteword, $i, 1))), 8, "0", STR_PAD_LEFT);
		}
		return $binvalue;
	}
	
	function myPrint($thisvar,$color){
		echo '<pre style="color:'.$color.'">';
		print_r($thisvar);
		echo "</pre>";
	}

	function myMicrotime() {
		list($usec, $sec) = explode(' ', microtime()); 
	return ((float)$usec + (float)$sec); 
	}

	function GetGenre($genreid) {
		if ($genreid == 'RX'){
		return 'Remix';
		}
		elseif($genreid == 'CR'){
		return 'Cover';
		}
		else{
		$genreid = (int)$genreid;
		}
		$thatgenreid=(isset($this->LookGenre[$genreid]) ? $this->LookGenre[$genreid] : $genreid);
		if ($thatgenreid>147)  $thatgenreid='Unknown';
		return $thatgenreid;
	}

	function myPutKeys($KeysA,$oldArray){
		for ($i=0;$i<count($KeysA);$i++){
		$newArray[$KeysA[$i]]=$oldArray[$i];
		}
	return $newArray;
	}

	function ExtractInfo($Class,$SubClass,$Content){
	switch ($Class){
		case '2':
		$val = unpack ("A1Encoding/a*Value", $Content);
		break;
		case '3':
		$valA = unpack ("A1Encoding/a*Value", $Content);
		$valB = $this->myPutKeys(array("Description","Value"),explode(chr(0),$valA['Value']));
		$val=array_merge($valA,$valB);
		break;
		case '4':
		$val['Value'] = $Content;
		break;
		case '0':
		$val['Value'] = $Content;
		break;
		case '5':  
		$valA = unpack ("A1Encoding/a3Lang/a*Value", $Content);
		$valB = $this->myPutKeys(array("Description","Value"),explode(chr(0),$valA['Value']));
		$val=array_merge($valA,$valB);
		break;
		case '13':  
		$val = unpack ("A1Encoding/a3Lang/a*Value", $Content);
		break;
		case '8':
		$val['Encoding']=substr($Content,0,1);
		$Content=substr($Content,1);
		$pos=strpos($Content,chr(0));
		$val['MIMEtype']=substr($Content,0,$pos);
		$Content=substr($Content,$pos+1);
		$pos=strpos($Content,chr(0));
		$val['Filename']=substr($Content,0,$pos);
		$Content=substr($Content,$pos+1);
		$pos=strpos($Content,chr(0));
		$val['Description']=substr($Content,0,$pos);
		$val['Value']=substr($Content,$pos+1);
		break;
		case '7':
		$val['Encoding']=substr($Content,0,1);
		$Content=substr($Content,1);
		$pos=strpos($Content,chr(0));
		$val['MIMEtype']=substr($Content,0,$pos);
		$Content=substr($Content,$pos+1);
		$val['Picturetype']=substr($Content,0,1);
		$Content=substr($Content,1);
		$pos=strpos($Content,chr(0));
		$val['Description']=substr($Content,0,$pos);
		$val['Value']=substr($Content,$pos+1);
		break;
		case '12':
		$val['Encoding']=substr($Content,0,1);
		$val['ImageFormat']=substr($Content,1,3);
		$val['Picturetype']=substr($Content,4,1);
		$Content=substr($Content,5);
		$pos=strpos($Content,chr(0));
		$val['Description']=substr($Content,0,$pos);
		$val['Value']=substr($Content,$pos+1);
		break;
		case '1':
		$val = $this->myPutKeys(array("Owner","Value"),explode(chr(0),$Content));
		break;
		case '10':
		$tempCont=strtolower(substr($Content,0,12));
		$pos=strpos($tempCont,"http");
		if ($pos===false) $pos=strpos($tempCont,"mail");
		$val['FrameId']=substr($Content,0,$pos);
		$Content=substr($Content,$pos);
		$pos=strpos($Content,chr(0));
		$val['URL']=substr($Content,0,$pos);
		$val['Value']=substr($Content,$pos+1);
		break;
		case '9':
		if ($Subclass=='2'){
		$pos=strpos($Content,chr(0));
		$val['Email']=substr($Content,0,$pos);
		$Content=substr($Content,$pos+1);
		$val['Rating']=substr($Content,0,1);
		$val['Value']=substr($Content,1);
		}
		else{
		$val['Value']=substr($Content,0);
		}
		break;
		case '11':
		$val['Encoding']=substr($Content,0,1);
		$Content=substr($Content,1);
		$pos=strpos($Content,chr(0));
		$val['Price']=substr($Content,0,$pos);
		$Content=substr($Content,$pos+1);
		if ($SubClass=='2'){
		$pos=strpos($Content,chr(0));
		$val['ValidAndUrl']=substr($Content,0,$pos);
		$Content=substr($Content,$pos+1);
		$val['ReceivedAs']=substr($Content,0,1);
		$Content=substr($Content,1);
		$pos=strpos($Content,chr(0));
		$val['seller']=substr($Content,0,$pos);
		$Content=substr($Content,$pos+1);
		$pos=strpos($Content,chr(0));
		$val['Description']=substr($Content,0,$pos);
		$Content=substr($Content,$pos+1);
		$val['MIME type']=substr($Content,0,1);
		$val['Value']=substr($Content,1);
		}
		else{
		$val['DateAndSeller']=substr($Content,$pos+1);
		}
		break;
		default:
		$val['Value']=$Content;
		break;
		}
	return $val;
	}

	function GoodFrame($namelen,$FrameId,$str){
	$FrameId=strrev($FrameId);
		for ($i=0;$i<strlen($FrameId);$i++){
		$ordX=ord(substr($FrameId,$i,1));
			if ($i>0){
				if ($ordX<65 || $ordX>90){  //only [A-Z]
				return false;
				}
			}
			else{
				if (($ordX<48 || $ordX>90) || ($ordX>57 && $ordX<65)){ //[A-Z] and numbers
				return false;
				}
			}
		}
	return true;
	}
	
	function MpegGoodHeader($tempHeader){
		$Byte1=ord(substr($tempHeader,0,1));
		$Byte2=ord(substr($tempHeader,1,1));
		$Byte3=ord(substr($tempHeader,2,1));
			if ($Byte1==255 && $Byte2>223 && $Byte3<240){
			return true;
			}
			else{
			return false;
			}
	}
	
	function ProcesId3v2(){
		rewind($this->TargetSrcFile);
		$this->conf['Id3v2HeaderPos']=0;
		$this->conf['Id3v2FramesPos']=0;
		$Id3Header = fread ($this->TargetSrcFile, 10);
		$val = unpack ("a3FileIdentifier/C1MayorVersion/C1MinorVersion/a1Flags/H8TagLen", $Id3Header);
			if ($val['FileIdentifier']=="ID3"){
			$this->conf['FileIdentifier']="WithID3v2";
				if ($val['MayorVersion']<=2){
				$this->conf['MajorVersion']=2;
				$this->conf['FrameNameLen']=3;
				$this->conf['FrameFlagLen']=0;
				$this->conf['PaddingBreakStr']=chr(0).chr(0).chr(0);
				}
				else{
				$this->conf['MajorVersion']=4;
				$this->conf['FrameNameLen']=4;
				$this->conf['FrameFlagLen']=2;
				$this->conf['PaddingBreakStr']=chr(0).chr(0).chr(0).chr(0);
					if ($val['MayorVersion']>4){
					$this->conf['BiggerMajorVersion']=$this->conf['MajorVersion'];
					}
				}
			$this->conf['MinorVersion']=$val['MinorVersion'];
			$StrFlags=$this->myStrBin2Bin($val['Flags']);
			$this->conf['HasSynchro']	= $this->LookHeaderFlags[$this->conf['MajorVersion']]['HasSynchro'][substr($StrFlags,0,1)];
			$this->conf['HasExtHeader']	= $this->LookHeaderFlags[$this->conf['MajorVersion']]['HasExtHeader'][substr($StrFlags,1,1)];
			$this->conf['Experimental']	= $this->LookHeaderFlags[$this->conf['MajorVersion']]['Experimental'][substr($StrFlags,2,1)];
			$this->conf['HasFooter']	= $this->LookHeaderFlags[$this->conf['MajorVersion']]['HasFooter'][substr($StrFlags,3,1)];
			$this->conf['FramesLen']=$this->myHex2IntSyn($val['TagLen']);
				if ($this->conf['FramesLen']>=$this->conf['FileSize']){
				$this->conf['FramesLen']=0;
				$this->conf['OffpaddingBreak']=0;
				$this->conf['FileIdentifier']='BadId3';
				}
				else{
				$this->conf['Id3v2FramesPos']=10;
				}
			}
			else{
			$this->conf['FramesLen']=0;
			$this->conf['OffpaddingBreak']=0;
			$this->conf['FileIdentifier']="NoID3v2";
			}
	
	
		if($this->conf['FileIdentifier']=='WithID3v2'){
		fseek($this->TargetSrcFile,$this->conf['Id3v2FramesPos']);
		$framedata=fread($this->TargetSrcFile,$this->conf['FramesLen']);
		$i=0;
		$Xoffset=0;
		$XoffsetAcu=0;
		$ready=false;
		$modified=false;
		$FrameDataLen=$this->conf['FramesLen'];
			while(($FrameDataLen-$XoffsetAcu)>$this->conf['FrameNameLen']){
			$TempTagLen=$FrameDataLen-$XoffsetAcu;
			$PrevFrameId=$FrameId;
			$FrameId=substr($framedata, 0, $this->conf['FrameNameLen']);
			if ($FrameId==$this->conf['PaddingBreakStr']){
			break;
			}
			elseif (strpos(strtolower($FrameId),"mp3")!==false){
			break;
			}
			else{
			$num=0;
				if ($this->GoodFrame($this->conf['FrameNameLen'],$FrameId,"ini")==false){
				$ready=false;
					if ($modified==false && $this->conf['FrameNameLen']==4 && $this->GoodFrame(3,substr($FrameId,0,3),"v3")==true){
						$XtempSize=$this->myBigEndian2Int(substr($framedata,3, 3));
						if ($XtempSize<=$TempTagLen){
						$FrameId=substr($FrameId,0,3);
						$this->conf['MajorVersionAlt']=2;
						$this->conf['FrameNameLen']=3;
						$this->conf['FrameFlagLen']=0;
						$this->conf['PaddingBreakStr']=chr(0).chr(0).chr(0);
						$tempSize=$XtempSize;
						$ready=true;
						}
					$modified=true; //solo una vez
					}
				}
				else{
				$ready=true;
				$tempSize=$this->myBigEndian2Int(substr($framedata,$this->conf['FrameNameLen'], $this->conf['FrameNameLen']));
					if ($tempSize>$TempTagLen){
					$ready=false;
					}
				}
				if ($ready==false){
				$num=0;
				$notappend=false;
					while($ready==false){
					$FrameId=substr($framedata, $num, $this->conf['FrameNameLen']);
						if ($this->GoodFrame($this->conf['FrameNameLen'],$FrameId,"error")==true){
							$tempSize=$this->myBigEndian2Int(substr($framedata,$num+$this->conf['FrameNameLen'], $this->conf['FrameNameLen']));
							if ($tempSize<=$TempTagLen){
							$ready=true;
							break;
							}
						}
					if (($TempTagLen-$num)<=0){
					$notappend=true;
					$ready=true;
					break;
					}
					$num++;
					}
				}
			}
			if ($notappend==false){
				if ($num>0){
				$previndex=count($this->id3v2Info[$PrevFrameId]['info'])-1;
				$this->id3v2Info[$PrevFrameId]['info'][$previndex]['adddata']=$num;
				$this->id3v2Info[$PrevFrameId]['info'][$previndex]['Value'].=substr($framedata,0,$num);
				}
			$inipos=$this->conf['FrameNameLen']*2;
			$Flags=substr($framedata, $inipos, $this->conf['FrameFlagLen']);
			$index=count($this->id3v2Info[$FrameId]['info']);
			$inipos+=$this->conf['FrameFlagLen'];
			$Content=substr($framedata,$inipos,$tempSize);
			$SyncData=0;
			if ($this->conf['HasSynchro']==2){
			$SyncData=substr_count($Content,chr(255).chr(0));
			if ($SyncData>0){
			$inipos+=$tempSize;
			$Content.=substr($inipos,$SyncData);
			}
			}
			$this->id3v2Info[$FrameId]['LongName']=isset($this->FX[$FrameId]['LongName']) ? $this->FX[$FrameId]['LongName'] : 'unkwonw';
			$this->id3v2Info[$FrameId]['info'][$index]=$this->ExtractInfo($this->FX[$FrameId]['Class'],$this->FX[$FrameId]['SubClass'],$Content);
			$this->id3v2Info[$FrameId]['info'][$index]['Size']=$tempSize;
			if ($SyncData>0){
			$this->id3v2Info[$FrameId]['info'][$index]['SyncData']=$SyncData;
			}
			}
			else{
			//echo "fallo en id3";
			}
			
			$Xoffset=($this->conf['FrameNameLen']*2)+$this->conf['FrameFlagLen']+$tempSize+$num+$SyncData;
			$XoffsetAcu+=$Xoffset;
			$framedata=substr($framedata,$Xoffset);
			$i++;
			if ($i>100) break;
		}
		
		$this->conf['MpegHeaderPos']=$this->conf['Id3v2FramesPos']+$this->conf['FramesLen'];
		fseek($this->TargetSrcFile,$this->conf['MpegHeaderPos']);
		$temp=fread($this->TargetSrcFile,4);
			if (bin2hex($temp)=='00000000'){
			$this->conf['OffPaddingPos']=$this->conf['MpegHeaderPos'];
			$this->conf['MpegHeaderPos']+=4;
			$ready=false;
				while ($ready===false){
				$temp=fread($this->TargetSrcFile,500);
				$ready=strpos($temp,chr(255));
				if ($ready===false)$this->conf['MpegHeaderPos']+=500;
				else $this->conf['MpegHeaderPos']+=$ready;
				}
			$this->conf['OffPaddingLen']=$this->conf['MpegHeaderPos']-$this->conf['OffPaddingPos'];
			}
		$this->conf['OffpaddingBreak']=$this->conf['MpegHeaderPos'];
		}
	}
	
	function ProcesMpeg(){
	fseek($this->TargetSrcFile,$this->conf['OffpaddingBreak']);
	$this->conf['tempHeader']=fread($this->TargetSrcFile,4);
	$ready=false;
	$y=0;
	$i=0;
	$x=0;
		while($ready==false){
			if($this->conf['MpegHeaderPos']>=$this->conf['FileSize']) $i=50;
			if ($this->MpegGoodHeader($this->conf['tempHeader'])==true){
			$this->mpegInfo=$this->ReadMpegHeader($this->conf['tempHeader']);
				$HasXing=false;
				$HasVbri=false;
				if ($y<=2){
				$temp=fread($this->TargetSrcFile,36);
				$HasXing=strpos($temp,"Xing");
				$HasVbri=strpos($temp,"VBRI");
				}
				if ($HasXing!==false || $HasVbri!==false){
				$ready=true;
				break;
				}
				elseif ($this->mpegInfo['ThisFramelen']>0){
					if ($this->jumpMpegHeader($this->conf['MpegHeaderPos'],$this->mpegInfo['ThisFramelen'])){
					$ready=true;
					break;
					} 
					else{
					$i++;
					$this->LookforMpegHeader();
					}
				}
				else{
				$i++;
				$this->LookforMpegHeader();
				}
			$y++;	
			}
			else{
			$i++;
			$this->LookforMpegHeader();
			}
			if ($i>=50){
			break;
			}
		}
		if ($ready){
		$this->conf['OffFileLen']=$this->conf['MpegHeaderPos']-$this->conf['OffpaddingBreak'];
		$this->ReadMoreMpegHeader($this->conf['tempHeader']);
		$this->Getid3v1();
		$this->GetVbr();
		$this->GetPlayTime();
		}
		else{
		$this->conf['FileIdentifier']="MaybeNoMP3file";
		}
	}

	function Getid3v1(){
		fseek($this->TargetSrcFile, -128, SEEK_END);
		$id3v1tag = fread($this->TargetSrcFile, 128);
		$id3v1name	  = trim(substr($id3v1tag,  0, 3));
		if ($id3v1name=="TAG" || $id3v1name=="ID3"){
		$this->id3v1Info['exist']=1;
		$this->id3v1Info['title']   = trim(substr($id3v1tag,  3, 30));
		$this->id3v1Info['artist']  = trim(substr($id3v1tag, 33, 30));
		$this->id3v1Info['album']   = trim(substr($id3v1tag, 63, 30));
		$this->id3v1Info['year']    = trim(substr($id3v1tag, 93,  4));
		$id3v1com = substr($id3v1tag, 97, 30);
			if ((substr($id3v1com , 28, 1) === chr(0)) && (substr($id3v1com , 29, 1) !== chr(0))) {
				$this->id3v1Info['track'] = ord(substr($id3v1com , 29, 1));
				$id3v1com  = substr($id3v1com , 0, 28);
			}
		$id3v1genre = ord(substr($id3v1tag, 127, 1));	
		$this->id3v1Info['comment'] = trim($id3v1com );
		$this->id3v1Info['genreID'] = $id3v1genre;
		$this->id3v1Info['genre'] = $this->GetGenre($id3v1genre);
		}
		else{
		$this->id3v1Info['exist']=0;
		return false;
		}
	}

	function GetPlayTime(){
		$this->mpegInfo['PlaySeconds']=($this->mpegInfo['AudioBytes']*8)/($this->mpegInfo['BitrateDec']*1000);
		$temptime=$this->mpegInfo['PlaySeconds'];
		$horas=floor($temptime/3600);
		$h=" ";$m="";$s="";
		if ($horas<10) $h=0;
		$temptime-=(3600*$horas);
		$minutos=floor($temptime/60);
		if ($minutos<10) $m=0;
		$temptime-=(60*$minutos);
		$segundos=floor($temptime);
		if ($segundos<10) $s=0;
		$this->mpegInfo['PlayTime']=sprintf($h."%d:".$m."%d:".$s."%d",$horas,$minutos,$segundos);
	}

	function GetVbr(){
		if ($this->mpegInfo['AudioVersion'] == 'MPEG1') {
			if ($this->mpegInfo['ChannelMode'] == 'Single channel') $VBRidOffset = 17; 
			else $VBRidOffset = 32; 
		} else { // 2 or 2.5
			if ($ChannelMode == 'Single channel') $VBRidOffset = 9;  
			else $VBRidOffset = 17; 
		}
	fseek($this->TargetSrcFile,$this->conf['MpegHeaderPos']+4+$VBRidOffset);
	$vbrMethod = fread($this->TargetSrcFile, 4);
		if ($vbrMethod=='Xing'){
		$this->mpegInfo['vbrMethod']=$vbrMethod;
		$vbrFlags = fread($this->TargetSrcFile, 4);
		$vbrByte4 = $this->myStrBin2Bin(substr($vbrFlags,3,1));
		$vbr['frames']    = substr($vbrByte4, 4, 1);
		$vbr['bytes']     = substr($vbrByte4, 5, 1);
		$vbr['toc']       = substr($vbrByte4, 6, 1);
		$vbr['vbr_scale'] = substr($vbrByte4, 7, 1);
		$vbrAudio=fread($this->TargetSrcFile, 8);
		
			$uno=$this->myBigEndian2Int(substr($vbrAudio,0,4));
			$dos=$this->myBigEndian2Int(substr($vbrAudio,4,4));
			
			if ($uno>0 && $dos>0){
				if ($dos>$uno){
				//echo "vbr dos mayor";
				$this->mpegInfo['AudioBytes']=$dos;
				$this->mpegInfo['AudioFrames']=$uno;
				}
				else{
				$this->mpegInfo['AudioBytes']=$uno;
				$this->mpegInfo['AudioFrames']=$dos;
				}
				if (($this->mpegInfo['AudioBytes'])>$this->conf['FileSize']){
				$this->mpegInfo['LostAudio']=round((($this->mpegInfo['AudioBytes']/$this->conf['FileSize'])*100)-100);
				if ($this->mpegInfo['LostAudio']>10){
				$this->mpegInfo['vbrMethod']='VeryBadXing';
				$this->mpegInfo['BitrateDec']=$this->mpegInfo['Bitrate'];
				$this->mpegInfo['AudioBytes']=$this->conf['FileSize']-($this->id3v1Info['exist']*128)-$this->conf['MpegHeaderPos'];
				$this->conf['FileIdentifier'] = 'MaybeNoID3file';
				}
				}
			
				
			}
			else{
			$this->mpegInfo['vbrMethod']='BadXing';
			$this->mpegInfo['BitrateDec']=$this->mpegInfo['Bitrate'];
			$AudioBytes=$this->conf['FileSize']-($this->id3v1Info['exist']*128)-$this->conf['MpegHeaderPos'];
				if ($dos>(($AudioBytes/3)*2)){
				$this->mpegInfo['AudioBytes']=$dos;
				}
				else{
				$this->mpegInfo['AudioBytes']=$AudioBytes;
				}
			}
		}
		elseif ($vbrMethod=='VBRI'){
		$this->mpegInfo['vbrMethod']=$vbrMethod;
		}
		else{
		$this->mpegInfo['vbrMethod']='CBR';
		$this->mpegInfo['BitrateDec']=$this->mpegInfo['Bitrate'];
		$this->mpegInfo['AudioBytes']=$this->conf['FileSize']-($this->id3v1Info['exist']*128)-$this->conf['MpegHeaderPos'];
		if($this->mpegInfo['MaybeVbr']==1){
		$this->mpegInfo['vbrMethod']='UnkVBR';
		$this->mpegInfo['BitrateDec']=$this->CalculateVbr($this->conf['MpegHeaderPos'],$this->mpegInfo['ThisFramelen']);
		$this->mpegInfo['AudioBytes']=$this->conf['FileSize']-($this->id3v1Info['exist']*128)-$this->conf['MpegHeaderPos'];
		}
		}
		
		//if ($vbrMethod=='Xing' || $vbrMethod=='VBRI'){
		if ($this->mpegInfo['vbrMethod']=='Xing'){
			$this->mpegInfo['AudioFrames']--; // don't count the Xing / VBRI frame
			$coe=0;
			if ($this->mpegInfo['AudioVersion']=="MPEG1" && $this->mpegInfo['LayerDescrip']=="LayerI"){$coe=384;}
			elseif (($this->mpegInfo['AudioVersion']=="MPEG2" || $this->mpegInfo['AudioVersion']=="MPEG2.5") && $this->mpegInfo['LayerDescrip']=="LayerIII"){$coe=576;}
			else{$coe=1152;}
			if ($coe!=0){
			$VBRBitrate=((($this->mpegInfo['AudioBytes']/$this->mpegInfo['AudioFrames']) * 8) * (($this->mpegInfo['SamplingRate'] / $coe)) / 1000);
	    //echo "VBRBitrate=(((".$this->mpegInfo['AudioBytes']."/".$this->mpegInfo['AudioFrames'].") * 8) * ((".$this->mpegInfo['SamplingRate']." / ".$coe.")) / 1000)=".$VBRBitrate;
	
			$this->mpegInfo['Bitrate']=round($VBRBitrate);
			$this->mpegInfo['BitrateDec']=$VBRBitrate;
			}
		}
	
	}


	function ReadMoreMpegHeader($MpegHeader){
	$MpegHeader=substr($this->myStrBin2Bin($MpegHeader),24);
	$ChannelMode =substr($MpegHeader,0,2);
	$ModeExten	 =substr($MpegHeader,2,2);
	$Copyright	 =substr($MpegHeader,4,1);
	$Original	 =substr($MpegHeader,5,1);
	$Emphasis	 =substr($MpegHeader,6,2);
	$this->mpegInfo['ChannelMode']=$this->ChannelMode[$ChannelMode];
	if ($this->mpegInfo['ChannelMode']=='Joint stereo'){
		if($this->mpegInfo['LayerDescrip']=='LayerIII'){
		$this->mpegInfo['IntensityMSstereo']=$this->Intensity['IntensityMSstereo'][$ModeExten];
		}
		else{
		$this->mpegInfo['Band']=$SubbandsAR['Band'][$ModeExten];
		}
	}
	$this->mpegInfo['Copyright']=(int)$Copyright;
	$this->mpegInfo['Original']=(int)$Original;
	$this->mpegInfo['Emphasis']=$this->Emphasis[$Emphasis];
	}
	
	function CalculateVbr($pos,$framelen){
	$index=0;
	$jumps=30;
		while($index<$jumps){
		$pos=$pos+$framelen;
		if ($framelen==0)break;
		fseek($this->TargetSrcFile,$pos);
		$tempHeader=fread($this->TargetSrcFile,4);
			if ($this->MpegGoodHeader($tempHeader)==true){
			$ff=array();
			$mpegInfo=$this->ReadMpegHeader($tempHeader);
				if (isset($mpegInfo['ThisFramelen'])){
				$framelen=$mpegInfo['ThisFramelen'];
				$ff[$index]['framelen']=$framelen;
				$ff[$index]['bitrate']=$mpegInfo['Bitrate'];
				}
				else{
				break;
				}
			}
			else{
			break;
			}
		$index++;
		}
	
	krsort($ff);	
	$i=0;
	$tt=0;
	while (list($k,$v)=each($ff)){
	$tt+=$v['framelen'];
	if ($i>20)break;
	$i++;
	}
	reset($ff);
	$i=0;
	$xx=0;
	while (list($k,$v)=each($ff)){
	$xx+=($v['framelen']/$tt)*$v['bitrate'];
	if ($i>20)break;
	$i++;
	}
	unset($this->mpegInfo['MaybeVbr']); 
	return $xx;
	}


	function jumpMpegHeader($pos,$framelen){
	$index=0;
		while($index<$this->jumps){
		$pos=$pos+$framelen;
		if ($framelen==0)break;
		fseek($this->TargetSrcFile,$pos);
		$tempHeader=fread($this->TargetSrcFile,4);
			if ($this->MpegGoodHeader($tempHeader)==true){
			$mpegInfo=$this->ReadMpegHeader($tempHeader);
				if (isset($mpegInfo['ThisFramelen'])){
				$prevframelen=$framelen;
				$framelen=$mpegInfo['ThisFramelen'];
					if ($framelen>$prevframelen+2 || $framelen<$prevframelen-2){
					$this->mpegInfo['MaybeVbr']=1;
					}
				}
				else{
				break;
				}
			}
			else{
			break;
			}
		$index++;
		}
		if ($index==$this->jumps)	return true;
		else return false;
	}


	function LookforMpegHeader(){
	$this->conf['MpegHeaderPos']+=1;
	fseek($this->TargetSrcFile,$this->conf['MpegHeaderPos']);
	$ready=false;
	while ($ready==false){
	if($this->conf['MpegHeaderPos']>=$this->conf['FileSize']) break;
		$temp=fread($this->TargetSrcFile,1000);
		while (strlen($temp)>0){
			$x=strpos($temp,chr(255));
			if ($x===false){
			$FileOffsetLen+=1000;
			$this->conf['MpegHeaderPos']+=1000;
			break;
			}
			else{
			$this->conf['MpegHeaderPos']+=$x;
				while($ready==false){
				fseek($this->TargetSrcFile,$this->conf['MpegHeaderPos']);
				$temp2=hexdec(bin2hex(fread($this->TargetSrcFile,20)));
					if ($temp2!=-1){
					$ready=true;
					}
					else{
					$this->conf['MpegHeaderPos']+=20;
					}
				}
			fseek($this->TargetSrcFile,$this->conf['MpegHeaderPos']);
			$this->conf['tempHeader']=fread($this->TargetSrcFile,4);
			break;
			}
		}
	}
	}

	function ReadMpegHeader($MpegHeader){
	$HeaderBits=$this->myStrBin2Bin($MpegHeader);
	$BadSamplingRate=false;
	$mpegInfo=array();
	$mpegInfo['AudioVersion']=$this->LookAudioVersion[substr($HeaderBits,11,2)];
		if ($mpegInfo['AudioVersion']=='Reserved'){
		$mpegInfo['ThisFramelen']=0;
		}
		else{
		$mpegInfo['LayerDescrip']=$this->LookLayerDescrip[substr($HeaderBits,13,2)];
		$mpegInfo['ProtecBit']=(int)substr($HeaderBits,15,1);
		$mpegInfo['Bitrate']=$this->LookBitrateValues[$mpegInfo['AudioVersion']][$mpegInfo['LayerDescrip']][$this->LookBitrateIndex[substr($HeaderBits,16,4)]];
		$mpegInfo['SamplingRate']=$this->LookSamplingRate[$mpegInfo['AudioVersion']][substr($HeaderBits,20,2)];
			if ($mpegInfo['SamplingRate']=='Reserved'){
			$mpegInfo['ThisFramelen']=0;
			}else{
			$mpegInfo['PaddingBit']=(int)substr($HeaderBits,22,1);
			$coef=$this->LookFrameLen[$mpegInfo['AudioVersion']][$mpegInfo['LayerDescrip']]['coef'];
			$slotlen=$this->LookFrameLen[$mpegInfo['AudioVersion']][$mpegInfo['LayerDescrip']]['slotlen'];
			$FrameLengthInBytes = ($coef * $mpegInfo['Bitrate'] * 1000 / $mpegInfo['SamplingRate'] + $mpegInfo['PaddingBit']) * $slotlen;
			$mpegInfo['ThisFramelen']=floor($FrameLengthInBytes);
			}
		}
	return $mpegInfo;
	}

	function ShowInfo(){
		if (1==1){
		while (list($k,$v)=each($this->id3v2Info)){
			for($i=0;$i<count($v['info']);$i++){
			if ($k=="NCON" || $k=="GEOB" || $k=="APIC" || $k=="PIC"){
			$this->id3v2Info[$k]['info'][$i]['Value']="BetterNotShowIt";
			}
			else{
			$this->id3v2Info[$k]['info'][$i]['Value']=htmlspecialchars($this->id3v2Info[$k]['info'][$i]['Value']);
			}
			}
		}
		}
		$this->myPrint($this->conf,'red');
		$this->myPrint($this->id3v2Info,'green');
		$this->myPrint($this->mpegInfo,'blue');
		$this->myprint($this->id3v1Info,'orange');
	}
	
	function GetInfo($TargetNamFile){
		if ($this->OpenFile($TargetNamFile)){
		$this->ProcesId3v2();
		$this->ProcesMpeg();
		$this->CloseFile();
		}
	}
	
	
	function myReaddir($dir){
	$i;
	$handle=opendir($dir);
	$onlymp3=true;
		while (false !== ($file = readdir($handle))) { 
			if ($file!="." && $file!=".."){
				if(is_dir($dir."/".$file)){
				echo "<h3>".$dir."/".$file."</h3>";
					if ($file!='Recycled'){
					$this->myReaddir($dir."/".$file);
					}
				}
				else{
					if ($onlymp3){
						if (eregi("(.mp3)$",$file)) $ok=true;
						else $ok=false;
					}
					else{
					$ok=true;
					}
					if ($ok){
					echo $i."--".$dir."/".$file.'<br>';
					set_time_limit(5);
					$this->GetInfo($dir."/".$file);
					$this->ShowInfo();
					$i++;
					}
				}
			}
		}
	closedir($handle); 
	}
}//end class
?>