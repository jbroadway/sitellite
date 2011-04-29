; <?php /*

[Form]

message			= "*Required fields"
error_mode		= all

[user_id]

type			= text
alt				= "*Username"
rule 1			= "unique `sitellite_user/username`, The username you have chosen is already in use."
rule 2			= "not empty, You must enter a username."
rule 3 			= "regex `^[a-zA-Z0-9]+$`, The username you have chosen is incorrect; do not use spaces or special characters."

[password]

type			= password
alt				= "*Password"
rule 1			= "not empty, You must enter a password."
ignoreEmpty		= no

[verify]

type			= password
alt				= "*Verify Password"
rule 1			= "equals `password`, Your password does not match."
ignoreEmpty		= no

[email]

type			= text
alt				= "*Email"
rule 1			= "email, Your email address does not appear to be valid."
rule 2			= "not empty, You must enter your email address."

[firstname]

type			= text
alt				= "*First Name"
rule 1          = "not empty, You must enter your first name."

[lastname]

type			= text
alt				= "*Last Name"
rule 1          = "not empty, You must enter your last name."

[company]

type			= text
alt				= Company

[website]

type			= text
alt				= Website
default_value	= "http://"

[country]

type			= text
alt				= Country

[province]

type			= text
alt				= State/Province

[about]

type			= textarea
alt				= "A little about yourself..."
rows			= 5

[sig]

type			= textarea
alt				= "Signature (for comments)"
rows			= 3

[public]

type			= checkbox
fieldset		= no
value[]		= Add me to public member list
default_value   = 0

[submit_button]

type		= submit
setValues	= Register
extra		= "class='submit'"

; */ ?>
