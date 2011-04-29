a:26:{s:8:"db_query";O:8:"stdClass":4:{s:4:"name";s:8:"db_query";s:7:"comment";s:34:"Alias of the query() method above.";s:4:"code";b:0;s:4:"line";s:33:"db_query ($sql = '', $cache = 0) ";}s:8:"db_table";O:8:"stdClass":4:{s:4:"name";s:8:"db_table";s:7:"comment";s:34:"Alias of the table() method above.";s:4:"code";b:0;s:4:"line";s:30:"db_table ($table, $pkey = '') ";}s:8:"db_fetch";O:8:"stdClass":5:{s:4:"name";s:8:"db_fetch";s:7:"comment";s:236:"Returns the results of a single database query.  Returns<br />
false if no results are returned, or on error.  Returns an<br />
object if there is only one result.  Returns an array of<br />
objects if there are multiple results.<br />
";s:4:"code";b:0;s:6:"params";a:3:{s:6:"access";s:6:"public";s:5:"param";s:17:"mixed bind values";s:6:"return";s:5:"mixed";}s:4:"line";s:12:"db_fetch () ";}s:14:"db_fetch_array";O:8:"stdClass":5:{s:4:"name";s:14:"db_fetch_array";s:7:"comment";s:307:"Same as db_fetch(), except it always returns an array, including<br />
an empty array on error or no results (fetch the error with<br />
db_error() to differentiate between the two), and an array<br />
of one item if there is only one result (whereas db_fetch()<br />
would return that as an object).<br />
";s:4:"code";b:0;s:6:"params";a:3:{s:6:"access";s:6:"public";s:5:"param";s:17:"mixed bind values";s:6:"return";s:5:"array";}s:4:"line";s:18:"db_fetch_array () ";}s:14:"db_shift_array";O:8:"stdClass":5:{s:4:"name";s:14:"db_shift_array";s:7:"comment";s:186:"Same as db_fetch_array(), except it returns an array of the first<br />
value of each object (like db_shift() does for a single result).<br />
Please note: Keys are not preserved.<br />
";s:4:"code";b:0;s:6:"params";a:3:{s:6:"access";s:6:"public";s:5:"param";s:17:"mixed bind values";s:6:"return";s:5:"array";}s:4:"line";s:18:"db_shift_array () ";}s:8:"db_pairs";O:8:"stdClass":5:{s:4:"name";s:8:"db_pairs";s:7:"comment";s:203:"Similar to db_shift_array(), except it returns an associative array<br />
of the first two columns of the query results, where the first column<br />
is the key and the second column is the value.<br />
";s:4:"code";b:0;s:6:"params";a:3:{s:6:"access";s:6:"public";s:5:"param";s:17:"mixed bind values";s:6:"return";s:5:"array";}s:4:"line";s:12:"db_pairs () ";}s:10:"db_execute";O:8:"stdClass":5:{s:4:"name";s:10:"db_execute";s:7:"comment";s:303:"Similar to db_fetch(), but used for SQL statements that return no<br />
results (ie. inserts, updates, deletes, etc.).  Returns true or<br />
false as to whether the statement executed successfully, or on<br />
inserts with an auto-incrementing primary key, returns the last<br />
inserted value.<br />
";s:4:"code";b:0;s:6:"params";a:3:{s:6:"access";s:6:"public";s:5:"param";s:17:"mixed bind values";s:6:"return";s:7:"boolean";}s:4:"line";s:14:"db_execute () ";}s:9:"db_single";O:8:"stdClass":5:{s:4:"name";s:9:"db_single";s:7:"comment";s:176:"Similar to db_fetch(), but only returns a single result as an<br />
object, even if there are multiple results.  Returns false on<br />
error or if there are no results.<br />
";s:4:"code";b:0;s:6:"params";a:3:{s:6:"access";s:6:"public";s:5:"param";s:17:"mixed bind values";s:6:"return";s:6:"object";}s:4:"line";s:13:"db_single () ";}s:8:"db_shift";O:8:"stdClass":5:{s:4:"name";s:8:"db_shift";s:7:"comment";s:148:"Similar to db_shift(), but only returns the first column of<br />
the first result.  Returns false on error or if there are no<br />
results.<br />
";s:4:"code";b:0;s:6:"params";a:3:{s:6:"access";s:6:"public";s:5:"param";s:17:"mixed bind values";s:6:"return";s:5:"mixed";}s:4:"line";s:12:"db_shift () ";}s:7:"db_rows";O:8:"stdClass":5:{s:4:"name";s:7:"db_rows";s:7:"comment";s:63:"Returns the number of rows from the last executed query.<br />
";s:4:"code";b:0;s:6:"params";a:2:{s:6:"access";s:6:"public";s:6:"return";s:3:"int";}s:4:"line";s:11:"db_rows () ";}s:9:"db_lastid";O:8:"stdClass":5:{s:4:"name";s:9:"db_lastid";s:7:"comment";s:109:"Returns the last inserted value from an insert to a table with<br />
an auto-incrementing primary key.<br />
";s:4:"code";b:0;s:6:"params";a:2:{s:6:"access";s:6:"public";s:6:"return";s:3:"int";}s:4:"line";s:13:"db_lastid () ";}s:8:"db_error";O:8:"stdClass":5:{s:4:"name";s:8:"db_error";s:7:"comment";s:83:"Returns the error message, if an error occurred during the last<br />
query.<br />
";s:4:"code";b:0;s:6:"params";a:2:{s:6:"access";s:6:"public";s:6:"return";s:6:"string";}s:4:"line";s:12:"db_error () ";}s:10:"db_err_sql";O:8:"stdClass":5:{s:4:"name";s:10:"db_err_sql";s:7:"comment";s:93:"Returns the erroneous SQL statement, if an error occurred during<br />
the last query.<br />
";s:4:"code";b:0;s:6:"params";a:2:{s:6:"access";s:6:"public";s:6:"return";s:6:"string";}s:4:"line";s:14:"db_err_sql () ";}s:8:"db_quote";O:8:"stdClass":5:{s:4:"name";s:8:"db_quote";s:7:"comment";s:158:"Returns the specified string as a value quoted and ready for<br />
insertion into an SQL statement.  This is called automatically<br />
on bind values.<br />
";s:4:"code";b:0;s:6:"params";a:3:{s:6:"access";s:6:"public";s:5:"param";s:6:"string";s:6:"return";s:6:"string";}s:4:"line";s:19:"db_quote ($string) ";}s:18:"db_create_sequence";O:8:"stdClass":2:{s:4:"name";s:18:"db_create_sequence";s:4:"line";s:26:"db_create_sequence ($sqn) ";}s:16:"db_drop_sequence";O:8:"stdClass":2:{s:4:"name";s:16:"db_drop_sequence";s:4:"line";s:24:"db_drop_sequence ($sqn) ";}s:10:"db_next_id";O:8:"stdClass":2:{s:4:"name";s:10:"db_next_id";s:4:"line";s:18:"db_next_id ($sqn) ";}s:20:"db_get_sequence_name";O:8:"stdClass":2:{s:4:"name";s:20:"db_get_sequence_name";s:4:"line";s:28:"db_get_sequence_name ($sqn) ";}s:13:"db_fetch_mode";O:8:"stdClass":2:{s:4:"name";s:13:"db_fetch_mode";s:4:"line";s:30:"db_fetch_mode ($mode = false) ";}s:11:"db_pear_emu";O:8:"stdClass":2:{s:4:"name";s:11:"db_pear_emu";s:4:"line";s:27:"db_pear_emu ($bool = true) ";}s:12:"dropSequence";O:8:"stdClass":2:{s:4:"name";s:12:"dropSequence";s:4:"line";s:25:"dropSequence ($seq_name) ";}s:6:"nextId";O:8:"stdClass":2:{s:4:"name";s:6:"nextId";s:4:"line";s:19:"nextId ($seq_name) ";}s:7:"dbm_add";O:8:"stdClass":5:{s:4:"name";s:7:"dbm_add";s:7:"comment";s:50:"Add a new connection.  Alias of DBM::add().<br />
";s:4:"code";b:0;s:6:"params";a:3:{s:6:"access";s:6:"public";s:5:"param";s:7:"boolean";s:6:"return";s:7:"boolean";}s:4:"line";s:74:"dbm_add ($name, $connstr = '::', $user = '', $pass = '', $persistent = 0) ";}s:10:"dbm_remove";O:8:"stdClass":5:{s:4:"name";s:10:"dbm_remove";s:7:"comment";s:51:"Remove a connection.  Alias of DBM::remove()<br />
";s:4:"code";b:0;s:6:"params";a:3:{s:6:"access";s:6:"public";s:5:"param";s:7:"boolean";s:6:"return";s:7:"boolean";}s:4:"line";s:39:"dbm_remove ($name, $disconnect = true) ";}s:15:"dbm_set_current";O:8:"stdClass":5:{s:4:"name";s:15:"dbm_set_current";s:7:"comment";s:102:"Set the specified connection to be the currently active one.  Alias of<br />
DBM::setCurrent().<br />
";s:4:"code";b:0;s:6:"params";a:3:{s:6:"access";s:6:"public";s:5:"param";s:7:"boolean";s:6:"return";s:7:"boolean";}s:4:"line";s:48:"dbm_set_current ($name, $affectGlobalDB = true) ";}s:15:"dbm_get_current";O:8:"stdClass":5:{s:4:"name";s:15:"dbm_get_current";s:7:"comment";s:83:"Retrieves the currently active database object.  Alias of DBM::getCurrent().<br />
";s:4:"code";b:0;s:6:"params";a:2:{s:6:"access";s:6:"public";s:6:"return";s:6:"object";}s:4:"line";s:20:"&dbm_get_current () ";}}