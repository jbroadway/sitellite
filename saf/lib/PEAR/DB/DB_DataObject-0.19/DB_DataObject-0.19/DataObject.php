<?php
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Author:  Alan Knowles <alan@akbkhome.com>
// +----------------------------------------------------------------------+
/**
 * Object Based Database Query Builder and data store
 *
 * @package  DB_DataObject
 * @category DB
 *
 * $Id: DataObject.php,v 1.1.1.1 2005/04/29 04:44:36 lux Exp $
 */

/**
 * Needed classes
 */
require_once 'DB.php';
require_once 'PEAR.php';

/**
 * these are constants for the get_table array
 * user to determine what type of escaping is required around the object vars.
 */
define('DB_DATAOBJECT_INT',  1);  // does not require ''
define('DB_DATAOBJECT_STR',  2);  // requires ''
define('DB_DATAOBJECT_DATE', 4);  // is date #TODO
define('DB_DATAOBJECT_TIME', 8);  // is time #TODO
define('DB_DATAOBJECT_BOOL', 16); // is boolean #TODO
define('DB_DATAOBJECT_TXT',  32); // is long text #TODO

/**
 * Theses are the standard error codes, most methods will fail silently - and return false
 * to access the error message either use $table->_lastError
 * or $last_error = PEAR::getStaticProperty('DB_DataObject','lastError');
 * the code is $last_error->code, and the message is $last_error->message (a standard PEAR error)
 */

define('DB_DATAOBJECT_ERROR_INVALIDARGS',   -1);  // wrong args to function
define('DB_DATAOBJECT_ERROR_NODATA',        -2);  // no data available
define('DB_DATAOBJECT_ERROR_INVALIDCONFIG', -3);  // something wrong with the config
define('DB_DATAOBJECT_ERROR_NOCLASS',       -4);  // no class exists
define('DB_DATAOBJECT_ERROR_NOAFFECTEDROWS',-5);  // no rows where affected by update/insert/delete
define('DB_DATAOBJECT_ERROR_NOTSUPPORTED'  ,-6);  // limit queries on unsuppored databases
define('DB_DATAOBJECT_ERROR_INVALID_CALL'  ,-7);  // overlad getter/setter failure

/**
 * Used in methods like delete() and count() to specify that the method should
 * build the condition only out of the whereAdd's and not the object parameters.
 */
define('DB_DATAOBJECT_WHEREADD_ONLY', true);

/**
 *
 * storage for connection and result objects,
 * it is done this way so that print_r()'ing the is smaller, and
 * it reduces the memory size of the object.
 * -- future versions may use $this->_connection = & PEAR object..
 *   although will need speed tests to see how this affects it.
 * - includes sub arrays
 *   - connections = md5 sum mapp to pear db object
 *   - results     = [id] => map to pear db object
 *   - ini         = mapping of database to ini file results
 *   - links       = mapping of database to links file
 *   - lasterror   = pear error objects for last error event.
 *   - config      = aliased view of PEAR::getStaticPropery('DB_DataObject','options') * done for performance.
 *   - array of loaded classes by autoload method - to stop it doing file access request over and over again!
 */
$GLOBALS['_DB_DATAOBJECT']['RESULTS'] = array();
$GLOBALS['_DB_DATAOBJECT']['CONNECTIONS'] = array();
$GLOBALS['_DB_DATAOBJECT']['INI'] = array();
$GLOBALS['_DB_DATAOBJECT']['LINKS'] = array();
$GLOBALS['_DB_DATAOBJECT']['LASTERROR'] = null;
$GLOBALS['_DB_DATAOBJECT']['CONFIG'] = array();
$GLOBALS['_DB_DATAOBJECT']['CACHE'] = array();
$GLOBALS['_DB_DATAOBJECT']['LOADED'] = array();
$GLOBALS['_DB_DATAOBJECT']['OVERLOADED'] = false;
/**
 * The main "DB_DataObject" class is really a base class for your own tables classes
 *
 * // Set up the class by creating an ini file (refer to the manual for more details
 * [DB_DataObject]
 * database         = mysql:/username:password@host/database
 * schema_location = /home/myapplication/database
 * class_location  = /home/myapplication/DBTables/
 * clase_prefix    = DBTables_
 *
 *
 * //Start and initialize...................... - dont forget the &
 * $config = parse_ini_file('example.ini',true);
 * $options = &PEAR::setStaticProperty('DB_DataObject','options');
 * $options = $config['DB_DataObject'];
 *
 * // example of a class (that does not use the 'auto generated tables data')
 * class mytable extends DB_DataObject {
 *     // mandatory - set the table
 *     var $_database_dsn = "mysql://username:password@localhost/database";
 *     var $__table = "mytable";
 *     function _get_table() {
 *         return array(
 *             'id' => 1, // integer or number
 *             'name' => 2, // string
 *        );
 *     }
 *     function _get_keys() {
 *         return array('id');
 *     }
 * }
 *
 * // use in the application
 *
 *
 * Simple get one row
 *
 * $instance = new mytable;
 * $instance->get("id",12);
 * echo $instance->somedata;
 *
 *
 * Get multiple rows
 *
 * $instance = new mytable;
 * $instance->whereAdd("ID > 12");
 * $instance->whereAdd("ID < 14");
 * $instance->find();
 * while ($instance->fetch()) {
 *     echo $instance->somedata;
 * }
 *
 * @package  DB_DataObject
 * @author   Alan Knowles <alan@akbkhome.com>
 * @since    PHP 4.0
 */
