<?php
// vim: tabstop=4: expandtab: sts=4: ai: sw=4:

/**
 * @package Pitlib
 * @author Charles Brunet <cbrunet@php.net>
 * @author Kaloyan K. Tsvetkov <kaloyan@kaloyan.info>
 * @license http://opensource.org/licenses/lgpl-license.php
 *    GNU Lesser General public License Version 2.1
 * @subpackage Pitlib.Driver
 * @version 0.1.0
 *
 * @todo grayscale function
 */

/////////////////////////////////////////////////////////////////////////////

/**
 * Pitlib ImLib2 driver
 *
 * @package Pitlib
 * @subpackage Pitlib.Driver
 *
 * @see http://pp.siedziba.pl/
 * @see http://mmcc.cx/php_imlib/index.php
 */
class Pitlib_Driver_Imlib2 extends Pitlib_Driver {

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Maps to supported image types for reading files
     * @var array
     */
    protected $__read = array (
        Pitlib_Type::WBMP,
 //       Pitlib_Type::GIF,
        Pitlib_Type::JPEG,
        Pitlib_Type::PBM,			
        Pitlib_Type::PNG,
		Pitlib_Type::TGA,	
        Pitlib_Type::TIFF,			
        Pitlib_Type::XPM,
        );			

    /**
     * Maps to supported image types for writing files
     * @var array
     */
     protected $__write = array (
        Pitlib_Type::BMP,
        Pitlib_Type::GIF,
        Pitlib_Type::JPEG,
        Pitlib_Type::PBM,			
        Pitlib_Type::PNG,
		Pitlib_Type::TGA,	
        Pitlib_Type::TIFF,			
//        Pitlib_Type::XPM,
        );

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Constructor
     */
    public function __constructor() {
    }

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Checks whether the environment is compatible with this driver
     *
     * @return boolean
     * @access public
     */
    public function is_compatible() {

        if (!extension_loaded('imlib')) {
            throw new Pitlib_Exception (
                    'The Pitlib_Driver_ImLib driver is unnable to be '
                    . ' initialized, because the ImLib2 (php_imlib) '
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
        return 'imlib2';
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
    protected function __resize(Pitlib_Tmp $tmp, $width, $height) {

        // create new target
        //
        if (!$_ = imlib_create_scaled_image($tmp->target, $width, $height)) {
            return false;
        }

        // set new target
        //
        $this->__destroy_target($tmp);
        $tmp->target = $_;

        return true;
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
    protected function __copy(Pitlib_Tmp $tmp_target, Pitlib_Tmp $tmp_source,
            $destination_x, $destination_y) {

        return imlib_blend_image_onto_image(
                $tmp_target->target,
                $tmp_source->source,
                1, // malpha ? merge_alpha ?
                0, 0,
                $tmp_source->image_width, $tmp_source->image_height,
                $destination_x, $destination_y,
                $tmp_source->image_width, $tmp_source->image_height,
                '0', '1', '0' // ???
                );
    }

    /**
     * Make the image greyscale: not supported
     *
     * @param Pitlib_Tmp $tmp
     * @return boolean
     * @access protected
     */
    protected function __grayscale(Pitlib_Tmp $tmp) {
        parent::__grayscale ($tmp);
    }

    /**
     * Rotate the image clockwise:
     *    only rectangular rotates are supported (90,180,270)
     *
     * @param Pitlib_Tmp $tmp
     * @param float $angle
     * @param Pitlib_Color $color
     * @return boolean
     * @access protected
     */
    protected function __rotate(Pitlib_Tmp $tmp, $angle, Pitlib_Color $color) {

        // skip full loops
        //
        if (($angle % 360) == 0) {
            return true;
        }

        if ($_ = imlib_create_rotated_image($tmp->target, $angle)) {

            // Calculate result canvas size
            $rad = deg2rad ($angle);
            $sin = sin ($rad);
            $cos = cos ($rad);
            $x = $tmp->image_width / 2;
            $y = $tmp->image_height / 2;
            $x1 = abs ($cos * $x + $sin * $y);
            $x2 = abs ($cos * $x - $sin * $y);
            $y1 = abs ($sin * $x - $cos * $y);
            $y2 = abs ($sin * $x + $cos * $y);
            $x = round (max ($x1, $x2) * 2);
            $y = round (max ($y1, $y2) * 2);

            if (!$color) {
                $color = Pitlib::Color (0, 0, 0, 255);
            }
            // Fill with color
            $t = $this->__canvas ($x, $y, $color);

            // Copy rotated image on canvas
            $dx = round ((imlib_image_get_width ($_) - $x) / 2);
            $dy = round ((imlib_image_get_height ($_) - $y) / 2);
            $this->__destroy_target($tmp);
            $tmp->target = $_;
            $this->__crop ($tmp, $dx, $dy, $t->image_width, $t->image_height);
            $tmp->image_width = $t->image_width;
            $tmp->image_height = $t->image_height;
            $tmp->source = $tmp->target;
            $this->__copy ($t, $tmp, 0, 0);
            $this->__destroy_source($tmp);
            $tmp->target = $t->target;
            return true;
        }

        parent::__rotate ($tmp, $angle, $color);
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
    protected function __crop(Pitlib_Tmp $tmp, $x, $y, $width, $height) {

        if (!$_ = imlib_create_cropped_image($tmp->target, $x, $y,
                    $width, $height)) {
            return false;
        }
        $this->__destroy_target($tmp);
        $tmp->target = $_;

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
    protected function __flip(Pitlib_Tmp $tmp) {
        imlib_image_flip_vertical($tmp->target);
        return true;
    }

    /**
     * Horizontally mirror (flop) the image
     * 
     * @param Pitlib_Tmp $tmp
     * @return boolean
     * @access protected
     */
    protected function __flop(Pitlib_Tmp $tmp) {
        imlib_image_flip_horizontal($tmp->target);
        return true;
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
    protected function __canvas($width, $height, Pitlib_Color $color) {

        $t = new Pitlib_Tmp;
        $t->target = imlib_create_image($width, $height);

        list($r, $g, $b) = $color->get();
        imlib_image_fill_rectangle(
                $t->target,
                0, 0, 
                $width, $height,
                $r, $g, $b, 255
                );
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
    protected function __tmpimage($handler, $filename=null) {

        if (!isset($filename)) {
            $filename = $this->__tmpfile();
        }

        imlib_save_image($handler, $filename);

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
    protected function __open(Pitlib_Tmp $tmp) {

        $error_open = !($tmp->source =
                imlib_load_image(realpath($tmp->source_filename)));
        $error_open &= !($tmp->target = imlib_clone_image($tmp->source));

        // get width & height of the image
        //
        if (!$error_open) {
            $tmp->image_width = imlib_image_get_width($tmp->source);
            $tmp->image_height = imlib_image_get_height($tmp->source);
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
    protected function __write(Pitlib_Tmp $tmp) {

        $ret = false;

        if (!$tmp->target) {
            throw new Pitlib_Exception ("Not target in __write!!!");
        }

        if ($tmp->save) {
            $tmp->save = strtolower ($tmp->save);

            // convert, then save
            //
            imlib_image_set_format(
                    $tmp->target, $tmp->save
                    );

            $t = $this->__tmpfile();
            if (!imlib_save_image($tmp->target, $t)) {
                return false;
            }

            $ret = @copy($t, $tmp->target_filename);
            @unlink($t);

        } else {

            fclose(fopen($tmp->target_filename, 'w'));

            // no convert, just save
            //
            $ret = imlib_save_image(
                    $tmp->target, realpath($tmp->target_filename)
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
     * @abstract
     */	
    protected function __destroy_source(Pitlib_Tmp $tmp) {
        return imlib_free_image($tmp->source);
    }

    /**
     * Destroy the target for the provided temporary object
     *
     * @param Pitlib_Tmp $tmp
     * @return boolean
     * @access protected
     * @abstract
     */	
    protected function __destroy_target(Pitlib_Tmp $tmp) {
        return imlib_free_image($tmp->target);
    }

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    //--end-of-class--	
}

/////////////////////////////////////////////////////////////////////////////

?>
