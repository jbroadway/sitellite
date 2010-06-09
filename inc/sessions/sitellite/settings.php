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
; Content requirements:
;
; - This file contains configuration information pertaining to our sessions.
;
; - Required sections are [Session], [Handler], [Source], and
;   [Store].  Required options are 'timeout', 'useclass', 'driver' (inside
;   the Handler, Source, and Store sections, and any options specific to
;   the chosen drivers (consult saf/docs for details).
;

[Session]

; Sometime session sources and handlers can set inactivity timeouts.
; This value specifies the timeout length in seconds.  Set it to 0 if
; you don't want sessions to time out on the server side, or to a
; large number (ie. 31536000 is 1 year) to not expire for a long time.
timeout			= 7200

; Although you're unlikely to ever need to, this allows you to specify
; an alternate directory for retrieving user privileges.
path			= inc/conf/auth

[Handler]

; Here are all of the values specific to the session handler.  The only
; required value is the driver, and all others are dependent on the
; specified driver.  See each driver's documentation for more information.

; This sets the session handler to use.  The session handler handles the
; interaction between the browser and the server.
driver			= Cookie

; The name to use for the session cookie.
cookiename 		= sitellite_session_id

; Sometime session sources and handlers can set inactivity timeouts.
; This value specifies the timeout length in seconds.  Set it to 0 if
; you don't want sessions to time out but to be erased when the user's
; browser is closed.  Set it to a large number (ie. 31536000 is 1 year)
; to not expire for a long time.
cookieexpires	= 7200

[Source 1]

driver			= MultiUser
database		= db
tablename		= sitellite_user
map username	= username
map password	= password
map sessionid	= session_id
map timeout		= expires
session_table	= sitellite_user_session

;[Source 2]
;
;driver			= LDAP
;host			= 127.0.0.1
;port			= 389
;rdn			= ""
;password		= ""
;dn				= ""
;map username	= uid
;map password	= userPassword
;map sessionid	= session_id
;map timeout	= expires
;set role		= member
;set team		= none

[Store]

; This sets the session store to use.  The session store handles the
; authentication of the user.
driver			= PHP
;driver = Memcache
;server = localhost
;port = 11211

;
; THE END
;
; DO NOT ALTER THIS LINE, IT IS HERE FOR SECURITY REASONS */ ?>
