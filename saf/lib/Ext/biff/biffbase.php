<?php
/***********************************************************************
* Script......... : class BiffBase - BiffWriter base class
* Author......... : Christian Novak - cnovak@gmx.net
* Copyright...... : (c) 2001 Christian Novak
* Documentation.. : http://www.cnovak.com
* History........ : rev 2.0 introduces "A1" standard spread sheet notation.
*                       Please read the manual "biffmanual.htm" available at 
*                       http://www.cnovak.com
*                 : rev 1.9 aimed to improve the speed by:
*                       - removing many convinient but expensive array_ 
*                         functions. 
*                       - Many bound checks were removed and made 
*                         available in a instantiated class "biffsave.php". 
*                       - The parser was improved
*                       - ATTENTION using the base class "biffwriter" only
*                         requires to provide ALL PARAMETERS to ALL FUNCTIONS.
*                         Using the sub class "biffsafe" provides this 
*                         convinience again.
*                 : rev 1.8 includes some changes to the parser 
*                 : rev 1.7 option to save the file instead of streaming added;
*                       bug fix for xlsWriteNumber concerning big/little
*                       endian order with IEEE 64-bit floating point values
*                 : rev 1.6 horizontal, vertical page breaks, default row 
*                       and cell notes added, eliminated call to constant() 
*                       function
*                 : rev 1.5 bugfix byte order on non Intel cpu's submitted by 
*                      John O'Donnel - johno@innismaggiore.com
* Requires        : PHP 4 >= 4.0.0
* 
* This library is distributed in the hope that it will be useful, but 
* WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY 
* or FITNESS FOR A PARTICULAR PURPOSE. 
*
* This library is copyright (c) by Christian Novak and is 
* _FREE_ for _NON_ _COMMERCIAL_ purposes.
************************************************************************/
define('FONT_0', 0); define('FONT_1', 0x40); define('FONT_2', 0x80); define('FONT_3', 0xC0);
define('ALIGN_GENERAL', 0x0); define('ALIGN_LEFT', 0x1); 
define('ALIGN_CENTER', 0x2); define('ALIGN_RIGHT', 0x3);
define('CELL_FILL', 0x4); define('CELL_LEFT_BORDER', 0x8); 
define('CELL_RIGHT_BORDER', 0x10); define('CELL_TOP_BORDER', 0x20); 
define('CELL_BOTTOM_BORDER',0x40); define('CELL_BOX_BORDER', 0x78);
define('CELL_SHADED', 0x80);
define('FONT_NORMAL', 0x0); define('FONT_BOLD', 0x1); define('FONT_ITALIC', 0x2);
define('FONT_UNDERLINE', 0x4); define('FONT_STRIKEOUT', 0x8);
define('CELL_LOCKED', 0x40); define('CELL_HIDDEN', 0x80);
define('XLS_DATE', 2415033);
define('ID_BOF_REC', 9); define('LEN_BOF_REC', 4); define('VERSION', 7); define('TYPE', 0x10);
define('ID_BACKUP_REC', 64); define('LEN_BACKUP_REC', 2);
define('ID_PRINTROWHEADERS_REC', 42); define('LEN_PRINTROWHEADERS_REC', 2);
define('ID_PRINTGRIDLINES_REC', 43); define('LEN_PRINTGRIDLINES_REC', 2);
define('ID_HPAGEBREAKS', 27); define('LEN_HPAGEBREAKS', 2);
define('ID_VPAGEBREAKS', 26); define('LEN_VPAGEBREAKS', 2);
define('ID_DEFROWHEIGHT', 37); define('LEN_DEFROWHEIGHT', 2);
define('ID_HEADER_REC', 20); define('LEN_HEADER_REC', 1);
define('ID_FOOTER_REC', 21); define('LEN_FOOTER_REC', 1);

