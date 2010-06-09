; <?php /*

[Form]

name            = sitemailer2-msg-form
description     = Template Editor
author          = You <you@yourWebSite.com>
copyright       = "Copyright (C) 2004, Me Inc."
license         = "http://www.opensource.org/licenses/index.php#PICK ONE!!!"
version         = 0.1
error_mode		= all
extra           = "onsubmit=`xed_copy_value (this, 'body')`"

[collection]

type	= hidden

[_collection]

type	= hidden

[_key]

type	= hidden

[title]

type = info

[status]

type    = info

[recur]

type    = info

[complete]

type    = info

[from_name]

type    = info

[from_email]

type    = info
rule 1 = "not empty, You must specify a reply email address."

[template]

type		= info

;newsletter
[newsletter]

type        = info

[start]

type = info

[subject]

type       = info

[body]

type = textarea
; clean = off

[submit_button]

type     = msubmit
button 1 = Stop Sending
button 2 = Cancel

; */ ?>