Class DB_DataObject
{
   /**
    * The Version - use this to check feature changes
    *
    * @access   private
    * @var      string
    */
    var $_DB_DataObject_version = "1.0";

    /**
     * The Database table (used by table extends)
     *
     * @access  private
     * @var     string
     */
    var $__table = '';  // database table

    /**
     * The Number of rows returned from a query
     *
     * @access  public
     * @var     int
     */
    var $N = 0;  // Number of rows returned from a query


    /* ============================================================= */
    /*                      Major Public Methods                     */
    /* (designed to be optionally then called with parent::method()) */
    /* ============================================================= */


    /**
     * Get a result using key, value.
     *
     * for example
     * $object->get("ID",1234);
     * Returns Number of rows located (usually 1) for success,
     * and puts all the table columns into this classes variables
     *
     * see the fetch example on how to extend this.
     *
     * if no value is entered, it is assumed that $key is a value
     * and get will then use the first key in _get_keys
     * to obtain the key.
     *
     * @param   string  $k column
     * @param   string  $v value
     * @access  public
     * @return  int     No. of rows
     */
    function get($k = NULL, $v = NULL)
    {
        global $_DB_DATAOBJECT;
        if (empty($_DB_DATAOBJECT['CONFIG'])) {
            DB_DataObject::_loadConfig();
        }

        if ($v === NULL) {
            $v = $k;
            $keys = $this->_get_keys();
            if (!$keys) {
                DB_DataObject::raiseError("No Keys available for {$this->__table}", DB_DATAOBJECT_ERROR_INVALIDCONFIG);
                return false;
            }
            $k = $keys[0];
        }
        if (@$_DB_DATAOBJECT['CONFIG']['debug']) {
            $this->debug("$k $v " .serialize(@$keys), "GET");
        }
        if ($v === NULL) {
            DB_DataObject::raiseError("No Value specified for get", DB_DATAOBJECT_ERROR_INVALIDARGS);
            return false;
        }
        $this->$k = $v;
        return $this->find(1);
    }

    /**
     * An autoloading, caching static get method  using key, value (based on get)
     *
     * Usage:
     * $object = DB_DataObject::staticGet("DbTable_mytable",12);
     * or
     * $object =  DB_DataObject::staticGet("DbTable_mytable","name","fred");
     *
     * or write it into your extended class:
     * function &staticGet($k,$v=NULL) { return DB_DataObject::staticGet("This_Class",$k,$v);  }
     *
     * @param   string  $class class name
     * @param   string  $k     column (or value if using _get_keys)
     * @param   string  $v     value (optional)
     * @access  public
     * @return  object
     */
    function &staticGet($class, $k, $v = NULL)
    {

        global $_DB_DATAOBJECT;
        if (empty($_DB_DATAOBJECT['CONFIG'])) {
            DB_DataObject::_loadConfig();
        }

        

        $key = "$k:$v";
        if ($v === NULL) {
            $key = $k;
        }
        if (@$_DB_DATAOBJECT['CONFIG']['debug']) {
            DB_DataObject::debug("$class $key","STATIC GET");
        }
        if (@$_DB_DATAOBJECT['CACHE'][$class][$key]) {
            return $_DB_DATAOBJECT['CACHE'][$class][$key];
        }
        if (@$_DB_DATAOBJECT['CONFIG']['debug']) {
            DB_DataObject::debug("$class $key","STATIC GET");
        }

        $newclass = DB_DataObject::_autoloadClass($class);
        if (!$newclass) {
            DB_DataObject::raiseError("could not autoload $class", DB_DATAOBJECT_ERROR_NOCLASS);
            return false;
        }
        $obj = &new $newclass;
        if (!$obj) {
            DB_DataObject::raiseError("Error creating $newclass", DB_DATAOBJECT_ERROR_NOCLASS);
            return false;
        }

        if (!@$_DB_DATAOBJECT['CACHE'][$class]) {
            $_DB_DATAOBJECT['CACHE'][$class] = array();
        }
        if (!$obj->get($k,$v)) {
            DB_DataObject::raiseError("No Data return from get $k $v", DB_DATAOBJECT_ERROR_NODATA);
            return false;
        }
        $_DB_DATAOBJECT['CACHE'][$class][$key] = $obj;
        return $_DB_DATAOBJECT['CACHE'][$class][$key];
    }

    /**
     * find results, either normal or crosstable
     *
     * for example
     *
     * $object = new mytable();
     * $object->ID = 1;
     * $object->find();
     *
     *
     * will set $object->N to number of rows, and expects next command to fetch rows
     * will return $object->N
     *
     * @param   boolean $n Fetch first result
     * @access  public
     * @return  int
     */
    function find($n = false)
    {
        global $_DB_DATAOBJECT;
        if (empty($_DB_DATAOBJECT['CONFIG'])) {
            DB_DataObject::_loadConfig();
        }

        if (@$_DB_DATAOBJECT['CONFIG']['debug']) {
            $this->debug($n, "__find",1);
        }
        if (!$this->__table) {
            echo "NO \$__table SPECIFIED in class definition";
            exit;
        }
        $this->N = 0;
        $tmpcond = $this->_condition;
        $this->_build_condition($this->_get_table()) ;
        $this->_query('SELECT ' .
            $this->_data_select .
            ' FROM ' . $this->__table . " " .
            $this->_join .
            $this->_condition . ' '.
            $this->_group_by . ' '.
            $this->_order_by . ' '.
            $this->_limit); // is select
        ////add by ming ... keep old condition .. so that find can reuse
        $this->_condition = $tmpcond;
        if (@$_DB_DATAOBJECT['CONFIG']['debug']) {
            $this->debug("CHECK autofetchd $n", "__find", 1);
        }
        if ($n && $this->N > 0 ) {
             if (@$_DB_DATAOBJECT['CONFIG']['debug']) {
                $this->debug("ABOUT TO AUTOFETCH", "__find", 1);
            }
            $this->fetch() ;
        }
        if (@$_DB_DATAOBJECT['CONFIG']['debug']) {
            $this->debug("DONE", "__find", 1);
        }
        return $this->N;
    }

    /**
     * fetches next row into this objects var's
     *
     * returns 1 on success 0 on failure
     *
     *
     *
     * Example
     * $object = new mytable();
     * $object->name = "fred";
     * $object->find();
     * $store = array();
     * while ($object->fetch()) {
     *   echo $this->ID;
     *   $store[] = $object; // builds an array of object lines.
     * }
     *
     * to add features to a fetch
     * function fetch () {
     *    $ret = parent::fetch();
     *    $this->date_formated = date('dmY',$this->date);
     *    return $ret;
     * }
     *
     * @access  public
     * @return  boolean on success
     */
    function fetch()
    {

        global $_DB_DATAOBJECT;
        if (empty($_DB_DATAOBJECT['CONFIG'])) {
            DB_DataObject::_loadConfig();
        }
        if (!@$this->N) {
            DB_DataObject::raiseError("fetch: No Data Available", DB_DATAOBJECT_ERROR_NODATA);
            return false;
        }
        $result = &$_DB_DATAOBJECT['RESULTS'][$this->_DB_resultid];
        $array = $result->fetchRow(DB_FETCHMODE_ASSOC);
        if (@$_DB_DATAOBJECT['CONFIG']['debug']) {
            $this->debug(serialize($array),"FETCH", 3);
        }

        if (!is_array($array)) {
            // this is probably end of data!!
            //DB_DataObject::raiseError("fetch: no data returned", DB_DATAOBJECT_ERROR_NODATA);
            return false;
        }

        foreach($array as $k=>$v) {
            $kk = str_replace(".", "_", $k);
            $kk = str_replace(" ", "_", $kk);
             if (@$_DB_DATAOBJECT['CONFIG']['debug']) {
                $this->debug("$kk = ". $array[$k], "fetchrow LINE", 3);
            }
            $this->$kk = $array[$k];
        }

        // set link flag
        $this->_link_loaded=false;
        if (@$_DB_DATAOBJECT['CONFIG']['debug']) {
            $this->debug("{$this->__table} DONE", "fetchrow");
        }

        return true;
    }

    /**
     * Adds a condition to the WHERE statement, defaults to AND
     *
     * $object->whereAdd(); //reset or cleaer ewhwer
     * $object->whereAdd("ID > 20");
     * $object->whereAdd("age > 20","OR");
     *
     * @param    string  $cond  condition
     * @param    string  $logic optional logic "OR" (defaults to "AND")
     * @access   public
     * @return   none
     */
    function whereAdd($cond = false, $logic = 'AND')
    {
        if ($cond === false) {
            $this->_condition = '';
            return;
        }
        if ($this->_condition) {
            $this->_condition .= " {$logic} {$cond}";
            return;
        }
        $this->_condition = " WHERE {$cond}";
    }

    /**
     * Adds a order by condition
     *
     * $object->orderBy(); //clears order by
     * $object->orderBy("ID");
     * $object->orderBy("ID,age");
     *
     * @param  string $order  Order
     * @access public
     * @return void
     */
    function orderBy($order = false)
    {
        if ($order === false) {
            $this->_order_by = '';
            return;
        }
        if (!$this->_order_by) {
            $this->_order_by = " ORDER BY {$order} ";
            return;
        }
        $this->_order_by .= " , {$order}";
    }

    /**
     * Adds a group by condition
     *
     * $object->groupBy(); //reset the grouping
     * $object->groupBy("ID DESC");
     * $object->groupBy("ID,age");
     *
     * @param  string  $group  Grouping
     * @access public
     * @return  none
     */
    function groupBy($group = false)
    {
        if ($group === false) {
            $this->_group_by = '';
            return;
        }
        if (!$this->_group_by) {
            $this->_group_by = " GROUP BY {$group} ";
            return;
        }
        $this->_group_by .= " , {$group}";
    }

    /**
     * Sets the Limit
     *
     * $boject->limit(); // clear limit
     * $object->limit(12);
     * $object->limit(12,10);
     *
     * Note this will emit an error on databases other than mysql/postgress
     * as there is no 'clean way' to implement it. - you should consider refering to
     * your database manual to decide how you want to implement it.
     *
     * @param  string $a  limit start (or number), or blank to reset
     * @param  string $b  number
     * @access public
     * @return void
     */
    function limit($a = null, $b = null)
    {
        if ($a === NULL) {
           $this->_limit = '';
           return;
        }


        $db = $this->getDatabaseConnection();

        if (($db->features['limit'] == 'alter') && ($db->phptype != 'oci8')) {
            if ($b === NULL) {
               $this->_limit = " LIMIT $a";
               return;
            }
             
            $this->_limit = $db->modifyLimitQuery('',$a,$b);
            
        } else {
            DB_DataObject::raiseError(
                "DB_DataObjects only supports mysql and postgres limit queries at present, \n".
                "Refer to your Database manual to find out how to do limit queries manually.\n",
                DB_DATAOBJECT_ERROR_NOTSUPPORTED, PEAR_ERROR_DIE);
        }
    }

    /**
     * Adds a select columns
     *
     * $object->selectAdd(); // resets select to nothing!
     * $object->selectAdd("*"); // default select
     * $object->selectAdd("unixtime(DATE) as udate");
     * $object->selectAdd("DATE");
     *
     * @param  string  $k
     * @access public
     * @return void
     */
    function selectAdd($k = null)
    {
        if ($k === NULL) {
            $this->_data_select = '';
            return;
        }
        if ($this->_data_select)
            $this->_data_select .= ', ';
        $this->_data_select .= " $k ";
    }
    /**
     * Adds multiple Columns or objects to select with formating.
     *
     * $object->selectAs(null); // adds "table.colnameA as colnameA,table.colnameB as colnameB,......"
     *                      // note with null it will also clear the '*' default select
     * $object->selectAs(array('a','b'),'%s_x'); // adds "a as a_x, b as b_x"
     * $object->selectAdd($object,'prefix_%s'); // calls $object->get_table and adds it all as
     *                  objectTableName.colnameA as prefix_colnameA
     *
     * @param  array|object|null the array or object to take column names from.
     * @param  string           format in sprintf format (use %s for the colname)
     * @param  string           table name eg. if you have joinAdd'd or send $from as an array.
     * @access public
     * @return void
     */
    function selectAs($from = null,$format = '%s',$tableName=false)
    {
        if ($from === null) {
            // blank the '*' 
            $this->selectAdd();
            $from = $this;
        }
        
        
        $table = $this->__table;
        if (is_object($from)) {
            $table = $from->__table;
            if ($tableName !== false) {
                $table = $tableName;
            }
            $from = array_keys($from->_get_table());
        }
        foreach ($from as $k) {
            $this->selectAdd(sprintf("%s.%s as {$format}",$table,$k,$k));
        }
    }
    /**
     * Insert the current objects variables into the database
     *
     * Returns the ID of the inserted element - mysql specific = fixme?
     *
     * for example
     *
     * Designed to be extended
     *
     * $object = new mytable();
     * $object->name = "fred";
     * echo $object->insert();
     *
     * @access public
     * @return  mixed|false key value or false on failure
     */
    function insert()
    {
        global $_DB_DATAOBJECT;
        // connect will load the config!
        $this->_connect();

        $__DB  = &$_DB_DATAOBJECT['CONNECTIONS'][$this->_database_dsn_md5];
        $items = $this->_get_table();
        if (!$items) {
            DB_DataObject::raiseError("insert:No table definition for {$this->__table}", DB_DATAOBJECT_ERROR_INVALIDCONFIG);
            return false;
        }
        $options= &$_DB_DATAOBJECT['CONFIG'];

        // turn the sequence keys into an array
        if ((@$options['ignore_sequence_keys']) &&
                (@$options['ignore_sequence_keys'] != 'ALL') &&
                (!is_array($options['ignore_sequence_keys']))) {
            $options['ignore_sequence_keys']  = explode(',', $options['ignore_sequence_keys']);
        }

        $datasaved = 1;
        $leftq     = '';
        $rightq    = '';
        $key       = false;
        $keys      = $this->_get_keys();
        $dbtype    = $_DB_DATAOBJECT['CONNECTIONS'][$this->_database_dsn_md5]->dsn["phptype"];

        // big check for using sequences
        if (    ($key = @$keys[0]) &&
                ($dbtype != 'mysql') &&
                (@$options['ignore_sequence_keys'] != 'ALL') &&
                (!is_array(@$options['ignore_sequence_keys']) ||
                    @!in_array($this->__table,$options['ignore_sequence_keys']))
            )
        {

            if (!($seq = @$options['sequence_'. $this->__table])) {
                $seq = $this->__table;
            }
            $this->$key = $__DB->nextId($seq);
        }

        foreach($items as $k => $v) {
            if (!isset($this->$k)) {
                continue;
            }
            if ($leftq) {
                $leftq  .= ', ';
                $rightq .= ', ';
            }
            $leftq .= "$k ";

            if ($this->$k === null) {
                $rightq .= " NULL ";
                continue;
            }

            if ($v & DB_DATAOBJECT_STR) {
                $rightq .= $__DB->quote($this->$k) . " ";
                continue;
            }
            if (is_numeric($this->$k)) {
                $rightq .=" {$this->$k} ";
                continue;
            }
            // at present we only cast to integers
            // - V2 may store additional data about float/int
            $rightq .= ' ' . intval($this->$k) . ' ';

        }
        if ($leftq) {
            $r = $this->_query("INSERT INTO {$this->__table} ($leftq) VALUES ($rightq) ");
            if (PEAR::isError($r)) {
                DB_DataObject::raiseError($r);
                return false;
            }
            if ($r < 1) {
                DB_DataObject::raiseError('No Data Affected By insert',DB_DATAOBJECT_ERROR_NOAFFECTEDROWS);
                return false;
            }

            if ($key &&
                ($items[$key] & DB_DATAOBJECT_INT) &&
                ($dbtype == 'mysql') &&
                (@$options['ignore_sequence_keys'] != 'ALL') &&
                    (
                        !@$options['ignore_sequence_keys'] ||
                        (is_array(@$options['ignore_sequence_keys']) &&
                            in_array($this->__table,@$options['ignore_sequence_keys']))
                    )
                )
            {
                $this->$key = mysql_insert_id($_DB_DATAOBJECT['CONNECTIONS'][$this->_database_dsn_md5]->connection);
            }

            $this->_clear_cache();
            if ($key) {
                return $this->$key;
            }
            return true;
        }
        DB_DataObject::raiseError("insert: No Data specifed for query", DB_DATAOBJECT_ERROR_NODATA);
        return false;
    }

    /**
     * Updates  current objects variables into the database
     * uses the _get_keys() to decide how to update
     * Returns the  true on success
     *
     * for example
     *
     * $object = new mytable();
     * $object->get("ID",234);
     * $object->email="testing@test.com";
     * if(!$object->update())
     *   echo "UPDATE FAILED";
     *
     * to only update changed items :
     * $dataobject->get(132);
     * $original = $dataobject; // clone/copy it..
     * $dataobject->setFrom($_POST);
     * if ($dataobject->validate()) {
     *    $dataobject->update($original);
     * } // otherwise an error...
     *
     *
     * @param object dataobject (optional) - used to only update changed items.
     * @access public
     * @return  int rows affected or false on failure
     */
    function update($dataObject = false)
    {
        global $_DB_DATAOBJECT;
        // connect will load the config!
        $this->_connect();

        $items = $this->_get_table();
        $keys  = $this->_get_keys();

        if (!$items) {
            DB_DataObject::raiseError("update:No table definition for {$this->__table}", DB_DATAOBJECT_ERROR_INVALIDCONFIG);
            return false;
        }
        $datasaved = 1;
        $settings  = '';



        $__DB  = &$_DB_DATAOBJECT['CONNECTIONS'][$this->_database_dsn_md5];

        foreach($items as $k => $v) {
            if (!isset($this->$k)) {
                continue;
            }
            if (($dataObject !== false) && (@$dataObject->$k == $this->$k)) {
                continue;
            }


            if ($settings)  {
                $settings .= ', ';
            }
            /* special values ... at least null is handled...*/
            if ($this->$k === null) {
                $settings .= "$k = NULL";
            }

            if ($v & DB_DATAOBJECT_STR) {
                $settings .= $k .' = '. $__DB->quote($this->$k) . ' ';
                continue;
            }
            if (is_numeric($this->$k)) {
                $settings .= "$k = {$this->$k} ";
                continue;
            }
            // at present we only cast to integers
            // - V2 may store additional data about float/int
            $settings .= "$k = " . intval($this->$k) . ' ';
        }

        //$this->_condition=""; // dont clear condition
        if (@$_DB_DATAOBJECT['CONFIG']['debug']) {
            $this->debug("got keys as ".serialize($keys),3);
        }
        $this->_build_condition($items,$keys);
        //  echo " $settings, $this->condition ";
        if ($settings && $this->_condition) {
            $r = $this->_query("UPDATE  {$this->__table}  SET {$settings} {$this->_condition} ");
            if (PEAR::isError($r)) {
                $this->raiseError($r);
                return false;
            }
            if ($r < 1) {
                DB_DataObject::raiseError('No Data Affected By update',DB_DATAOBJECT_ERROR_NOAFFECTEDROWS);
                return false;
            }

            $this->_clear_cache();
            return $r;
        }
        DB_DataObject::raiseError("update: No Data specifed for query $settings , {$this->_condition}", DB_DATAOBJECT_ERROR_NODATA);
        return false;
    }

    /**
     * Deletes items from table which match current objects variables
     *
     * Returns the true on success
     *
     * for example
     *
     * Designed to be extended
     *
     * $object = new mytable();
     * $object->ID=123;
     * echo $object->delete(); // builds a conditon
     * $object = new mytable();
     * $object->whereAdd('age > 12');
     * $object->delete(true); // use the condition
     *
     * @param bool $useWhere (optional) If DB_DATAOBJECT_WHEREADD_ONLY is passed in then
     *             we will build the condition only using the whereAdd's.  Default is to
     *             build the condition only using the object parameters.
     *
     * @access public
     * @return bool True on success
     */
    function delete($useWhere = false)
    {
        global $_DB_DATAOBJECT;
        // connect will load the config!
        $this->_connect();

        if (!$useWhere) {

            $keys = $this->_get_keys();
            $this->_condition = ''; // default behaviour not to use where condition
            $this->_build_condition($this->_get_table(),$keys);
            // if primary keys are not set then use data from rest of object.
            if (!$this->_condition) {
                $this->_build_condition($this->_get_table(),array(),$keys);
            }
        }

        // don't delete without a condition
        if ($this->_condition) {
            $r = $this->_query("DELETE FROM {$this->__table} {$this->_condition}");
            if (PEAR::isError($r)) {
                $this->raiseError($r);
                return false;
            }
            if ($r < 1) {
                DB_DataObject::raiseError('No Data Affected By delete',DB_DATAOBJECT_ERROR_NOAFFECTEDROWS);
                return false;
            }
            $this->_clear_cache();
            return $r;
        } else {
            DB_DataObject::raiseError("delete: No condition specifed for query", DB_DATAOBJECT_ERROR_NODATA);
            return false;
        }
    }

    /**
     * fetches a specific row into this object variables
     *
     * Not recommended - better to use fetch()
     *
     * Returens true on success
     *
     * @param  int   $row  row
     * @access public
     * @return boolean true on success
     */
    function fetchRow($row = NULL)
    {
        global $_DB_DATAOBJECT;
        if (empty($_DB_DATAOBJECT['CONFIG'])) {
            DB_DataObject::_loadConfig();
        }
        if (@$_DB_DATAOBJECT['CONFIG']['debug']) {
            $this->debug("{$this->__table} $row of {$this->N}", "fetchrow",3);
        }
        if (!$this->__table) {
            DB_DataObject::raiseError("fetchrow: No table", DB_DATAOBJECT_ERROR_INVALIDCONFIG);
            return false;
        }
        if ($row === NULL) {
            DB_DataObject::raiseError("fetchrow: No row specified", DB_DATAOBJECT_ERROR_INVALIDARGS);
            return false;
        }
        if (!$this->N) {
            DB_DataObject::raiseError("fetchrow: No results avaiable", DB_DATAOBJECT_ERROR_NODATA);
            return false;
        }
        if (@$_DB_DATAOBJECT['CONFIG']['debug']) {
            $this->debug("{$this->__table} $row of {$this->N}", "fetchrow",3);
        }


        $result = &$_DB_DATAOBJECT['RESULTS'][$this->_DB_resultid];
        $array  = $result->fetchrow(DB_FETCHMODE_ASSOC,$row);
        if (!is_array($array)) {
            DB_DataObject::raiseError("fetchrow: No results available", DB_DATAOBJECT_ERROR_NODATA);
            return false;
        }

        foreach($array as $k => $v) {
            $kk = str_replace(".", "_", $k);
            if (@$_DB_DATAOBJECT['CONFIG']['debug']) {
                $this->debug("$kk = ". $array[$k], "fetchrow LINE", 3);
            }
            $this->$kk = $array[$k];
        }

        if (@$_DB_DATAOBJECT['CONFIG']['debug']) {
            $this->debug("{$this->__table} DONE", "fetchrow", 3);
        }
        return true;
    }

    /**
     * Find the number of results from a simple query
     *
     * for example
     *
     * $object = new mytable();
     * $object->name = "fred";
     * echo $object->count();
     *
     * @param bool $whereAddOnly (optional) If DB_DATAOBJECT_WHEREADD_ONLY is passed in then
     *             we will build the condition only using the whereAdd's.  Default is to
     *             build the condition using the object parameters as well.
     *
     * @access public
     * @return int
     */
    function count($whereAddOnly = false)
    {
        global $_DB_DATAOBJECT;
        $items   = $this->_get_table();
        $tmpcond = $this->_condition;
        $__DB    = $this->getDatabaseConnection();

        if (!$whereAddOnly && $items)  {
            foreach ($items as $key => $val) {
                if (isset($this->$key))  {
                    $this->whereAdd($key . ' = ' . $__DB->quote($this->$key));
                }
            }
        }
        $keys = $this->_get_keys();

        if (!$keys[0]) {
            echo 'CAN NOT COUNT WITHOUT PRIMARY KEYS';
            exit;
        }

        $r = $this->_query(
            "SELECT count({$this->__table}.{$keys[0]}) as __num
                FROM {$this->__table} {$this->_join} {$this->_condition}");
        if (PEAR::isError($r)) {
            return false;
        }
        $this->_condition = $tmpcond;
        $result  = &$_DB_DATAOBJECT['RESULTS'][$this->_DB_resultid];
        $l = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
        return $l['__num'];
    }

    /**
     * sends raw query to database
     *
     * Since _query has to be a private 'non overwriteable method', this is a relay
     *
     * @param  string  $string  SQL Query
     * @access public
     * @return void or PEAR_Error
     */
    function query($string)
    {
        return $this->_query($string);
    }


    /**
     * an escape wrapper around quote ..
     * can be used when adding manual queries =
     * eg.
     * $object->query("select * from xyz where abc like '". $object->quote($_GET['name']) . "'");
     *
     * @param  string  $string  SQL Query
     * @access public
     * @return void or PEAR_Error
     */
    function escape($string)
    {
        global $_DB_DATAOBJECT;
        $this->_connect();

        $__DB  = &$_DB_DATAOBJECT['CONNECTIONS'][$this->_database_dsn_md5];
        return substr($__DB->quote($string),1,-1);
    }

    /* ==================================================== */
    /*        Major Private Vars                            */
    /* ==================================================== */

    /**
     * The Database connection dsn (as described in the PEAR DB)
     * only used really if you are writing a very simple application/test..
     * try not to use this - it is better stored in configuration files..
     *
     * @access  private
     * @var     string
     */
    var $_database_dsn = '';

    /**
     * The Database connection id (md5 sum of databasedsn)
     *
     * @access  private
     * @var     string
     */
    var $_database_dsn_md5 = '';

    /**
     * The Database name
     * created in __connection
     *
     * @access  private
     * @var  string
     */
    var $_database = '';

    /**
     * The WHERE condition
     *
     * @access  private
     * @var     string
     */
    var $_condition = '';

    /**
     * The GROUP BY condition
     *
     * @access  private
     * @var     string
     */
    var $_group_by = '';

    /**
     * The ORDER BY condition
     *
     * @access  private
     * @var     string
     */
    var $_order_by = '';

    /**
     * The Limit by statement
     *
     * @access  private
     * @var     string
     */
    var $_limit = '';

    /**
     * The default Select
     * @access  private
     * @var     string
     */
    var $_data_select = '*';

    /**
     * Database result id (references global $__DB_DataObject_results
     *
     * @access  private
     * @var     integer
     */
    var $_DB_resultid; // database result object


    /* =========================================================== */
    /*  Major Private Methods - the core part!*/
    /* =========================================================== */

    /**
     * Autoload  the table definitions
     *
     *
     * @access private
     * @return boolean
     */
    function _loadDefinitions()
    {

        global $_DB_DATAOBJECT;
        if (isset($_DB_DATAOBJECT['INI'][$this->_database])) {
            return true;
        }
        if (empty($_DB_DATAOBJECT['CONFIG'])) {
            DB_DataObject::_loadConfig();
        }
        $location = $_DB_DATAOBJECT['CONFIG']['schema_location'];

        $ini   = $location . "/{$this->_database}.ini";

        if (isset($_DB_DATAOBJECT['CONFIG']["ini_{$this->_database}"])) {
            $ini = $_DB_DATAOBJECT['CONFIG']["ini_{$this->_database}"];
        }
        $links = str_replace('.ini','.links.ini',$ini);

        $_DB_DATAOBJECT['INI'][$this->_database] = parse_ini_file($ini, true);
        /* load the link table if it exists. */
        if (file_exists($links)) {
            /* not sure why $links = ... here  - TODO check if that works */
            $_DB_DATAOBJECT['LINKS'][$this->_database] = parse_ini_file($links, true);
        }
        return true;
    }

    /**
     * get an associative array of table columns
     *
     * @access private
     * @return array (associative)
     */
    function _get_table()
    {
        global $_DB_DATAOBJECT;
        if (!@$this->_database) {
            $this->_connect();
        }
        
        $this->_loadDefinitions();


        $ret = array();
        if (isset($_DB_DATAOBJECT['INI'][$this->_database][$this->__table])) {
            $ret =  $_DB_DATAOBJECT['INI'][$this->_database][$this->__table];
        }
        
        return $ret;
    }

    /**
     * get an  array of table primary keys
     *
     * This is defined in the table definition which should extend this class
     *
     * @access private
     * @return array
     */
    function _get_keys()
    {
        global $_DB_DATAOBJECT;
        if (!@$this->_database) {
            $this->_connect();
        }
        $this->_loadDefinitions();

        if (isset($_DB_DATAOBJECT['INI'][$this->_database][$this->__table."__keys"])) {
            return array_keys($_DB_DATAOBJECT['INI'][$this->_database][$this->__table."__keys"]);
        }
        return array();
    }

    /**
     * clear the cache values for this class  - normally done on insert/update etc.
     *
     * @access private
     * @return void
     */
    function _clear_cache()
    {
        global $_DB_DATAOBJECT;
        
        $class = get_class($this);
        if (@$_DB_DATAOBJECT['CACHE'][$class]) {
            unset($_DB_DATAOBJECT['CACHE'][$class]);
        }
    }

    /**
     * connects to the database
     *
     *
     * TODO: tidy this up - This has grown to support a number of connection options like
     *  a) dynamic changing of ini file to change which database to connect to
     *  b) multi data via the table_{$table} = dsn ini option
     *  c) session based storage.
     *
     * @access private
     * @return void
     */
    function _connect()
    {
        global $_DB_DATAOBJECT;
        if (empty($_DB_DATAOBJECT['CONFIG'])) {
            DB_DataObject::_loadConfig();
        }

        // is it already connected ?

        if ($this->_database_dsn_md5 && @$_DB_DATAOBJECT['CONNECTIONS'][$this->_database_dsn_md5]) {
            if (PEAR::isError($_DB_DATAOBJECT['CONNECTIONS'][$this->_database_dsn_md5])) {
                DB_DataObject::raiseError(
                        $_DB_DATAOBJECT['CONNECTIONS'][$this->_database_dsn_md5]->message,
                        $_DB_DATAOBJECT['CONNECTIONS'][$this->_database_dsn_md5]->code, PEAR_ERROR_DIE
                );
                return;
            }

            if (!$this->_database) {
                $this->_database = $_DB_DATAOBJECT['CONNECTIONS'][$this->_database_dsn_md5]->dsn["database"];
            }
            return;
        }

        // it's not currently connected!
        // try and work out what to use for the dsn !

        $options= &$_DB_DATAOBJECT['CONFIG'];
        $dsn = @$this->_database_dsn;

        if (!$dsn) {
            if (!$this->_database) {
                $this->_database = @$options["table_{$this->__table}"];
            }
            if (@$this->_database && @$options["database_{$this->_database}"])  {
                $dsn = $options["database_{$this->_database}"];
            } else if ($options['database']) {
                $dsn = $options['database'];
            }
        }

        $this->_database_dsn_md5 = md5($dsn);

        if (@$_DB_DATAOBJECT['CONNECTIONS'][$this->_database_dsn_md5]) {
            if (@$_DB_DATAOBJECT['CONFIG']['debug']) {
                $this->debug("USING CACHED CONNECTION", "CONNECT",3);
            }
            if (!$this->_database) {
                $this->_database = $_DB_DATAOBJECT['CONNECTIONS'][$this->_database_dsn_md5]->dsn["database"];
            }
            return;
        }
        if (@$_DB_DATAOBJECT['CONFIG']['debug']) {
            $this->debug("NEW CONNECTION", "CONNECT",3);
            /* actualy make a connection */
            $this->debug("{$dsn} {$this->_database_dsn_md5}", "CONNECT",3);
        }

        $_DB_DATAOBJECT['CONNECTIONS'][$this->_database_dsn_md5] = DB::connect($dsn);

        if (@$_DB_DATAOBJECT['CONFIG']['debug']) {
            $this->debug(serialize($_DB_DATAOBJECT['CONNECTIONS']), "CONNECT",5);
        }
        if (PEAR::isError($_DB_DATAOBJECT['CONNECTIONS'][$this->_database_dsn_md5])) {
            DB_DataObject::raiseError(
                        $_DB_DATAOBJECT['CONNECTIONS'][$this->_database_dsn_md5]->message,
                        $_DB_DATAOBJECT['CONNECTIONS'][$this->_database_dsn_md5]->code, PEAR_ERROR_DIE
            );

        }

        if (!$this->_database) {
            $this->_database = $_DB_DATAOBJECT['CONNECTIONS'][$this->_database_dsn_md5]->dsn["database"];
        }

        return true;
    }

    /**
     * sends query to database - this is the private one that must work - internal functions use this rather than $this->query()
     *
     * @param  string  $string
     * @access private
     * @return mixed none or PEAR_Error
     */
    function _query($string)
    {
        global $_DB_DATAOBJECT;

        $this->_connect();

        $options = &$_DB_DATAOBJECT['CONFIG'];

        if (@$_DB_DATAOBJECT['CONFIG']['debug']) {
            $this->debug("QUERY".$string,$log="sql");
        }
        $__DB = &$_DB_DATAOBJECT['CONNECTIONS'][$this->_database_dsn_md5];

        if (@$options['debug_ignore_updates'] &&
            (strtolower(substr(trim($string), 0, 6)) != 'select') &&
            (strtolower(substr(trim($string), 0, 4)) != 'show') &&
            (strtolower(substr(trim($string), 0, 8)) != 'describe')) {

            $this->debug('Disabling Update as you are in debug mode');
            return DB_DataObject::raiseError("Disabling Update as you are in debug mode", NULL) ;

        }
        
        
         
        $result = $__DB->query($string);

        if (DB::isError($result)) {
            if (@$_DB_DATAOBJECT['CONFIG']['debug']) {
                DB_DataObject::debug($string, "SENT");
            }
            return DB_DataObject::raiseError($result->message, $result->code);
        }
        if (@$_DB_DATAOBJECT['CONFIG']['debug']) {
            $this->debug('DONE QUERY', 'query');
        }
        switch (strtolower(substr(trim($string),0,6))) {
            case 'insert':
            case 'update':
            case 'delete':
                return $__DB->affectedRows();;
        }
        // lets hope that copying the result object is OK!
        $_DB_resultid  = count($_DB_DATAOBJECT['RESULTS']); // add to the results stuff...
        $_DB_DATAOBJECT['RESULTS'][$_DB_resultid] = $result; 
        $this->_DB_resultid = $_DB_resultid;
        
        $this->N = 0;
        if (@$_DB_DATAOBJECT['CONFIG']['debug']) {
            $this->debug(serialize($result), 'RESULT',5);
        }
        if (method_exists($result, 'numrows')) {
            $this->N = $result->numrows();
        }
    }

    /**
     * Builds the WHERE based on the values of of this object
     *
     * @param   mixed   $keys
     * @param   array   $filter (used by update to only uses keys in this filter list).
     * @param   array   $negative_filter (used by delete to prevent deleting using the keys mentioned..)
     * @access  private
     * @return  string
     */
    function _build_condition(&$keys, $filter = array(),$negative_filter=array())
    {
        global $_DB_DATAOBJECT;
        $this->_connect();

        $__DB  = &$_DB_DATAOBJECT['CONNECTIONS'][$this->_database_dsn_md5];

        foreach($keys as $k => $v) {
            // index keys is an indexed array
            /* these filter checks are a bit suspicious..
                - need to check that update really wants to work this way */

            if ($filter) {
                if (!in_array($k, $filter)) {
                    continue;
                }
            }
            if ($negative_filter) {
                if (in_array($k, $negative_filter)) {
                    continue;
                }
            }
            if (!isset($this->$k)) {
                continue;
            }

            if ($v & DB_DATAOBJECT_STR) {
                $this->whereAdd(" {$this->__table}.{$k}  = " . $__DB->quote($this->$k) );
                continue;
            }
            if (is_numeric($this->$k)) {
                $this->whereAdd(" {$this->__table}.{$k} = {$this->$k}");
                continue;
            }
            /* this is probably an error condition! */
            $this->whereAdd(" {$this->__table}.{$k} = 0");
        }
    }

    /**
     * autoload Class relating to a table
     *
     * @param  string  $table  table
     * @access private
     * @return string classname on Success
     */
    function staticAutoloadTable($table)
    {
        global $_DB_DATAOBJECT;
        if (empty($_DB_DATAOBJECT['CONFIG'])) {
            DB_DataObject::_loadConfig();
        }
        $class   = DB_DataObject::_autoloadClass($_DB_DATAOBJECT['CONFIG']['class_prefix'] . ucfirst($table));
        return $class;
    }

    /**
     * autoload Class
     *
     * @param  string  $class  Class
     * @access private
     * @return string classname on Success
     */
    function _autoloadClass($class)
    {
        global $_DB_DATAOBJECT;
        if (empty($_DB_DATAOBJECT['CONFIG'])) {
            DB_DataObject::_loadConfig();
        }
        $table   = substr($class,strlen($_DB_DATAOBJECT['CONFIG']['class_prefix']));

        // only include the file if it exists - and barf badly if it has parse errors :)
        
        $file = $_DB_DATAOBJECT['CONFIG']['require_prefix'].ucfirst($table).'.php';
        if (!isset($_DB_DATAOBJECT['LOADED'][$file]) && $fh = @fopen($file,'r',1)) {
            fclose($fh);
            include_once $_DB_DATAOBJECT['CONFIG']['require_prefix'].ucfirst($table).".php";
        }
        
        $_DB_DATAOBJECT['LOADED'][$file] = true;
        
        if (!class_exists($class)) {
            DB_DataObject::raiseError("autoload:Could not autoload {$class}", DB_DATAOBJECT_ERROR_INVALIDCONFIG);
            return false;
        }
        return $class;
    }
    
    
    
    /**
     * Have the links been loaded?
     *
     * @access  private
     * @var     boolean
     */
    var $_link_loaded = false;

    /**
     * load related objects
     *
     * There are two ways to use this, one is to set up a <dbname>.links.ini file
     * into a static property named <dbname>.links and specifies the table joins,
     * the other highly dependent on naming columns 'correctly' :)
     * using colname = xxxxx_yyyyyy
     * xxxxxx = related table; (yyyyy = user defined..)
     * looks up table xxxxx, for value id=$this->xxxxx
     * stores it in $this->_xxxxx_yyyyy
     *
     * @author Tim White <tim@cyface.com>
     * @access public
     * @return boolean , true on success
     */
    function getLinks()
    {
        global $_DB_DATAOBJECT;
        // get table will load the options.
        if ($this->_link_loaded) {
            return;
        }
        $cols  = $this->_get_table();
        if (!isset($_DB_DATAOBJECT['LINKS'][$this->_database])) {
            return;
        }
        $links = &$_DB_DATAOBJECT['LINKS'][$this->_database];
        /* if you define it in the links.ini file with no entries */
        if (isset($links[$this->__table]) && (!@$links[$this->__table])) {
            return;
        }
        if (@$links[$this->__table]) {
            foreach($links[$this->__table] as $key => $match) {
                list($table,$link) = explode(':', $match);
                $k = '_' . str_replace('.', '_', $key);
                if ($p = strpos($key,".")) {
                      $key = substr($key, 0, $p);
                }
                $this->$k = $this->getLink($key, $table, $link);
            }
            return;
        }
        foreach (array_keys($cols) as $key) {
            if (!($p = strpos($key, '_'))) {
                continue;
            }
            // does the table exist.
            $k = "_{$key}";
            $this->$k = $this->getLink($key);
        }
        $this->_link_loaded = true;
    }

    /**
     * return name from related object
     *
     * There are two ways to use this, one is to set up a <dbname>.links.ini file
     * into a static property named <dbname>.links and specifies the table joins,
     * the other is highly dependant on naming columns 'correctly' :)
     *
     * NOTE: the naming convention is depreciated!!! - use links.ini
     *
     * using colname = xxxxx_yyyyyy
     * xxxxxx = related table; (yyyyy = user defined..)
     * looks up table xxxxx, for value id=$this->xxxxx
     * stores it in $this->_xxxxx_yyyyy
     *
     * you can also use $this->getLink('thisColumnName','otherTable','otherTableColumnName')
     *
     *
     * @param string $row    either row or row.xxxxx
     * @param string $table  name of table to look up value in
     * @param string $link   name of column in other table to match
     * @author Tim White <tim@cyface.com>
     * @access public
     * @return mixed object on success
     */
    function &getLink($row, $table = NULL, $link = false)
    {
        /* see if autolinking is available
         * This will do a recursive call!
         */
        global $_DB_DATAOBJECT;

        $this->_get_table(); /* make sure the links are loaded */

        if ($table === NULL) {
            $links = array();
            if (isset($_DB_DATAOBJECT['LINKS'][$this->_database])) {
                $links = &$_DB_DATAOBJECT['LINKS'][$this->_database];
            }

            if (isset($links[$this->__table])) {
                if (@$links[$this->__table][$row]) {
                    list($table,$link) = explode(':', $links[$this->__table][$row]);
                    if ($p = strpos($row,".")) {
                        $row = substr($row,0,$p);
                    }
                    return $this->getLink($row,$table,$link);
                } else {
                    DB_DataObject::raiseError("getLink: $row is not defined as a link (normally this is ok)", DB_DATAOBJECT_ERROR_NODATA);
                    return false; // technically a possible error condition?
                }
            } else { // use the old _ method
                if (!($p = strpos($row, '_'))) {
                    return;
                }
                $table = substr($row, 0, $p);
                return $this->getLink($row, $table);
            }
        }
        if (!isset($this->$row)) {
            DB_DataObject::raiseError("getLink: row not set $row", DB_DATAOBJECT_ERROR_NODATA);
            return false;
        }

        $class = $this->staticAutoloadTable($table);
        if (!$class) {
            DB_DataObject::raiseError("getLink:Could not find class for row $row, table $table", DB_DATAOBJECT_ERROR_INVALIDCONFIG);
            return false;
        }
        if ($link) {
            return DB_DataObject::staticGet($class, $link, $this->$row);
        }
        return DB_DataObject::staticGet($class, $this->$row);
    }

    /**
     * return a list of options for a linked table
     *
     * This is highly dependant on naming columns 'correctly' :)
     * using colname = xxxxx_yyyyyy
     * xxxxxx = related table; (yyyyy = user defined..)
     * looks up table xxxxx, for value id=$this->xxxxx
     * stores it in $this->_xxxxx_yyyyy
     *
     * @access public
     * @return array of results (empty array on failure)
     */
    function &getLinkArray($row, $table = NULL)
    {
        global $_DB_DATAOBJECT;

        $this->_get_table(); /* make sure the links are loaded */


        $ret = array();
        if (!$table) {
            $links = array();
            if (isset($_DB_DATAOBJECT['LINKS'][$this->_database])) {
                $links = &$_DB_DATAOBJECT['LINKS'][$this->_database];
            }

            if (@$links[$this->__table][$row]) {
                list($table,$link) = explode(':',$links[$this->__table][$row]);
            } else {
                if (!($p = strpos($row,'_'))) {
                    return $ret;
                }
                $table = substr($row,0,$p);
            }
        }
        $class = $this->staticAutoloadTable($table);
        if (!$class) {
            DB_DataObject::raiseError("getLinkArray:Could not find class for row $row, table $table", DB_DATAOBJECT_ERROR_INVALIDCONFIG);
            return $ret;
        }

        $c = new $class;
        // if the user defined method list exists - use it...
        if (method_exists($c, 'listFind')) {
            $c->listFind($this->id);
        } else {
            $c->find();
        }
        while ($c->fetch()) {
            $ret[] = $c;
        }
        return $ret;
    }

    /**
     * The JOIN condition
     *
     * @access  private
     * @var     string
     */
    var $_join = '';

    /**
     * joinAdd - adds another dataobject to this, building a joined query.
     *
     * example (requires links.ini to be set up correctly)
     * // get all the images for product 24
     * $i = new DataObject_Image();
     * $pi = new DataObjects_Product_image();
     * $pi->product_id = 24; // set the product id to 24
     * $i->joinAdd($pi); // add the product_image connectoin
     * $i->find();
     * while ($i->fetch()) {
     *     // do stuff
     * }
     * // an example with 2 joins
     * // get all the images linked with products or productgroups
     * $i = new DataObject_Image();
     * $pi = new DataObject_Product_image();
     * $pgi = new DataObject_Productgroup_image();
     * $i->joinAdd($pi);
     * $i->joinAdd($pgi);
     * $i->find();
     * while ($i->fetch()) {
     *     // do stuff
     * }
     *
     *
     * @param    optional $obj    object      the joining object (no value resets the join)
     * @param    optional $joinType    string  'LEFT'|'INNER'|'RIGHT'|'' Inner is default, '' indicates 
     *                                  just select ... from a,b,c with no join and 
     *                                      links are added as where items.
     * @param    optional $joinAs    string   if you want to select the table as anther name
     *                                      usefull when you want to select multiple columsn
     *                                      from a secondary table.
     * @return   none
     * @access   public
     * @author   Stijn de Reede      <sjr@gmx.co.uk>
     */
    function joinAdd($obj = false,$joinType='INNER',$joinAs=false)
    {
        global $_DB_DATAOBJECT;
        if ($obj === false) {
            $this->_join = '';
            return;
        }
        if (!is_object($obj)) {
            DB_DataObject::raiseError("joinAdd: called without an object", DB_DATAOBJECT_ERROR_NODATA,PEAR_ERROR_DIE);
        }
        
        $this->_connect(); /*  make sure $this->_database is set.  */

        $this->_get_table(); /* make sure the links are loaded */

        $__DB  = &$_DB_DATAOBJECT['CONNECTIONS'][$this->_database_dsn_md5];


        $links = array();
        if (isset($_DB_DATAOBJECT['LINKS'][$this->_database])) {
            $links = &$_DB_DATAOBJECT['LINKS'][$this->_database];
        }

        $ofield = false; // object field
        $tfield = false; // this field

         /* look up the links for obj table */

        if (isset($links[$obj->__table])) {
            foreach ($links[$obj->__table] as $k => $v) {
                /* link contains {this column} = {linked table}:{linked column} */
                $ar = explode(':', $v);
                if ($ar[0] == $this->__table) {
                    $ofield = $k;
                    $tfield = $ar[1];
                    break;
                }
            }
        }

        /* otherwise see if there are any links from this table to the obj. */

        if (($ofield === false) &&isset($links[$this->__table])) {
            foreach ($links[$this->__table] as $k => $v) {
                /* link contains {this column} = {linked table}:{linked column} */
                $ar = explode(':', $v);
                if ($ar[0] == $obj->__table) {
                    $tfield = $k;
                    $ofield = $ar[1];
                    break;
                }
            }
        }
        
        /* did I find a conneciton between them? */

        if ($ofield === false) {
            DB_DataObject::raiseError("joinAdd: {$obj->__table} has no link with {$this->__table}", DB_DATAOBJECT_ERROR_NODATA);
            return false;
        }
        $joinType = strtoupper($joinType);
        if ($joinAs === false) {
            $joinAs = $obj->__table;
        }
        
        switch ($joinType) {
            case 'INNER':
            case 'LEFT': 
            case 'RIGHT': // others??? .. cross, left outer, right outer, natural..?
                $this->_join .= "\n {$joinType} JOIN {$obj->__table} AS {$joinAs}".
                                " ON {$joinAs}.{$ofield}={$this->__table}.{$tfield} ";
                break;
            case '': // this is just a standard multitable select..
                $this->_join .= "\n , {$obj->__table} AS {$joinAs} ";
                $this->whereAdd("{$joinAs}.{$ofield}={$this->__table}.{$tfield}");
        }
         
        /* now add where conditions for anything that is set in the object */

        $items = $obj->_get_table();
        if (!$items) {
            DB_DataObject::raiseError("joinAdd: No table definition for {$obj->__table}", DB_DATAOBJECT_ERROR_INVALIDCONFIG);
            return false;
        }

        foreach($items as $k => $v) {
            if (!isset($obj->$k)) {
                continue;
            }
            if ($v & DB_DATAOBJECT_STR) {
                $this->whereAdd("{$joinAs}.{$k} = " . $__DB->quote($obj->$k));
                continue;
            }
            if (is_numeric($obj->$k)) {
                $this->whereAdd("{$joinAs}.{$k} = {$obj->$k}");
                continue;
            }
            /* this is probably an error condition! */
            $this->whereAdd("{$joinAs}.{$k} = 0");
        }


    }

    /**
     * Copies items that are in the table definitions from an
     * array or object into the current object
     * will not override key values.
     *
     *
     * @param    array | object  $from
     * @param    string  $format eg. map xxxx_name to $object->name using 'xxxx_%s' (defaults to %s - eg. name -> $object->name
     * @access   public
     * @return   true on success or array of key=>setValue error message
     */
    function setFrom(&$from, $format = '%s')
    {
        global $_DB_DATAOBJECT;
        $keys  = $this->_get_keys();
        $items = $this->_get_table();
        if (!$items) {
            DB_DataObject::raiseError("setFrom:Could not find table definition for {$this->__table}", DB_DATAOBJECT_ERROR_INVALIDCONFIG);
            return;
        }
        $overload_return = array();
        foreach (array_keys($items) as $k) {
            if (in_array($k,$keys)) {
                continue; // dont overwrite keys
            }
            if (!$k) {
                continue; // ignore empty keys!!! what
            }
            if (is_object($from) && isset($from->{sprintf($format,$k)})) {
                if ($_DB_DATAOBJECT['OVERLOADED'] ) {
                    $kk = (strtolower($k) == 'from') ? '_from' : $k;
                    $ret = $this->{'set'.$kk}($from->{sprintf($format,$k)});
                    if (is_string($ret)) {
                        $overload_return[$k] = $ret;
                    }
                    continue;
                }
                $this->$k = $from->{sprintf($format,$k)};
                continue;
            }
            
            if (is_object($from)) {
                continue;
            }
            
            if (!isset($from[sprintf($format,$k)])) {
                continue;
            }
            if (is_object($from[sprintf($format,$k)])) {
                continue;
            }
            if (is_array($from[sprintf($format,$k)])) {
                continue;
            }
            if ($_DB_DATAOBJECT['OVERLOADED']) {
                $kk = (strtolower($k) == 'from') ? '_from' : $k;
                $ret =  $this->{'set'.$kk}($from[sprintf($format,$k)]);
                if (is_string($ret)) {
                    $overload_return[$k] = $ret;
                }
                continue;
            }
            $this->$k = $from[sprintf($format,$k)];
        }
        if ($overload_return) {
            return $overload_return;
        }
        return true;
    }

    /**
     * Returns an associative array from the current data
     * (kind of oblivates the idea behind DataObjects, but
     * is usefull if you use it with things like QuickForms.
     *
     * you can use the format to return things like user[key]
     * by sending it $object->toArray('user[%s]')
     *
     *
     * @param   string sprintf format for array
     * @access   public
     * @return   array of key => value for row
     */

    function toArray($format = '%s')
    {
        global $_DB_DATAOBJECT;
        $ret = array();
 
        foreach($this->_get_table() as $k=>$v) {
             
            if (!isset($this->$k)) {
                $ret[sprintf($format,$k)] = '';
                continue;
            }
            // call the overloaded getXXXX() method.
            if ($_DB_DATAOBJECT['OVERLOADED']) {
                $ret[sprintf($format,$k)] = $this->{'get'.$k}();
                continue;
            }
            $ret[sprintf($format,$k)] = $this->$k;
        }
        return $ret;
    }

    /**
     * validate - override this to set up your validation rules
     *
     * validate the current objects values either just testing strings/numbers or
     * using the user defined validate{Row name}() methods.
     * will attempt to call $this->validate{column_name}() - expects true = ok  false = ERROR
     * you can the use the validate Class from your own methods.
     *
     * @access  public
     * @return  array of validation results or true
     */
    function validate()
    {
        require_once 'Validate.php';
        $table = &$this->_get_table();
        $ret   = array();

        foreach($table as $key => $val) {
            // ignore things that are not set. ?
            if (!isset($this->$key)) {
                continue;
            }
            // call user defined validation
            $method = "Validate" . ucfirst($key);
            if (method_exists($this, $method)) {
                $ret[$key] = $this->$method();
                continue;
            }
            // if the string is empty.. assume it is ok..
            if (!strlen($this->$key)) {
                continue;
            }
            
            switch ($val) {
                case  DB_DATAOBJECT_STR:
                    $ret[$key] = Validate::string($this->$key, VALIDATE_PUNCTUATION . VALIDATE_NAME);
                    continue;
                case  DB_DATAOBJECT_INT:
                    $ret[$key] = Validate::number($this->$key, array('decimal'=>'.'));
                    continue;
            }
        }

        foreach ($ret as $key => $val) {
            if ($val == false) return $ret;
        }
        return true; // everything is OK.
    }

    /**
     * Gets the DB object related to an object - so you can use funky peardb stuf with it :)
     *
     * @access public
     * @return object The DB connection
     */
    function &getDatabaseConnection()
    {
        global $_DB_DATAOBJECT;

        $this->_connect();
        if (!isset($_DB_DATAOBJECT['CONNECTIONS'][$this->_database_dsn_md5])) {
            return false;
        }
        return $_DB_DATAOBJECT['CONNECTIONS'][$this->_database_dsn_md5];
    }

    /**
     * Gets the DB result object related to the objects active query
     *  - so you can use funky pear stuff with it - like pager for example.. :)
     *
     * @access public
     * @return object The DB result object
     */
    function &getDatabaseResult()
    {
        global $_DB_DATAOBJECT;
        $this->_connect();
        if (!isset($_DB_DATAOBJECT['RESULTS'][$this->_DB_resultid])) {
            return false;
        }
        return $_DB_DATAOBJECT['RESULTS'][$this->_DB_resultid];
    }

    /**
     * Overload Extension support
     *  - enables setCOLNAME/getCOLNAME
     *  if you define a set/get method for the item it will be called.
     * otherwise it will just return/set the value.
     * NOTE this currently means that a few Names are NO-NO's 
     * eg. links,link,linksarray, from, Databaseconnection,databaseresult
     *
     * note 
     *  - set is automatically called by setFrom.
     *   - get is automatically called by toArray()
     *  
     * setters return true on success. = strings on failure
     * getters return the value!
     *
     * this fires off trigger_error - if any problems.. pear_error, 
     * has problems with 4.3.2RC2 here
     *
     * @access public
     * @return true?
     * @see overload
     */

    
    function __call($method,$params,&$return) {
         
        // ignore constructors : - mm
        if ($method == get_class($this)) {
            return true;
        }
        $type = strtolower(substr($method,0,3));
        $class = get_class($this);
        if (($type != 'set') && ($type != 'get')) {
            return false;
        }
         
        
        
        // deal with naming conflick of setFrom = this is messy ATM!
        
        if (strtolower($method) == 'set_from') {
            $this->from = $params[0];
            return $return = true;
        }
        
        $element = substr($method,3);
        if ($element{0} == '_') {
            return false;
        }
         
        
        // dont you just love php's case insensitivity!!!!
        
        $array =  array_keys(get_class_vars($class));
        
        if (!in_array($element,$array)) {
            // munge case
            foreach($array as $k) {
                $case[strtolower($k)] = $k;
            }
            // does it really exist?
            if (!isset($case[$element])) {
                return false;            
            }
            // use the mundged case
            $element = $case[$element]; // real case !
        }
        
        
        if ($type == 'get') {
            $return = $this->$element;
            return true;
        }
        $this->$element = $params[0];
        return $return = true;
    }
        
    
    


    /* ----------------------- Debugger ------------------ */

    /**
     * Debugger. - use this in your extended classes to output debugging information.
     *
     * Uses DB_DataObject::DebugLevel(x) to turn it on
     *
     * @param    string $message - message to output
     * @param    string $logtype - bold at start
     * @param    string $level   - output level
     * @access   public
     * @return   none
     */
    function debug($message, $logtype = 0, $level = 1)
    {
        global $_DB_DATAOBJECT;

        if (DB_DataObject::debugLevel()<$level) {
            return;
        }

        if (!ini_get('html_errors')) {
            echo "$logtype       : $message\n";
            flush();
            return;
        }
        echo "<PRE><B>$logtype</B> $message</PRE>\n";
        flush();
    }

    /**
     * sets and returns debug level
     * eg. DB_DataObject::debugLevel(4);
     *
     * @param   int     $v  level
     * @access  public
     * @return  none
     */
    function debugLevel($v = NULL)
    {
        global $_DB_DATAOBJECT;
        if (empty($_DB_DATAOBJECT['CONFIG'])) {
            DB_DataObject::_loadConfig();
        }
        if ($v !== NULL) {
            $_DB_DATAOBJECT['CONFIG']['debug']  = $v;
        }
        return @$_DB_DATAOBJECT['CONFIG']['debug'];
    }

    /**
     * Last Error that has occured
     * - use $this->_lastError or
     * $last_error = &PEAR::getStaticProperty('DB_DataObject','lastError');
     *
     * @access  public
     * @var     object PEAR_Error (or false)
     */
    var $_lastError = false;

    /**
     * Default error handling is to create a pear error, but never return it.
     * if you need to handle errors you should look at setting the PEAR_Error callback
     * this is due to the fact it would wreck havoc on the internal methods!
     *
     * @param  int $message    message
     * @param  int $type       type
     * @param  int $behaviour  behaviour (die or continue!);
     * @access public
     * @return error object
     */
    function raiseError($message, $type = NULL, $behaviour = NULL)
    {
        global $_DB_DATAOBJECT;
        
        if ($behaviour == PEAR_ERROR_DIE && @$_DB_DATAOBJECT['CONFIG']['dont_die']) {
            $behaviour = NULL;
        }
        
        if (PEAR::isError($message)) {
            $error = $message;
        } else {
            $error = PEAR::raiseError($message, $type, $behaviour);
        }
        // this will never work totally with PHP's object model.
        // as this is passed on static calls (like staticGet in our case)

        if (@is_object($this) && is_subclass_of($this,'db_dataobject')) {
            $this->_lastError = $error;
        }

        $_DB_DATAOBJECT['LASTERROR'] = $error;

        // no checks for production here?.......
        DB_DataObject::debug($message,"ERROR",1);
        return $error;
    }

    /**
     * Define the global $_DB_DATAOBJECT['CONFIG'] as an alias to  PEAR::getStaticProperty('DB_DataObject','options');
     *
     * After Profiling DB_DataObject, I discoved that the debug calls where taking
     * considerable time (well 0.1 ms), so this should stop those calls happening. as
     * all calls to debug are wrapped with direct variable queries rather than actually calling the funciton
     * THIS STILL NEEDS FURTHER INVESTIGATION
     *
     * @access   public
     * @return   object an error object
     */
    function _loadConfig()
    {
        global $_DB_DATAOBJECT;

        $_DB_DATAOBJECT['CONFIG'] = &PEAR::getStaticProperty('DB_DataObject','options');


    }
}
// technially 4.3.2RC1 was broken!!
if ((phpversion() != '4.3.2-RC1') && (version_compare( phpversion(), "4.3.1") > 0)) {
   overload('DB_DataObject');
   $GLOBALS['_DB_DATAOBJECT']['OVERLOADED'] = true;
}

?>