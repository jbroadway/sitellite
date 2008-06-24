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

[set_name]

type			= hidden

[path]

type			= hidden

[output_mode]

type			= text
alt			= Output Mode
rule 1			= not empty, You must enter an output mode.

[name]

type			= text
alt			= Template Name
rule 1			= not empty, You must enter a template name.
rule 2			= "func `sitetemplate_rule_name`, The template name you have chosen already exists for the specified output mode."

[edit_buttons]

type			= template
template		= tpl_newfeatures.spt

[body]

alt			= ""
type 			= textarea
rule 1			= "func `sitetemplate_filter_template`, Your template contains XML syntax errors."
extra			= "style='width: 700px; height: 450px; padding-left: 2px'"

[submit_buttons]

type			= template
template		= tpl_newsubmit.spt
submitButtons		= submit_button

; */ ?>
