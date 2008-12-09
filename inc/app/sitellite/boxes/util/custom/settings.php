[Meta]

name = Customized Form
description = Customizable user input form

[form_type]

alt = Type
type = selector
table = sitellite_form_type
key = id
title = name

[title]

alt = Subject
type = text
rule 1 = "not empty, You must enter a subject for your form"

[email]

alt = "Send To (Email)"
type = text
rule 1 = "not empty, You must enter an email address to receive the form"

[save]

alt = Save to DB
type = select
setValues = "eval: assocify (array ('yes', 'no'))"

[cc]

alt = CC User
type = select
setValues = "eval: assocify (array ('optional', 'yes', 'no'))"

[fields]

alt = Fields
type = checkbox
value 1 = "Account #"
value 2 = "Pass phrase"
value 3 = "Salutation"
value 4 = "First Name"
value 5 = "Last Name"
value 6 = "Email Address"
value 7 = "Birthday"
value 8 = "Gender"
value 9 = "Address (incl City/State/Country)"
value 10 = "Company"
value 11 = "Job Title"
value 12 = "Phone Number"
value 13 = "Daytime Phone"
value 14 = "Evening Phone"
value 15 = "Mobile Phone"
value 16 = "Fax Number"
value 17 = "Preferred method of contact"
value 18 = "Best time to reach you"
value 19 = "May we contact you"
value 20 = "Comments"
