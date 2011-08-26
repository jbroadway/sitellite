<?php
// vim: tabstop=4: expandtab: sts=4: ai: sw=4:

/**
 * @author Kaloyan K. Tsvetkov <kaloyan@kaloyan.info>
 * @author Charles Brunet <cbrunet@php.net>
 * @license http://opensource.org/licenses/lgpl-license.php
 *    GNU Lesser General Public License Version 2.1
 * @package Pitlib
 * @subpackage Pitlib.Driver
 * @version 0.1.0
 *
 * @todo Set compression for jpeg and png
 */

/////////////////////////////////////////////////////////////////////////////

/**
 * Pitlib "Imagick" driver (as extension)
 *
 * @package Pitlib
 * @subpackage Pitlib.Driver
 */
class Pitlib_Driver_Imagick_Ext extends Pitlib_Driver {

    /**
     * Maps to supported image types for reading files
     * @var array
     */
    protected $__read = array (
            Pitlib_Type::ART,
            Pitlib_Type::BMP,
//            Pitlib_Type::CUT,
//            Pitlib_Type::DCM,
            Pitlib_Type::DCX,
            Pitlib_Type::DIB,
            Pitlib_Type::DJVU,
            Pitlib_Type::DNG,
            Pitlib_Type::DPX,
            Pitlib_Type::FAX,
            Pitlib_Type::FITS,
            Pitlib_Type::GIF,
            Pitlib_Type::ICO,
            Pitlib_Type::JPEG,
            Pitlib_Type::MTV,
            Pitlib_Type::OTB,
            Pitlib_Type::P7,
            Pitlib_Type::PALM,
            Pitlib_Type::PBM,
            Pitlib_Type::PCD,
            Pitlib_Type::PCX,
            Pitlib_Type::PGM,
            Pitlib_Type::PICT,
            Pitlib_Type::PNG,
//            Pitlib_Type::RLA,
//            Pitlib_Type::SVG,
            Pitlib_Type::TGA,
            Pitlib_Type::TIFF,
            Pitlib_Type::WBMP,
//            Pitlib_Type::WPG,
            Pitlib_Type::XBM,
//            Pitlib_Type::XCF,
            Pitlib_Type::XPM,
            );

    /**
     * Maps to supported image types for saving files
     * @var array
     */
    protected $__write = array (
            Pitlib_Type::AVS,
            Pitlib_Type::BMP,
            Pitlib_Type::CIN,
            Pitlib_Type::CMYK,
            Pitlib_Type::DCX,
            Pitlib_Type::DPX,
            Pitlib_Type::FAX,
            Pitlib_Type::FITS,
            Pitlib_Type::GIF,
            Pitlib_Type::JPEG,
            Pitlib_Type::MTV,
            Pitlib_Type::OTB,
//            Pitlib_Type::P7,
            Pitlib_Type::PALM,
            Pitlib_Type::PBM,
            Pitlib_Type::PCD,
            Pitlib_Type::PCX,
            Pitlib_Type::PDF,
            Pitlib_Type::PGM,
            Pitlib_Type::PICT,
            Pitlib_Type::PNG,
            Pitlib_Type::TGA,
            Pitlib_Type::TIFF,
            Pitlib_Type::WBMP,
            Pitlib_Type::XBM,
            Pitlib_Type::XPM,
            );



    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Constructor
     */
    Public Function __construct() {
    }

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Checks whether the environment is compatible with this driver
     *
     * @return boolean
     * @access public
     */
    Public Function is_compatible() {

        if (!extension_loaded('imagick')) {
            throw new Pitlib_Exception (
                    'The Pitlib_Driver_Imagick_Ext driver is '
                    . ' unnable to be initialized, '
                    . ' because the IMagick (php_imagick) '
                    . ' module is not installed'
                    );
        }

        // give access to all the memory
        //
        @ini_set("memory_limit", -1);

        // no time limit
        //
        @set_time_limit(-1);

        return true;
    }

    public function name () {
        return 'imagick_ext';
    }

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Do the actual resize of an image
     *
     * @param Pitlib_Tmp $tmp
     * @param integer $width
     * @param integer $height
     * @return boolean
     * @access protected
     */
    protected Function __resize(Pitlib_Tmp $tmp, $width, $height) {
        return $tmp->target->resizeImage ($width, $height, Imagick::FILTER_UNDEFINED, 0);
    }

    /**
     * Copy one image to another
     *
     * @param Pitlib_Tmp $tmp_target
     * @param Pitlib_Tmp $tmp_source
     * @param integer $destination_x
     * @param integer $destination_y
     * @return boolean
     * @access protected
     */
    protected Function __copy (Pitlib_Tmp $tmp_target, Pitlib_Tmp $tmp_source, $destination_x, $destination_y) {
        return $tmp_target->target->compositeImage (
                $tmp_source->source,
                imagick::COMPOSITE_OVER,
                $destination_x, $destination_y);
    }

    /**
     * Make the image greyscale: not supported
     *
     * @param Pitlib_Tmp $tmp
     * @return boolean
     * @access protected
     */
    protected Function __grayscale(Pitlib_Tmp $tmp) {
        return $tmp->target->setImageColorspace (2);
    }

