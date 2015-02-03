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

[Database]

; The name of the current database connection, used by the global $dbm
; object for managing multiple connections.  See saf.Database.Manager
; for more info.
connection_name	= mysqlweb

; The driver corresponds to which database software you are using.
; Currently, only MySQL is officially supported.  For a list of drivers,
; see the folder saf/lib/Database/Driver.
driver			= MySQL

; This is the name of the database host server.  If it is the same
; as the web server, often "localhost" is fine.
hostname		= localhost

; This is the name of your database on the database server.
database		= DBNAME

; The username you must supply to the database server in order to
; connect and send queries.
username		= USER

; The password you must supply to the database server in order to
; connect and send queries.
password		= PASS

; Specifies whether or not to use persistent connections when connecting
; to your database.  Must be specified as a 0 (false) or 1 (true).
persistent		= 0

[Site]

; The domain name of the site, used by the scheduler to refer to in
; command-line mode where no domain is used in the script request.
domain			= DOMAIN

; The full directory path to the web site document root directory.
; Note: This is the filesystem path and *not* the web-visible path.
; If set to empty, uses either the $_SERVER['DOCUMENT_ROOT']
; (if usedocroot is set to 1 below) or the dirname of the value
; of the __FILE__ constant otherwise, which may or may not
; be accurate, depending on your web server configuration.
docroot			= ""

; Determines whether to use the document root or the directory in which
; Sitellite is installed, which may be different places, as the document
; root for things like the Site Manager.  Set to 0 for "no" and 1 for
; "yes".
usedocroot		= 0

; Sets the path in the URL to Sitellite.  This should NOT include a
; trailing slash (ie. "/mydir" not "/mydir/") and also not include
; the "/index" or anything after.  For installations into the root
; of a web site, this can be empty.  Note: If usedocroot is not set
; to be true, this value and docroot above as well will be ignored
; in favour of auto-discovery.  The use of these is recommended when
; auto-discovery of your directory structure fails.
prefix			= ""

; If your site sits behind an Secure Socket Layer (SSL), then change this
; to 1.  If not, leave as is (0).  Sitellite will set the protocol to
; http or https, depending on this value.
secure			= 0

; Sets the path to the Sitellite Application Framework (SAF) from the
; docroot.  Note: No preceeding or trailing slashes.  Default is 'saf'.
safpath			= saf

; Remove /index/ from URLs -- requires Apache's mod_rewrite module to
; be installed.  Please note that this feature is very new and considered
; to be beta quality.
remove_index	= 1

; Put new pages under the current page when adding pages in the Web View.
; By default, new pages are added to the root of the site unless otherwise
; specified.
new_pages_under_current = 0

[Server]

; This is the name of the default handler, when no specific request is
; made (ie. a request for yourWebSite.com/ or /index).
default_handler			= index

; This is the name of the default handler type, when no specific request is
; made (ie. a request for yourWebSite.com/ or /index).  The type may be one
; of 'document', 'box', or 'form'.
default_handler_type	= document

; This is the default template set (ie. theme) used to render pages.  By using
; this setting, you can add your own template themes, install multiple themes
; in the same Sitellite installation, by adding instead of changing what's
; already there.
;default_template_set	= "test"
default_template_set	= "default"

; This is the default template used to render pages.  The default value
; is "default", which says to use html.default.tpl as your default.  If your
; template sets are properly named, you likely do not need to modify this
; setting.
default_template		= "default"

; This is the name of the error handler, when an invalid request is made.
error_handler			= sitellite/error

; This is the name of the error handler type, when an invalid request is
; made.  The type may be one of 'document', 'box', or 'form'.
error_handler_type		= box

; This determines whether SCS will send an X-Powered-By HTTP header with
; the response.  The default is 'on', which shouldn't be a problem, but
; some may prefer to turn it off as one extra security-through-obscurity
; measure.
send_version_header		= on

