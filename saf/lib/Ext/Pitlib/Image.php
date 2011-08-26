<?php
// vim: tabstop=4: expandtab: sts=4: ai: sw=4:

/**
 * @author Charles Brunet
 * @author Kaloyan K. Tsvetkov <kaloyan@kaloyan.info>
 * @license http://opensource.org/licenses/lgpl-license.php
 *    GNU Lesser General public License Version 2.1
 * @package Pitlib
 * @subpackage Pitlib.Core
 * @version 0.1.0
 */

/////////////////////////////////////////////////////////////////////////////

/**
 * Pitlib Image
 *
 * @package Pitlib
 * @subpackage Pitlib.Core
 */
class Pitlib_Image {

    /**
     * Source Image 
     *
     * This is the image that is the source for the 
     * image operations
     *
     * @var string
     * @access protected
     */
    protected $__source = null;

    /**
     * Target Image
     *
     * This is the image that we are going to write 
     * the results from the image operations
     *
     * @var string
     * @access protected
     */
    protected $__target = null;
    
    /**
     * Image quality for target (only supported for some formats and drivers)
     *
     * @var integer
     * @access protected
     */
    protected $__quality = 80;

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Operation Queue
     * @var array
     * @access protected
     */
    protected $__operations = array();

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Constructor
     *
     * @param string $source source image for the image operations
     * @param string $target target image for the image operations
     */
    public function __construct($source=null, $target=null) {
        $this->source($source);
        $this->target($target);
    }

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Get/Set source image
     *
     * @param string $new_source
     * @return string
     * @access public
     */
    public function source($new_source = null) {

        // set new source
        //
        if (isset($new_source)) {

            // is it readable
            //
            if (!is_readable($new_source)) {
                throw new Pitlib_Exception (
                        sprintf(
                            'Not storing source file "%s", '
                            . ' because it is not '
                            . ' readable',
                            $new_source
                            )
                        );
            } 
            $this->__source = $new_source;
        }

        // return source
        //
        return $this->__source;
    }

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Get/Set target image
     *
     * @param string $new_target
     * @return string
     * @access public
     */
    public function target($new_target = null) {

        // set new target
        //
        if (isset($new_target)) {

            // is it writable
            //
            if (!is_writable(dirname($new_target))) {
                throw new Pitlib_Exception (
                        sprintf(
                            'Not storing target file "%s", '
                            . ' because target file '
                            . ' directory "%s" is '
                            . ' not writable',
                            $new_target,
                            dirname($new_target)
                            )
                        );
            }
            $this->__target = $new_target;
        }

        // return target
        //
        return $this->__target;
    }

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Store an operation in the image operation queue
     *
     * @param callback $callback
     * @param array $params
     *
     * @access public
     */
    public function operation($callback, $params) {
        $o = new stdclass;
        $o->callback = $callback;
        $o->params = $params;
        $this->__operations[] = $o;
    }

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Set image quality
     *
     * @param integer $quality
     *
     */
    public function set_quality ($quality) {
        $this->__quality = $quality % 101;
    }

    public function get_quality () {
        return $this->__quality;
    }

