<?php
// vim: tabstop=4: expandtab: sts=4: ai: sw=4:

/**
 * @author Charles Brunet <cbrunet@php.net>
 * @license http://opensource.org/licenses/lgpl-license.php
 *    GNU Lesser General public License Version 2.1
 * @package Pitlib
 * @subpackage Pitlib.Driver
 * @version 0.1.0
 *
 * @todo Support newer versions of netpbm
 * @todo Detect NetPBM version
 */

/////////////////////////////////////////////////////////////////////////////

/**
 * @see Pitlib_Driver_Shell
 */
require_once PITLIB_DIR . "Driver/Shell.php";

/////////////////////////////////////////////////////////////////////////////

/**
 * This is the path to where the NetPBM executables are
 */
if (!defined('PITLIB_NETPBM_SHELL_PATH')) {
    define('PITLIB_NETPBM_SHELL_PATH', '');
}

/////////////////////////////////////////////////////////////////////////////

/**
 * Pitlib "NetPBM" driver (via shell)
 *
 * @package Pitlib
 * @subpackage Pitlib.Driver
 */
class Pitlib_Driver_Netpbm extends Pitlib_Driver_Shell {

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Maps to supported images types for reading files
     * @var array
     */
    protected $__read = array (
            //Pitlib_Type::BMP,
            //Pitlib_Type::FAX,
            Pitlib_Type::FITS,
            Pitlib_Type::GIF,
            Pitlib_Type::JPEG,
            Pitlib_Type::ICO,
            Pitlib_Type::MTV,
            Pitlib_Type::P7,
            Pitlib_Type::PALM,
            Pitlib_Type::PBM,
            Pitlib_Type::PCD,
            Pitlib_Type::PCX,
            Pitlib_Type::PGM,
//            Pitlib_Type::PICT,
            Pitlib_Type::PNG,
            //Pitlib_Type::RLA,
//            Pitlib_Type::TGA,
//            Pitlib_Type::TIFF,
//            Pitlib_Type::XBM,
//            Pitlib_Type::XPM,
            );

    /**
     * Maps to supported images types for writing files
     * @var array
     */
    protected $__write = array (
            Pitlib_Type::BMP,
            Pitlib_Type::DJVU,
            Pitlib_Type::FAX,
            Pitlib_Type::FITS,
            Pitlib_Type::GIF,
            Pitlib_Type::JPEG,
            Pitlib_Type::ICO,
//            Pitlib_Type::P7,
//            Pitlib_Type::PALM,
//            Pitlib_Type::PBM,
            Pitlib_Type::PCX,
//            Pitlib_Type::PGM,
            Pitlib_Type::PICT,
            Pitlib_Type::PNG,
            Pitlib_Type::RLA,
            Pitlib_Type::SVG,
            Pitlib_Type::TGA,
            Pitlib_Type::TIFF,
            Pitlib_Type::XBM,
            Pitlib_Type::XPM,
            );

    /**
     * Maps to program files to read images
     * @var array
     */
    protected $__readconv = array (
            Pitlib_Type::BMP  => 'bmptopnm',
			Pitlib_Type::FAX  => 'g3topbm',
			Pitlib_Type::FITS => 'fitstopnm',
            Pitlib_Type::GIF  => 'giftopnm',
			Pitlib_Type::JPEG => 'jpegtopnm',
			Pitlib_Type::ICO  => 'winicontoppm', //'winicontopnm',
			Pitlib_Type::MTV  => 'mtvtoppm',
			Pitlib_Type::P7   => 'xvminitoppm',
			Pitlib_Type::PALM => 'palmtopnm',
			Pitlib_Type::PBM  => '',
			Pitlib_Type::PCD  => 'hpcdtoppm',
            Pitlib_Type::PCX  => 'pcxtoppm',
			Pitlib_Type::PGM  => '',
			Pitlib_Type::PICT => 'picttoppm',
			Pitlib_Type::PNG  => 'pngtopnm',
			Pitlib_Type::RLA  => 'rlatopam',
			Pitlib_Type::TGA  => 'tgatoppm',
			Pitlib_Type::TIFF => 'tifftopnm',
			Pitlib_Type::XPM  => 'xpmtoppm',
			Pitlib_Type::XBM  => 'xbmtopbm',
            );

