; <?php /*

[Collection]

name = sitellite_form_submission
display = Form Submissions
singular = Submission

key_field = id
key_field_name	= ID

title_field		= id
title_field_name= ID

body_field = comments

add = no
edit = box:sitellite/util/custom/edit
translate = no

is_versioned = no

order_by = ts
sorting_order = desc

[Source]

name			= Database

[Store]

name			= Blank

[link:send_email]

requires = rw
text = Send Email
url = /index/sitellite-send-form

[link:export]

requires = r
text = Export Contacts
url = /index/sitellite-export-form

[link:sitemailer2]

requires = r
requires resource = app_sitemailer2
text = "Add to Newsletter"
url = "/index/sitemailer2-import-submissions-form"

[browse:id]

header = ID
width = "10%"
align = center

[browse:ts]

header = "Submitted On"
width = "18%"

[browse:form_type]

header = Form Type
width = "15%"
filter_import = sitellite.CustomForm
filter = sitellite_custom_form_type

[browse:first_name]

header = First Name
width = "15%"

[browse:last_name]

header = Last Name
width = "18%"

[browse:company]

header = Company
width = "18%"

[facet:first_name]

display = Text
type = text
fields = "first_name, last_name, email, city, state, country, company, job_title, comments"

[facet:form_type]

display = Form Type
type = select
values = "db_pairs ('select id, name from sitellite_form_type order by name asc')"

[facet:state]

display = State
type = select
values = "db_shift_array (`select distinct state from sitellite_form_submission where state != '' order by state asc`)"

; */ ?>