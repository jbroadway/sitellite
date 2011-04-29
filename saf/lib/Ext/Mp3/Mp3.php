<?
// This function parse ID3 tag from MP3 file. It's quite fast.
// syntax mp3_id(filename)
// function will return -1 if file not exists or no frame cynch found at the beginning of file. i realized that some songs downloaded thru gnutella have about four lines of text info at the beginning. it seepms players can handle. so i will implement it later.
// variable bitrates are not yet implemented, as they are quite slow to check. you can find them to read lot of first frames and check their bitrates. If theyre not the same, its variable bitrate. and also you then cannot compute real song lenght, unless you will scan the whole file for frames and compute its lenght... (at least what i read)
// there is second version of ID3 tag which is tagged at the beginning of the file and its quite large. you can learnt more about at http://www.id3.org/. i dont finding this so interesting. there are too good things on new version: you can write more than 30 chars at field and the tag is on the beginning of file. there are so many fields in v2 that i found really unusefull in many case. while it seems that id3v2 will still write tag v1 at the end, i can see no reason why to implement it, cos it is really 'slow' to parse all these informations.

// You can use 'genres' to determine what means the 'genreid' number. if you think you will not need it, delete it to. And also we need to specify all variables for mp3 header.

// Converted to a class by Lux (08/01/02): john.luxford@gmail.com
// To keep the function-like structure intact, we'll make the syntax something like: Mp3Parser::mp3_id (file)
class Mp3Parser {

// New function by Luca (18/02/01): devel@lluca.com

 /* This function strip null chars from a string. For example: 
  * If you get a 30 chars string for the comment, but the comment name has 4 chars like "Moon",
  * and it has a track number (ID3 1.1), you get "Moon<all_null_caracters><track#>",
  * compleating the 30 chars, in hex:
  * "4D6F6F6E0000000000000000000000000000000000000000000000000006" where just
  *  ~~~~~~~~                                                  ==
  *     \-------> this is useful data. <-----------------------/
  * This function looks for the first null char, and cut the string
  * so it converts this string to "4D6F6F6E" = "Moon". And then you can look if there is a track number.
  * This function strips trailing spaces too.
  */
 function strip_nulls( $str ) {
   $res = explode( chr(0), $str );
   return chop( $res[0] );
 }

// end


// here goes the function

