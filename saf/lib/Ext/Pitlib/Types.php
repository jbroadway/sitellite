<?php
// vim: tabstop=4: expandtab: sts=4: ai: sw=4:

/**
 * @author Charles Brunet
 * @license http://opensource.org/licenses/lgpl-license.php
 *    GNU Lesser General public License Version 2.1
 * @package Pitlib
 * @subpackage Pitlib.Core
 * @version 0.1.0
 */

/////////////////////////////////////////////////////////////////////////////

/**
 * Pitlib Image Types
 *
 * @package Pitlib
 * @subpackage Pitlib.Core
 */
class Pitlib_Type {

    const ART  = 'ART';
    const AVS  = 'AVS';
    const BMP  = 'BMP';
    const CIN  = 'CIN';
    const CUT  = 'CUT';
    const CMYK = 'CMYK';
    const DCM  = 'DCM';
    const DCX  = 'DCX';
    const DIB  = 'DIB';
    const DJVU = 'DJVU';
    const DNG  = 'DNG';
    const DPX  = 'DPX';
    const FAX  = 'FAX';
    const FITS = 'FITS';
    const GIF  = 'GIF';
    const ICO  = 'ICO';
    const JPEG = 'JPEG';
    const MTV  = 'MTV';
    const OTB  = 'OTB';
    const P7   = 'P7';
    const PALM = 'PALM';
    const PBM  = 'PBM';
    const PCD  = 'PCD';
    const PCX  = 'PCX';
    const PDF  = 'PDF';
    const PGM  = 'PGM';
    const PICT = 'PICT';
    const PNG  = 'PNG';
    const RLA  = 'RLA';
    const SVG  = 'SVG';
    const TGA  = 'TGA';
    const TIFF = 'TIFF';
    const WBMP = 'WBMP';
    const WPG  = 'WPG';
    const XBM  = 'XBM';
    const XPM  = 'XPM';
    const XCF  = 'XCF';

