; <?php /*

[Form]

name            = edit
description     = Stylesheet Editor
author          = You <you@yourWebSite.com>
copyright       = "Copyright (C) 2004, Me Inc."
license         = "http://www.opensource.org/licenses/index.php#PICK ONE!!!"
version         = 0.1

; your form definition goes here

extra			= "id='sitetemplate-editor'"
error_mode		= all

[path]

type			= hidden

[edit_buttons]

type			= template
template		= tpl_cssfeatures.spt

[body]

alt				= ""
type 			= textarea
extra			= "style='width: 700px; height: 450px; padding-left: 2px'"

[submit_buttons]

type			= template
template		= tpl_csssubmit.spt
submitButtons	= submit_button

; */ ?>