define('ID_LEFT_MARGIN_REC', 38); define('ID_RIGHT_MARGIN_REC', 39); 
define('ID_TOP_MARGIN_REC', 40); define('ID_BOTTOM_MARGIN_REC', 41); 
define('LEN_MARGIN_REC', 8);

define('ID_IS_PASSWORD_REC' , 19); define('LEN_PASSWORD_REC', 2);
define('ID_IS_PROTECT_REC', 18);
define('ID_FONT_REC', 49); define('LEN_FONT_REC', 5);
define('ID_FORMAT_COUNT', 0x1F); define('LEN_FORMAT_COUNT', 2);
define('ID_FORMAT_REC', 30); define('LEN_FORMAT_REC', 1);

define('ID_EOF_REC', 0xA);
define('ID_CELL_TEXT', 4); define('LEN_CELL_TEXT', 8);
define('ID_CELL_NUMBER', 3); define('LEN_CELL_NUMBER', 0xF);
define('ID_COL_WIDTH', 36); define('LEN_COL_WIDTH', 4);
define('ID_NOTE_REC', 28); define('LEN_NOTE', 6);

define('MAX_ROWS', 65535); define('MAX_COLS', 255);
define('MAX_NOTE_CHARS', 2048);
define('MAX_TEXT_CHARS', 256);
define('MAX_FONTS', 4);

