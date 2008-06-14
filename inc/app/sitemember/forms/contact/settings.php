; <?php /*

[Form]

message			= Use this form to send an email to this user.
error_mode		= all

[user]

type			= hidden

[subject]

type			= text
rule 1			= not empty, You must enter a subject line.

[email]

type			= text
rule 1			= not empty, You must enter your email address.

[message]

type			= textarea
rule 1			= not empty, You must enter a message.

[submit_button]

type			= submit
setValues		= Send Message

; */ ?>