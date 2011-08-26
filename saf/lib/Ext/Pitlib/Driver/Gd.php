<?php
// vim: tabstop=4: expandtab: sts=4: ai: sw=4:

/**
 * @author Charles Brunet <cbrunet@php.net>
 * @author Kaloyan K. Tsvetkov <kaloyan@kaloyan.info>
 * @license http://opensource.org/licenses/lgpl-license.php
 *     GNU Lesser General public License Version 2.1
 * @package Pitlib
 * @subpackage Pitlib.Driver
 * @version 0.1.0
 *
 * @todo Support GD file format
 */

/////////////////////////////////////////////////////////////////////////////

/**
 * Pitlib GD(GD2) driver
 *
 * @package Pitlib
 * @subpackage Pitlib.Driver
 */
class Pitlib_Driver_Gd Extends Pitlib_Driver {

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Maps to supported images types
     * @var array
     * @access protected
     */
    protected $__read = array(
        Pitlib_Type::GIF,
        Pitlib_Type::JPEG,
        Pitlib_Type::WBMP,
        Pitlib_Type::XPM,
        Pitlib_Type::XBM,
        Pitlib_Type::PNG,
        );

    protected $__write = array(
        Pitlib_Type::GIF,
        Pitlib_Type::JPEG,
        Pitlib_Type::WBMP,
        Pitlib_Type::PNG,
        );

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Checks whether the environment is compatible with this driver
     *
     * @return boolean
     * @access public
     */
    public function is_compatible() {

        if (!extension_loaded('gd')) {
            throw new Pitlib_Exception (
                    'The Pitlib_Driver_GD driver is unnable to be '
                    . ' initialized, because the GD (php_gd2) '
                    . ' module is not installed'
                    );
        }

        // give access to all the memory
        //
        @ini_set("memory_limit", -1);

        return true;
    }

    public function name () {
        return 'gd';
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
        $_ = imageCreateTrueColor($width, $height);
        imageSaveAlpha($_, true);
        imageAlphaBlending($_, false);

        $r = imageCopyResized(
                $_, $tmp->target,
                0,0,
                0,0,
                $width, $height,
                $tmp->image_width, $tmp->image_height
                );

        // set new target
        //
        $this->__destroy_target($tmp);
        $tmp->target = $_;

        return $r;
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

        imageAlphaBlending($tmp_target->target, true);
        $r = imageCopy($tmp_target->target, $tmp_source->source,
                $destination_x, $destination_y,
                0, 0,
                $tmp_source->image_width, $tmp_source->image_height
                );
        imageAlphaBlending($tmp_target->target, false);

        return $r;
    }

