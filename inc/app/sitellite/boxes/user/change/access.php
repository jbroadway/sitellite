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
;   the contents of this file to anonymous visitors to your web site.
;
; Content requirements:
;
; - This file contains configuration information pertaining to our form.
;
; - There is a [Form] section that defines form object attributes,
;   such as name, action, method, message, and extra.  Each additional
;   section corresponds to the name of a widget (aka form field).  The
;   valid properties for a form widget are type, display_value,
;   setValues, extra, and rules.
;
;   setValues takes a list of comma-separated values, which each must
;   be single-quoted.  To use variables in setValues, simply refer to
;   them through the $GLOBALS array, and also wrap the entire
;   comma-separated list in double-quotes (ie.
;   "'item one', $GLOBALS['somevar']->someproperty, 'item three'".
;   In cases where a single value is being set, it should be written
;   as follows: 'value string', with just the single-quotes.
;
;   rules also takes a list of comma-separated values, but does not
;   wrap each value in single-quotes.  However, the entire list must
;   always be wrapped in double-quotes, and single-quotes must be
;   used where appropriate in the rule definitions (ie.
;   "contains '@', length '3+'"
;

sitellite_access = public
sitellite_status = approved
sitellite_action = on

;
; THE END
;
; DO NOT ALTER THIS LINE, IT IS HERE FOR SECURITY REASONS */ ?>