    /**
     * Save the image after being processed
     *
     * @param mixed $overwrite_mode if this mode is ON 
     *		({@link Pitlib::OVERWRITE_ENABLED}), and there is already a file 
     *		with the same as the target, this method will throw a 
     *		warning stating that you are abut to overwrite an 
     *		existing file
     * @return boolean
     *
     * @access public
     */
    public function save($overwrite_mode = Pitlib::OVERWRITE_DISABLED) {

        // check files
        //
        if (!is_writable(dirname($this->__target))) {
            throw new Pitlib_Exception (
                    sprintf(
                        'Target file directory "%s" is not writable',
                        dirname($this->__target)
                        )
                    );
        }
        if (!is_readable($this->__source)) {
            throw new Pitlib_Exception (
                    sprintf(
                        'Source file "%s" is not readable',
                        $this->__source
                        )
                    );
        }
        if (Pitlib::OVERWRITE_DISABLED == $overwrite_mode) {
            if (file_exists($this->__target)) {
                throw new Pitlib_Exception (
                        sprintf(
                            'Overwriting target file "%s" is not allowed',
                            $this->__target
                            )
                        );				
            }
        }

        // Get the driver. It none assigned, try to get the best available
        $d =& Pitlib::driver();
        // init ?
        //
        if (!$tmp = call_user_func_array(
                    array(
                        &$d,
                        'prepare'
                        ),
                    array(&$this)
                    )
           ) {
            throw new Pitlib_Exception (
                    'Failed to initialize image operations'
                    );
        }

        // do the queue
        //
        $failed = false;
        foreach ($this->__operations as $o) {

            // callback exists ?
            //
            if (!is_callable($o->callback)) {
                throw new Pitlib_Exception (
                        sprintf(
                            'Operation %s::%s() not found',
                            get_class($o->callback[0]),
                            $o->callback[1]
                            )
                        );
                continue;
            }

            // do call it
            //
            $o->params['tmp'] =& $tmp;
            if (!call_user_func_array($o->callback, $o->params)) {
                throw new Pitlib_Exception (
                        sprintf(
                            'Failed performing %s::%s()',
                            get_class($o->callback[0]),
                            $o->callback[1]
                            )
                        );
                $failed = true;
            }
        }

        // save ?
        //
        if (!call_user_func_array(
                    array(
                        &$d,
                        'save'
                        ),
                    array($tmp)
                    )
           ) {
           //print_r ($tmp);
            throw new Pitlib_Exception (
                    'Failed to save the result of the image operations'
                    );
        }

        return $failed;
    }

/////////////////////////////////////////////////////////////////////////////

    /**
     * Resize an image
     *
     * Use this method to resize an image.
     * The resize operation can be performed in three modes. The 
     * proportional mode set by Pitlib::RESIZE_PROPORTIONAL will attempt to fit 
     * the image inside the "frame" create by the $width and $height arguments, 
     * while the stretch mode set by Pitlib::RESIZE_STRETCH will stretch the 
     * image if necessary to fit into that "frame". The "fitting" mode set by 
     * Pitlib::RESIZE_FIT will attempt to resize the image proportionally only
     * if it does not fit inside the "frame" set by the provided width and
     * height: if it does fit, the image will not be resized at all.
     *
     * @param integer $width
     * @param integer $height
     * @param mixed $mode mode for resizing the image:
     *   either Pitlib::RESIZE_STRETCH or Pitlib::RESIZE_PROPORTIONAL 
     *   or Pitlib::RESIZE_FIT 
     * @return Pitlit_Image
     *
     * @access public
     */

    public function resize ($width, $height,
            $mode = Pitlib::RESIZE_PROPORTIONAL) {
        $d = Pitlib::driver ();
        $this->operation (
            array ($d, __FUNCTION__),
            array ('tmp'    => null,
                   'width'  => $width,
                   'height' => $height,
                   'mode'   => $mode));
        return $this;
    }

    /**
     * Resize an image by making it fit a particular width
     *
     * Use this method to resize an image
     * object by making it fit a particular width while keeping the
     * proportions ratio.
     *
     * @param integer $width
     * @return Pitlib_Image
     *
     * @access public
     */
    public function width ($width) {
        $d = Pitlib::driver ();
        $this->operation (
                array ($d, 'resize'),
                array(
                    'tmp'    => null,
                    'width'  => $width,
                    'height' => 0,
                    'mode'   => Pitlib::RESIZE_PROPORTIONAL,
                    )
                );
        return $this;
    }

    /**
     * Resize an image by making it fit a particular height
     *
     * Use this method to resize an image
     * object by making it fit a particular height while keeping the
     * proportions ratio.
     *
     * @param integer $width
     * @return Pitlib_Image
     *
     * @access public
     */
    public function height ($height) {
        $d = Pitlib::driver ();
        $this->operation (
                array ($d, 'resize'),
                array(
                    'tmp'    => null,
                    'width'  => 0,
                    'height' => $height,
                    'mode'   => Pitlib::RESIZE_PROPORTIONAL,
                    )
                );
        return $this;
    }

