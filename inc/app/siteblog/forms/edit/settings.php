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
extra       = "style='width: 575px'"
alt			= Post Title

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
format			= "%Y-%m-%d %H:%M:%S"
displayFormat	= "%a, %e %b, %Y - %l:%M%P"

[body]

type        = xed.Widget.Xeditor

[submit_buttons]

type        = msubmit
button 1    = Submit
button 2    = Cancel

; */ ?>
