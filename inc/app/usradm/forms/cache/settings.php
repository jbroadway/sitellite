; <?php /*

[Form]

error_mode = all
formhelp = on

[duration]

type = text
alt = Duration
formhelp = "Sets the page-level cache duration.  The value is specified in seconds.  0 or blank mean no page-level caching.  Note: Page-level caching should only be used on pages that can't ever contain user-specific data.  If this rule is not kept, then data specific to one user could end up in the hands of another.  This means that page-level caching is not always the right choice in all circumstances."
extra = "size='10'"

[location]

type = text
alt = Cache Location
formhelp = "The location to store the page-level cache.  It may be a directory name (ie. cache/pages), mod:proxy, which will generates the proper HTTP headers for a proxy/cache server to provide the caching, bdb:path/to/page_store.db, which uses the Berkeley DB software to store the cached pages, or memcache:server:port, which uses Memcached to store the cached pages.  Note: Berkeley DB stores require PHP's DBA extension and Memcached requires PHP's Memcache extension."
extra = "size='38'"

[proxystore]

type = text
alt = Proxy Tag Location
formhelp = "The location to store proxy cache 'tag' files, which are empty files that are created and updated on creation and re-creation of a mod:proxy cached page.  These help determine whether or not to regenerate the file or send the appropriate HTTP headers instead."
extra = "size='38'"

[querycaching]

type = text
alt = Query Cache Duration
formhelp = "Sets the per-query cache duration.  The value is specified in seconds.  0 or blank mean no per-query caching.  Per-query caching caches individual database queries, and must be specified on a per-query basis (see the API references for more info).  Note: Query caching also requires PHP\'s DBA extension."
extra = "size='10'"

[qcachelocation]

type = text
alt = Query Cache Location
formhelp = "The location to store the per-query cache.  It may be any valid directory name.  In this directory, a few Berkeley DB files will be created, one called query_store.db and one for each query being cached."
extra = "size='38'"

[menucaching]

type = text
alt = Menu Cache Duration
formhelp = "Sets the menu cache duration.  The value is specified in seconds.  0 or blank mean no menu caching.  Menu caching caches the global site navigation structure."

[cacheable]

type = textarea
alt = Cacheable URLs
formhelp = "A list of cacheable and non-cacheable URLs, one per line.  The URL is specified as the keyword and the values 'yes' and 'no' determine whether that URL is cacheable.  An asterisk (*) is a wildcard character.  Example: /index/company_* = yes"
labelPosition = left
rows = 20

[submit_button]

type = msubmit
button 1 = Save
button 2 = "Clear Cache, onclick=`if (confirm ('Are you sure you want to clear the cache?')) { window.location.href = 'usradm-cache-clear-action'; } return false`"
button 3 = "Cancel, onclick=`window.location.href = 'cms-cpanel-action'; return false`"

; */ ?>