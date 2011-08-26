; <?php /*

[Form]

name		= emailafriend
method		= post
title		= Email a Friend
message		= Use this form to email this page to a friend. The email will automatically include the link to the current page.
verify_session = On
verify_sender = On
error_mode = all

[url]

type		= hidden

[email]

type		= text
alt			= "Send to:"
rule 1		= not empty, You must enter an email address to send to.
rule 2		= email, The email address you entered appears to be invalid.

[yourEmail]

type		= text
alt			= "Your email:"
rule 1		= not empty, You must enter your email address.
rule 2		= email, The email address you entered appears to be invalid.

[msg]

type		= textarea
alt			= "Brief message (optional):"
rows		= 4
cols		= 30

[submitButton]

type		= submit
setValues	= 'Send'
extra		= "class='submit'"

; */ ?>