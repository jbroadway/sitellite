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
; - This file contains configuration information pertaining to our box.
;
; - Required options are 'name', 'version', 'path', 'description',
;   'author', and 'parameters'.  If there is more than one author,
;   separate their names with a comma and a space.  The parameters value
;   is a list of parameters that this box may accept.  Otherwise, go nuts.
;   These four options must fall under the [Meta] section.  We recommended
;   that you put box-specific options under a different section, such as
;   [Custom].
;

[Meta]

name			= Sidebar Randomizer
description		= Displays a random sidebar from the selected position.

[position]

type			= select
alt				= Position
;alt				= An artificial positioning of the sidebar, used to group sidebar items.
default_value	= left
setValues		= "eval: assocify (db_shift_array ('select id from sitellite_sidebar_position order by id asc'))"

;
; THE END
;
; DO NOT ALTER THIS LINE, IT IS HERE FOR SECURITY REASONS */ ?>