; <?php /*

[Form]

name            = subscribe
description     = 
author          = You <you@yourWebSite.com>
copyright       = "Copyright (C) 2004, Me Inc."
license         = "http://www.opensource.org/licenses/index.php#PICK ONE!!!"
version         = 0.1

[Required]

alt = * Denotes a required field.
type = info
;setValue = "* Denotes a required field."

[email]

alt = Email Address*
type	= text
rule 1= "not empty, You must enter an email address."

[fname]

alt = First Name*
type  = text
rule 1= "not empty, You must enter a first name."

[lname]

alt = Last Name*
type  = text
rule 1= "not empty, You must enter a last name."

[organization]

alt = Your Organization*
type  = text
rule 1= "not empty, You must enter your organization name."

[website]

alt = Website URL*
type  = text
rule 1= "not empty, You must enter your website."

[newsletter]

type = hidden

[done]

type     = submit
setValues = Subscribe

; your form definition goes here

; */ ?>