class BiffBase {
   var $picture = array ('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '' );
   var $eof = array(0xa, 0x28, 0xa9, 0x29, 0x20, 0x62, 0x79, 0x20, 0x63, 0x6e, 0x6f, 0x76, 0x61, 0x6b, 0x40, 0x67, 0x6d, 0x78, 0x2e, 0x6e, 0x65, 0x74);
   var $stream = array();
   var $outfile = 'sample.xls';
   var $maxcolwidth = array();
   var $hpagebreaks = array();
   var $vpagebreaks = array();
   var $err_level = 1;
   var $fonts = 0;
   var $big_endian = FALSE;
   var $parse_order = array (
			'ID_BOF_REC' => 9, 
			'ID_BACKUP_REC' => 64,
			'ID_PRINTROWHEADERS_REC' => 42,
			'ID_PRINTGRIDLINES_REC' => 43,
			'ID_HPAGEBREAKS' => 27,
			'ID_VPAGEBREAKS' => 26,
			'ID_DEFROWHEIGHT' => 37,
			'ID_FONT_REC' => 49, 
			'ID_HEADER_REC' => 20, 
			'ID_FOOTER_REC' => 21,
			'ID_LEFT_MARGIN_REC' => 38,
			'ID_RIGHT_MARGIN_REC' => 39,
			'ID_TOP_MARGIN_REC' => 40,
			'ID_BOTTOM_MARGIN_REC' => 41,
			'ID_COL_WIDTH' => 36, 
			'ID_FORMAT_COUNT' => 0x1F, 
			'ID_FORMAT_REC' => 30, 
			'ID_CELL_TEXT' => 4, 
			'ID_CELL_NUMBER' => 3, 
			'ID_IS_PROTECT_REC' => 18, 
			'ID_IS_PASSWORD_REC' => 19,
			'ID_NOTE_REC' => 28,
			'ID_EOF_REC' => 0xA
);


   function BiffBase() 
   {
      $this->BOF();
      $num = 1.23456789;         // IEEE 64-bit 3F F3 C0 CA 42 83 DE 1B 
      $little_endian = pack('C8', 0x1B, 0xDE, 0x83, 0x42, 0XCA, 0xC0, 0xF3, 0X3F);
      $result = pack('d', $num);
      if ($result === $little_endian) {
          $big_endian = FALSE;
      }
      else {
          $big_endian = TRUE;
      }
   }

   function swap_bytes($str) 
   {
      $swap = '';
      $y = strlen($str) / 2;
      for ($x=0; $x<$y; $x++) {
          $swap .= substr($str, $x * 2, 2);
      }
      return($swap);
   } // end func

   function xlsSetDefRowHeight($value)
   {
      $this->stream[] = ID_DEFROWHEIGHT;
      $this->stream[] = pack('vvv', ID_DEFROWHEIGHT, LEN_DEFROWHEIGHT, $value * 20);
   } // end func


   function xlsCellNote($row, $col, $value) 
   {
      $len = strlen($value);
      $this->stream[] = ID_NOTE_REC; 
      $this->stream[] = pack('vvvvv', ID_NOTE_REC, LEN_NOTE + $len, $row, $col, $len) . $value;
   }

   function xlsAddHPageBreak($row) 
   {
      $this->hpagebreaks[] = $row;
   }
   
   function xlsAddVPageBreak($col) 
   {
      $this->vpagebreaks[] = $col;
   }
   
   function assemblePageBreaks() 
   {
      $h = NULL;
      $cnt_hpagebreaks = count($this->hpagebreaks);
      if ($cnt_hpagebreaks > 0) {
         sort($this->hpagebreaks);
         foreach($this->hpagebreaks as $x) {
            $h .= pack('v', $x);
         }
         $this->stream[] = ID_HPAGEBREAKS;
         $this->stream[] = pack('vvv', ID_HPAGEBREAKS, LEN_HPAGEBREAKS + ($cnt_hpagebreaks * 2) , $cnt_hpagebreaks) . $h;
      }
      $cnt_vpagebreaks = count($this->vpagebreaks);
      $v = NULL;
      if ($cnt_vpagebreaks > 0) {
         sort($this->vpagebreaks);
         foreach($this->vpagebreaks as $x) {
            $v .= pack('v', $x);
         }
         $this->stream[] = ID_VPAGEBREAKS;
         $this->stream[] = pack('vvv', ID_VPAGEBREAKS, LEN_VPAGEBREAKS + ($cnt_vpagebreaks * 2), $cnt_vpagebreaks) . $v;
      }
   }

   function xlsAddFormat($picstring) 
   {
      $this->picture[] = $picstring;
      return(count($this->picture) -1);
   }

   function xlsPrintMargins($left = .5, $right = .5, $top = .5, $bottom = .5) 
   {
      $left = pack('d', $left);
      if ($this->big_endian) { 
         $left = strrev($left);
      }
      $right = pack('d', $right);
      if ($this->big_endian) {
         $right = strrev($right);
      }
      $top = pack('d', $top);
      if ($this->big_endian) {
         $top = strrev($top);
      }
      $bottom = pack('d', $bottom);
      if ($this->big_endian) {
         $bottom = strrev($bottom);
      }
      $this->stream[] = ID_LEFT_MARGIN_REC;
      $this->stream[] = pack('vv', ID_LEFT_MARGIN_REC, LEN_MARGIN_REC) . $left;
      $this->stream[] = ID_RIGHT_MARGIN_REC;
      $this->stream[] = pack('vv', ID_RIGHT_MARGIN_REC, LEN_MARGIN_REC) . $right;
      $this->stream[] = ID_TOP_MARGIN_REC;
      $this->stream[] = pack('vv', ID_TOP_MARGIN_REC, LEN_MARGIN_REC) . $top;
      $this->stream[] = ID_BOTTOM_MARGIN_REC;
      $this->stream[] = pack('vv', ID_BOTTOM_MARGIN_REC, LEN_MARGIN_REC) . $bottom;
   }

   function xlsFooter($foot) 
   {
      $this->stream[] = ID_FOOTER_REC;
      foreach($this->eof as $x) {$foot .= chr($x); }
      $len = strlen($foot);
      $this->stream[] = pack('vvC', ID_FOOTER_REC, LEN_FOOTER_REC + $len, $len) . $foot;
   }

   function xlsHeader($head) 
   {
      $this->stream[] = ID_HEADER_REC;
      $len = strlen($head);
      $this->stream[] = pack('vvC', ID_HEADER_REC, LEN_HEADER_REC + $len, $len) . $head;
   }

   function xlsSetPrintGridLines() 
   {
      $this->stream[] = ID_PRINTGRIDLINES_REC;
      $this->stream[] = pack('vvv', ID_PRINTGRIDLINES_REC, LEN_PRINTGRIDLINES_REC, 1);
   }

   function xlsSetPrintHeaders() 
   {
      $this->stream[] = ID_PRINTROWHEADERS_REC;
      $this->stream[] = pack('vvv', ID_PRINTROWHEADERS_REC, LEN_PRINTROWHEADERS_REC, 1);
   }

   function xlsSetBackup() 
   {
      $this->stream[] = ID_BACKUP_REC;
      $this->stream[] = pack('vvv', ID_BACKUP_REC, LEN_BACKUP_REC, 1);
   }


   function xlsProtectSheet($fpass = '', $fprot = TRUE) 
   {
      if (!empty($fpass)) {
         $pw = $this->_encode_pw($fpass);
         $this->stream[] = ID_IS_PASSWORD_REC;
         $this->stream[] = pack('vvv', ID_IS_PASSWORD_REC, LEN_PASSWORD_REC, $pw);
      } 
      if ($fprot) {
         $this->stream[] = ID_IS_PROTECT_REC;
         $this->stream[] = pack('vvv', ID_IS_PROTECT_REC, 0x2, 1);
      }
   }

   function xlsSetDefFonts() 
   {
      $this->xlsSetFont('Arial', 10, FONT_NORMAL);
      $this->xlsSetFont('Courier New', 10, FONT_NORMAL);
      $this->xlsSetFont('Times New Roman', 10, FONT_NORMAL);
      $this->xlsSetFont('System', 10, FONT_NORMAL);
   }

   function xlsSetColWidth($col_start, $col_end, $width) 
   {
      for ($x = $col_start; $x <= $col_end; $x++) {
         $this->maxcolwidth[$x] = $width;
      }
   }

   function SetColWidth($firstrow, $lastrow, $width) 
   {
      $width++;
      $this->stream[] = ID_COL_WIDTH;
      $this->stream[] = pack('vvCCv', ID_COL_WIDTH, LEN_COL_WIDTH, $firstrow, $lastrow, $width * 256);
   }

   function BOF() 
   {
      $this->stream[] = ID_BOF_REC;
      $this->stream[] = pack('vvvv', ID_BOF_REC, LEN_BOF_REC, VERSION, TYPE);
   } 

   function EOF() 
   {
      $this->stream[] = ID_EOF_REC;
      $this->stream[] = pack('v', ID_EOF_REC);
   } 

   function xlsParse($fname = '') 
   {
      $fstorage = !empty($fname);
      foreach($this->maxcolwidth as $key => $value) {
         $this->SetcolWidth($key, $key, $value);
      }
      $this->EOF();
      $this->SetDefFormat();
      $this->assemblePageBreaks();
      if ($fstorage) {
         $fp = fopen($fname, "wb");
      }
      else {
         header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
         header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
         header("Cache-Control: no-store, no-cache, must-revalidate");
         header("Cache-Control: post-check=0, pre-check=0", false);
         header("Pragma: no-cache");
         header("Content-Disposition: attachment; filename=$this->outfile"); 
         header("Content-Type: application/octet-stream");
      }
      $len1 = count($this->parse_order);
      $len2 = count($this->stream);
      for ($x = 0 ; $x < $len1; $x++) {
         $code = array_shift($this->parse_order);
         if (in_array($code, $this->stream, TRUE)) {
            for ($y = 0; $y < $len2; $y++) {
               if ($code === $this->stream[$y]) {
                  if ($fstorage) {
                     fwrite($fp, $this->stream[$y + 1], strlen($this->stream[$y + 1]));
                  }
                  else {
                     print $this->stream[$y + 1];
                  }
               }
            }
         }
      }
      if ($fstorage) {
         fclose($fp);
      }      
      return($fname);
   }

   function xlsWriteText($row, $col, $value, $col_width, $cell_picture, $cell_font, $cell_alignment, $cell_status) 
   {
      $len = strlen($value);
		$this->_adjcolwidth($col, $col_width, $len);
      $this->stream[] = ID_CELL_TEXT; 
      $this->stream[] = pack('vvvvCCCC', ID_CELL_TEXT, LEN_CELL_TEXT + $len, $row, $col, $cell_status, $cell_picture + $cell_font, $cell_alignment, $len). $value;
   }

   function xlsWriteNumber($row, $col, $value, $col_width, $cell_picture, $cell_font, $cell_alignment, $cell_status) 
   {
      $len = strlen(strval($value));
		$this->_adjcolwidth($col, $col_width, $len);
      $x = pack('d', $value);
      if ($this->big_endian) {
         $x = strrev($x);
      }
      $this->stream[] = ID_CELL_NUMBER;
      $this->stream[] = pack('vvvvCCC', ID_CELL_NUMBER, LEN_CELL_NUMBER, $row, $col, $cell_status, $cell_picture + $cell_font, $cell_alignment). $x;
   }

   function SetDefFormat() 
   {
      $y = count($this->picture);
      $this->stream[] = ID_FORMAT_COUNT;
      $this->stream[] = pack('vvv', ID_FORMAT_COUNT, LEN_FORMAT_COUNT, 0x15); 
      for ($x = 0; $x < $y; $x++) {
         $len_format_str = strlen($this->picture[$x]);
         $this->stream[] = ID_FORMAT_REC;
         $this->stream[] = pack('vvC', ID_FORMAT_REC, LEN_FORMAT_REC + $len_format_str, $len_format_str) . $this->picture[$x];
      }
   }

   function xlsSetFont($font_name, $font_size = 10, $font_format = FONT_NORMAL) 
   {
      if ($this->fonts > 3 AND $this->err_level > 0) {
         trigger_error('BIFFWRITER ERROR: too many fonts', E_USER_ERROR);
      }
      $len = strlen($font_name);
      $this->stream[] = ID_FONT_REC; 
      $this->stream[] = pack('vvvCCC', ID_FONT_REC, LEN_FONT_REC + $len, $font_size * 20, $font_format, 0x0, $len) .  $font_name;
      $this->fonts++;
   }

   function _encode_pw($pws)
   {
      $pws_len = strlen($pws);
      $enc_pw = (int) 0;
      for ($x=0; $x<$pws_len; $x++) {
         $char = substr($pws, $x, 1);
         $ord = ord($char);
         $sh = $this->_rl_14($ord, $x+1);
         $enc_pw = $sh ^ $enc_pw;
      }
      $enc_pw = $enc_pw ^ $pws_len;
      $enc_pw = $enc_pw ^ 0xce4b;
      return($enc_pw);
   } // end func

   function _rl_14($value, $num)
   { 
      $bin = sprintf("%016b", $value);
      for ($x = 0; $x < $num ; $x++) {
         if (substr($bin, 1, 1) === '1') {
            $a = '1';
         }
         else {
            $a = '0';
         }
         $bin = '0' .substr($bin, 2, 15) . $a;
      }
      return(base_convert($bin, 2, 10));       
   } // end func

   function xlsDate($month, $day, $year) 
   {
      return(juliantojd($month, $day, $year) - XLS_DATE  + 1);
   }

	function _adjcolwidth($col, $col_width, $len)
	{
		if ($col_width > 0) {
			$this->maxcolwidth[$col] = $col_width;
		}
		if ($col_width == 0) {
			if (isset($this->maxcolwidth[$col])) {
				if ($this->maxcolwidth[$col] < $len) {
					$this->maxcolwidth[$col] = $len;
				}
			}
			else {
				$this->maxcolwidth[$col] = $len;              
			}
		}
	} // end func


} // end of class
?>
