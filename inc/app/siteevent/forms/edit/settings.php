; <?php /*

[Form]

error_mode		= all
extra			= "id=`cms-edit-form` onsubmit=`xed_copy_value (this, 'details')`"

[tab1]

type			= tab
title = Edit

[_collection]

type			= hidden

[_key]

type			= hidden

[_return]

type			= hidden

[title]

type			= text
alt				= Page Title
rule 0			= not empty, You must enter a title for your event.
extra			= "size=`40`"

[short_title]

type			= text
alt				= Short Title

[date]

type			= calendar
data_value		= SITEEVENT_TODAY
showTime        = false
displayFormat   = "date"

[until_date]

type			= calendar
alt				= End Date
showTime        = false
displayFormat   = "date"

[time]

type			= select
setValues		= "eval: formdata_get ('hours')"
extra			= "id=`time` style=`font-family: monospace; padding-top: 2px`"

[until_time]

type			= select
alt				= End Time
setValues		= "eval: formdata_get ('hours')"
extra			= "id=`until_time` style=`font-family: monospace; padding-top: 2px`"

[recurring]

type			= select
alt				= Repeat Event
setValues		= "eval: formdata_get ('recurring')"
extra			= "id=`recurring`"

[details]

type			= xed.Widget.Xeditor
rule 1			= not empty, You must enter some details for your event.

[tab2]

type			= tab
title = Properties

[header_properties]

type			= section
title			= Properties

[priority]

type			= select
alt				= Priority
setValues		= "eval: array ('normal' => 'Normal', 'high' => 'High')"
extra			= "id=`priority` style=`display: none`"

[category]

type			= selector
alt				= Category
table			= siteevent_category
key				= name
extra			= "id=`category` style=`display: none`"

[audience]

type			= selector
alt				= Audience
table			= siteevent_audience
key				= id
title			= name
multiple		= true
size			= 5
extra			= "id=`audience` style=`display: none`"

[public]

type			= select
setValues		= "eval: array ('yes' => intl_get ('The public is welcome at this event.'), 'no' => intl_get ('This event is not open to the public.'))"

[media]

type			= select
setValues		= "eval: array ('yes' => intl_get ('This event is open to the media.'), 'no' => intl_get ('This event is not open to the media.'))"

[header_contact]

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
alt				= Web Site
default_value	= "http://"

[sponsor]

type			= text
alt				= Sponsor

[rsvp]

type			= text
alt				= RSVP

[header_loc]

type			= section
title			= Location Information

[loc_name]

type			= text
alt				= Name

[loc_address]

type			= text
alt				= Address

[loc_city]

type			= text
alt				= City

[loc_province]

type			= text
alt				= "Province/State"

[loc_country]

type			= text
alt				= Country

[loc_map]

type			= text
alt				= Map Link
default_value	= "http://"

[tab3]

type			= tab
title = State

[sitellite_status]

type			= status
alt				= Status
setDefault		= draft
setValue		= draft
extra			= "id=`sitellite_status` style=`display: none` onfocus=`formhelp_show (this, 'The status determines what stage of its lifecycle that your document is in.  Only Approved pages can be viewed on the live site.  Queued pages are set to be approved on the specified Publish On date (below) by the Sitellite scheduler.')` onblur=`formhelp_hide ()`"

[sitellite_access]

type			= access
alt				= Access Level
setValue		= public
extra			= "id=`sitellite_access` style=`display: none` onfocus=`formhelp_show (this, 'The access level of a document determines who is allowed to view it.  This allows you to make portions of your site completely private, or only available to specific user roles (ie. members-only).')` onblur=`formhelp_hide ()`"

[sitellite_startdate]

type			= calendar
alt				= "Publish On (If Status is `Queued`)"
nullable		= true
showsTime		= true
displayFormat   = "datetime"

[sitellite_expirydate]

type			= calendar
alt				= "Archive On (If Status is `Approved`)"
nullable		= true
showsTime		= true
displayFormat   = "datetime"

[sitellite_owner]

type			= owner
alt				= Created By

[sitellite_team]

type			= team
alt				= Owned by Team

[changelog]

type			= textarea
alt				= Change Summary
rows			= 3
labelPosition	= left
extra			= "id=`changelog` style=`display: none` onfocus=`formhelp_show (this, 'The change summary helps give other site editors, including yourself, a more complete history of the changes that have been made to this document.')` onblur=`formhelp_hide ()`"
setValue		= Story added.

[tab-end]

type			= tab

[submit_button]

type			= msubmit
button 0		= Save
button 1		= Save and continue
button 2		= Cancel

; */ ?>