    static $_mime = array (
            
            'image/art'         => Pitlib_Type::ART,

            'application/x-stardent-avs' => Pitlib_Type::AVS,

            'image/bmp'         => Pitlib_Type::BMP,
			'image/x-bmp'       => Pitlib_Type::BMP,
			'image/x-bitmap'    => Pitlib_Type::BMP,
			'image/x-xbitmap'   => Pitlib_Type::BMP,
			'image/x-win-bitmap'=> Pitlib_Type::BMP,
			'image/x-windows-bmp'=> Pitlib_Type::BMP,
			'image/ms-bmp'      => Pitlib_Type::BMP,
			'image/x-ms-bmp'    => Pitlib_Type::BMP,
			'application/bmp'   => Pitlib_Type::BMP,
			'application/x-bmp' => Pitlib_Type::BMP,
			'application/x-win-bitmap'=> Pitlib_Type::BMP,

            'image/x-cin'       => Pitlib_Type::CIN,

            'image/x-cmyk'      => Pitlib_Type::CMYK,

            'image/cut'         => Pitlib_Type::CUT,
            'application/x-dr-halo-bitmap' => Pitlib_Type::CUT,
            'image/x-halo-cut'  => Pitlib_Type::CUT,
            'zz-application/zz-winassoc-cut' => Pitlib_Type::CUT,
            'application/x-cut' => Pitlib_Type::CUT,
            'application/cut'   => Pitlib_Type::CUT,

            'image/dicom'       => Pitlib_Type::DCM,
            'image/x-dicom'     => Pitlib_Type::DCM,
            'x-lml/x-evm'       => Pitlib_Type::DCM,

            'image/dcx'         => Pitlib_Type::DCX,
            'image/x-dcx'       => Pitlib_Type::DCX,
            'image/x-pc-paintbrush' => Pitlib_Type::DCX,
            'image/vnd.swiftview-pcx' => Pitlib_Type::DCX,

            'image/dib'         => Pitlib_Type::DIB,
            'application/dib'   => Pitlib_Type::DIB,
            'application/x-dib' => Pitlib_Type::DIB,
            'image/ms-bmp'      => Pitlib_Type::DIB,
            'image/x-bmp'       => Pitlib_Type::DIB,
            'image/x-ms-bmp'    => Pitlib_Type::DIB,
            'image/x-win-bitmap' => Pitlib_Type::DIB,
            'image/x-xbitmap'   => Pitlib_Type::DIB,
            'zz-application/zz-winassoc-dib' => Pitlib_Type::DIB,

            'image/djvu'        => Pitlib_Type::DJVU,
            'image/vnd.djvu'    => Pitlib_Type::DJVU,
            'image/x-djvu'      => Pitlib_Type::DJVU,
            'image/dejavu'      => Pitlib_Type::DJVU,
            'image/x-dejavu'    => Pitlib_Type::DJVU,
            'image/djvw'        => Pitlib_Type::DJVU,
            'image/x.djvu'      => Pitlib_Type::DJVU,

            'application/x-ding' => Pitlib_Type::DNG,

            'image/dpx'         => Pitlib_Type::DPX,

            'image/fax'         => Pitlib_Type::FAX,
            'application/x-fax' => Pitlib_Type::FAX,
            'image/g3fax'       => Pitlib_Type::FAX,
            'image/x-fax'       => Pitlib_Type::FAX,

            'image/fits'        => Pitlib_Type::FITS,
            'application/x-fits' => Pitlib_Type::FITS,
            'application/fits'  => Pitlib_Type::FITS,
            'image/x-fits'      => Pitlib_Type::FITS,

            'image/gif'         => Pitlib_Type::GIF,
			'application/x-gif' => Pitlib_Type::GIF,
			'application/gif'   => Pitlib_Type::GIF,
			'image/x-gif'       => Pitlib_Type::GIF,

            'image/ico'         => Pitlib_Type::ICO,
            'image/x-icon'      => Pitlib_Type::ICO,
            'application/ico'   => Pitlib_Type::ICO,
            'application/x-ico' => Pitlib_Type::ICO,

            'image/jpeg'        => Pitlib_Type::JPEG,
            'application/jpg'   => Pitlib_Type::JPEG,
            'application/x-jpg' => Pitlib_Type::JPEG,
            'image/jpg'         => Pitlib_Type::JPEG,
			'image/pjpeg'       => Pitlib_Type::JPEG,
			'image/pipeg'       => Pitlib_Type::JPEG,

            'image/x-mtv'       => Pitlib_Type::MTV,
            'application/x-mtv' => Pitlib_Type::MTV,

            'image/x-otb'       => Pitlib_Type::OTB,

            'application/x-xv-thumbnail' => Pitlib_Type::P7,

            'image/x-palm'      => Pitlib_Type::PALM,
            'application/x-palm' => Pitlib_Type::PALM,

			'image/x-portable-bitmap' => Pitlib_Type::PBM,
			'application/x-portable-bitmap' => Pitlib_Type::PBM,
			'image/x-portable-anymap' => Pitlib_Type::PBM,
			'image/x-portable/anymap' => Pitlib_Type::PBM,

            'image/pcd'         => Pitlib_Type::PCD,
            'application/pcd'   => Pitlib_Type::PCD,
            'application/x-photo-cd' => Pitlib_Type::PCD,
            'image/x-photo-cd'  => Pitlib_Type::PCD,

            'image/pcx'         => Pitlib_Type::PCX,
            'application/pcx'   => Pitlib_Type::PCX,
            'application/x-pcx' => Pitlib_Type::PCX,
            'image/x-pc-paintbrush' => Pitlib_Type::PCX,
            'image/x-pcx'       => Pitlib_Type::PCX,
            'zz-application/zz-winassoc-pcx' => Pitlib_Type::PCX,

            'application/pdf'   => Pitlib_Type::PDF,
            'application/x-pdf' => Pitlib_Type::PDF,
            'application/acrobat' => Pitlib_Type::PDF,
            'applications/vnd.pdf' => Pitlib_Type::PDF,
            'text/pdf'          => Pitlib_Type::PDF,
            'text/x-pdf'        => Pitlib_Type::PDF,

            'image/x-portable-graymap' => Pitlib_Type::PGM,
            'image/x-pgm'       => Pitlib_Type::PGM,

            'image/pict'        => Pitlib_Type::PICT,
            'image/x-macpict'   => Pitlib_Type::PICT,
            'image/x-pict'      => Pitlib_Type::PICT,
            'image/x-quicktime' => Pitlib_Type::PICT,
            'image/x-quicktime' => Pitlib_Type::PICT,

            'image/png'         => Pitlib_Type::PNG,
            'application/png'   => Pitlib_Type::PNG,
            'application/x-png' => Pitlib_Type::PNG,
            'image/x-png'       => Pitlib_Type::PNG,

            'application/x-rla-image' => Pitlib_Type::RLA,

            'image/svg'         => Pitlib_Type::SVG,
            'image/svg-xml'     => Pitlib_Type::SVG,
            'text/xml-svg'      => Pitlib_Type::SVG,
            'image/vnd.adobe.svg+xml' => Pitlib_Type::SVG,
            'image/svg-xml'     => Pitlib_Type::SVG,

			'image/targa'       => Pitlib_Type::TGA,
			'application/tga'   => Pitlib_Type::TGA,
			'application/x-tga' => Pitlib_Type::TGA,
			'application/x-targa' => Pitlib_Type::TGA,
			'image/tga'         => Pitlib_Type::TGA,
			'image/x-tga'       => Pitlib_Type::TGA,
			'image/x-targa'     => Pitlib_Type::TGA,

			'image/tiff'        => Pitlib_Type::TIFF,
			'image/x-tif'       => Pitlib_Type::TIFF,
			'image/x-tiff'      => Pitlib_Type::TIFF,
			'application/tif'   => Pitlib_Type::TIFF,
			'application/x-tif' => Pitlib_Type::TIFF,
			'application/tiff'  => Pitlib_Type::TIFF,
			'application/x-tiff' => Pitlib_Type::TIFF,
			'image/tif'         => Pitlib_Type::TIFF,

            'image/wbmp'        => Pitlib_Type::WBMP,

            'image/wpg'         => Pitlib_Type::WPG,
            'application/wpg'   => Pitlib_Type::WPG,
            'application/x-wpg' => Pitlib_Type::WPG,
            'image/x-wpg'       => Pitlib_Type::WPG,
            'image/x-wordperfect-graphics' => Pitlib_Type::WPG,
            'application/x-wpg-viewer' => Pitlib_Type::WPG,
            'zz-application/zz-winassoc-wpg' => Pitlib_Type::WPG,

            'image/x-xbitmap'   => Pitlib_Type::XBM,
            'image/x-xbm'       => Pitlib_Type::XBM,

            'image/xcf'         => Pitlib_Type::XCF,
            'application/xcf'   => Pitlib_Type::XCF,
            'application/x-xcf' => Pitlib_Type::XCF,
            'image/x-xcf'       => Pitlib_Type::XCF,
            'application/x-gimp-image' => Pitlib_Type::XCF,

            'image/x-xpixmap'   => Pitlib_Type::XPM,
            'image/x-xpm'       => Pitlib_Type::XPM,
        );

