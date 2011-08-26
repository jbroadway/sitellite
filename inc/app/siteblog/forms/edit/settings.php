; <?php /*

[Form]

name            = edit
description     = 
author          = You <you@yourWebSite.com>
copyright       = "Copyright (C) 2004, Me Inc."
license         = "http://www.opensource.org/licenses/index.php#PICK ONE!!!"
version         = 0.1
name            = siteblog-edit-form
extra           = "onsubmit=`xed_copy_value (this, 'body')`"

; your form definition goes here

[refer]

type   = hidden

[collection]

type	= hidden

[_collection]

type	= hidden

[_key]

type	= hidden 

[_return]

type = hidden

[subject]

type        = text
extra       = "style='width: 565px'"
alt			= Post Title

[twitter]

type		= hidden
alt			= Post to Twitter
extra		= "style='width: 565px' maxlength='125'"
append		= "<br />(note: only sends if status is `published`, automatically adds a link to the post using <a href='http://bit.ly/' target='_blank'>bit.ly</a>)"

[author]

type        = info
alt			= Author

[status]

type        = select 
alt			= Status

[category]

type        = select
alt         = Category

;[icategory]

;type        = info
;alt         = Category

;[oldcat]

;type        = hidden

[created]

type = calendar
alt = Posted On
setValue = "eval: date ('Y-m-d H:i:s')"
nullable		= false
showsTime		= true
displayFormat	= "datetime"

[body]

type        = xed.Widget.Xeditor

[submit_button]

type			= msubmit
button 0		= Save
button 1		= Save and continue
button 2		= Cancel

; */ ?>