    /**
     * Maps to program files to write images
     * @var array
     */
    protected $__writeconv = array (
            Pitlib_Type::BMP  => 'ppmtobmp',
			Pitlib_Type::DJVU => 'pamtodjvurle',
			Pitlib_Type::FAX  => 'pbmtog3',
			Pitlib_Type::FITS => 'pnmtofits', //'pamtofits',
            Pitlib_Type::GIF  => 'ppmtogif', // 'pamtogif',
			Pitlib_Type::JPEG => 'pnmtojpeg',
			Pitlib_Type::ICO  => 'ppmtowinicon',
			Pitlib_Type::P7   => 'pamtoxvmini',
			Pitlib_Type::PALM => 'pnmtopalm',
			Pitlib_Type::PBM  => 'pgmtoppm', // 'pamditherbw',
            Pitlib_Type::PCX  => 'ppmtopcx',
			Pitlib_Type::PGM  => 'ppmtopgm',
			Pitlib_Type::PICT => 'ppmtopict',
			Pitlib_Type::PNG  => 'pnmtopng',
            Pitlib_Type::SVG  => 'pamtosvg',
			Pitlib_Type::TGA  => 'pamtotga',
			Pitlib_Type::TIFF => 'pamtotiff',
			Pitlib_Type::XPM  => 'ppmtoxpm',
			Pitlib_Type::XBM  => 'pbmtoxbm',
            );

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Constructor
     */
    public function __construct() {
        // executable 
        //
        if (PITLIB_NETPBM_SHELL_PATH) {
            $this->__exec = PITLIB_NETPBM_SHELL_PATH;
        } else {
            $this->__exec = dirname($this->__exec('ppmmake')) .
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
    public function is_compatible() {

        if (!$this->__exec) {
            throw new Pitlib_Exception (
                    'The Pitlib_Driver_NetPBM_Shell driver is '
                    . ' unable to be initialized, because '
                    . ' the NetPBM executables '
                    . ' were not found. Please locate '
                    . ' where those files are and set the '
                    . ' path to them by defining the '
                    . ' PITLIB_NETPBM_SHELL_PATH constant.'
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
        return 'netpbm';
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

        @$this->__destroy_source($tmp);
        $tmp->source = $tmp->target;
        $tmp->target = $this->__tmpfile();

        // call `pamscale`
        //
        $cmd = $this->__command(
                'pnmscale',
                "-w {$width} -h {$height} -quiet "
                . escapeshellarg(realpath($tmp->source))
                . " > "
                . escapeshellarg($tmp->target)
                );

        exec($cmd, $result, $errors);
        if ($errors) {
            return false;
        }
        if (file_exists (realpath($tmp->source).'.alpha')) {
            $cmd = $this->__command(
                    'pnmscale',
                    "-w {$width} -h {$height} -quiet "
                    . escapeshellarg(realpath($tmp->source).'.alpha')
                    . " > "
                    . escapeshellarg($tmp->target.'.alpha')
                    );

            exec($cmd, $result, $errors);
        }

        @$this->__destroy_source($tmp);
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
    protected function __copy(Pitlib_Tmp $tmp_target, Pitlib_Tmp $tmp_source,
            $destination_x, $destination_y) {

        @$this->__destroy_source($tmp_target);
        $tmp_target->source = $tmp_target->target;
        $tmp_target->target = $this->__tmpfile();

        $destination_x = intval ($destination_x);
        $destination_y = intval ($destination_y);

        // Tenir compte de transparence
        if (file_exists ($tmp_source->source.'.alpha')) {
            $alpha = '-alpha='.$tmp_source->source.'.alpha';
        }
        else {
            $alpha = '';
        }

        // call `pamcomp`
        //
        $cmd = $this->__command(
                'pnmcomp',
                " -x {$destination_x} -y {$destination_y} {$alpha} -quiet "
                . escapeshellarg(realpath($tmp_source->source))
                . " "
                . escapeshellarg(realpath($tmp_target->source))
                . " > "
                . escapeshellarg($tmp_target->target)
                );
        exec($cmd, $result, $errors);
        return ($errors == 0);
    }

    /**
     * Make the image greyscale
     *
     * @param Pitlib_Tmp $tmp
     * @return boolean
     * @access protected
     */
    protected function __grayscale(Pitlib_Tmp $tmp) {

        @$this->__destroy_source($tmp);
        $tmp->source = $tmp->target;
        $tmp->target = $this->__tmpfile();

        // call `ppmtopgm`
        //
        $cmd = $this->__command(
                'ppmtopgm',
                "-quiet "
                . escapeshellarg(realpath($tmp->source))
                . " > "
                . escapeshellarg($tmp->target)
                );
        exec($cmd, $result, $errors);
        return ($errors == 0);
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

        if ($angle >= 360) {
            $angle = 360 - ($angle % 360);
        }
        else {
            $angle = (360 - $angle) % 360;
        }

        if ($angle == 360) {
            return true;
        }
        else if ($angle >= 270) {
            $flip = '-r270';
            $angle -= 270;
        }
        else if ($angle >= 180) {
            $flip = '-r180';
            $angle -= 180;
        }
        else if ($angle >= 90) {
            $flip = '-r90';
            $angle -= 90;
        }
        else {
            $flip = null;
        }

        if ($flip) {
            @$this->__destroy_source($tmp);
            $tmp->source = $tmp->target;
            $tmp->target = $this->__tmpfile();

            // call `pnmflip`
            //
            $cmd = $this->__command(
                    'pnmflip',
                    " -quiet {$flip} "
                    . escapeshellarg(realpath($tmp->source))
                    . " > "
                    . escapeshellarg($tmp->target)
                    );
            exec($cmd, $result, $errors);
            if ($errors) {
                return false;
            }
        }

        // ajouter option background

        if ($angle) {
            @$this->__destroy_source($tmp);
            $tmp->source = $tmp->target;
            $tmp->target = $this->__tmpfile();

            // call `pnmrotate`
            //
            $cmd = $this->__command(
                    'pnmrotate',
                    " -quiet {$angle} "
                    . escapeshellarg(realpath($tmp->source))
                    . " > "
                    . escapeshellarg($tmp->target)
                    );
            exec($cmd, $result, $errors);
            if ($errors) {
                return false;
            }
        }

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

        @$this->__destroy_source($tmp);
        $tmp->source = $tmp->target;
        $tmp->target = $this->__tmpfile();

        // call `pamcut`
        //
        $cmd = $this->__command(
                'pamcut',
                " -w {$width} -h {$height} -l {$x} -t {$y} -quiet"
                . " "
                . escapeshellarg(realpath($tmp->source))
                . " > "
                . escapeshellarg($tmp->target)
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

        @$this->__destroy_source($tmp);
        $tmp->source = $tmp->target;
        $tmp->target = $this->__tmpfile();

        // call `pamflip`
        //
        $cmd = $this->__command(
                'pnmflip',
                " -tb -quiet "
                . escapeshellarg(realpath($tmp->source))
                . " > "
                . escapeshellarg($tmp->target)
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

        @$this->__destroy_source($tmp);
        $tmp->source = $tmp->target;
        $tmp->target = $this->__tmpfile();

        // call `pamflip`
        //
        $cmd = $this->__command(
                'pnmflip',
                " -lr -quiet "
                . escapeshellarg(realpath($tmp->source))
                . " > "
                . escapeshellarg($tmp->target)
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

        $color = 'rgb:'.dechex($r).'/'.dechex($g).'/'.dechex($b);

        // call `ppmmake`
        //
        $cmd = $this->__command(
                'ppmmake',
                " -quiet {$color} {$width} {$height} " 
                . " > "
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
        fclose(fopen($filename, 'w'));

        copy ($handler, $filename);
        if (file_exists ($handler.'.alpha')) {
            copy ($handler.'.alpha', $filename.'.alpha');
        }

        // call `convert`
        //
        /*
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
         */
        return $this->prepare(
                new Pitlib_Image($filename)
                );
    }

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    protected function __pamfile($filename) {

        $cmd = $this->__command(
                'pamfile',
                escapeshellarg($filename));
        exec($cmd, $result, $errors);

        $r = preg_match ('/(.*):\s+(P.M) (.*), ([0-9]+) by ([0-9]+)  '.
                'maxval ([0-9]+)/', $result[0], $data);
        if ($r) {
            return $data;
        }
        else {
            return false;
        }
    }

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

        $type = Pitlib_Type::from_filename ($tmp->source_filename);
        if (!$type) {
            $conv = '';
        }
        else if (array_key_exists ($type, $this->__readconv)) {
            $conv = $this->__readconv[$type];
        }

        if ($conv == '') {
            copy ( realpath($tmp->source_filename), $tmp->target);
            if (file_exists (realpath($tmp->source_filename).'.alpha')) {
                copy ( realpath($tmp->source_filename).'.alpha',
                        $tmp->target.'.alpha');
            }
        }
        else {
            $options = '';
            if ($conv == 'giftopnm') {
                $options .= '--alphaout='.escapeshellarg($tmp->target.'.alpha');
            }

            if (!$conv) {
                throw new Pitlib_Exception ('Netpbm converter not found for file '.
                        $tmp->source_filename);
            }

            // prepare target
            //
            $cmd = $this->__command(
                    $conv,
                    "-quiet {$options} "
                    . escapeshellarg(realpath($tmp->source_filename))
                    . ' > '
                    . escapeshellarg($tmp->target)
                    );

            exec($cmd, $result, $errors);
            if ($errors) {
                return false;
            }

            if ($conv == 'pngtopnm') {
                $cmd = $this->__command(
                        $conv,
                        "-quiet -alpha "
                        . escapeshellarg(realpath($tmp->source_filename))
                        . ' > '
                        . escapeshellarg($tmp->target.'.alpha')
                        );

                exec($cmd, $result, $errors);
                if ($errors) {
                    return false;
                }
            }
        }

        $data = $this->__pamfile ($tmp->target);
        if ($data !== false) {
            $tmp->image_width = $data[4];
            $tmp->image_height = $data[5];
        }
        else {
            return false;
        }

        // prepare source
        //
        copy($tmp->target, $tmp->source);
        if (file_exists ($tmp->target.'.alpha')) {
            copy($tmp->target.'.alpha', $tmp->source.'.alpha');
        }

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

        // weird ... only works with absolute names
        //
        fclose(fopen($tmp->target_filename, 'w'));

        if (!$tmp->save) {
            $tmp->save = Pitlib_Type::from_filename ($tmp->target_filename);
        }

        $conv = $this->__writeconv[$tmp->save];

        if (!$conv) {
            throw new Pitlib_Exception ('NetPBM converter not found for file '.
                $tmp->target_filename);
        }

        switch ($conv) {
            case 'ppmtogif':
                @$this->__destroy_source($tmp);
                $tmp->source = $tmp->target;
                $tmp->target = $this->__tmpfile();
                $cmd = $this->__command(
                        'ppmquant',
                        '256 -quiet '
                        . escapeshellarg($tmp->source)
                        . ' > '
                        . escapeshellarg($tmp->target)
                        );
                @exec($cmd, $result, $errors);
                break;
        }

        // convert and save
        //
        $cmd = $this->__command(
                $conv,
                "-quiet "
                . escapeshellarg(realpath($tmp->target))
                . ' > '
                . escapeshellarg($tmp->target_filename)
                );

        @exec($cmd, $result, $errors);

        // dispose
        //
        @$this->__destroy_source($tmp);
        @$this->__destroy_target($tmp);

        return ($errors == 0);
    }

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    protected function __destroy_source (Pitlib_Tmp $tmp) {
        @unlink ($tmp->source.'.alpha');
        return @unlink ($tmp->source);
    }

    protected function __destroy_target (Pitlib_Tmp $tmp) {
        @unlink ($tmp->target.'.alpha');
        return @unlink ($tmp->target);
    }


    //--end-of-class--	
}

/////////////////////////////////////////////////////////////////////////////

?>
