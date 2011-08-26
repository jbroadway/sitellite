<?php
// vim: tabstop=4: expandtab: sts=4: ai: sw=4:

/**
 * @author Charles Brunet <cbrunet@php.net>
 * @author Kaloyan K. Tsvetkov <kaloyan@kaloyan.info>
 * @license http://opensource.org/licenses/lgpl-license.php
 *    GNU Lesser General Public License Version 2.1
 * @package Pitlib
 * @subpackage Pitlib.Driver
 * @version 0.1.0
 *
 * @todo set compression for jpeg and png
 */

/////////////////////////////////////////////////////////////////////////////


/**
 * @see Pitlib_Driver_Shell
 */
require_once PITLIB_DIR . "Driver/Shell.php";

/////////////////////////////////////////////////////////////////////////////

/**
 * This is the path to where the Image Magick executables are
 */
if (!defined('PITLIB_IMAGICK_SHELL_PATH')) {
    define('PITLIB_IMAGICK_SHELL_PATH', '');
}

/////////////////////////////////////////////////////////////////////////////

/**
 * Pitlib "Imagick" driver (via shell)
 *
 * @package Pitlib
 * @subpackage Pitlib.Driver
 */
class Pitlib_Driver_Imagick_Shell extends Pitlib_Driver_Shell {

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

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
    Public function __construct() {
        // executable 
        //
        if (PITLIB_IMAGICK_SHELL_PATH) {
            $this->__exec = PITLIB_IMAGICK_SHELL_PATH;
        }
        else {
            $this->__exec = dirname($this->__exec('convert')) . 
                DIRECTORY_SEPARATOR;
        }
    }

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Checks whether the environment is compatible with this driver
     *
     * @return boolean
     * @access public
     */
    Public function is_compatible() {

        if (!$this->__exec) {
            throw new Pitlib_Exception (
                    'The Pitlib_Driver_Imagick_Shell driver is '
                    . ' unable to be initialized, because '
                    . ' the Image Magick (imagick) executables '
                    . ' were not found. Please locate '
                    . ' where those files are and set the '
                    . ' path to them by defining the '
                    . ' PITLIB_IMAGICK_SHELL_PATH constant.'
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
        return 'imagick_shell';
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

        // call `convert -geometry`
        //
        $cmd = $this->__command(
                'convert',
                "-geometry {$width}x{$height}! "
                . escapeshellarg(realpath($tmp->target))
                . " "
                . escapeshellarg(realpath($tmp->target))
                );

        exec($cmd, $result, $errors);
        return ($errors == 0);
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
    protected function __copy (Pitlib_Tmp $tmp_target, Pitlib_Tmp $tmp_source,
            $destination_x, $destination_y) {

        // call `composite -geometry`
        //
        $cmd = $this->__command(
                'composite',
                " -geometry {$tmp_source->image_width}x{$tmp_source->image_height}+{$destination_x}+{$destination_y} "
                . escapeshellarg(realpath($tmp_source->source))
                . " "
                . escapeshellarg(realpath($tmp_target->target))
                . " "
                . escapeshellarg(realpath($tmp_target->target))
                );

        exec($cmd, $result, $errors);
        return ($errors == 0);
    }

    /**
     * Make the image grayscale
     *
     * @param Pitlib_Tmp $tmp
     * @return boolean
     * @access protected
     */
    protected function __grayscale(Pitlib_Tmp $tmp) {

        // call `convert -colorspace`
        //
        $cmd = $this->__command(
                'convert',
                " -colorspace gray "
                . escapeshellarg(realpath($tmp->target))
                . " PGM:"
                . escapeshellarg(realpath($tmp->target))
                );

        exec($cmd, $result, $errors);
        return ($errors == 0);
    }

    /**
     * Rotate the image clockwise: partial support
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

        // rectangular rotates are OK
        //
        if (($angle % 90) == 0) {

            // call `convert -rotate`
            //
            $cmd = $this->__command(
                    'convert',
                    " -rotate {$angle} "
                    . escapeshellarg(realpath($tmp->target))
                    . " TIF:"
                    // ^ 
                    // GIF saving hack
                    . escapeshellarg(realpath($tmp->target))
                    );
            exec($cmd, $result, $errors);
            if ($errors) {
                return false;
            }

            $w1 = $tmp->image_width;
            $h1 = $tmp->image_height;
            $tmp->image_width = ($angle % 180) ? $h1 : $w1;
            $tmp->image_height = ($angle % 180) ? $w1 : $h1;
            return true;
        }
        else {
            $a = $tmp->image_height;
            $b = $tmp->image_width;

            // do the virtual `border`
            //
            $c = $a * cos(deg2rad($angle)) * sin(deg2rad($angle));
            $d = $b * cos(deg2rad($angle)) * sin(deg2rad($angle));

            // do the rest of the math
            //
            $a2 = $b * sin(deg2rad($angle)) + $a * cos(deg2rad($angle));
            $b2 = $a * sin(deg2rad($angle)) + $b * cos(deg2rad($angle));

            $a3 = 2 * $d + $a;
            $b3 = 2 * $c + $b;

            $a4 = $b3 * sin(deg2rad($angle)) + $a3 * cos(deg2rad($angle));
            $b4 = $a3 * sin(deg2rad($angle)) + $b3 * cos(deg2rad($angle));

            // create the `border` canvas
            //
            $t = $this->__canvas(ceil($b + 2*$c), ceil($a + 2*$d), $color);

            // copy the image
            //
            $cmd = $this->__command(
                    'composite',
                    " -geometry {$b}x{$a}+" . ceil($c) . "+" . ceil($d) . " "
                    . escapeshellarg(realpath($tmp->target))
                    . " "
                    . escapeshellarg(realpath($t->target))
                    . " "
                    . escapeshellarg(realpath($t->target))
                    );
            exec($cmd, $result, $errors);
            if ($errors) {
                return false;
            }

            // rotate the whole thing
            //
            $cmd = $this->__command(
                    'convert',
                    " -rotate {$angle} "
                    . escapeshellarg(realpath($t->target))
                    . " "
                    . escapeshellarg(realpath($t->target))
                    );
            exec($cmd, $result, $errors);
            if ($errors) {
                return false;
            }

            // `final` result
            //
            $cmd = $this->__command(
                    'convert',
                    " -crop " . ceil($b2) . "x" . ceil($a2) . "+"
                    . ceil(($b4 - $b2)/2) . "+" . ceil(($a4 - $a2)/2)
                    . " "
                    . escapeshellarg(realpath($t->target))
                    . " TIF:"
                    // ^ 
                    // GIF saving hack
                    . escapeshellarg(realpath($t->target))
                    );

            exec($cmd, $result, $errors);
            if ($errors) {
                return false;
            }

            $this->__destroy_target($tmp);
            $tmp->target = $t->target;

            $tmp->image_width = $b2;
            $tmp->image_height = $a2;
            return true;
        }
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

        // call `convert -crop`
        //
        $cmd = $this->__command(
                'convert',
                " -crop {$width}x{$height}" . ($x < 0 ? "-{$x}" : "+{$x}") .
                    ($y < 0 ? "-{$y}" : "+{$y}")
                . " "
                . escapeshellarg(realpath($tmp->target))
                . " "
                . escapeshellarg(realpath($tmp->target))
                );

        exec($cmd, $result, $errors);
        return ($errors == 0);
    }

    /**
     * Vertically mirror (flip) the image
     * 
     * @param Pitlib_Tmp $tmp
     * @return boolean
     * @access protected
     */
    protected function __flip(Pitlib_Tmp $tmp) {

        // call `convert -flip`
        //
        $cmd = $this->__command(
                'convert',
                " -flip "
                . escapeshellarg(realpath($tmp->target))
                . " "
                . escapeshellarg(realpath($tmp->target))
                );

        exec($cmd, $result, $errors);
        return ($errors == 0);
    }

    /**
     * Horizontally mirror (flop) the image
     * 
     * @param Pitlib_Image &$image
     * @return boolean
     * @access protected
     */
    protected function __flop(Pitlib_Tmp $tmp) {

        // call `convert -flop`
        //
        $cmd = $this->__command(
                'convert',
                " -flop "
                . escapeshellarg(realpath($tmp->target))
                . " "
                . escapeshellarg(realpath($tmp->target))
                );

        exec($cmd, $result, $errors);
        return ($errors == 0);
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

        list($r, $g, $b) = $color->get();

        $t = new Pitlib_Tmp;
        $t->target = $this->__tmpfile();
        $t->image_width = $width;
        $t->image_height = $height;


        // weird ... only works with absolute names
        //
        fclose(fopen($t->target, 'w'));

        // call `convert -fill`
        //
        $cmd = $this->__command(
                'convert',
                "-size {$width}x{$height} "
                . escapeshellarg("xc:rgb({$r},{$g},{$b})") . " PNG:"
                . escapeshellarg(realpath($t->target))
                );
        exec($cmd, $result, $errors);
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

        // weird ... only works with absolute names
        //
        //fclose(fopen($filename, 'w'));

        // call `convert`
        //
        $cmd = $this->__command(
                'convert',
                escapeshellarg(realpath($handler))
                . ' PNG:'
                // ^
                // PNG: no pixel losts
                . escapeshellarg($filename)
                );

        exec($cmd, $result, $errors);
        if ($errors) {
            return false;
        }

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

        $tmp->source = $this->__tmpfile();
        $tmp->target = $this->__tmpfile();

        // call `identify`
        //
        $cmd = $this->__command(
                'identify',
                '-format %w:%h:%m '
                . escapeshellarg(
                    realpath($tmp->source_filename)
                    )
                );

        // exec ?
        //
        exec($cmd, $result, $errors);
        if ($errors != 0) {
            return false;
        }

        // not supported ?
        //
        if (preg_match('~^'
            . preg_quote('identify: No decode delegate for this image format')
            . '~Uis', $result[0])) {
            return false;
        }

        // result is not what was expected
        //
        $data  = explode(':', $result[0]);
        if (count($data) < 3) {
            return false;
        }

        // supported ... obviously
        //
        $tmp->image_width = $data[0];
        $tmp->image_height = $data[1];


        // prepare target
        //
        $cmd = $this->__command(
                'convert',
                escapeshellarg(realpath($tmp->source_filename))
                . ' PNG:'
                . escapeshellarg($tmp->target)
                );

        exec($cmd, $result, $errors);
        if ($errors) {
            return false;
        }

        // prepare source
        //
        copy($tmp->target, $tmp->source);

        return true;
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

        // weird ... only works with absolute names
        //
        //fclose(fopen($tmp->target_filename, 'w'));

        if ($tmp->save) {

            // convert and save
            //
            $cmd = $this->__command(
                    'convert',
                    escapeshellarg(realpath($tmp->target))
                    . ' ' . $tmp->save . ':'
                    . escapeshellarg($tmp->target_filename)
                    );
        } else {

            // no "real" convert, just save
            //
            $cmd = $this->__command(
                    'convert',
                    escapeshellarg(realpath($tmp->target))
                    . " "
                    . escapeshellarg($tmp->target_filename)
                    );
        }

        exec($cmd, $result, $errors);

        // dispose
        //
        $this->__destroy_source($tmp);
        $this->__destroy_target($tmp);

        return ($errors == 0);
    }

    //--end-of-class--	
}

/////////////////////////////////////////////////////////////////////////////

?>