 function mp3_id($file) {
 	// Lux: we keep all variables internal within this function, that way our global namespace
 	// is kept clean, and we can still call this method like a function without instantiating
 	// a whole class to do so.
   //global $version, $layer, $crc, $bitrate, $bitindex, $freq, $mode, $copy, $genres;
// Corrected by Luca (18/06/01): luca@linuxmendoza.org.ar
$genres = Array(
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
'AlternRock',
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
'Pop-Folk',
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
'Folk',
'Folk-Rock',
'National Folk',
'Swing',
'Fast Fusion',
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
'Slow Jam',
'Club',
'Tango',
'Samba',
'Folklore',
'Ballad',
'Power Ballad',
'Rhythmic Soul',
'Freestyle',
'Duet',
'Punk Rock',
'Drum Solo',
'Acapella',
'Euro-House',
'Dance Hall'
);

$genreids = Array(
"Blues" => 0,
"Classic Rock" => 1,
"Country" => 2,
"Dance" => 3,
"Disco" => 4,
"Funk" => 5,
"Grunge" => 6,
"Hip-Hop" => 7,
"Jazz" => 8,
"Metal" => 9,
"New Age" => 10,
"Oldies" => 11,
"Other" => 12,
"Pop" => 13,
"R&B" => 14,
"Rap" => 15,
"Reggae" => 16,
"Rock" => 17,
"Techno" => 18,
"Industrial" => 19,
"Alternative" => 20,
"Ska" => 21,
"Death Metal" => 22,
"Pranks" => 23,
"Soundtrack" => 24,
"Euro-Techno" => 25,
"Ambient" => 26,
"Trip-Hop" => 27,
"Vocal" => 28,
"Jazz+Funk" => 29,
"Fusion" => 30,
"Trance" => 31,
"Classical" => 32,
"Instrumental" => 33,
"Acid" => 34,
"House" => 35,
"Game" => 36,
"Sound Clip" => 37,
"Gospel" => 38,
"Noise" => 39,
"AlternRock" => 40,
"Bass" => 41,
"Soul" => 42,
"Punk" => 43,
"Space" => 44,
"Meditative" => 45,
"Instrumental Pop" => 46,
"Instrumental Rock" => 47,
"Ethnic" => 48,
"Gothic" => 49,
"Darkwave" => 50,
"Techno-Industrial" => 51,
"Electronic" => 52,
"Pop-Folk" => 53,
"Eurodance" => 54,
"Dream" => 55,
"Southern Rock" => 56,
"Comedy" => 57,
"Cult" => 58,
"Gangsta" => 59,
"Top 40" => 60,
"Christian Rap" => 61,
"Pop/Funk" => 62,
"Jungle" => 63,
"Native American" => 64,
"Cabaret" => 65,
"New Wave" => 66,
"Psychadelic" => 67,
"Rave" => 68,
"Showtunes" => 69,
"Trailer" => 70,
"Lo-Fi" => 71,
"Tribal" => 72,
"Acid Punk" => 73,
"Acid Jazz" => 74,
"Polka" => 75,
"Retro" => 76,
"Musical" => 77,
"Rock & Roll" => 78,
"Hard Rock" => 79,
"Folk" => 80,
"Folk-Rock" => 81,
"National Folk" => 82,
"Swing" => 83,
"Fast Fusion" => 84,
"Bebob" => 85,
"Latin" => 86,
"Revival" => 87,
"Celtic" => 88,
"Bluegrass" => 89,
"Avantgarde" => 90,
"Gothic Rock" => 91,
"Progressive Rock" => 92,
"Psychedelic Rock" => 93,
"Symphonic Rock" => 94,
"Slow Rock" => 95,
"Big Band" => 96,
"Chorus" => 97,
"Easy Listening" => 98,
"Acoustic" => 99,
"Humour" => 100,
"Speech" => 101,
"Chanson" => 102,
"Opera" => 103,
"Chamber Music" => 104,
"Sonata" => 105,
"Symphony" => 106,
"Booty Bass" => 107,
"Primus" => 108,
"Porn Groove" => 109,
"Satire" => 110,
"Slow Jam" => 111,
"Club" => 112,
"Tango" => 113,
"Samba" => 114,
"Folklore" => 115,
"Ballad" => 116,
"Power Ballad" => 117,
"Rhythmic Soul" => 118,
"Freestyle" => 119,
"Duet" => 120,
"Punk Rock" => 121,
"Drum Solo" => 122,
"Acapella" => 123,
"Euro-House" => 124,
"Dance Hall" => 125
);

 // end
 $version=Array("00"=>2.5, "10"=>2, "11"=>1);
 $layer  =Array("01"=>3, "10"=>2, "11"=>1);
 $crc=Array("Yes", "No");
 $bitrate["0001"]=Array(32,32,32,32,8,8);
 $bitrate["0010"]=Array(64,48,40,48,16,16);
 $bitrate["0011"]=Array(96,56,48,56,24,24);
 $bitrate["0100"]=Array(128,64,56,64,32,32);
 $bitrate["0101"]=Array(160,80,64,80,40,40);
 $bitrate["0110"]=Array(192,96,80,96,48,48);
 $bitrate["0111"]=Array(224,112,96,112,56,56);
 $bitrate["1000"]=Array(256,128,112,128,64,64);
 $bitrate["1001"]=Array(288,160,128,144,80,80);
 $bitrate["1010"]=Array(320,192,160,160,96,96);
 $bitrate["1011"]=Array(352,224,192,176,112,112);
 $bitrate["1100"]=Array(384,256,224,192,128,128);
 $bitrate["1101"]=Array(416,320,256,224,144,144);
 $bitrate["1110"]=Array(448,384,320,256,160,160);
 $bitindex=Array("1111"=>"0","1110"=>"1","1101"=>"2",
"1011"=>"3","1010"=>"4","1001"=>"5","0011"=>"3","0010"=>4,"0001"=>"5");
 $freq["00"]=Array("11"=>44100,"10"=>22050,"00"=>11025);
 $freq["01"]=Array("11"=>48000,"10"=>24000,"00"=>12000);
 $freq["10"]=Array("11"=>32000,"10"=>16000,"00"=>8000);
 $mode=Array("00"=>"Stereo","01"=>"Joint stereo","10"=>"Dual channel","11"=>"Mono");
 $copy=Array("No","Yes");






   if(!$f=@fopen($file, "r")) { return -1; break; } else {

// read first 4 bytes from file and determine if it is wave file if so, header begins five bytes after word 'data'

     $tmp=fread($f,4);
     if($tmp=="RIFF") {
       $idtag["ftype"]="Wave";
       fseek($f, 0);
       $tmp=fread($f,128);
       $x=StrPos($tmp, "data");
       fseek($f, $x+8);
       $tmp=fread($f,4);
     }

// now convert those four bytes to BIN. maybe it can be faster and easier. dunno how yet. help?

     for($y=0;$y<4;$y++) {
       $x=decbin(ord($tmp[$y]));
       for($i=0;$i<(8-StrLen($x));$i++) {$x.="0";}
       $bajt.=$x;
     }

// every mp3 framesynch begins with eleven ones, lets look for it. if not found continue looking for some 1024 bytes (you can search multiple for it or you can disable this, it will speed up and not many mp3 are like this. anyways its not standart)

//     if(substr($bajt,1,11)!="11111111111") {
//        return -1;
//        break;
//     }
     if(substr($bajt,1,11)!="11111111111") {
       fseek($f, 4);
       $tmp=fread($f,2048);
         for($i=0;$i<2048;$i++){
           if(ord($tmp[$i])==255 && substr(decbin(ord($tmp[$i+1])),0,3)=="111") {
              $tmp=substr($tmp, $i,4);
              $bajt="";
              for($y=0;$y<4;$y++) {
                $x=decbin(ord($tmp[$y]));
                for($i=0;$i<(8-StrLen($x));$i++) {$x.="0";}
                $bajt.=$x;
              }
              break;
            }
          }
     }
     if($bajt=="") {
        return -1;
        break;
     }


// now parse all the info from frame header

     $len=filesize($file);
     $idtag["version"]=$version[substr($bajt,11,2)];
     $idtag["layer"]=$layer[substr($bajt,13,2)];
     $idtag["crc"]=$crc[$bajt[15]];
     $idtag["bitrate"]=$bitrate[substr($bajt,16,4)][$bitindex[substr($bajt,11,4)]];
     $idtag["frequency"]=$freq[substr($bajt,20,2)][substr($bajt,11,2)];
     $idtag["padding"]=$copy[$bajt[22]];
     $idtag["mode"]=$mode[substr($bajt,24,2)];
     $idtag["copyright"]=$copy[$bajt[28]];
     $idtag["original"]=$copy[$bajt[29]];

// lets count lenght of the song

     if($idtag["layer"]==1) {
       $fsize=(12*($idtag["bitrate"]*1000)/$idtag["frequency"]+$idtag["padding"])*4; }
     else {
       $fsize=144*(($idtag["bitrate"]*1000)/$idtag["frequency"]+$idtag["padding"]);}
     // Modified by Luca (18/02/01): devel@lluca.com
     $idtag["lenght_sec"]=round($len/Round($fsize)/38.37);
     // end
     $idtag["lenght"]=date("i:s",round($len/Round($fsize)/38.37));

// now lets see at the end of the file for id3 tag. if exists then  parse it. if file doesnt have an id 3 tag if will return -1 in field 'tag' and if title is empty it returns file name.

     if(!$len) $len=filesize($file);
     fseek($f, $len-128);
     $tag = fread($f, 128);
     if(Substr($tag,0,3)=="TAG") {
       $idtag["file"]=$file;
       $idtag["tag"]=-1;
       // Modified by Luca (18/02/01): devel@lluca.com
       $idtag["title"]=Mp3Parser::strip_nulls( Substr($tag,3,30) );
       $idtag["artist"]=Mp3Parser::strip_nulls( Substr($tag,33,30) );
       $idtag["album"]=Mp3Parser::strip_nulls( Substr($tag,63,30) );
       $idtag["year"]=Mp3Parser::strip_nulls( Substr($tag,93,4) );
       $idtag["comment"]=Mp3Parser::strip_nulls( Substr($tag,97,30) );
       // If the comment is less than 29 chars, we look for the presence of a track #
       if ( strlen( $idtag["comment"] ) < 29 ) {
         if ( Ord(Substr($tag,125,1)) == chr(0) ) // If char 125 is null then track (maybe) is present
           $idtag["track"]=Ord(Substr($tag,126,1));
         else // If not, we are sure is not present.
           $idtag["track"]=0;
       } else { // If the comment is 29 or 30 chars long, there's no way to put track #
         $idtag["track"]=0;
       }
       // end
       $idtag["genreid"]=Ord(Substr($tag,127,1));
       $idtag["genre"]=$genres[$idtag["genreid"]];
       $idtag["filesize"]=$len;
     } else {
       $idtag["tag"]=0;
     }

// close opened file and return results.

   if(!$idtag["title"]) {
     $idtag["title"]=Str_replace("\\","/", $file);
     $idtag["title"]=substr($idtag["title"],strrpos($idtag["title"],"/")+1, 255);
   }
   fclose($f);
   return $idtag;
   }
 }


//-------- Function ends HERE

// New function by Luca (18/02/01): devel@lluca.com

