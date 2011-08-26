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
 * Pitlib Temporary Object
 *
 * @package Pitlib
 * @subpackage Pitlib.Core
 */
class Pitlib_Tmp {

    /**
     * Object for processing the source image
     * @var mixed
     * @access public
     */
    public $source;

    /**
     * Source image filename
     * @var string
     * @access public
     */
    public $source_filename;

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Object for processing the target image
     * @var mixed
     * @access public
     */
    public $target;

    /**
     * Filename with which to save the processed file
     * @var string
     * @access public
     */
    public $target_filename;

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Image width
     * @var integer
     * @access public
     */
    public $image_width;

    /**
     * Image height
     * @var integer
     * @access public
     */
    public $image_height;

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Image type with which to save the processed file
     * @var string
     * @access public
     */
    public $save;


    /**
     * Target quality
     * @var integer
     * @access public
     */
    public $quality;

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    //--end-of-class--	
}

/////////////////////////////////////////////////////////////////////////////

?>
