<?php
   	/* This code is released under the GNU LGPL. Go read it over here:
	*
	* http://www.gnu.org/copyleft/lesser.txt 
	*/
	
$this->LookAudioVersion=array('00'=>'MPEG2.5','01'=>'Reserved','10'=>'MPEG2','11'=>'MPEG1');
$this->LookLayerDescrip=array('00'=>'reserved','01'=>'LayerIII','10'=>'LayerII','11'=>'LayerI');
$this->Emphasis=array('00'=>'none','01'=>'50/15 ms','10'=>'reserved','11'=>'CCIT J.17');
$this->ChannelMode=array('00'=>'Stereo','01'=>'Joint stereo','10'=>'Dual channel','11'=>'Single channel');

$this->Intensity=array(
	'IntensityMSstereo'=>array('00'=>'off','01'=>'on-off','10'=>'off-on','11'=>'on'),
	'Band'=>array('00'=>'4 to 31','01'=>'8 to 31','10'=>'12 to 31','11'=>'16 to 31')
);

$this->LookSamplingRate=array(
	'MPEG1'=>array('00'=>'44100','01'=>'48000','10'=>'32000','11'=>'Reserved'),
	'MPEG2'=>array('00'=>'22050','01'=>'24000','10'=>'16000','11'=>'Reserved'),
	'MPEG2.5'=>array('00'=>'11025','01'=>'12000','10'=>'8000','11'=>'Reserved')
);


$this->LookBitrateValues=array(
	'MPEG1'=>array(
		'LayerI'=>array('free',32,64,96,128,160,192,224,256,288,320,352,384,416,448,'bad'),
		'LayerII'=>array('free',32,48,56,64,80,96,112,128,160,192,224,256,320,384,'bad'),
		'LayerIII'=>array('free',32,40,48,56,64,80,96,112,128,160,192,224,256,320,'bad')
	),
	'MPEG2'=>array(

		'LayerI'=>array('free',32,48,56,64,80,96,112,128,144,160,176,192,224,256,'bad'),
		'LayerII'=>array('free',8,16,24,32,40,48,56,64,80,96,112,128,144,160,'bad'),
		'LayerIII'=>array('free',8,16,24,32,40,48,56,64,80,96,112,128,144,160,'bad')
	),	
	'MPEG2.5'=>array(
		'LayerI'=>array('free',32,48,56,64,80,96,112,128,144,160,176,192,224,256,'bad'),
		'LayerII'=>array('free',8,16,24,32,40,48,56,64,80,96,112,128,144,160,'bad'),
		'LayerIII'=>array('free',8,16,24,32,40,48,56,64,80,96,112,128,144,160,'bad')
	)
);
$this->LookBitrateIndex=array(
	'0000'=>0,
	'0001'=>1,
	'0010'=>2,
	'0011'=>3,
	'0100'=>4,
	'0101'=>5,
	'0110'=>6,
	'0111'=>7,
	'1000'=>8,
	'1001'=>9,
	'1010'=>10,
	'1011'=>11,
	'1100'=>12,
	'1101'=>13,
	'1110'=>14,
	'1111'=>15
);

$this->LookFrameLen=array(
	'MPEG1'=>array(
		'LayerI'=>array('coef'=>12,'slotlen'=>4),
		'LayerII'=>array('coef'=>144,'slotlen'=>1),
		'LayerIII'=>array('coef'=>144,'slotlen'=>1)
	),
	'MPEG2'=>array(
		'LayerI'=>array('coef'=>24,'slotlen'=>4),
		'LayerII'=>array('coef'=>72,'slotlen'=>1),
		'LayerIII'=>array('coef'=>72,'slotlen'=>1)
	)
);
$this->LookFrameLen['MPEG2.5']=$this->LookFrameLen['MPEG2'];

