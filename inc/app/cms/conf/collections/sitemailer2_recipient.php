; <?php /*

[Collection]

name		    = sitemailer2_recipient
display			= Mailing List Recipients
singular		= Mailing List Recipient
key_field		= id
key_field_name	= ID
title_field		= email
title_field_name= Recipient
is_versioned	= no
visible        = no

; These allow you to override the add and edit forms for your collection,
; which gives you control over all aspects of the behaviour of the form.

add			    = form:sitemailer2/subscriber/add
edit			= form:sitemailer2/subscriber/edit

; Additional fields to display

[Source]

name			= Database

[Store]

name			= Database

[browse:email]

header			= Email Address
width			= "13%"
length			= 30

[browse:firstname]

header			= First Name
width			= "13%"
length			= 20

[browse:lastname]

header			= Last Name
width			= "13%"
length			= 20

[browse:organization]

header			= Organization
width			= "13%"
filter_import	= sitemailer2.Filters
virtual			= sitemailer2_filter_org_link

[browse:newsletter]

header			= Newsletters
width			= "13%"
filter_import	= sitemailer2.Filters
virtual			= sitemailer2_filter_newsletters

[browse:status]

header			= Status
width			= "11%"
filter			= ucfirst

[browse:created]

header			= Created
width			= "11%"
filter_import	= sitemailer2.Filters
filter			= sitemailer2_filter_date

[facet:email]

display			= Text
type			= text
fields			= "email, firstname, lastname, organization, website"

[facet:newsletter]

display			= Newsletter
;type	    	= sitemailer2.Facet.Join
join_table  	= sitemailer2_recipient_in_newsletter
key1			= recipient
key2			= newsletter

type = join
pkey = id
join_table sitemailer2_recipient_in_newsletter
join_main_key = recipient
join_foreign_key = newsletter

values      	= "db_pairs ('select id, name from sitemailer2_newsletter order by name asc')"
count       	= off

[facet:status]

display			= Status
type			= select
values			= "array ('active' => 'Active', 'unverified' => 'Unverified', 'disabled' => 'Disabled')"
count			= off

; */ ?>