    /**
     * Resize an image by stretching it by the provided width and height
     *
     * Use this method to resize an image
     * object by stretching it to fit a particular height without keeping
     * the proportions ratio.
     *
     * @param integer $width
     * @param integer $height
     * @return Pitlib_Image
     *
     * @access public
     */
    public function stretch ($width, $height) {
        $d = Pitlib::driver ();
        $this->operation (
                array ($d, 'resize'),
                array(
                    'tmp'    => null,
                    'width'  => $width,
                    'height' => $height,
                    'mode'   => Pitlib::RESIZE_STRETCH,
                    )
                );
        return $this;
    }
    
    /**
     * Resize an image by "fitting" in the provided width and height
     *
     * Use this method to resize an image
     * object if it is bigger then the "frame" set by the provided width and 
     * height: if it is smaller it will not be resized
     *
     * @param integer $width
     * @param integer $height
     * @return Pitlib_Image
     *
     * @access public
     * @static
     */
    public function fit ($width, $height) {
        $d = Pitlib::driver ();
        $this->operation (
                array ($d, 'resize'),
                array(
                    'tmp'    => null,
                    'width'  => $width,
                    'height' => $height,
                    'mode'   => Pitlib::RESIZE_FIT,
                    )
                );
        return $this;
    }

    /**
     * Resize an image by "framing" it with the provided width and height
     *
     * Use this method to resize an image
     * object by placing it inside the "frame" set by the provided width and 
     * height. First the image will be resized in the same manner as {@link 
     * Pitlib_Image::fit()} does, and then it will be placed in the center of a
     * canvas with the proportions of the provided width and height (achieving
     * a "passepartout" framing effect). The background of the "passepartout" 
     * is set by the $color argument
     *
     * @param integer $width
     * @param integer $height
     * @param Pitlib_Color $color    passepartout background
     * @return Pitlib_Image
     *
     * @access public
     */
    public function frame ($width, $height, $color=null) {
        $d = Pitlib::driver ();
        $this->operation(
                array ($d, __FUNCTION__),
                array(
                    'tmp'    => null,
                    'width'  => $width,
                    'height' => $height,
                    'color'  => $color,
                    )
                );
        return $this;
    }

    /**
     * Convert an image from one file-type to another
     *
     * Use this method to convert an image
     * object from its original file-type to another.
     *
     * @param mixed $type Image type or MIME type of the file-type to which
     *     this image should be converted to
     * @return Pitlib_Image
     *
     * @access public
     */
    public function convert ($type) {
        $d = Pitlib::driver ();
        $this->operation (
                array ($d, __FUNCTION__),
                array(
                    'tmp'  => null,
                    'type' => $type
                    )
                );
        return $this;
    }