$this->LookHeaderFlags=array(
	2=>array(	
		'HasSynchro'	=>array(0=>0,1=>1),
		'Hascompresion'	=>array(0=>0,1=>1),
		'HasExtHeader'	=>array(0=>0,1=>0),
		'Experimental'	=>array(0=>0,1=>0),
		'HasFooter'		=>array(0=>0,1=>0)
	),
	3=>array(
		'HasSynchro'	=>array(0=>0,1=>1),
		'Hascompresion'	=>array(0=>0,1=>0),
		'HasExtHeader'	=>array(0=>0,1=>1),
		'Experimental'	=>array(0=>0,1=>1),
		'HasFooter'		=>array(0=>0,1=>0)
	),
	4=>array(	
		'HasSynchro'	=>array(0=>0,1=>1),
		'Hascompresion'	=>array(0=>0,1=>0),
		'HasExtHeader'	=>array(0=>0,1=>1),
		'Experimental'	=>array(0=>0,1=>1),
		'HasFooter'		=>array(0=>0,1=>1)
	)
);





$this->Codes['00']='ISO-8859-1';	//Terminated with $this->00.
$this->Codes['01']='UTF-16';		//with BOM. All strings in the same frame SHALL have the same byteorder. Terminated with $this->00 00.
$this->Codes['02']='UTF-16BE';		//without BOM. Terminated with $this->00 00.
$this->Codes['03']='UTF-8'; 		//Terminated with $this->00

$this->HexPictureType['00']='Other';
$this->HexPictureType['01']='32x32 pixels "file icon" (PNG only)';
$this->HexPictureType['02']='Other file icon';
$this->HexPictureType['03']='Cover (front)';
$this->HexPictureType['04']='Cover (back)';
$this->HexPictureType['05']='Leaflet page';
$this->HexPictureType['06']='Media (e.g. lable side of CD)';
$this->HexPictureType['07']='Lead artist/lead performer/soloist';
$this->HexPictureType['08']='Artist/performer';
$this->HexPictureType['09']='Conductor';
$this->HexPictureType['0A']='Band/Orchestra';
$this->HexPictureType['0B']='Composer';
$this->HexPictureType['0C']='Lyricist/text writer';
$this->HexPictureType['0D']='Recording Location';
$this->HexPictureType['0E']='During recording';
$this->HexPictureType['0F']='During performance';
$this->HexPictureType['10']='Movie/video screen capture';
$this->HexPictureType['11']='A bright coloured fish';
$this->HexPictureType['12']='Illustration';
$this->HexPictureType['13']='Band/artist logotype';
$this->HexPictureType['14']='Publisher/Studio logotype';




$this->FX['UFI']=array(
'LongName'	=>'Unique file identifier',
'Descrip'	=>'This frame\'s purpose is to be able to identify the audio file in a database that may contain more information relevant to the content',
'Class'		=>1,
'SubClass'	=>'',
'Versions'	=>'111'
);

$this->FX['UFID']=$this->FX['UFI'];


