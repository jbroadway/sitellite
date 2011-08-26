<?php
// vim: tabstop=4: expandtab: sts=4: ai: sw=4:

/**
 * @author Kaloyan K. Tsvetkov <kaloyan@kaloyan.info>
 * @author Charles Brunet <cbrnuet@php.net>
 * @license http://opensource.org/licenses/lgpl-license.php
 *     GNU Lesser General Public License Version 2.1
 * @package Pitlib
 * @subpackage Pitlib.Driver
 * @version 0.1.0
 */

/////////////////////////////////////////////////////////////////////////////

/**
 * Pitlib "Magick Wand" driver
 *
 * @package Pitlib
 * @subpackage Pitlib.Driver
 *
 * @see http://www.magickwand.org/
 */
Class Pitlib_Driver_Magickwand Extends Pitlib_Driver {

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
            Pitlib_Type::P7,
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

        if (!extension_loaded('magickwand')) {
            throw new Pitlib_Exception (
                    'The Pitlib_Driver_Magick_Wand driver is '
                    . ' unnable to be initialized, '
                    . ' because the MagickWand (php_magickwand) '
                    . ' module is not installed'
                    );
        }

        // give access to all the memory
        //
        @ini_set("memory_limit", -1);

        return true;
    }

    public function name () {
        return 'magickwand';
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
        return MagickResizeImage(
                $tmp->target, $width, $height, MW_GaussianFilter, 0
                );
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
    protected Function __copy(Pitlib_Tmp $tmp_target, Pitlib_Tmp $tmp_source, $destination_x, $destination_y) {

        return MagickCompositeImage(
                $tmp_target->target, $tmp_source->source,
                MW_OverCompositeOp,
                $destination_x, $destination_y);
    }

    /**
     * Make the image greyscale
     *
     * @param Pitlib_Tmp $tmp
     * @return boolean
     * @access protected
     */
    protected Function __grayscale(Pitlib_Tmp $tmp) {
        return MagickSetImageType($tmp->target, MW_GrayscaleType);
    }

    /**
     * Rotate the image clockwise
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

        list($r, $g, $b) = $color->get();
        $ret = MagickRotateImage(
                $tmp->target,
                NewPixelWand("rgb($r,$g,$b)"),
                $angle
                );

        $tmp->image_width = MagickGetImageWidth($tmp->target);
        $tmp->image_height = MagickGetImageHeight($tmp->target);

        return $ret;
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
        if (!MagickCropImage($tmp->target, $width, $height, $x, $y)) {
            return false;
        }

        $t = NewMagickWand();
        MagickNewImage($t, $width, $height);
        MagickSetImageFormat ($t, MagickGetImageFormat ($tmp->target));
        if (!MagickCompositeImage($t, $tmp->target, MW_OverCompositeOp, 0, 0)) {
            return false;
        }

        $this->__destroy_target($tmp);
        $tmp->target = $t;
        $tmp->image_width = $width;
        $tmp->image_height = $height;

        return true;
    }

    /**
     * Vertically mirror (flip) the image
     * 
     * @param Pitlib_Tmp $tmp
     * @return boolean
     * @access protected
     */
    protected Function __flip(Pitlib_Tmp $tmp) {
        return MagickFlipImage($tmp->target);
    }

    /**
     * Horizontally mirror (flop) the image
     * 
     * @param Pitlib_Tmp $tmp
     * @return boolean
     * @access protected
     */
    protected Function __flop(Pitlib_Tmp $tmp) {
        return MagickFlopImage($tmp->target);
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

        $t = new Pitlib_Tmp;
        $t->target = NewMagickWand();

        list($r, $g, $b) = $color->get();
        MagickNewImage(
                $t->target,
                $width, $height,
                sprintf("#%02x%02x%02x", $r, $g, $b)
                );

        $t->image_width = $width;
        $t->image_height = $height;

        return $t;
    }

    /**
     * Generate a temporary object for the provided argument
     *
     * @param mixed $handler
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

        MagickSetImageFormat($handler, "PNG");
        MagickWriteImage($handler, $filename);
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

        $tmp->source = NewMagickWand();
        $error_open = !MagickReadImage(
                $tmp->source, $tmp->source_filename);
        $error_open &= !($tmp->target = CloneMagickWand(
                    $tmp->source));

        // get width & height of the image
        //
        if (!$error_open) {
            $tmp->image_width = MagickGetImageWidth($tmp->source);
            $tmp->image_height = MagickGetImageHeight($tmp->source);
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
            MagickSetImageFormat( $tmp->target, $tmp->save );

            $t = $this->__tmpfile();
            if (!MagickWriteImage($tmp->target, $t)) {
                return false;
            }

            $ret = @copy($t, $tmp->target_filename);
            @unlink($t);
        } else {

            if (! (MagickGetImageFormat ($tmp->target))) {
                MagickSetImageFormat ($tmp->target, Pitlib_Type::from_filename ($tmp->target_filename));
            }

            // no convert, just save
            //
            $ret = MagickWriteImage(
                    $tmp->target, $tmp->target_filename
                    );
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
        return DestroyMagickWand($tmp->source);
    }

    /**
     * Destroy the target for the provided temporary object
     *
     * @param Pitlib_Tmp $tmp
     * @return boolean
     * @access protected
     */	
    protected Function __destroy_target(Pitlib_Tmp $tmp) {
        return DestroyMagickWand($tmp->target);
    }

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    //--end-of-class--	
}

/////////////////////////////////////////////////////////////////////////////

?>
