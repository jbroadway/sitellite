; <?php /* DO NOT ALTER THIS LINE, IT IS HERE FOR SECURITY REASONS
;
; +----------------------------------------------------------------------+
; | Sitellite Content Management System                                  |
; +----------------------------------------------------------------------+
; | Copyright (c) 2010 Sitellite.org Community                           |
; +----------------------------------------------------------------------+
; | This software is released under the GNU GPL License.                 |
; | Please see the accompanying file docs/LICENSE for licensing details. |
; |                                                                      |
; | You should have received a copy of the GNU GPL License               |
; | along with this program; if not, visit www.sitellite.org.            |
; | The license text is also available at the following web site         |
; | address: <http://www.sitellite.org/index/license                     |
; +----------------------------------------------------------------------+
; | Authors: John Luxford <john.luxford@gmail.com>                       |
; +----------------------------------------------------------------------+
;
; Formatting rules of this document:
;
; - Lines that begin with a semi-colon (;) are comments and are not
;   processed.
;
; - Lines enclosed in square brackets ([]) denote new sections.
;
; - Lines with a keyword = value on them represent configuration options.
;
; - Option values that contain non-alphanumeric characters must be
;   surrounded by double-quotes (").  Escaping double-quotes inside one
;   another (ie. "<table border=\"0\"></table>") is unfortunately not
;   possible.
;
; - Do not remove or alter in any way the first and last lines of this
;   file.  They are in place for security reasons, and changing them will
;   compromise the security of your web site by potentially displaying
;   the contents of this file anonymous visitors to your web site.
;
; About this document:
;
; This document contains configuration information for the Sitellite
; Content Server.  Additional application-specific properties can be
; set in inc/conf/properties.php and inc/app/$app/conf/properties.php
;

[Cache]

; This sets the page-level cache duration.  The value is specified in
; seconds, and 0 means no page-level caching.  Note: Page-level caching
; should only be used on pages that can't ever contain user-specific
; data.  If this rule is not kept, then data specific to one user could
; end up in the hands of another.  This means that page-level caching
; is not always the right choice in all circumstances.
duration		= 3600

; This is the location to store the page-level cache.  It may be
; a directory name (ie. cache/pages), mod:proxy, which will not actually
; cache the pages but instead set the proper HTTP headers for a
; proxy/cache server or the visitors' browsers to cache the page for us,
; or bdb:path/to/page_store.db, which specifies the path to a Berkeley
; DB file which can contain the pages as key/value pairs in a single
; file and use a BDB daemon to retrieve them, or memcache:server:port,
; which uses a Memcached server. Memcached and BDB may prove faster than
; the filesystem at performing lookups because they can keep the data
; in memory between requests.  BDB stores require PHP to be configured
; with the DBA extension and linked against a Berkeley DB3 installation
; (ie. --with-db3).  For more information, see php.net/dba. Memcached
; stores require PHP's Memcache extension, and store the URI as the key
; and the HTML as the value.
location		= store:cache/pages:sitellite_cache_file_list

; This is the location to store proxy cache "tag" files, which are empty
; files that are created and updated on creation and re-creation of
; a mod:proxy cached page.  The modification times of these files are
; used to determine whether or not to regenerate the file or simply
; send the appropriate "still good" HTTP headers instead.  Specified
; as a directory (ie. cache/proxy).
proxystore		= cache/proxy

; This sets the per-query cache duration.  The value is specified in
; seconds, and 0 means no per-query caching.  Per-query caching caches
; individual database queries, and must be specified on a per-query
; basis (see saf.Database.Query in DocReader or saf/docs for more info).
querycaching	= 0

; This is the location to store the per-query cache.  It may be any
; valid directory name (ie. cache/queries).  In this directory, a few
; Berkeley DB files will be created, one called query_store.db and
; one for each query being cached.
qcachelocation	= cache/queries

; "This sets the duration of the menu cache.  The value is specified
; in seconds, and 0 means no menu caching.
menucaching		= 0

[Cacheable]

; This is a list of cacheable and non-cacheable URLs.  The URL is
; specified as the keyword and the values 'yes' and 'no' determine
; whether that URL is cacheable.
;
; An asterisk (*) is a wildcard character that basically says "anything
; goes".

; Enable caching for various pages
; / = yes
; /index = yes
; /index/index = yes
; /index/* = yes

; Disable caching on all dynamic content
; /index/*-action = no
; /index/*-form = no
; /index/*-app = no

; Enable caching for specific dynamic content
; /index/news-app = yes
; /index/news-*-action = yes

; Below are the apps that should not be cacheable at all.  We recommend that
; you do not alter this list, nor add any additional caching rules below it,
; or you may cause erroneous behaviour in Sitellite's user interface.
/index/appdoc-* = no
/index/boxchooser-* = no
/index/cms-* = no
/index/filechooser-* = no
/index/formchooser-* = no
/index/imagechooser-* = no
/index/myadm-* = no
/index/scheduler-* = no
/index/siteconnector-* = no
/index/sitemailer-* = no
/index/sitemailer2-* = no
/index/sitemember-* = no
/index/sitesearch-* = no
/index/sitetemplate-* = no
/index/sitetracker-* = no
/index/usradm-* = no
/index/xed-* = no

;
; THE END
;
; DO NOT ALTER THIS LINE, IT IS HERE FOR SECURITY REASONS */ ?>