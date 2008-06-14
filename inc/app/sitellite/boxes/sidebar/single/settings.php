; <?php /* DO NOT ALTER THIS LINE, IT IS HERE FOR SECURITY REASONS
;
; +----------------------------------------------------------------------+
; | Sitellite - Content Management System                                |
; +----------------------------------------------------------------------+
; | Copyright (c) 2001 Simian Systems                                    |
; +----------------------------------------------------------------------+
; | This software is released under the Simian Public License.           |
; | Please see the accompanying file LICENSE for licensing details!      |
; |                                                                      |
; | You should have received a copy of the Simian Public License         |
; | along with this program; if not, write to Simian Systems,            |
; | 101-314 Broadway, Winnipeg, MB, R3C 0S7, CANADA.  The Simian         |
; | Public License is also available at the following web site           |
; | address: <http://www.simian.ca/license.php>                          |
; +----------------------------------------------------------------------+
; | Authors: John Luxford <lux@simian.ca>                                |
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

name			= Sidebar Item
description		= Displays a single sidebar item as opposed to all items from a position.

[sidebar]

type			= select
alt				= Sidebar Item
;default_value	= left
setValues		= "eval: db_pairs ('select id, if(title != ``, concat(title, ` (`, position, `)`), concat(id, ` (`, position, `)`)) as t from sitellite_sidebar order by t asc')"

;
; THE END
;
; DO NOT ALTER THIS LINE, IT IS HERE FOR SECURITY REASONS */ ?>