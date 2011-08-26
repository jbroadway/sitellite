[Form]

error_mode = all



[registration]

type = select
alt = "Subscriber registration"
setValues = "eval: array ('none' => 'None', 'email' => 'Email-only', 'full' => 'Collect all data', 'req' => 'All data required')"
setDefault = email

[verification]

type = select
alt = "Subscribe email verification"
setValues = "eval: array ('yes' => 'Yes', 'no' => 'No')"
setDefault = yes

[unsubscribe_email_verification]

type = select
alt = "Unsubscribe email verification"
setValues = "eval: array ('yes' => 'Yes', 'no' => 'No')"
setDefault = yes

[archive]

type = select
alt = "Public archive of messages"
setValues = "eval: array ('yes' => 'Yes', 'no' => 'No')"
setDefault = yes

[rss]

type = select
alt = "RSS subscribers"
setValues = "eval: array ('yes' => 'Yes', 'no' => 'No')"
setDefault = yes

[confirmation_email_domain]

type = text
alt = Confirmation Email Address
rule 1 = "not empty, Please specify a confirmation email domain."

[confirmation_from]
type = text
alt = The name to appear in a confirmation email
rule 1 = "not empty, Please specify a confirmation email name."

[confirmation_subject]
type = text
alt = The subject line of a confirmation email
rule 1 = "not empty, Please specify a confirmation email subject."

[mailer_domain]

type = text 
alt = The Domain SiteMailer2 Resides On

[bounced]

type = info
alt  = Bounced Message Detection Settings

[bounces]

type = select
alt = "Disable subscriber after bounces"
setValues = "eval: array (0 => 'Disabled', 1, 2, 3, 4, 5)"
setDefault = 2

[email_id]

type = text
alt  = Email ID For Bounced Messages

[email_password]
type = text
alt = Email Password For Bounced Messages

[email_server]
type = text
alt  = Server For Bounced Messages

[email_port]
type = text
alt = Port For Server For Bounced Messages



[submit_button]

type = msubmit
button 1 = Save
button 2 = "Cancel, onclick=`window.location.href = 'sitemailer2-app'; return false`"