    static $_ext = array (
            'art'  => Pitlib_Type::ART  ,
            'avs'  => Pitlib_Type::AVS  ,
            'bmp'  => Pitlib_Type::BMP,
            'cin'  => Pitlib_Type::CIN  ,
            'cmyk' => Pitlib_Type::CMYK ,
            'cut'  => Pitlib_Type::CUT  ,
            'dcm'  => Pitlib_Type::DCM  ,
            'dcx'  => Pitlib_Type::DCX  ,
            'dib'  => Pitlib_Type::DIB  ,
            'djvu' => Pitlib_Type::DJVU ,
            'dng'  => Pitlib_Type::DNG  ,
            'dpx'  => Pitlib_Type::DPX  ,
            'fax'  => Pitlib_Type::FAX  ,
            'fits' => Pitlib_Type::FITS ,
            'gif'  => Pitlib_Type::GIF,
            'ico'  => Pitlib_Type::ICO  ,
            'jpg'  => Pitlib_Type::JPEG,
            'jpeg' => Pitlib_Type::JPEG,
            'mtv'  => Pitlib_Type::MTV  ,
            'otb'  => Pitlib_Type::OTB  ,
            'p7'   => Pitlib_Type::P7   ,
            'palm' => Pitlib_Type::PALM ,
            'pbm'  => Pitlib_Type::PBM,
            'pcd'  => Pitlib_Type::PCD  ,
            'pcx'  => Pitlib_Type::PCX  ,
            'pdf'  => Pitlib_Type::PDF  ,
            'pgm'  => Pitlib_Type::PGM  ,
            'pict' => Pitlib_Type::PICT ,
            'png'  => Pitlib_Type::PNG,
            'rla'  => Pitlib_Type::RLA  ,
            'svg'  => Pitlib_Type::SVG  ,
            'tga'  => Pitlib_Type::TGA,
            'tiff' => Pitlib_Type::TIFF,
            'wbmp' => Pitlib_Type::WBMP,
            'wpg'  => Pitlib_Type::WPG  ,
            'xbm'  => Pitlib_Type::XBM,
            'xcf'  => Pitlib_Type::XCF  ,
            'xpm'  => Pitlib_Type::XPM,
        );

    /**
     * Get image type from mime type
     *
     * @param string $mime
     *
     * @return integer
     */
    public static function from_mime ($mime) {
        $mime = strtolower ($mime);
        if (isset (Pitlib_Type::$_mime[$mime])) {
            return Pitlib_Type::$_mime[$mime];
        }
        return false;
    }

    public static function to_mime ($type) {
        return array_search ($type, Pitlib_Type::$_mime);
    }

    /**
     * Get image type from file using FileInfo extension
     *
     * @param string $filename Path to the image file to read
     *
     * @return integer File type number
     */
    public static function from_file ($filename) {

        if ( function_exists ('finfo_open')) {
            $finfo = finfo_open ( FILEINFO_MIME );
            $info = finfo_file ($finfo, $filename);
            finfo_close ($finfo);
        }
        else {
            $info = mime_content_type ($filename);
        }

        return Pitlib_Type::from_mime ($info);
    }
    
    /**
     * Get image type from filename using file extension to guess
     *
     * @param string $filename Name of file to guest type
     *
     * @return integer File type number
     */
    public static function from_filename ($filename) {
        
        $p = pathinfo ($filename);
        if (!isset ($p['extension'])) {
            return false;
        }
        $ext = strtolower ( $p['extension'] );
        if (empty ($ext)) {
            return false;
        }

        if (isset (Pitlib_Type::$_ext[$ext])) {
            return Pitlib_Type::$_ext[$ext];
        }

        return false;
    }
    
    public static function get_extension ($type) {
        return array_search ($type, Pitlib_Type::$_ext);
    }

    //--end-of-class--	
}

/////////////////////////////////////////////////////////////////////////////

?>
