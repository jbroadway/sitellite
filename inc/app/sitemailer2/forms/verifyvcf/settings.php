[Form]

action		= sitemailer2-verifyvcf-form
message		= Please verify the import data.
method		= post

[email]

type        = text
rule 1      = "not empty, You must enter an email address for your subscriber."
rule 2		= "contains '@', Your email address does not appear to be valid."
rule 3		= "unique 'sitemailer2_recipient.email', There is already an account for this email address."

[firstname]

type		= text
alt			= First Name

[lastname]

type		= text
alt			= Last Name

[organization]

type		= text

[website]

type		= text
alt			= Web Site
default_value = "http://"

[group]

type        = hidden

[submit]

type		= msubmit
button 1	= "Import"
button 2	= "Cancel, onclick=`window.location.href = 'sitemailer-app'; return false`"