 /* This function completes and crop a string to a specified lenght, with a specified string.
  */
 function str_padtrunc( $str, $len, $with = " " ) {
   $l = strlen( $str );
   if ( $len < $l ) {
     return substr( $str, 0, $len );
   } elseif ( $len > $l ) {
     $s = "";
     for ( $i = 0; $i < ($len - $l); $i++) {
       $s .= $with;
     }
     return $str . $s;
   } else
     return $str;
 }


 /* This function sets the ID3 TAG of a mp3 (or not?) file.
  * The argument is an array with this structure (compatible with mp3_id):
  *  ARRAY FIELD             DESCRIPTION      TYPE
  *  $id3tag["title"] ...... Song Title ..... 30 chars
  *  $id3tag["album"] ...... Album .......... 30 chars
  *  $id3tag["artist"] ..... Artist ......... 30 chars
  *  $id3tag["year"] ....... Year ........... 4 chars
  *  $id3tag["comment"] .... Comment ........ 28 chars (ID3v1.1)
  *  $id3tag["genreid"] .... Genre ID ....... 0 - 114 (1 char)
  *  $id3tag["track"] ...... Track number ... 0 - 255 (1 char)
  * If you use $genres array to get the genre, you can use $genreids array to get the id back.
  * Just the present fields are modified, so if your array look like this:
  *  $id3tag["year"] = "1999";
  *  $id3tag["track"] = 4;
  * Just the year and track info will be modified. If the track number is modified/added, the comment
  * will be truncated to 28 chars.
  * Returns True if the id3 tag it was successfuly updated, false if not.
  */
 function set_id3( $file, $id3tag ) {
   $id3 = mp3_id( $file );
   if ( !is_array( $id3 ) ) // If I couldn't open the file
     return false; // quit with error.
   $fields = array( "title", "artist", "album", "year", "comment", "track", "genreid" );
   // Checks if the ID is present, if not, create an empty one.
   reset( $fields );
   if ( !$id3["tag"] )
     while ( $field = each( $fields ) )
       $id3[$field["value"]] = "";
   // Update the ID3 TAG info with the new one
   reset( $fields );
   while ( $field = each( $fields ) )
     if ( isset( $id3tag[$field["value"]] ) )
       $id3[$field["value"]] = $id3tag[$field["value"]];
   // Make the TAG with this info.
   $tag =  "TAG";
   $tag .= Mp3Parser::str_padtrunc( $id3["title"], 30, chr(0) );
   $tag .= Mp3Parser::str_padtrunc( $id3["artist"], 30, chr(0) );
   $tag .= Mp3Parser::str_padtrunc( $id3["album"], 30, chr(0) );
   $tag .= Mp3Parser::str_padtrunc( $id3["year"], 4, chr(0) );
   $tag .= Mp3Parser::str_padtrunc( str_padtrunc( $id3["comment"], 28, chr(0) ), 29, chr(0) );
   $tag .= chr( $id3["track"] );
   $tag .= chr( $id3["genreid"] );
   // Try to open the file with read/write perms...
   if ( !$f = @fopen( $file, "r+" ) ) {
     return false;
   } else {
     $len = filesize( $file );
     if ( !$id3["tag"] ) // If TAG doesn't exist, put the pointer at the end of the file
       fseek( $f, $len );
     else                // If exists, put the pointer at the begining of the TAG.
       fseek( $f, $len - 128 );
     echo "\n";
     // Saves the data
     if ( fputs( $f, $tag, 128 ) != 128 ) { // If doesn't writes 128 bytes...
       fclose( $f ); // Close File
       return false; // Return with error
     }
   }
   fclose( $f );
   return true;
 }

} // end class

// end

?>