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
alt				= Output Mode
append			= " examples: <em>html</em>, <em>pdf</em>"
rule 1			= not empty, You must enter an output mode.

[name]

type			= text
alt				= Template Name
append			= " examples: <em>products</em>, <em>homepage</em>, <em>memberarea</em>"
rule 1			= not empty, You must enter a template name.
rule 2			= "func `sitetemplate_rule_name`, The template name you have chosen already exists for the specified output mode."
rule 3			= "regex `^[-a-zA-Z0-9_.]+$`, Template names only can contain alphanumeric values and cannot contain spaces."

[edit_buttons]

type			= template
template		= tpl_newfeatures.spt

[body]

alt				= ""
type 			= textarea
rule 1			= "func `sitetemplate_filter_template`, Your template contains XML syntax errors."
extra			= "style='width: 700px; height: 450px; padding-left: 2px'"

[submit_buttons]

type			= template
template		= tpl_newsubmit.spt
submitButtons		= submit_button

; */ ?>
