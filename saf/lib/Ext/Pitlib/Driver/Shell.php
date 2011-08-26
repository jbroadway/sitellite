<?php
// vim: tabstop=4: expandtab: sts=4: ai: sw=4:

/**
 * @author Charles Brunet <cbrunet@php.net>
 * @author Kaloyan K. Tsvetkov <kaloyan@kaloyan.info>
 * @license http://opensource.org/licenses/lgpl-license.php
 *     GNU Lesser General Public License Version 2.1
 * @package Pitlib
 * @subpackage Pitlib.Driver
 * @version 0.1.0
 */

/////////////////////////////////////////////////////////////////////////////

/**
 * Set the PITLIB_NICE_LEVEL constant up w/ the nice level (for *nix systems)
 * for the priority of shell commands. The allowed values are 1 to 19, where 19
 * is the lowest priority. Providing 0 as nice level will disable calling nice
 * at all and the shell command will be called without using it.
 */
if (!defined('PITLIB_NICE_LEVEL')) {
    define('PITLIB_NICE_LEVEL', '0');
}

// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

/**
 * Set the PITLIB_START_PRIORITY constant up w/ the process priority level (for 
 * windows-based systems). The allowed values are 'LOW', 'NORMAL', 'HIGH', 
 * 'REALTIME', 'ABOVENORMAL' and 'BELOWNORMAL'. Any other value will disable
 * this feature and the shell command will be called without using this feature.
 */
if (!defined('PITLIB_START_PRIORITY')) {
    define('PITLIB_START_PRIORITY', 'NORMAL');
}

/////////////////////////////////////////////////////////////////////////////

/**
 * Common file for all "shell" based solutions 
 *
 * @package Pitlib
 * @subpackage Pitlib.Driver
 *
 * @abstract
 */
abstract class Pitlib_Driver_Shell Extends Pitlib_Driver {

    /**
     * Path to the executables
     * @var string
     * @access private
     */
    private $__exec = '';

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Try to locate the program
     * @param string $program
     * @return string
     *
     * @access protected
     */
    protected function __exec($program) {

        // safe mode ?
        //
        if (!ini_get('safe_mode') || !$path = ini_get('safe_mode_exec_dir')) {
            ($path = getenv('PATH')) || ($path = getenv('Path'));
        }

        $executable = false;
        $p = explode(PATH_SEPARATOR, $path);
        $p[] = getcwd();

        $ext = array();		
        if (OS_WINDOWS) {
            $ext = getenv('PATHEXT')
                ? explode(PATH_SEPARATOR, getenv('PATHEXT'))
                : array('.exe','.bat','.cmd','.com');

            // extension ?
            //
            array_unshift($ext, '');
        }

        // walk the variants
        //
        foreach ($ext as $e) {
            foreach ($p as $dir) {
                $exe = $dir . DIRECTORY_SEPARATOR . $program . $e;

                // *nix only implementation
                //
                if (OS_WINDOWS ? is_file($exe) : is_executable($exe)) {
                    $executable = $exe;
                    break;
                }
            }
        }

        return $executable;
    }

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    /**
     * Run a command
     * @param string $program
     * @param string $args
     * @return string
     * @access protected
     */
    protected function __command($program, $args = '') {

        $priority_prefix = '';

        if (OS_WINDOWS) {

            // Windows systems
            //
            $allowed_priorities = array(
                    'LOW', 'NORMAL', 'HIGH', 'REALTIME', 'ABOVENORMAL',
                    'BELOWNORMAL'
                    );
            $start_priority = strToUpper(PITLIB_START_PRIORITY);
            if (in_array($start_priority, $allowed_priorities)) {
                $priority_prefix = "start /B /{$start_priority} ";
            }

        } else {

            // *Nix system
            //
            $nice_level = intval(PITLIB_NICE_LEVEL);
            if($nice_level <= 19 && $nice_level > 0) {
                $priority_prefix = "nice -$nice_level ";
            }
        }

        return $priority_prefix . $this->__exec . $program . ' ' . $args;
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
        return @unlink($tmp->source);
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
        return @unlink($tmp->target);
    }

    // -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- 

    //--end-of-class--	
}

/////////////////////////////////////////////////////////////////////////////

?>