    /**
     * Make the image greyscale
     *
     * @param Pitlib_Tmp $tmp
     * @return boolean
     * @access protected
     */
    protected function __grayscale(Pitlib_Tmp $tmp) {

        // the shorter path: function already exists
        //
        if (function_exists('imagefilter')) {
            return imagefilter($tmp->target, IMG_FILTER_GRAYSCALE);
            return true;
        }

        // a bit wicked path: prior to `PHP 4.3.11` and
        // `PHP 5.0.4` there is a bug w/ imageCopyMergeGray()
        //
        if ((version_compare(PHP_VERSION, '4.3.11') > 0)
                || (
                    (version_compare(PHP_VERSION, '5.0.4') > 0)
                   )) {
            return imageCopyMergeGray($tmp->target, $tmp->target,
                    0, 0, 0, 0,
                    $tmp->image_width, $tmp->image_height, 0);
        }

        // Pixel per pixel conversion...
        // create 256 color palette
        //
        $palette = array();
        for ($c=0; $c<256; $c++) {
            $palette[$c] = imageColorAllocate($tmp->target, $c, $c, $c);
        }

        // read origonal colors pixel by pixel
        //
        for ($y=0; $y<$tmp->image_height; $y++) {
            for ($x=0; $x<$tmp->image_width; $x++) {

                $rgb = imageColorAt($tmp->target, $x, $y);

                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                $gs = (($r*0.299)+($g*0.587)+($b*0.114));
                imageSetPixel($tmp->target, $x, $y, $palette[$gs]);
            }
        }

        return true;
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
    protected function __rotate(Pitlib_Tmp $tmp, $angle, Pitlib_Color $color) {

        // skip full loops
        //
        if (($angle % 360) == 0) {
            return true;
        }

        if (! function_exists ('imageRotate')) {
            if (($angle % 180) == 0) {
                $this->__flip ($tmp);
                $this->__flop ($tmp);
                return true;
            }
            else if (($angle % 90) == 0) {
                $this->__rotate90 ($tmp, $angle);
                return true;
            }
            else {
                $this->__rotate_bicubic ($tmp, $angle, $color);
                return true;
            }
        }

        list($r, $g, $b) = $color->get();
        $rotate_color = imageColorAllocate($tmp->target, $r, $g, $b); 

        if ($t = imageRotate($tmp->target, $angle * -1, $rotate_color)) {
            imageDestroy($tmp->target);
            $tmp->target = $t;

            $tmp->image_width = imageSX($tmp->target);
            $tmp->image_height = imageSY($tmp->target);

            return true;
        }

        parent::__rotate ($tmp, $angle, $color);
    }

    protected function __rotate90 (Pitlib_Tmp $tmp, $angle) {
        $t = imageCreateTrueColor($tmp->image_height, $tmp->image_width);
        for ($x = 0; $x < $tmp->image_width; ++$x) {
            for ($y = 0; $y < $tmp->image_height; ++$y) {
                imageCopy ($t, $tmp->target, $y, $x, $x, $y, 1, 1);
            }
        }

        $this->__destroy_target($tmp);
        $tmp->target = $t;
        
        if ($angle == 90) {
            $this->__flop ($tmp);
        }
        else {
            $this->__flip ($tmp);
        }

        return true;
    }
    
	/**
	 * Perform image rotation using bicubic algorithm
	 * 
	 * @param resource $destination Destination image
	 * @param resource $src_img Source image
	 * @param integer $angle Angle of rotation
	 * @param array $background Red, Green, Blue, and Alpha bcakground color
	 * @param boolean $bicubic Use bicubic algorithm (broken...)
	 * @access protected
	 */
	protected function __rotate_bicubic (Pitlib_Tmp $tmp, $angle, Pitlib_Color $color)
	{
        // Calculate result canvas size
        $rad = deg2rad ($angle);
        $sin = sin ($rad);
        $cos = cos ($rad);
        $sx = $tmp->image_width;
        $sy = $tmp->image_height;
        $cx = $sx / 2;
        $cy = $sy / 2;
        $x1 = abs ($cos * $cx + $sin * $cy);
        $x2 = abs ($cos * $cx - $sin * $cy);
        $y1 = abs ($sin * $cx - $cos * $cy);
        $y2 = abs ($sin * $cx + $cos * $cy);
        $dx = round (max ($x1, $x2) * 2);
        $dy = round (max ($y1, $y2) * 2);
        $cx = $dx / 2;
        $cy = $dy / 2;
        $xx = ($dx-$sx)/2;
        $yy = ($dy-$sy)/2;

        if (!$color) {
            $color = Pitlib::Color (0, 0, 0, 255);
        }
        // Fill with color
        $t = $this->__canvas ($dx, $dy, $color);
        list ($r, $g, $b, $a) = $color->get ();
        $bgcolor = imagecolorallocatealpha ($t->target, $r, $g, $b, $a);

        $bicubic = true;

		for ($y = 0; $y < $dy; $y++) {
			for ($x = 0; $x < $dx; $x++) {
				// rotate...
				$ox = round ((($x-$cx) * $cos + ($y-$cy) * $sin) + $cx - $xx);
				$oy = round ((($y-$cy) * $cos - ($x-$cx) * $sin) + $cy - $yy);

				if ( $ox > 0 && $ox < ($sx-1) && $oy > 0 && $oy < ($sy-1) ) {
					if ($bicubic == true) {
						$sY  = $oy + 1;
						$siY  = $oy;
						$siY2 = $oy - 1;
						$sX  = $ox + 1;
						$siX  = $ox;
						$siX2 = $ox - 1;

						$c1 = imagecolorsforindex($tmp->target, imagecolorat($tmp->target, $siX, $siY2));
						$c2 = imagecolorsforindex($tmp->target, imagecolorat($tmp->target, $siX, $siY));
						$c3 = imagecolorsforindex($tmp->target, imagecolorat($tmp->target, $siX2, $siY2));
						$c4 = imagecolorsforindex($tmp->target, imagecolorat($tmp->target, $siX2, $siY));

						$r = ($c1['red']  + $c2['red']  + $c3['red']  + $c4['red']  ) >> 2;
						$g = ($c1['green'] + $c2['green'] + $c3['green'] + $c4['green']) >> 2;
						$b = ($c1['blue']  + $c2['blue']  + $c3['blue']  + $c4['blue'] ) >> 2;
						$a = ($c1['alpha']  + $c2['alpha']  + $c3['alpha']  + $c4['alpha'] ) >> 2;

						$color = imagecolorallocatealpha ($t->target, $r,$g,$b,$a);
					} else {
						$color = imagecolorat($tmp->target, $ox, $oy);
					}
				} else {
					// this line sets the background colour
					$color = $bgcolor;
				}
				imagesetpixel($t->target, $x, $y, $color);
			}
		}
        $this->__destroy_target ($tmp);
        $tmp->target = $t->target;
	}

    /**
     * Vertically mirror (flip) the image
     * 
     * @param Pitlib_Tmp $tmp
     * @return boolean
     * @access protected
     */
    protected function __flip(Pitlib_Tmp $tmp) {
        $t = imageCreateTrueColor($tmp->image_width, $tmp->image_height);
        imageAlphaBlending($t, true);

        for ($y = 0; $y < $tmp->image_height; ++$y) {
            imageCopy(
                    $t, $tmp->target,
                    0, $y,
                    0, $tmp->image_height - $y - 1,
                    $tmp->image_width, 1
                    );
        }
        imageAlphaBlending($t, false);

        $this->__destroy_target($tmp);
        $tmp->target = $t;

        return true;
    }

    /**
     * Horizontally mirror (flop) the image
     * 
     * @param asido_image &$image
     * @return boolean
     * @access protected
     */
    protected function __flop(Pitlib_Tmp $tmp) {

        $t = imageCreateTrueColor($tmp->image_width, $tmp->image_height);
        imageAlphaBlending($t, true);

        for ($x = 0; $x < $tmp->image_width; ++$x) {
            imageCopy(
                    $t,
                    $tmp->target,
                    $x, 0,
                    $tmp->image_width - $x - 1, 0,
                    1, $tmp->image_height
                    );
        }
        imageAlphaBlending($t, false);

        $this->__destroy_target($tmp);
        $tmp->target = $t;

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
     * @access protected
     */
    protected function __crop(Pitlib_Tmp $tmp, $x, $y, $width, $height) {

        $t = imageCreateTrueColor($width, $height);
        imageAlphaBlending($t, true);
        $r = imageCopy($t, $tmp->target,
                0, 0,
                $x, $y,
                $width, $height
                );
        imageAlphaBlending($t, false);

        $this->__destroy_target($tmp);
        $tmp->target = $t;
        $tmp->image_width = $width;
        $tmp->image_height = $height;

        return $r;
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
        $t->target = imageCreateTrueColor($width, $height);

        list($r, $g, $b) = $color->get();
        imageFill($t->target, 1, 1, 
                imageColorAllocate($t->target, $r, $g, $b)
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

        imageAlphaBlending($handler, 0);
        imageSaveAlpha($handler, 1); 
        imagePNG($handler, $filename);
        // ^
        // PNG: no pixel losts

        return $this->prepare( new Pitlib_Image($filename));
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

        $error_source = false;
        $error_target = false;

        // get image dimensions
        //
        if ($i = @getImageSize($tmp->source_filename)) {
            $tmp->image_width = $i[0];
            $tmp->image_height = $i[1];
        }

        // image type ?
        //
        switch(@$i[2]) {

            case 1:	// GIF
                $error_source = (false == (
                            $tmp->source = @imageCreateFromGIF(
                                $tmp->source_filename
                                )
                            ));

                $error_target = false == (
                        $tmp->target = imageCreateTrueColor(
                            $tmp->image_width, $tmp->image_height
                            )
                        );
                $error_target &= imageCopyResampled(
                        $tmp->target, $tmp->source, 
                        0, 0, 0, 0,
                        $tmp->image_width, $tmp->image_height,
                        $tmp->image_width, $tmp->image_height
                        );

                break;

            case 2: // JPG
                $error_source = (false == (
                            $tmp->source = imageCreateFromJPEG(
                                $tmp->source_filename
                                )
                            ));

                $error_target = (false == (
                            $tmp->target = imageCreateFromJPEG(
                                $tmp->source_filename
                                )
                            ));
                break;

            case 3: // PNG
                $error_source = (false == (
                            $tmp->source = @imageCreateFromPNG(
                                $tmp->source_filename
                                )
                            ));

                $error_target = (false == (
                            $tmp->target = @imageCreateFromPNG(
                                $tmp->source_filename
                                )
                            ));
                break;

            case 15: // WBMP
                $error_source = (false == (
                            $tmp->source = @imageCreateFromWBMP(
                                $tmp->source_filename
                                )
                            ));

                $error_target = (false == (
                            $tmp->target = @imageCreateFromWBMP(
                                $tmp->source_filename
                                )
                            ));
                break;

            case 16: // XBM
                $error_source = (false == (
                            $tmp->source = @imageCreateFromXBM(
                                $tmp->source_filename
                                )
                            ));

                $error_target = (false == (
                            $tmp->target = @imageCreateFromXBM(
                                $tmp->source_filename
                                )
                            ));
                break;

            case 4: // SWF

            case 5: // PSD

            case 6: // BMP

            case 7: // TIFF(intel byte order)

            case 8: // TIFF(motorola byte order)

            case 9: // JPC

            case 10: // JP2

            case 11: // JPX

            case 12: // JB2

            case 13: // SWC

            case 14: // IFF

            default:

                $error_source = (false == (
                            $tmp->source = @imageCreateFromString(
                                file_get_contents(
                                    $tmp->source_filename
                                    )
                                )
                            ));

                $error_target = (false == (
                            $tmp->source = @imageCreateFromString(
                                file_get_contents(
                                    $tmp->source_filename
                                    )
                                )
                            ));
                break;
        }

        return !($error_source || $error_target);
    }

    /**
     * Write the image after being processed
     *
     * @param Pitlib_Tmp $tmp
     * @return boolean
     * @access protected
     */
    protected function __write(Pitlib_Tmp $tmp) {

        // try to guess format from extension
        //
        if (!$tmp->save) {
            $tmp->save = Pitlib_Type::from_filename ($tmp->target_filename);
        }

        $result = false;
        switch($tmp->save) {

            case Pitlib_Type::GIF:
                imageTrueColorToPalette($tmp->target, true, 256);
                $result = @imageGIF($tmp->target, $tmp->target_filename);
                break;

            case Pitlib_Type::JPEG:
                $result = @imageJPEG($tmp->target, $tmp->target_filename,
                    $tmp->quality);
                break;

            case Pitlib_Type::WBMP:
                $result = @imageWBMP($tmp->target, $tmp->target_filename);
                break;

            default :
            case Pitlib_Type::PNG:

                imageSaveAlpha($tmp->target, true);
                imageAlphaBlending($tmp->target, false);

                $result = @imagePNG($tmp->target, $tmp->target_filename);
                break;
        }

        @$this->__destroy_source($tmp);
        @$this->__destroy_target($tmp);

        return $result;
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
        return imageDestroy($tmp->source);
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
        return imageDestroy($tmp->target);
    }

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    //--end-of-class--	
}

/////////////////////////////////////////////////////////////////////////////

?>
