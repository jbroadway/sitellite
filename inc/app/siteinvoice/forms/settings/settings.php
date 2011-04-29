; <?php /*

[Form]

message = All fields are required, except Extra Information.
error_mode = all

[company_name]

type = text
alt = Company Name
rule 1 = not empty, You must enter your company name.

[company_email]

type = text
alt = Billing Email Address
rule 1 = not empty, You must enter your billing email address.
extra = "size=`30`"

[company_email_name]

type = text
alt = Billing Email Name
rule 1 = not empty, You must enter your billing email name.
extra = "size=`30`"

[company_website]

type = text
alt = "Company Website (minus http://)"
rule 1 = not empty, You must enter your company website.

[company_phone]

type = text
alt = Company Phone Number
rule 1 = not empty, You must enter your company phone number.

[company_address]

type = text
alt = "Company Address (on a single line)"
rule 1 = not empty, You must enter your company address.
extra = "size=`50`"

[bcc_list]

type = text
alt = "BCC Email List (optional)"
extra = "size=`50`"

[extra_info]

type = textarea
alt = "Extra Information (appears below invoice details)"
cols = 40
rows = 3
labelPosition = left

[default_currency]

type = text
alt = "Default Currency (ie. `USD`)"
rule 1 = not empty, You must enter a default currency.
extra = "size=`7`"

[currencies]

type = siteinvoice.Widget.Currencies
alt = "Currencies (ie. `USD, CAD, EUR, GBP`)"
rule 1 = not empty, You must enter a list of currencies.
extra = "size=`30`"

[taxes]

type = siteinvoice.Widget.Assoc
alt = Taxes
lines = 3
keys_editable = yes
key_name = Name
value_name = Percent

[reminders]

type = siteinvoice.Widget.Assoc
alt = Reminder Email Subject Lines
lines = 3
keys_editable = no
key_name = "Overdue (Days)"
value_name = Subject

[paypal_id]

type = text
alt = Paypal ID

[submit_button]

type = submit
setValues = "Save"

; */ ?>