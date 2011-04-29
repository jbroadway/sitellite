; <?php /*

[Form]

message         = "*Required fields"
error_mode      = all

[email]

type            = text
alt             = "*Email"
rule 1          = "not empty, You must enter an email address."

[firstname]

type            = text
alt             = "*First Name"
rule 1          = "not empty, You must enter your first name."

[lastname]

type            = text
alt             = "*Last Name"
rule 1          = "not empty, You must enter your last name."

[company]

type            = text
alt             = Company

[website]

type            = text
alt             = Website
default_value   = "http://"

[country]

type            = text
alt             = Country

[province]

type            = text
alt             = State/Province

[profile]

type            = textarea
alt             = "A little about yourself..."
rows            = 5

[sig]

type            = textarea
alt             = "Signature (for comments)"
rows            = 3

[public]

type            = checkbox
fieldset        = no
value 1         = Add me to public member list

[submit_button]

type            = submit
setValues       = Save Preferences
extra           = "class='submit'"

; */ ?>