    /**
     * Watermark an image
     *
     * Use this method to watermark an image
     * object. You can set the position of the watermark (the gravity) by using 
     * each of the nine available "single" positions (single means the 
     * watermark will appear only once), or the "tile" position, which applied 
     * the watermark all over the image like a tiled wallpaper. If the 
     * watermark image is larger than the image that is supposed to be 
     * watermarked you can shrink the watermark image: the scale of its 
     * shrinking is determined by the $scalable_factor argument.
     * 
     * @param string $watermark_image path to the file which is going to be
     *   use as watermark
     * @param mixed $position position(gravity) of the watermark: the 
     *   available values are Pitlib::WATERMARK_TOP_LEFT, 
     *   Pitlib::WATERMARK_TOP_CENTER, Pitlib::WATERMARK_TOP_RIGHT, 
     *   Pitlib::WATERMARK_MIDDLE_LEFT, Pitlib::WATERMARK_MIDDLE_CENTER, 
     *   Pitlib::WATERMARK_MIDDLE_RIGHT, Pitlib::WATERMARK_BOTTOM_LEFT, 
     *   Pitlib::WATERMARK_BOTTOM_CENTER, Pitlib::WATERMARK_BOTTOM_RIGHT and 
     *   Pitlib::WATERMARK_TILE
     * @param mixed $scalable whether to shrink the watermark or not if the 
     *   watermark image is bigger than the image that is supposed to be 
     *   watermarked. 
     * @param float $scalable_factor watermark scaling factor
     * @return Pitlib_Image
     *
     * @access public
     */
    public function watermark ($watermark_image,
            $position = Pitlib::WATERMARK_BOTTOM_RIGHT,
            $scalable = Pitlib::WATERMARK_SCALABLE_ENABLED,
            $scalable_factor = Pitlib::WATERMARK_SCALABLE_FACTOR
            ) {
        $d = Pitlib::driver ();
        $this->operation (
                array ($d, __FUNCTION__),
                array(
                    'tmp'             => null,
                    'watermark_image' => $watermark_image,
                    'position'        => $position,
                    'scalable'        => $scalable,
                    'scalable_factor' => $scalable_factor
                    )
                );
        return $this;
    }

    /**
     * Grayscale the image
     * 
     * @return Pitlib_Image
     *
     * @access public
     */
    public function grayscale () {
        $d = Pitlib::driver ();
        $this->operation (
                array ($d, __FUNCTION__),
                array( 'tmp' => null )
                );
        return $this;
    }

    /**
     * Grayscale the image
     * 
     * @return Pitlib_Image
     *
     * @access public
     */
    public function greyscale () {
        return $this->grayscale ();
    }

   /**
     * Rotate the image (clockwise)
     * 
     * @param integer $angle 
     * @param Pitlib_Color $color    background color for when non-rectangular
     *     angles are used
     * @return Pitlib_Image
     *
     * @access public
     */
    public function rotate ($angle, $color=null) {
        $d = Pitlib::driver ();
        $this->operation(
                array ($d, __FUNCTION__),
                array (
                    'tmp'   => null,
                    'angle' => $angle,
                    'color' => $color,
                    )
                );
        return $this;
    }

   /**
     * Copy an image onto the image object
     *
     * @param string $applied_image  filepath to the image that is going to be
     *    copied
     * @param integer $x
     * @param integer $y
     * @return Pitlib_Image
     *
     * @access public
     */
    public function copy ($applied_image, $x, $y) {
        $d = Pitlib::driver ();
        $this->operation (
                array ($d, __FUNCTION__),
                array (
                    'tmp'   => null,
                    'image' => $applied_image,
                    'x'     => $x,
                    'y'     => $y
                    )
                );
        return $this;
    }

    /**
     * Crop an image object
     *
     * @param integer $x
     * @param integer $y
     * @param integer $width
     * @param integer $height
     * @return Pitlib_Image
     *
     * @access public
     */
    public function crop ($x, $y, $width, $height) {
        $d = Pitlib::driver ();
        $this->operation (
                array ($d, __FUNCTION__),
                array (
                    'tmp'    => null,
                    'x'      => $x,
                    'y'      => $y,
                    'width'  => $width,
                    'height' => $height
                    )
                );
        return $this;
    }

    /**
     * Creates a vertical mirror (flip) by reflecting the pixels around the
     *    central X-axis
     * 
     * @return Pitlib_Image
     *
     * @access public
     */
    public function flip () {
        $d = Pitlib::driver ();
        $this->operation (
                array ($d, __FUNCTION__),
                array ('tmp' => null)
                );
        return $this;
    }

    /**
     * Creates a horizontal mirror (flop) by reflecting the pixels around the
     *    central Y-axis
     * 
     * @return Pitlib_Image
     *
     * @access public
     */
    public function flop () {
        $d = Pitlib::driver ();
        $this->operation (
                array ($d, __FUNCTION__),
                array ('tmp' => null)
                );
        return $this;
    }

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    //--end-of-class--	
}

/////////////////////////////////////////////////////////////////////////////

?>
