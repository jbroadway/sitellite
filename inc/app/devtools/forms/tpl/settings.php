; <?php /*

[Form]

name            = tpl
description     = Create a new template set
author          = Lux <john.luxford@gmail.com>
license         = "http://www.opensource.org/licenses/gpl-license.php"
version         = 0.1

; your form definition goes here

error_mode		= all

[setname]

type			= text
alt				= Set Name
rule 1			= not empty, You must enter a name for your template set.
rule 2			= "func `devtools_rule_tpl_unique`, Your template set name is already taken."

[submit_button]

type			= msubmit
button 1		= Create
button 2		= Cancel

; */ ?>