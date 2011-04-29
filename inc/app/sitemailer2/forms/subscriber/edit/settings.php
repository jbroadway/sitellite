[Form]

error_mode = all

[_key]

type = hidden

[email]

type = info
alt = Email Address
;rule 1 = "not empty, You must enter an email address for your subscriber."
;rule 2 = "contains '@', Your email address does not appear to be valid."
;rule 3 = "unique 'sitemailer2_recipient.email', There is already an account for this email address."

[firstname]

type = text
alt = First Name

[lastname]

type = text
alt = Last Name

[organization]

type = text
alt = Organization

[website]

type = text
alt = Web Site
setDefault = "http://"

[status]

type = select
alt = Status
setValues = "eval: array ('active' => 'Active', 'disabled' => 'Disabled', 'unverified' => 'Unverified')"

[newsletters]

type = multiple
size = 5
alt = Newsletters
setValues = "eval: db_pairs ('select id, name from sitemailer2_newsletter order by name asc')"

[submit_button]

type = msubmit
button 1 = Save
button 2 = Cancel
