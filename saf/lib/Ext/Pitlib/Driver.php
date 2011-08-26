<?php
// vim: tabstop=4: expandtab: sts=4: ai: sw=4:

/**
 * @author Charles Brunet <cbrunet@php.net>
 * @author Kaloyan K. Tsvetkov <kaloyan@kaloyan.info>
 * @license http://opensource.org/licenses/lgpl-license.php
 *    GNU Lesser General public License Version 2.1
 * @package Pitlib
 * @subpackage Pitlib.Driver
 * @version $Id: class.driver.php 17 2007-12-05 11:57:55Z Mrasnika $
 */

/////////////////////////////////////////////////////////////////////////////

/**
 * Pitlib abstract driver
 *
 * @package Pitlib
 * @subpackage Pitlib.Driver
 * @abstract
 */
abstract Class Pitlib_Driver {

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Maps to supported image types
     * @var array
     * @access protected
     */
    protected $__read = array();
    protected $__write = array();

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Checks whether the environment is compatible with this driver
     *
     * @return boolean
     * @access public
     * @abstract
     */
    abstract public function is_compatible();

    /**
     * Return the driver name
     *
     * @return string
     * @access public
     * @abstract
     */
    abstract public function name ();

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Resize an image
     *
     * @param Pitlib_Tmp $tmp
     * @param integer $width
     * @param integer $height
     * @param mixed $mode
     * @return boolean
     * @access public
     */
    public function resize (Pitlib_Tmp $tmp, $width, $height, $mode) {

        // no params ?
        //
        if (!$width && !$height) {
            throw new Pitlib_Exception (
                    sprintf(
                        'Neither width nor height provided '
                        . ' for resizing operation '
                        . ' of "%s" ',
                        $tmp->source_filename
                        )
                    );
        }

        // resize by only one parameter ?
        //
        if (!$width || !$height) {
            $mode = Pitlib::RESIZE_PROPORTIONAL;

            if (!$width) {
                $width = floor(
                        $tmp->image_width * $height / $tmp->image_height
                        );
            }
            if (!$height) {
                $height = floor(
                        $tmp->image_height * $width / $tmp->image_width
                        );
            }
        }

        // stretch or proportional
        //
        switch ($mode) {

            case Pitlib::RESIZE_STRETCH:
                $p_width = $width;
                $p_height = $height;
                break;

            case Pitlib::RESIZE_FIT:

                if (($tmp->image_height <= $height) &&
                        ($tmp->image_width <= $width)) {
                    $p_width = $tmp->image_width;
                    $p_height = $tmp->image_height;

                    // break the switch\case
                    //
                    break;
                }

                // if indeed has to be resized, fall
                // back to the proportional resize
                //
                ;

            default:
            case Pitlib::RESIZE_PROPORTIONAL:

                $p1_width = $width;
                $p1_height = round(
                        $tmp->image_height * $width / $tmp->image_width
                        );

                if ($p1_height - $height > 1) {
                    $p_height = $height;
                    $p_width = round(
                            $tmp->image_width * $height / $tmp->image_height
                            );
                } else {
                    $p_width = $p1_width;
                    $p_height = $p1_height;
                }
                break;
        }

        // do the resize
        //
        $r = $this->__resize($tmp, $p_width, $p_height);

        // new dimensions ?
        //
        $tmp->image_width = $p_width;
        $tmp->image_height = $p_height;

        return $r;
    }

    /**
     * Convert an image from one file-type to another
     *
     * @param Pitlib_Tmp $tmp
     * @param mixed $type Image type or Mime type
     * @return boolean
     * @access public
     */
    public function convert(Pitlib_Tmp $tmp, $type) {

        if (strpos ('/', $type)) {
            $type = Pitlib_Type::from_mime ($type);
        }

        // supported format ? (for writing)
        //
        if (!$this->supported($type, Pitlib::SUPPORT_WRITE)) {
            throw new Pitlib_Exception(
                    sprintf(
                        'Requested conversion format "%s" is not supported',
                        $type
                        )
                    );
        }

        $tmp->save = $type;
        return true;
    }

    /**
     * Watermark an image 
     *
     * @param Pitlib_Tmp $tmp
     * @param string $watermark_image
     * @param mixed $position
     * @param mixed $scalable
     * @param float $scalable_factor
     * @return boolean
     * @access public
     */
    public function watermark(Pitlib_Tmp $tmp, $watermark_image, $position,
            $scalable, $scalable_factor) {

        // open
        //
        $wi = new Pitlib_Image($watermark_image);
        $wt = $this->prepare($wi);

        // dimensions
        //
        $target_width = $tmp->image_width;
        $target_height = $tmp->image_height;

        $watermark_width =& $wt->image_width;
        $watermark_height =& $wt->image_height;

        // watermark scalable ?
        //
        if ((Pitlib::WATERMARK_SCALABLE_ENABLED == $scalable)
                && ($watermark_width > $target_width * $scalable_factor
                    || $watermark_height > $target_height * $scalable_factor)
           ){

            // jump thru tha loop
            //
            $t2 = $this->__tmpimage($wt->source);

            if ($this->resize($t2,
                        intval($target_width * $scalable_factor),
                        intval($target_height * $scalable_factor),
                        Pitlib::RESIZE_PROPORTIONAL)
               ){

                unlink($t2->source_filename);
                @unlink ($t2->source_filename.'.alpha'); //ugly, but I don't know where to erase it...
                $this->__destroy_target($wt);

                // new watermark created, destroy old
                //
                $this->__destroy_source($wt);

                // copy new watermark
                //
                $wt->source = $t2->target;
                $this->__destroy_source($t2);
                // ^
                // DO NOT UNSET $t2->target!!!

                // adjust watermark dimensions
                //
                $watermark_width = $t2->image_width;
                $watermark_height =$t2->image_height;
            }
        }
        else {
            $this->__destroy_target ($wt);
        }

        // position
        //
        switch ($position) {

            // tile watermark
            //
            case Pitlib::WATERMARK_TILE :
                $watermark_x = 1;
                $watermark_y = 1;

                // create tile
                //
                for ($x = 0; $x < ceil($target_width / $watermark_width);
                        $x++) {
                    for ($y = 0; $y < ceil($target_height / $watermark_height);
                            $y++) {

                        // skip the first one
                        //
                        if (!$x && !$y) continue;

                        // copy the watermark
                        //
                        $this->__copy($tmp, $wt,
                                $watermark_x + $x*$watermark_width,
                                $watermark_y + $y*$watermark_height
                                );
                    }
                }
                break;

                // top left, north west
                //
            case Pitlib::WATERMARK_TOP_LEFT :
                $watermark_x = 1;
                $watermark_y = 1;
                break;

                // top center, north
                //
            case Pitlib::WATERMARK_TOP_CENTER :
                $watermark_x = ($target_width - $watermark_width)/2;
                $watermark_y = 1;
                break;

                // top right, north east
                //
            case Pitlib::WATERMARK_TOP_RIGHT :
                $watermark_x = $target_width - $watermark_width ;
                $watermark_y = 1;
                break;

                // middle left, west
                //
            case Pitlib::WATERMARK_MIDDLE_LEFT :
                $watermark_x = 1;
                $watermark_y = ($target_height - $watermark_height)/2;
                break;

                // middle center, center
                //
            case Pitlib::WATERMARK_MIDDLE_CENTER :
                $watermark_x = ($target_width - $watermark_width)/2;
                $watermark_y = ($target_height - $watermark_height)/2;
                break;

                // middle right, east
                //
            case Pitlib::WATERMARK_MIDDLE_RIGHT :

                $watermark_x = $target_width - $watermark_width ;
                $watermark_y = ($target_height - $watermark_height)/2;
                break;

                // bottom left, south west
                //
            case Pitlib::WATERMARK_BOTTOM_LEFT :
                $watermark_x = 1;
                $watermark_y = $target_height - $watermark_height ;
                break;

                // bottom center, south
                //
            case Pitlib::WATERMARK_BOTTOM_CENTER :
                $watermark_x = ($target_width - $watermark_width)/2;
                $watermark_y = $target_height - $watermark_height ;
                break;

            default :

                // bottom right, south east
                //
            case Pitlib::WATERMARK_BOTTOM_RIGHT :
                $watermark_x = $target_width - $watermark_width ;
                $watermark_y = $target_height - $watermark_height ;
                break;
        }

        // copy the watermark
        //
        $this->__copy($tmp, $wt, $watermark_x, $watermark_y);

        // destroy watermark image
        //
        $this->__destroy_source($wt);
        return true;
    }

    /**
     * Make the image greyscale
     *
     * @param Pitlib_Tmp $tmp
     * @return boolean
     * @access public
     */
    public function grayscale(Pitlib_Tmp $tmp) {
        return $this->__grayscale($tmp);
    }

    /**
     * Rotate the image clockwise
     *
     * @param Pitlib_Tmp $tmp
     * @param float $angle
     * @param Pitlib_Color $color
     * @return boolean
     * @access public
     */
    public function rotate(Pitlib_Tmp $tmp, $angle, Pitlib_Color $color=null) {

        // color ?
        //
        if (!isset($color)) {
            $color = new Pitlib_Color;
            $color->set(255, 255, 255);
        }

        return $this->__rotate($tmp, $angle, $color);
    }

    /**
     * Resize an image by "framing" it with the provided width and height
     *
     * @param Pitlib_Tmp $tmp
     * @param integer $width
     * @param integer $height
     * @param Pitlib_Color $color
     * @return boolean
     * @access public
     */
    public function frame(Pitlib_Tmp $tmp, $width, $height,
            Pitlib_Color $color=null) {

        // color ?
        //
        if (!isset($color)) {
            $color = new Pitlib_Color;
            $color->set(255, 255, 255);
        }

        // resize it
        //
        $this->resize($tmp, $width, $height, Pitlib::RESIZE_FIT);

        // get canvas
        //
        $t2 = $this->__canvas($width, $height, $color);

        // target
        //
        $t3 = new Pitlib_Tmp;
        $t3->source =& $tmp->target;
        $t3->image_width = $tmp->image_width;
        $t3->image_height = $tmp->image_height;

        // apply the image
        //
        $this->__copy(
                    $t2, $t3,
                    round(($t2->image_width - $t3->image_width)/2),
                    round(($t2->image_height - $t3->image_height)/2)
                    );

        // cook the result
        //
        $this->__destroy_target($tmp);
        $tmp->target = $t2->target;
        $tmp->image_width = $t2->image_width;
        $tmp->image_height = $t2->image_height;

        return true;
    }

    /**
     * Copy applied_image onto the current image
     *
     * @param Pitlib_Tmp $tmp
     * @param string $applied_image	filepath to the image that is going to be
     *     copied
     * @param integer $x
     * @param integer $y
     * @return boolean
     * @access public
     */
    public function copy(Pitlib_Tmp $tmp, $applied_image, $x, $y) {

        // open
        //
        $ci = new Pitlib_Image ($applied_image);
        $ct = $this->prepare($ci);

        $this->__copy($tmp, $ct, $x, $y);

        $this->__destroy_source($ct);
        $this->__destroy_target($ct);

        return true;
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
     * @access public
     */
    public function crop(Pitlib_Tmp $tmp, $x, $y, $width, $height) {
        return $this->__crop($tmp, $x, $y, $width, $height);
    }

    /**
     * Vertically mirror (flip) the image
     * 
     * @param Pitlib_Tmp $tmp
     * @return boolean
     * @access public
     */
    public function flip(Pitlib_Tmp $tmp) {
        return $this->__flip($tmp);
    }

    /**
     * Horizontally mirror (flop) the image
     * 
     * @param Pitlib_Tmp $tmp
     * @return boolean
     * @access public
     */
    public function flop(Pitlib_Tmp $tmp) {
        return $this->__flop($tmp);
    }

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Prepare an image for processing it
     *
     * @param Pitlib_Image $image
     * @return Pitlib_Tmp
     * @access public
     */
    public function prepare(Pitlib_Image $image) {

        // create new temporary object
        //
        $tmp = new Pitlib_Tmp;
        $tmp->source_filename = $image->source();
        $tmp->target_filename = $image->target();
        $tmp->quality = $image->get_quality();

        // failed opening ?
        //
        if (!$this->__open($tmp)) {
            print_r ($tmp);
            throw new Pitlib_Exception (
                    'Unable to open source image'
                    );
        }

        return $tmp;
    }

    /**
     * Save an image after being processed
     *
     * @param Pitlib_Tmp $tmp
     * @return boolean
     * @access public
     */
    public function save(Pitlib_Tmp $tmp) {
        return $this->__write($tmp);
    }

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Copy one image to another
     *
     * @param Pitlib_Tmp $tmp_target
     * @param Pitlib_Tmp $tmp_source
     * @param integer $destination_x
     * @param integer $destination_y
     * @return boolean
     * @access protected
     * @abstract
     */
    protected function __copy(Pitlib_Tmp $tmp_target,
            Pitlib_Tmp $tmp_source, $destination_x, $destination_y) {
        throw new Pitlib_Exception_OperationNotSupported;
    }

    /**
     * Do the actual resize of an image
     *
     * @param Pitlib_Tmp $tmp
     * @param integer $width
     * @param integer $height
     * @return boolean
     * @access protected
     * @abstract
     */
    protected function __resize(Pitlib_Tmp $tmp, $width, $height) {
        throw new Pitlib_Exception_OperationNotSupported;
    }

    /**
     * Make the image greyscale
     *
     * @param Pitlib_Tmp $tmp
     * @return boolean
     * @access protected
     * @abstract
     */
    protected function __grayscale(Pitlib_Tmp $tmp) {
        throw new Pitlib_Exception_OperationNotSupported;
    }

    /**
     * Rotate the image clockwise
     *
     * @param Pitlib_Tmp $tmp
     * @param float $angle
     * @param Pitlib_Color $color
     * @return boolean
     * @access protected
     * @abstract
     */
    protected function __rotate(Pitlib_Tmp $tmp, $angle,
            Pitlib_Color $color) {
        throw new Pitlib_Exception_OperationNotSupported;
    }

    /**
     * Get canvas
     *
     * @param integer $width
     * @param integer $height
     * @param Pitlib_Color $color
     * @return Pitlib_Tmp
     * @access protected
     * @abstract
     */
    protected function __canvas($width, $height, Pitlib_Color $color) {
        throw new Pitlib_Exception_OperationNotSupported;
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
     * @abstract
     */
    protected function __crop(Pitlib_Tmp $tmp, $x, $y,
            $width, $height) {
        throw new Pitlib_Exception_OperationNotSupported;
    }

    /**
     * Vertically mirror (flip) the image
     * 
     * @param Pitlib_Tmp $tmp
     * @return boolean
     * @access protected
     * @abstract
     */
    protected function __flip(Pitlib_Tmp $tmp) {
        throw new Pitlib_Exception_OperationNotSupported;
    }

    /**
     * Horizontally mirror (flop) the image
     * 
     * @param Pitlib_Tmp $tmp
     * @return boolean
     * @access protected
     * @abstract
     */
    protected function __flop(Pitlib_Tmp $tmp) {
        throw new Pitlib_Exception_OperationNotSupported;
    }

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Get supported mime-types
     *
     * @param mixed $mode
     * @return array
     * @access public
     */	
    public function get_supported_types($mode) {

        switch ($mode) {

            case 'Pitlib::SUPPORT_READ':
                return array_values($this->__read);
                break;

            case 'Pitlib::SUPPORT_WRITE':
                return array_values($this->__write);
                break;

            default :
            case 'Pitlib::SUPPORT_READ_WRITE' :
                return array_unique(
                        array_intersect(
                            array_values($this->__write),
                            array_values($this->__read)
                            )
                        );
                break;
        }
    }

    /**
     * Returnes whether an image format is supported or not
     *
     * @param string $type
     * @param integer $mode
     * @return boolean
     * @access public
     */
    public function supported($type, $mode) {
        return in_array(
                $type,
                $this->get_supported_types($mode)
                );
    }

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Open the source and target image for processing it
     *
     * @param Pitlib_Tmp $tmp
     * @return boolean
     * @access protected
     * @abstract
     */
    abstract protected function __open(Pitlib_Tmp $tmp);

    /**
     * Write the image after being processed
     *
     * @param Pitlib_Tmp $tmp
     * @return boolean
     * @access protected
     * @abstract
     */
    abstract protected function __write(Pitlib_Tmp $tmp);

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Return a name for a temporary file
     * @return string
     * @access protected
     */
    protected function __tmpfile() {
        return tempnam(Pitlib::$TEMPDIR, 'pitlib_');
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
     * @abstract
     */
    abstract protected function __tmpimage($handler, $filename=null);

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Destroy the source for the provided temporary object
     *
     * @param Pitlib_Tmp $tmp
     * @return boolean
     * @access protected
     * @abstract
     */	
    abstract protected function __destroy_source(Pitlib_Tmp $tmp);

    /**
     * Destroy the target for the provided temporary object
     *
     * @param Pitlib_Tmp $tmp
     * @return boolean
     * @access protected
     * @abstract
     */	
    abstract protected function __destroy_target(Pitlib_Tmp $tmp);

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    //--end-of-class--	
}

/////////////////////////////////////////////////////////////////////////////

?>
