; <?php /*

[Form]

message			= Please use this form to submit events for possible inclusion in our calendar.
verify_sender	= yes
error_mode		= all

[title]

alt				= "Title (required)"
type			= text
rule 1			= not empty, You must enter a title.
extra			= "size='30'"

[date]

alt				= "Date (required)"
type			= date
rule 1			= not empty, You must select a date.

[end_date]

alt				= "End Date (for multi-day events)"
type			= date
addblank		= true

[time]

alt				= Time
type			= select
setValues		= "eval: formdata_get ('hours')"
default_value	= "19:00:00"

[end_time]

alt				= End Time
type			= select
setValues		= "eval: formdata_get ('hours')"
default_value	= "21:00:00"

[category]

type			= select
alt				= Category
setValues		= "eval: assocify (db_shift_array ('select * from siteevent_category'))"

[audience]

type			= checkbox 
alt				= Audience
setValues		= "eval: (array ('' => 'For Everyone') + db_pairs ('select * from siteevent_audience'))"
multiple = true

[public]

type			= radio
setValues		= "eval: array ('yes' => intl_get ('The public is welcome at this event.'), 'no' => intl_get ('This event is not open to the public.'))"
setValue = "yes"
passover_isset = no
type = hidden

[media]

type			= radio
setValues		= "eval: array ('yes' => intl_get ('This event is open to the media.'), 'no' => intl_get ('This event is not open to the media.'))"
setValue = "yes"
passover_isset = no
type = hidden

[details]

type			= textarea
alt				= "Details (required, no html)"
cols			= 45
rows			= 10
rule 1			= not empty, You must enter your event details.

[section2]

type			= section
title			= Contact Information

[contact]

type			= text
alt				= Contact Person

[contact_email]

type			= text
alt				= Email

[contact_phone]

type			= text
alt				= Phone

[contact_url]

type			= text
alt				= Website
default_value	= "http://"

[sponsor]

type			= text
alt				= Sponsor

[rsvp]

type			= text
alt				= RSVP

[section3]

type			= section
title			= Location Information

[loc_name]

type			= text
alt				= Location

[loc_address]

type			= text
alt				= Address

[loc_addr2]

type			= text
alt			= "Address (Line 2)"

[loc_city]

type			= text
alt				= City

[loc_province]

type			= text
alt				= Province

[loc_country]

type			= text
alt				= Country

[section4]

type			= section
title			= Security Test

[security_test]

type			= security
alt				= ""

[submitButton]

type			= submit
setValues		= Submit

; */ ?>