    /**
     * Rotate the image clockwise: only rectangular rotates are supported (90,180,270)
     *
     * @param Pitlib_Tmp $tmp
     * @param float $angle
     * @param Pitlib_Color $color
     * @return boolean
     * @access protected
     */
    protected Function __rotate(Pitlib_Tmp $tmp, $angle, Pitlib_Color $color) {

        // skip full loops
        //
        if (($angle % 360) == 0) {
            return true;
        }

        // rectangular rotates are OK
        //
        //if (($angle % 90) == 0) {
            list ($r, $g, $b) = $color->get ();
            if ($tmp->target->rotateImage (new ImagickPixel ("rgb($r, $g, $b)"), $angle)) {
                $tmp->image_width = $tmp->target->getImageWidth ();
                $tmp->image_height = $tmp->target->getImageHeight ();
                return true;
            }
        //}

        return false;
    }

    /**
     * Crop the image 
     *
     * @param Pitlib_Tmp $tmp
     * @param integer $x
     * @param integer $y
     * @param integer $width
     * @param integer $height
     * @return boolean
     * @access protected
     */
    protected Function __crop(Pitlib_Tmp $tmp, $x, $y, $width, $height) {
        return $tmp->target->cropImage ($width, $height, $x, $y);
    }

    /**
     * Vertically mirror (flip) the image
     * 
     * @param Pitlib_Tmp $tmp
     * @return boolean
     * @access protected
     */
    protected Function __flip(Pitlib_Tmp $tmp) {
        return $tmp->target->flipImage ();
    }

    /**
     * Horizontally mirror (flop) the image
     * 
     * @param Pitlib_Image &$image
     * @return boolean
     * @access protected
     */
    protected Function __flop(Pitlib_Tmp $tmp) {
        return $tmp->target->flopImage ();
    }

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Get canvas
     *
     * @param integer $width
     * @param integer $height
     * @param Pitlib_Color $color
     * @return Pitlib_Tmp
     * @access protected
     */
    protected Function __canvas($width, $height, Pitlib_Color $color) {

        list($r, $g, $b) = $color->get();

        $t = new Pitlib_Tmp;
        $t->target = new imagick ();
        $t->target->newImage ($width, $height,
            new ImagickPixel ( "rgb($r, $g, $b)" ));

        $t->image_width = $width;
        $t->image_height = $height;

        return $t;
    }

    /**
     * Generate a temporary object for the provided argument
     *
     * @param mixed &$handler
     * @param string $filename the filename will be automatically generated 
     *	on the fly, but if you want you can use the filename provided by 
     *	this argument
     * @return Pitlib_Tmp
     * @access protected
     */
    protected Function __tmpimage($handler, $filename=null) {

        if (!isset($filename)) {
            $filename = $this->__tmpfile();
        }

        $handler->setImageFormat ("PNG");
        $handler->writeImage ($filename);
        // ^
        // PNG: no pixel losts

        return $this->prepare(
                new Pitlib_Image($filename)
                );
    }

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Open the source and target image for processing it
     *
     * @param Pitlib_Tmp $tmp
     * @return boolean
     * @access protected
     */
    protected Function __open(Pitlib_Tmp $tmp) {

        $error_open = !($tmp->source = new Imagick (realpath($tmp->source_filename)));
        $error_open &= !($tmp->target = $tmp->source->clone());

        // get width & height of the image
        //
        if (!$error_open) {
            $tmp->image_width = $tmp->source->getImageWidth();
            $tmp->image_height = $tmp->source->getImageHeight();
        }

        return !$error_open;
    }

    /**
     * Write the image after being processed
     *
     * @param Pitlib_Tmp $tmp
     * @return boolean
     * @access protected
     */
    protected Function __write(Pitlib_Tmp $tmp) {

        $ret = false;

        if ($tmp->save) {

            // convert, then save
            //
            $tmp->target->setImageFormat ($tmp->save);

            $t = $this->__tmpfile();
            if (!$tmp->target->writeImage($t)) {
                return false;
            }

            $ret = @copy($t, $tmp->target_filename);
            @unlink($t);

        } else {

            // weird ... only works with absolute names
            //
            fclose(fopen($tmp->target_filename, 'w'));

            // no convert, just save
            //
            $ret = $tmp->target->writeImage( realpath($tmp->target_filename));
        }

        // dispose
        //
        @$this->__destroy_source($tmp);
        @$this->__destroy_target($tmp);

        return $ret;
    }

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Destroy the source for the provided temporary object
     *
     * @param Pitlib_Tmp $tmp
     * @return boolean
     * @access protected
     */	
    protected Function __destroy_source(Pitlib_Tmp $tmp) {
        return $tmp->source->destroy();
    }

    /**
     * Destroy the target for the provided temporary object
     *
     * @param Pitlib_Tmp $tmp
     * @return boolean
     * @access protected
     */	
    protected Function __destroy_target(Pitlib_Tmp $tmp) {
        return $tmp->target->destroy();
    }

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    //--end-of-class--	
}

/////////////////////////////////////////////////////////////////////////////

?>
