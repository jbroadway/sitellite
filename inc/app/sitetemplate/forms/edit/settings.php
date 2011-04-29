; <?php /*

[Form]

name            = edit
description     = Template Editor
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
template		= tpl_features.spt

[body]

alt				= ""
type 			= textarea
rule 1			= "func `sitetemplate_filter_template`, Your template contains XML syntax errors."
extra			= "style='width: 700px; height: 450px; padding-left: 2px'"

[submit_buttons]

type			= template
template		= tpl_submit.spt
submitButtons	= submit_button

; */ ?>
