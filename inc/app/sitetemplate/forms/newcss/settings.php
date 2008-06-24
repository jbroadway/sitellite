; <?php /*

[Form]

name            = edit
description     = Template Editor
author          = You <you@yourWebSite.com>
copyright       = "Copyright (C) 2004, Me Inc."
license         = "http://www.opensource.org/licenses/index.php#PICK ONE!!!"
version         = 0.1

; your form definition goes here

extra			= "id='sitetemplate-newtpl'"
error_mode		= all

[set_name]

type			= hidden

[path]

type			= hidden

[name]

type			= text
alt			= Style Name
rule 1			= not empty, You must enter a template name.
rule 2			= "func `sitetemplate_rule_name`, The template name you have chosen already exists for the specified output mode."

[body]

alt			= ""
type 			= textarea
extra			= "style='width: 700px; height: 450px; padding-left: 2px'"

[submit_buttons]

type			= template
template		= tpl_newcsssubmit.spt
submitButtons		= submit_button

; */ ?>