$this->FX['TT1']=array(
'LongName'	=>'Content group description',
'Descrip'	=>'Is used if the sound belongs to a larger category of sounds/music. For example, classical music is often sorted in different musical sections (e.g. "Piano Concerto", "Weather - Hurricane")',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['TIT1']=$this->FX['TT1'];


$this->FX['TT2']=array(
'LongName'	=>'Title/Songname/Content description',
'Descrip'	=>'Is the actual name of the piece (e.g. "Adagio", "Hurricane Donna")',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['TIT2']=$this->FX['TT2'];


$this->FX['TT3']=array(
'LongName'	=>'Subtitle/Description refinement',
'Descrip'	=>'Is used for information directly related to the contents title (e.g. "Op. 16" or "Performed live at wembley")',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['TIT3']=$this->FX['TT3'];


$this->FX['TP1']=array(
'LongName'	=>'Lead artist(s)/Lead performer(s)/Soloist(s)/Performing group',
'Descrip'	=>'Is used for the main artist(s)',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['TPE1']=$this->FX['TP1'];


$this->FX['TP2']=array(
'LongName'	=>'Band/Orchestra/Accompaniment',
'Descrip'	=>'Is used for additional information about the performers in the recording',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['TPE2']=$this->FX['TP2'];


$this->FX['TP3']=array(
'LongName'	=>'Conductor',
'Descrip'	=>'Is used for the name of the conductor',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['TPE3']=$this->FX['TP3'];


$this->FX['TP4']=array(
'LongName'	=>'Interpreted, remixed, or otherwise modified by',
'Descrip'	=>'Contains more information about the people behind a remix and similar interpretations of another existing piece',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['TPE4']=$this->FX['TP4'];


$this->FX['TCM']=array(
'LongName'	=>'Composer(s)',
'Descrip'	=>'Is intended for the name of the composer(s)',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['TCOM']=$this->FX['TCM'];


$this->FX['TXT']=array(
'LongName'	=>'Lyricist(s)/text writer(s)',
'Descrip'	=>'Intended for the writer(s) of the text or lyrics in the recording',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['TEXT']=$this->FX['TXT'];


$this->FX['TLA']=array(
'LongName'	=>'Language(s)',
'Descrip'	=>'Should contain the languages of the text or lyrics in the audio file',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['TLAN']=$this->FX['TLA'];


$this->FX['TCO']=array(
'LongName'	=>'Content type',
'Descrip'	=>'',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['TCON']=$this->FX['TCO'];


$this->FX['TAL']=array(
'LongName'	=>'Album/Movie/Show title',
'Descrip'	=>'Intended for the title of the recording (source of sound) which the audio in the file is taken from',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['TALB']=$this->FX['TAL'];


$this->FX['TPA']=array(
'LongName'	=>'Part of a set',
'Descrip'	=>'Is a numeric string that describes which part of a set the audio came from. This frame is used if the source described in the "TAL" frame is divided into several mediums, e.g. a double CD',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['TPOS']=$this->FX['TPA'];


$this->FX['TRK']=array(
'LongName'	=>'Track number/Position in set',
'Descrip'	=>'Is a numeric string containing the order number of the audio-file on its original recording',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['TRCK']=$this->FX['TRK'];


$this->FX['TRC']=array(
'LongName'	=>'ISRC',
'Descrip'	=>'Should contian the International Standard Recording Code',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['TSRC']=$this->FX['TRC'];


$this->FX['TYE']=array(
'LongName'	=>'Year',
'Descrip'	=>'Numeric string with a year of the recording',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'110'
);

$this->FX['TYER']=$this->FX['TYE'];


$this->FX['TDA']=array(
'LongName'	=>'Date',
'Descrip'	=>'Is a numeric string in the DDMM format containing the date for the recording',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'110'
);

$this->FX['TDAT']=$this->FX['TDA'];


$this->FX['TIM']=array(
'LongName'	=>'Time',
'Descrip'	=>'Is a numeric string in the HHMM format containing the time for the recording',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'110'
);

$this->FX['TIME']=$this->FX['TIM'];


$this->FX['TRD']=array(
'LongName'	=>'Recording dates',
'Descrip'	=>'E.g. "4th-7th June, 12th June"',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'110'
);

$this->FX['TRDA']=$this->FX['TRD'];


$this->FX['TMT']=array(
'LongName'	=>'Media type',
'Descrip'	=>'Describes from which media the sound originated',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['TMED']=$this->FX['TMT'];


$this->FX['TFT']=array(
'LongName'	=>'File type',
'Descrip'	=>'Indicates which type of audio this tag defines',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['TFLT']=$this->FX['TFT'];


$this->FX['TBP']=array(
'LongName'	=>'Beats per Minute',
'Descrip'	=>'',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['TBPM']=$this->FX['TBP'];


$this->FX['TCR']=array(
'LongName'	=>'Copyright message',
'Descrip'	=>'Is intended for the copyright holder of the original sound, not the audio file itself',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['TCOP']=$this->FX['TCR'];


$this->FX['TPB']=array(
'LongName'	=>'Publisher',
'Descrip'	=>'Contains the name of the label or publisher',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['TPUB']=$this->FX['TPB'];


$this->FX['TEN']=array(
'LongName'	=>'Encoded by',
'Descrip'	=>'Contains the name of the person or organisation that encoded the audio file',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['TENC']=$this->FX['TEN'];


$this->FX['TSS']=array(
'LongName'	=>'Software/hardware and settings used for encoding',
'Descrip'	=>'Includes the used audio encoder and its settings when the file was encoded. Hardware refers to hardware encoders, not the computer on which a program was run',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['TSSE']=$this->FX['TSS'];


$this->FX['TOF']=array(
'LongName'	=>'Original filename',
'Descrip'	=>'Contains the preferred filename for the file, since some media doesn\'t allow the desired length of the filename',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['TOFN']=$this->FX['TOF'];


$this->FX['TLE']=array(
'LongName'	=>'Length',
'Descrip'	=>'Contains the length of the audiofile in milliseconds',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['TLEN']=$this->FX['TLE'];


$this->FX['TSI']=array(
'LongName'	=>'Size',
'Descrip'	=>'Contains the size of the audiofile in bytes excluding the tag',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'110'
);

$this->FX['TSIZ']=$this->FX['TSI'];


$this->FX['TDY']=array(
'LongName'	=>'Playlist delay',
'Descrip'	=>'Numbers of milliseconds of silence between every song in a playlist',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['TDLY']=$this->FX['TDY'];


$this->FX['TKE']=array(
'LongName'	=>'Initial key',
'Descrip'	=>'Conntains the musical key in which the sound starts',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['TKEY']=$this->FX['TKE'];


$this->FX['TOT']=array(
'LongName'	=>'Original album/Movie/Show title',
'Descrip'	=>'Is intended for the title of the original recording(/source of sound), if for example the music in the file should be a cover of a previously released song',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['TOAL']=$this->FX['TOT'];


$this->FX['TOA']=array(
'LongName'	=>'Original artist(s)/performer(s)',
'Descrip'	=>'Is intended for the performer(s) of the original recording, if for example the music in the file should be a cover of a previously released song',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['TOPE']=$this->FX['TOA'];


$this->FX['TOL']=array(
'LongName'	=>'Original Lyricist(s)/text writer(s)',
'Descrip'	=>'Is intended for the text writer(s) of the original recording, if for example the music in the file should be a cover of a previously released song',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['TOLY']=$this->FX['TOL'];


$this->FX['TOR']=array(
'LongName'	=>'Original release year',
'Descrip'	=>'Is intended for the year when the original recording, if for example the music in the file should be a cover of a previously released song, was released',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'110'
);

$this->FX['TORY']=$this->FX['TOR'];


$this->FX['TXX']=array(
'LongName'	=>'User defined text',
'Descrip'	=>'',
'Class'		=>3,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['TXXX']=$this->FX['TXX'];


$this->FX['WAF']=array(
'LongName'	=>'Official audio file webpage',
'Descrip'	=>'URL pointing at a file specific webpage',
'Class'		=>4,
'SubClass'	=>'',
'Versions'	=>'111'
);

$this->FX['WOAF']=$this->FX['WAF'];


$this->FX['WAR']=array(
'LongName'	=>'Official artist/performer webpage',
'Descrip'	=>'URL pointing at the artists official webpage',
'Class'		=>4,
'SubClass'	=>'',
'Versions'	=>'111'
);

$this->FX['WOAR']=$this->FX['WAR'];


$this->FX['WAS']=array(
'LongName'	=>'Official audio source webpage',
'Descrip'	=>'URL pointing at the official webpage for the source of the audio file, e.g. a movie',
'Class'		=>4,
'SubClass'	=>'',
'Versions'	=>'111'
);

$this->FX['WOAS']=$this->FX['WAS'];


$this->FX['WCM']=array(
'LongName'	=>'Commercial information',
'Descrip'	=>'URL pointing at a webpage with information such as where the album can be bought',
'Class'		=>4,
'SubClass'	=>'',
'Versions'	=>'111'
);

$this->FX['WCOM']=$this->FX['WCM'];


$this->FX['WCP']=array(
'LongName'	=>'Copyright/Legal information',
'Descrip'	=>'URL pointing at a webpage where the terms of use and ownership of the file is described',
'Class'		=>4,
'SubClass'	=>'',
'Versions'	=>'111'
);

$this->FX['WCOP']=$this->FX['WCP'];


$this->FX['WPB']=array(
'LongName'	=>'Publishers official webpage',
'Descrip'	=>'URL pointing at the official wepage for the publisher',
'Class'		=>4,
'SubClass'	=>'',
'Versions'	=>'111'
);

$this->FX['WPUB']=$this->FX['WPB'];


$this->FX['WXX']=array(
'LongName'	=>'User defined URL link',
'Descrip'	=>'',
'Class'		=>3,
'SubClass'	=>2,
'Versions'	=>'111'
);

$this->FX['WXXX']=$this->FX['WXX'];


$this->FX['IPL']=array(
'LongName'	=>'Involved people list',
'Descrip'	=>'The names of those involved, and how they were involved',
'Class'		=>2,
'SubClass'	=>2,
'Versions'	=>'111'
);

$this->FX['IPLS']=$this->FX['IPL'];

$this->FX['TIPL']=$this->FX['IPLS'];


$this->FX['MCI']=array(
'LongName'	=>'Music CD Identifier',
'Descrip'	=>'This frame is intended for music that comes from a CD, so that the CD can be identified in databases such as the CDDB',
'Class'		=>6,
'SubClass'	=>'',
'Versions'	=>'111'
);

$this->FX['MCDI']=$this->FX['MCI'];


$this->FX['ETC']=array(
'LongName'	=>'Event timing codes',
'Descrip'	=>'Allows synchronisation with key events in a song or sound',
'Class'		=>0,
'SubClass'	=>'',
'Versions'	=>'111'
);

$this->FX['ETCO']=$this->FX['ETC'];


$this->FX['MLL']=array(
'LongName'	=>'MPEG location lookup table',
'Descrip'	=>'To increase performance and accuracy of jumps within a MPEG audio file',
'Class'		=>0,
'SubClass'	=>'',
'Versions'	=>'111'
);

$this->FX['MLLT']=$this->FX['MLL'];


$this->FX['STC']=array(
'LongName'	=>'Synced tempo codes',
'Descrip'	=>'For a more accurate description of the tempo of a musical piece this frame might be used',
'Class'		=>0,
'SubClass'	=>'',
'Versions'	=>'111'
);

$this->FX['SYTC']=$this->FX['STC'];


$this->FX['ULT']=array(
'LongName'	=>'Unsychronised lyrics/text transcription',
'Descrip'	=>'Lyrics of the song or a text transcription of other vocal activities',
'Class'		=>5,
'SubClass'	=>2,
'Versions'	=>'111'
);

$this->FX['USLT']=$this->FX['ULT'];


$this->FX['SLT']=array(
'LongName'	=>'Synchronised lyrics/text',
'Descrip'	=>'This is another way of incorporating the words, said or sung lyrics, in the audio file as text, this time, however, in sync with the audio',
'Class'		=>0,
'SubClass'	=>'',
'Versions'	=>'111'
);

$this->FX['SYLT']=$this->FX['SLT'];


$this->FX['SEEK']=array(
'LongName'	=>'Seek frame',
'Descrip'	=>'This frame indicates where other tags in a file/stream can be found',
'Class'		=>0,
'SubClass'	=>'',
'Versions'	=>'001'
);


$this->FX['COM']=array(
'LongName'	=>'Comments',
'Descrip'	=>'',
'Class'		=>5,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['COMM']=$this->FX['COM'];


$this->FX['RVA']=array(
'LongName'	=>'Relative volume adjustment',
'Descrip'	=>'It allows the user to say how much he wants to increase/decrease the volume on each channel while the file is played',
'Class'		=>0,
'SubClass'	=>'',
'Versions'	=>'110'
);

$this->FX['RVAD']=$this->FX['RVA'];


$this->FX['SIGN']=array(
'LongName'	=>'Signature frame',
'Descrip'	=>' This frame enables a group of frames, grouped with the \'Group identification registration\', to be signed',
'Class'		=>0,
'SubClass'	=>'',
'Versions'	=>'001'
);


$this->FX['TDEN']=array(
'LongName'	=>'Encoding time',
'Descrip'	=>'Contains a timestamp describing when the audio was encoded',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'001'
);


$this->FX['TDOR']=array(
'LongName'	=>'Original release time',
'Descrip'	=>'Timestamp describing when the original recording of the audio was released',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'001'
);


$this->FX['TDRC']=array(
'LongName'	=>'Recording time',
'Descrip'	=>'Timestamp describing when the audio was recorded',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'001'
);


$this->FX['TDRL']=array(
'LongName'	=>'Release time',
'Descrip'	=>'Timestamp describing when the audio was first released',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'001'
);


$this->FX['TDTG']=array(
'LongName'	=>'Tagging time',
'Descrip'	=>'Timestamp describing then the audio was tagged',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'001'
);


$this->FX['EQU']=array(
'LongName'	=>'Equalisation',
'Descrip'	=>'It allows the user to predefine an equalisation curve within the audio file',
'Class'		=>0,
'SubClass'	=>'',
'Versions'	=>'110'
);

$this->FX['EQUA']=$this->FX['EQU'];


$this->FX['REV']=array(
'LongName'	=>'Reverb',
'Descrip'	=>'You may here adjust echoes of different kinds',
'Class'		=>0,
'SubClass'	=>'',
'Versions'	=>'111'
);

$this->FX['RVRB']=$this->FX['REV'];


$this->FX['ASPI']=array(
'LongName'	=>'Audio seek point index',
'Descrip'	=>'',
'Class'		=>0,
'SubClass'	=>'',
'Versions'	=>'001'
);


$this->FX['APIC']=array(
'LongName'	=>'Attached picture',
'Descrip'	=>'This frame contains a picture directly related to the audio file',
'Class'		=>7,
'SubClass'	=>'',
'Versions'	=>'011'
);


$this->FX['COMR']=array(
'LongName'	=>'Commercial frame',
'Descrip'	=>'This frame enables several competing offers in the same tag by bundling all needed information',
'Class'		=>11,
'SubClass'	=>2,
'Versions'	=>'011'
);


$this->FX['ENCR']=array(
'LongName'	=>'Encryption method registration',
'Descrip'	=>'To identify with which method a frame has been encrypted the encryption method must be registered in the tag with this frame',
'Class'		=>0,
'SubClass'	=>'',
'Versions'	=>'011'
);


$this->FX['GRID']=array(
'LongName'	=>'Group identification registration',
'Descrip'	=>'This frame enables grouping of otherwise unrelated frames',
'Class'		=>0,
'SubClass'	=>'',
'Versions'	=>'011'
);


$this->FX['PRIV']=array(
'LongName'	=>'Private frame',
'Descrip'	=>'This frame is used to contain information from a software producer that its program uses and does not fit into the other frames',
'Class'		=>0,
'SubClass'	=>'',
'Versions'	=>'011'
);


$this->FX['GEO']=array(
'LongName'	=>'General encapsulated object',
'Descrip'	=>'In this frame any type of file can be encapsulated',
'Class'		=>8,
'SubClass'	=>'',
'Versions'	=>'111'
);

$this->FX['GEOB']=$this->FX['GEO'];


$this->FX['CNT']=array(
'LongName'	=>'Play counter',
'Descrip'	=>'This is simply a counter of the number of times a file has been played',
'Class'		=>9,
'SubClass'	=>1,
'Versions'	=>'111'
);

$this->FX['PCNT']=$this->FX['CNT'];


$this->FX['POP']=array(
'LongName'	=>'Popularimeter',
'Descrip'	=>'The purpose of this frame is to specify how good an audio file is',
'Class'		=>9,
'SubClass'	=>2,
'Versions'	=>'111'
);

$this->FX['POPM']=$this->FX['POP'];


$this->FX['BUF']=array(
'LongName'	=>'Recommended buffer size',
'Descrip'	=>'',
'Class'		=>0,
'SubClass'	=>'',
'Versions'	=>'111'
);

$this->FX['RBUF']=$this->FX['BUF'];


$this->FX['OWNE']=array(
'LongName'	=>'Ownership frame',
'Descrip'	=>'The ownership frame might be used as a reminder of a made transaction or, if signed, as proof',
'Class'		=>11,
'SubClass'	=>1,
'Versions'	=>'011'
);


$this->FX['CRM']=array(
'LongName'	=>'Encrypted meta frame',
'Descrip'	=>'This frame contains one or more encrypted frames',
'Class'		=>0,
'SubClass'	=>'',
'Versions'	=>'100'
);


$this->FX['CRA']=array(
'LongName'	=>'Audio encryption',
'Descrip'	=>' This frame indicates if the actual audio stream is encrypted, and by whom',
'Class'		=>0,
'SubClass'	=>'',
'Versions'	=>'111'
);

$this->FX['AENC']=$this->FX['CRA'];


$this->FX['POSS']=array(
'LongName'	=>'Position synchronisation',
'Descrip'	=>'Delivers information to the listener of how far into the audio stream he picked up; in effect, it states the time offset of the first frame in the stream',
'Class'		=>0,
'SubClass'	=>'',
'Versions'	=>'011'
);


$this->FX['LNK']=array(
'LongName'	=>'Linked information',
'Descrip'	=>'To keep space waste as low as possible this frame may be used to link information from another ID3v2 tag that might reside in another audio file or alone in a binary file',
'Class'		=>10,
'SubClass'	=>'',
'Versions'	=>'111'
);

$this->FX['LINK']=$this->FX['LNK'];


$this->FX['USER']=array(
'LongName'	=>'Terms of use frame',
'Descrip'	=>'Contains a brief description of the terms of use and ownership of the file',
'Class'		=>13,
'SubClass'	=>'',
'Versions'	=>'011'
);


$this->FX['TOWN']=array(
'LongName'	=>'File owner/licensee',
'Descrip'	=>'The name of the owner or licensee of the file and it\'s contents',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'011'
);


$this->FX['TRSN']=array(
'LongName'	=>'Internet radio station name',
'Descrip'	=>'The name of the internet radio station from which the audio is streamed',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'011'
);


$this->FX['TRSO']=array(
'LongName'	=>'Internet radio station owner',
'Descrip'	=>'Contains the name of the owner of the internet radio station from which the audio is streamed',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'011'
);


$this->FX['WORS']=array(
'LongName'	=>'Official internet radio station homepage',
'Descrip'	=>'URL pointing at the homepage of the internet radio station',
'Class'		=>4,
'SubClass'	=>'',
'Versions'	=>'011'
);


$this->FX['WPAY']=array(
'LongName'	=>'Payment',
'Descrip'	=>'URL pointing at a webpage that will handle the process of paying for this file',
'Class'		=>4,
'SubClass'	=>'',
'Versions'	=>'011'
);


$this->FX['TMCL']=array(
'LongName'	=>'Musician credits list',
'Descrip'	=>'Mapping between instruments and the musician that played it',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'001'
);


$this->FX['TMOO']=array(
'LongName'	=>'Mood',
'Descrip'	=>'Intended to reflect the mood of the audio with a few keywords, e.g. "Romantic" or "Sad"',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'001'
);


$this->FX['TPRO']=array(
'LongName'	=>'Produced notice',
'Descrip'	=>'is intended for the production copyright holder of the original sound, not the audio file itself',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'001'
);


$this->FX['TSOA']=array(
'LongName'	=>'Album sort order',
'Descrip'	=>'Defines a string which should be used instead of the album name (TALB) for sorting purposes. E.g. an album named "A Soundtrack" might preferably be sorted as "Soundtrack".',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'001'
);


$this->FX['TSOP']=array(
'LongName'	=>'Performer sort order',
'Descrip'	=>'string which should be used instead of the performer (TPE2) for sorting purposes',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'001'
);


$this->FX['TSOT']=array(
'LongName'	=>'Title sort order',
'Descrip'	=>'A string which should be used instead of the title (TIT2) for sorting purposes',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'001'
);


$this->FX['TSST']=array(
'LongName'	=>'Set subtitle',
'Descrip'	=>'Is intended for the subtitle of the part of a set this track belongs to',
'Class'		=>2,
'SubClass'	=>1,
'Versions'	=>'001'
);


$this->FX['RVA2']=array(
'LongName'	=>'Relative volume adjustment (2)',
'Descrip'	=>'It allows the user to say how much he wants to increase/decrease the volume on each channel when the file is played',
'Class'		=>0,
'SubClass'	=>'',
'Versions'	=>'001'
);


$this->FX['EQU2']=array(
'LongName'	=>'Equalisation (2)',
'Descrip'	=>'It allows the user to predefine an equalisation curve within the audio file',
'Class'		=>0,
'SubClass'	=>'',
'Versions'	=>'001'
);


$this->FX['PIC']=array(
'LongName'	=>'Picture directly related to the audio file',
'Descrip'	=>'',
'Class'		=>12,
'SubClass'	=>'',
'Versions'	=>'100'
);

$this->FX['MJMD']=array(
'LongName'	=>'not yet',
'Descrip'	=>'',
'Class'		=>0,
'SubClass'	=>'',
'Versions'	=>'100'
);

$this->FX['NCON']=array(
'LongName'	=>'not yet',
'Descrip'	=>'',
'Class'		=>0,
'SubClass'	=>'',
'Versions'	=>'100'
);




$this->LookGenre=array(
	'Blues',
	'Classic Rock',
	'Country',
	'Dance',
	'Disco',
	'Funk',
	'Grunge',
	'Hip-Hop',
	'Jazz',
	'Metal',
	'New Age',
	'Oldies',
	'Other',
	'Pop',
	'R&B',
	'Rap',
	'Reggae',
	'Rock',
	'Techno',
	'Industrial',
	'Alternative',
	'Ska',
	'Death Metal',
	'Pranks',
	'Soundtrack',
	'Euro-Techno',
	'Ambient',
	'Trip-Hop',
	'Vocal',
	'Jazz+Funk',
	'Fusion',
	'Trance',
	'Classical',
	'Instrumental',
	'Acid',
	'House',
	'Game',
	'Sound Clip',
	'Gospel',
	'Noise',
	'Alt. Rock',
	'Bass',
	'Soul',
	'Punk',
	'Space',
	'Meditative',
	'Instrumental Pop',
	'Instrumental Rock',
	'Ethnic',
	'Gothic',
	'Darkwave',
	'Techno-Industrial',
	'Electronic',
	'Folk/Pop',
	'Eurodance',
	'Dream',
	'Southern Rock',
	'Comedy',
	'Cult',
	'Gangsta',
	'Top 40',
	'Christian Rap',
	'Pop/Funk',
	'Jungle',
	'Native American',
	'Cabaret',
	'New Wave',
	'Psychadelic',
	'Rave',
	'Showtunes',
	'Trailer',
	'Lo-Fi',
	'Tribal',
	'Acid Punk',
	'Acid Jazz',
	'Polka',
	'Retro',
	'Musical',
	'Rock & Roll',
	'Hard Rock',
	'Folk',			//80-> Winamp extensions:Added on December 12, 1997
	'National Folk',	
	'Folk/Rock',		
	'Swing',
	'Fast-Fusion',
	'Bebob',
	'Latin',
	'Revival',
	'Celtic',
	'Bluegrass',
	'Avantgarde',
	'Gothic Rock',
	'Progressive Rock',
	'Psychedelic Rock',
	'Symphonic Rock',
	'Slow Rock',
	'Big Band',
	'Chorus',
	'Easy Listening',
	'Acoustic',
	'Humour',
	'Speech',
	'Chanson',
	'Opera',
	'Chamber Music',
	'Sonata',
	'Symphony',
	'Booty Bass',
	'Primus',
	'Porn Groove',
	'Satire',
	'Slow Jam',		//111-> Added on January 26, 1998 to ensure compatibility with Winamp 1.7:
	'Club',
	'Tango',
	'Samba',
	'Folklore',
	'Ballad',			//116-> Added on April 13, 1998 to ensure compatibility with Winamp 1.90: 						
	'Power Ballad',
	'Rhythmic Soul',
	'Freestyle',
	'Duet',
	'Punk Rock',
	'Drum Solo',
	'A Cappella',
	'Euro-House',
	'Dance Hall',							
	'Goa',
	'Drum & Bass',
	'Club-House',
	'Hardcore',
	'Terror',
	'Indie',
	'BritPop',
	'Negerpunk',
	'Polsk Punk',
	'Beat',
	'Christian Gangsta Rap',
	'Heavy Metal',
	'Black Metal',
	'Crossover',
	'Contemporary Christian',
	'Christian Rock',
	'Merengue',		//142-> Added on Jun 1, 1998 to ensure compatibility with Winamp 1.91: 
	'Salsa',
	'Trash Metal',
	'Anime',
	'Jpop',
	'Synthpop'
	);	
?>