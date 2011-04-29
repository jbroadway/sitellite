[Meta]

name = Contact Form
description = Standard contact form

[email]

alt = "Send To (Email)"
type = text
rule 1 = "not empty, You must enter an email address to receive the contact form."

[save]

alt = Save to DB
type = select
setValues = "eval: assocify (array ('yes', 'no'))"
