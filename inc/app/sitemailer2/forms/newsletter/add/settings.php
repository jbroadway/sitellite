[Form]

[name]

type = text
alt = Newsletter Name

[from_name]

type = text
alt = From Name

[from_email]

type = text
alt = From Email
extra = "onfocus=`formhelp_show (this, 'Please Note: Changing this from the global default value will prevent the bounced message checker from checking this newsletter.')` onblur=`formhelp_hide ()`"

[template]

type = select

[subject]

type = text
alt = Subject

[public]

type = select
alt = Public
setValues = "eval: assocify (array ('yes', 'no'))"

[newsletters]

type = multiple
size = 5
alt = Import Subscribers From
setValues = "eval: db_pairs ('select id, name from sitemailer2_newsletter order by name asc')"

[submit_button]

type = msubmit
button 1 = Create
button 2 = Cancel