; This sets the default app to look in for boxes, forms, settings, etc.
; The default 'webapp' is where you can place your custom code for a
; single web site.  However, if you are looking to create reusable
; apps, it is best to create a new app directory structure and not
; use the default in that case.
default_app				= webapp

; Setting this to 'off' removes comments added by the html_marker(),
; which is used by XT to display the positioning of boxes and forms
; within a rendered page.  html_marker() can also be used inside your
; custom application code to produce markers to help you find problems
; more easily when viewing the outputted source in web browsers.
debug					= off

; This setting enables gzip encoding of pages sent to visitors should
; their browsers and the server support it.  Most browsers do.  On the
; server it requires the zlib PHP extension.
compress_output			= On

; Set this to the error reporting level that you wish to run your
; site at.  You may wish to increase this during development and/or
; decrease it on live web sites.  For more info, see:
; http://www.php.net/error_reporting
error_reporting			= "E_ALL ^ E_NOTICE"

; This allows Web Files downloads to be served directly by the web
; server, which is much more efficient than reading and passing on
; file data through PHP. The default 'readfile' uses PHP's readfile()
; function. Other options are 'lighttpd' which is compatible with
; Lighttpd and Apache's mod_xsendfile, and 'nginx' which is compatible
; with the nginx's XSendFile implementation.
xsendfile				= readfile

[I18n]

; Directory where language files live.
directory		= inc/lang

; Method of negotiating which language to use for each visitor.  See
; saf.I18n::negotiate() for more info on this.
negotiate		= http

; The method to use to serialize language strings.
serialize		= plain

[Emailing]

;This sets the Mail Transfer Agent to use when sending messages.
;Possible values are:
; mail - Uses PHP's built-in mail() function.
; smtp - Uses SMTP to send messages.
; sendmail - Uses the Sendmail program to send messages.
; qmail - Uses the Qmail program to send messages.
; Default is "mail".
mta             = mail

;
template        = default.spt

;
from_name       = Your Company Name

;
from_email      = test@yourwebsite.com

;
use_html        = On

;
use_nomail      = Off

;
overrule_from   = On

[Messaging]

; The Personal Workspace and the Workflow components of the Sitellite CMS
; have the ability to forward messages and system notifications to your
; email, Jabber instant messaging account, or cell phone (via SMS text
; messaging), and to receive messages from these sources also (if the
; task scheduler is configured to do so).  The following information
; is required in order to make use of these features.

; This is the email address to use in the 'From' field of outgoing
; emails and text messages.  This is also the address for message
; recipients to reply to when sending responsed to system or
; forwarded messages.
return_address	= test@sitellite.org

; The email server to check for incoming email.  This must be a POP3
; mail server.  Ordinarily, simply setting up a dedicated email account
; for Sitellite on your existing mail server is sufficient to enable
; this functionality.
email_server	= mail.sitellite.org

; The port of the POP3 mail server.  The default is usually correct.
email_port		= 110

; The username to access the email account designated to Sitellite.
email_username	= test@sitellite.org

; The password to access the email account designated to Sitellite.
email_password	= test

; Specifies whether to enable Jabber message forwarding and receiving.
; Set this to "on" to enable, and "off" to disable.
jabber			= on

; The jabber server to check for incoming instant messages.  This must
; be a Jabber server.
jabber_server	= 192.168.1.102

; The port of the Jabber server.  The default is usually correct.
jabber_port		= 5222

; The username to access the Jabber account designated to Sitellite.
jabber_username	= test

; The password to access the Jabber account designated to Sitellite.
jabber_password	= test

[Services]

; reCaptcha.net public key, used by the MailForm security widget.
recaptcha_public_key	= "6LdNfQYAAAAAAPS0z28jdEeiesWwZtNIxr3dobs4"

; reCaptcha.net private key, used by the MailForm security widget.
recaptcha_private_key	= "6LdNfQYAAAAAAMi6jcEWAUkDLWlmWt-Mbmj9ZiTN"

;
; THE END
;
; DO NOT ALTER THIS LINE, IT IS HERE FOR SECURITY REASONS */ ?>
