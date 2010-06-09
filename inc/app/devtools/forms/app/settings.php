; <?php /*

[Form]

name            = app
description     = Create a new app
author          = Lux <john.luxford@gmail.com>
license         = "http://www.opensource.org/licenses/gpl-license.php"
version         = 0.1

; your form definition goes here

error_mode		= all

[appname]

type			= text
alt				= App Name
rule 1			= not empty, You must enter a name for your app.
rule 2			= "func `devtools_rule_app_unique`, Your app name is already taken."

[submit_button]

type			= msubmit
button 1		= Create
button 2		= Cancel

; */ ?>