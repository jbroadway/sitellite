; <?php /*

[Form]

message			= "*Required fields"
error_mode		= all

[user_id]

type			= text
alt				= "*Username"
rule 1			= "unique `sitellite_user/username`, The username you have chosen is already in use."

[password]

type			= password
alt				= "*Password"
rule 1			= "not empty, You must enter a password."

[verify]

type			= password
alt				= "*Verify Password"
rule 1			= "equals `password`, Your password does not match."

[email]

type			= text
alt				= "*Email"
rule 1			= "not empty, You must enter an email address."

[firstname]

type			= text
alt				= "*First Name"
rule 1                  = "not empty, You must enter your first name."

[lastname]

type			= text
alt				= "*Last Name"
rule 1                  = "not empty, You must enter your last name."

[company]

type			= text

[website]

type			= text
default_value	= "http://"

[country]

type			= text

[province]

type			= text
alt				= State/Province

[public]

type			= checkbox
fieldset		= no
value 1			= Add me to public member list
default_value		= Add me to public member list

[about]

type			= textarea
alt				= "A little about yourself..."
rows			= 5

[sig]

type			= textarea
alt				= "Signature (for comments)"
rows			= 3

[submit_button]

type		= submit
setValues	= Register
extra		= "class='submit'"

; */ ?>
