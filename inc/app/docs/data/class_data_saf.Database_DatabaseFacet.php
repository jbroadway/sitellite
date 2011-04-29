a:9:{s:4:"name";s:13:"DatabaseFacet";s:7:"package";s:12:"saf.Database";s:4:"info";a:0:{}s:4:"vars";a:30:{s:11:"$connection";O:8:"stdClass":5:{s:4:"name";s:11:"$connection";s:7:"comment";s:49:"Contains the database connection resource.<br />
";s:4:"code";b:0;s:6:"params";a:1:{s:6:"access";s:6:"public";}s:4:"line";s:11:"$connection";}s:5:"$path";O:8:"stdClass":5:{s:4:"name";s:5:"$path";s:7:"comment";s:37:"Path to the Berkeley Database.<br />
";s:4:"code";b:0;s:6:"params";a:1:{s:6:"access";s:6:"public";}s:4:"line";s:5:"$path";}s:5:"$mode";O:8:"stdClass":2:{s:4:"name";s:5:"$mode";s:4:"line";s:29:"$mode = OCI_COMMIT_ON_SUCCESS";}s:8:"$handler";O:8:"stdClass":5:{s:4:"name";s:8:"$handler";s:7:"comment";s:63:"The database implementation used (db2, db3, gdbm, etc.).<br />
";s:4:"code";b:0;s:6:"params";a:1:{s:6:"access";s:6:"public";}s:4:"line";s:8:"$handler";}s:11:"$persistent";O:8:"stdClass":5:{s:4:"name";s:11:"$persistent";s:7:"comment";s:123:"A 1 or 0 (true or false, and true by default), specifying whether<br />
to establish a persistent connection or not.<br />
";s:4:"code";b:0;s:6:"params";a:1:{s:6:"access";s:6:"public";}s:4:"line";s:11:"$persistent";}s:13:"$transactions";O:8:"stdClass":5:{s:4:"name";s:13:"$transactions";s:7:"comment";s:91:"Boolean value denoting whether to enable transactions in the<br />
current database.<br />
";s:4:"code";b:0;s:6:"params";a:1:{s:6:"access";s:6:"public";}s:4:"line";s:17:"$transactions = 0";}s:7:"$driver";O:8:"stdClass":5:{s:4:"name";s:7:"$driver";s:7:"comment";s:59:"Contains the name of the database driver being used.<br />
";s:4:"code";b:0;s:6:"params";a:1:{s:6:"access";s:6:"public";}s:4:"line";s:7:"$driver";}s:5:"$host";O:8:"stdClass":5:{s:4:"name";s:5:"$host";s:7:"comment";s:46:"Contains the name of the database host.<br />
";s:4:"code";b:0;s:6:"params";a:1:{s:6:"access";s:6:"public";}s:4:"line";s:5:"$host";}s:5:"$name";O:8:"stdClass":5:{s:4:"name";s:5:"$name";s:7:"comment";s:52:"Contains the name of the database being used.<br />
";s:4:"code";b:0;s:6:"params";a:1:{s:6:"access";s:6:"public";}s:4:"line";s:5:"$name";}s:5:"$user";O:8:"stdClass":5:{s:4:"name";s:5:"$user";s:7:"comment";s:69:"Contains the username used to connect to the current database.<br />
";s:4:"code";b:0;s:6:"params";a:1:{s:6:"access";s:6:"public";}s:4:"line";s:5:"$user";}s:5:"$pass";O:8:"stdClass":5:{s:4:"name";s:5:"$pass";s:7:"comment";s:69:"Contains the password used to connect to the current database.<br />
";s:4:"code";b:0;s:6:"params";a:1:{s:6:"access";s:6:"public";}s:4:"line";s:5:"$pass";}s:4:"$dbd";O:8:"stdClass":5:{s:4:"name";s:4:"$dbd";s:7:"comment";s:43:"Contains the loaded database driver.<br />
";s:4:"code";b:0;s:6:"params";a:1:{s:6:"access";s:7:"private";}s:4:"line";s:4:"$dbd";}s:6:"$error";O:8:"stdClass":5:{s:4:"name";s:6:"$error";s:7:"comment";s:45:"Contains connection error information.<br />
";s:4:"code";b:0;s:6:"params";a:1:{s:6:"access";s:7:" public";}s:4:"line";s:14:"$error = false";}s:4:"$sql";O:8:"stdClass":5:{s:4:"name";s:4:"$sql";s:7:"comment";s:45:"Contains the SQL query to be executed.<br />
";s:4:"code";b:0;s:6:"params";a:1:{s:6:"access";s:6:"public";}s:4:"line";s:9:"$sql = ''";}s:5:"$rows";O:8:"stdClass":5:{s:4:"name";s:5:"$rows";s:7:"comment";s:73:"Contains the number of rows returned by the previous fetch() call.<br />
";s:4:"code";b:0;s:6:"params";a:1:{s:6:"access";s:6:"public";}s:4:"line";s:5:"$rows";}s:7:"$lastid";O:8:"stdClass":5:{s:4:"name";s:7:"$lastid";s:7:"comment";s:89:"Contains the lastid() of the last insert query sent to the execute()<br />
method.<br />
";s:4:"code";b:0;s:6:"params";a:1:{s:6:"access";s:6:"public";}s:4:"line";s:7:"$lastid";}s:7:"$tables";O:8:"stdClass":5:{s:4:"name";s:7:"$tables";s:7:"comment";s:70:"Contains a list of tables in the database.  Set by getTables().<br />
";s:4:"code";b:0;s:6:"params";a:1:{s:6:"access";s:6:"public";}s:4:"line";s:15:"$tables = false";}s:8:"$pearEmu";O:8:"stdClass":2:{s:4:"name";s:8:"$pearEmu";s:4:"line";s:16:"$pearEmu = false";}s:15:"$sequenceFormat";O:8:"stdClass":2:{s:4:"name";s:15:"$sequenceFormat";s:4:"line";s:26:"$sequenceFormat = '%s_seq'";}s:10:"$fetchMode";O:8:"stdClass":2:{s:4:"name";s:10:"$fetchMode";s:4:"line";s:32:"$fetchMode = DB_FETCHMODE_OBJECT";}s:7:"$result";O:8:"stdClass":5:{s:4:"name";s:7:"$result";s:7:"comment";s:64:"Contains the result identifier for the current execution.<br />
";s:4:"code";b:0;s:6:"params";a:1:{s:6:"access";s:6:"public";}s:4:"line";s:12:"$result = ''";}s:6:"$field";O:8:"stdClass":5:{s:4:"name";s:6:"$field";s:7:"comment";s:24:"Currently unused.<br />
";s:4:"code";b:0;s:6:"params";a:1:{s:6:"access";s:6:"public";}s:4:"line";s:11:"$field = ''";}s:20:"$_fetchModeFunctions";O:8:"stdClass":2:{s:4:"name";s:20:"$_fetchModeFunctions";s:4:"line";s:108:"$_fetchModeFunctions = array (
		DB_FETCHMODE_ASSOC		=> 'ocifetch',
		DB_FETCHMODE_OBJECT		=> 'ocifetch',
	)";}s:8:"$typemap";O:8:"stdClass":5:{s:4:"name";s:8:"$typemap";s:7:"comment";s:173:"Contains a key/value list of database types (regular<br />
expressions are used here to save repeating ourselves) and their<br />
corresponding MailForm widget types.<br />
";s:4:"code";b:0;s:6:"params";a:1:{s:6:"access";s:6:"public";}s:4:"line";s:273:"$typemap = array (
		'bpchar'					=> 'text',
		'char'						=> 'text',
		'varchar'					=> 'text',
		'text'						=> 'textarea',

		'date'						=> 'date',
		'time'						=> 'time',
		'timestamp'					=> 'datetime',
		'.*'						=> 'text',

		// arrays
		// booleans
		// blobs
	)";}s:7:"$column";O:8:"stdClass":5:{s:4:"name";s:7:"$column";s:7:"comment";s:32:"Column name of the facet.<br />
";s:4:"code";b:0;s:6:"params";a:1:{s:6:"access";s:6:"public";}s:4:"line";s:7:"$column";}s:6:"$title";O:8:"stdClass":5:{s:4:"name";s:6:"$title";s:7:"comment";s:33:"Dispaly name of the facet.<br />
";s:4:"code";b:0;s:6:"params";a:1:{s:6:"access";s:6:"public";}s:4:"line";s:6:"$title";}s:6:"$extra";O:8:"stdClass":5:{s:4:"name";s:6:"$extra";s:7:"comment";s:34:"Extra info about the facet.<br />
";s:4:"code";b:0;s:6:"params";a:1:{s:6:"access";s:6:"public";}s:4:"line";s:6:"$extra";}s:9:"$tableObj";O:8:"stdClass":5:{s:4:"name";s:9:"$tableObj";s:7:"comment";s:28:"DatabaseTable object.<br />
";s:4:"code";b:0;s:6:"params";a:1:{s:6:"access";s:6:"public";}s:4:"line";s:9:"$tableObj";}s:6:"$items";O:8:"stdClass":5:{s:4:"name";s:6:"$items";s:7:"comment";s:36:"List of options in the facet.<br />
";s:4:"code";b:0;s:6:"params";a:1:{s:6:"access";s:6:"public";}s:4:"line";s:17:"$items = array ()";}s:5:"$type";O:8:"stdClass":5:{s:4:"name";s:5:"$type";s:7:"comment";s:209:"Type of the facet.  Possible values are &#039;normal&#039;, &#039;self_ref&#039;, &#039;date&#039;, and &#039;time&#039;.<br />
This value is auto-determined by compile(), but can be customized as well.<br />
";s:4:"code";b:0;s:6:"params";a:1:{s:6:"access";s:6:"public";}s:4:"line";s:16:"$type = 'normal'";}}s:7:"methods";a:4:{s:13:"DatabaseFacet";O:8:"stdClass":5:{s:4:"name";s:13:"DatabaseFacet";s:7:"comment";s:26:"Constructor method.<br />
";s:4:"code";b:0;s:6:"params";a:2:{s:6:"access";s:6:"public";s:5:"param";s:6:"string";}s:4:"line";s:57:"DatabaseFacet (&$tableObj, $column, $title, $extra = '') ";}s:7:"compile";O:8:"stdClass":5:{s:4:"name";s:7:"compile";s:7:"comment";s:51:"Compile the facet options from the database.<br />
";s:4:"code";b:0;s:6:"params";a:1:{s:6:"access";s:6:"public";}s:4:"line";s:11:"compile () ";}s:7:"addItem";O:8:"stdClass":5:{s:4:"name";s:7:"addItem";s:7:"comment";s:62:"Add an item to the list.  Used internally by compile().<br />
";s:4:"code";b:0;s:6:"params";a:2:{s:6:"access";s:6:"public";s:5:"param";s:7:"integer";}s:4:"line";s:34:"addItem ($id, $title, $count = 0) ";}s:4:"show";O:8:"stdClass":5:{s:4:"name";s:4:"show";s:7:"comment";s:32:"Render the facet to HTML.<br />
";s:4:"code";b:0;s:6:"params";a:3:{s:6:"access";s:6:"public";s:5:"param";s:6:"string";s:6:"return";s:6:"string";}s:4:"line";s:22:"show ($linkUrl = '?') ";}}s:6:"params";a:1:{s:7:"package";s:8:"Database";}s:7:"extends";b:0;s:7:"comment";s:240:"A package for generating multi-faceted browsing interfaces for web sites.  A facet<br />
will look something like this:<br />
<br />
Browse by Section<br />
-----------------<br />
Local(16), Sports(24),<br />
World(21)<br />
<br />
<br />
";s:4:"code";s:1333:"<code><span style="color: #000000">
<br /><span style="color: #0000BB">&lt;?php<br /><br />loader_import&nbsp;</span><span style="color: #007700">(</span><span style="color: #DD0000">'saf.Database.Facet'</span><span style="color: #007700">);<br /><br /></span><span style="color: #0000BB">$facet&nbsp;</span><span style="color: #007700">=&nbsp;new&nbsp;</span><span style="color: #0000BB">DatabaseFacet&nbsp;</span><span style="color: #007700">(</span><span style="color: #0000BB">db_table&nbsp;</span><span style="color: #007700">(</span><span style="color: #DD0000">'products'</span><span style="color: #007700">,&nbsp;</span><span style="color: #DD0000">'id'</span><span style="color: #007700">),&nbsp;</span><span style="color: #DD0000">'category'</span><span style="color: #007700">,&nbsp;</span><span style="color: #DD0000">'Category'</span><span style="color: #007700">);<br /><br /></span><span style="color: #0000BB">$facet</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">compile&nbsp;</span><span style="color: #007700">();<br /><br />echo&nbsp;</span><span style="color: #0000BB">$facet</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">show&nbsp;</span><span style="color: #007700">();<br /><br /></span><span style="color: #0000BB">?&gt;<br /></span>
</span>
</code>";}