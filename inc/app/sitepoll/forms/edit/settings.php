; <?php /*

[Form]

error_mode		= all
extra			= "id=`cms-edit-form` onsubmit=`xed_copy_value (this, 'details')`"

;-----------------------------------------------------------------------------

[tab1]
type			= tab
title			= Edit

[_collection]

type			= hidden

[_key]

type			= hidden

[_return]

type			= hidden

[title]
type 			= text
alt 			= "Question"
rule 1 			= not empty, You must enter a question.
extra 			= "size='50'"

[option_1]
type			= text
extra 			= "size='45'"

[option_2]
type			= text
extra 			= "size='45'"

[option_3]
type			= text
extra			= "size='45'"

[option_4]
type			= text
extra 			= "size='45'"

[option_5]
type			= text
extra 			= "size='45'"

[option_6]
type			= text
extra 			= "size='45'"

[option_7]
type			= text
extra 			= "size='45'"

[option_8]
type			= text
extra 			= "size='45'"

[option_9]
type			= text
extra 			= "size='45'"

[option_11]
type			= text
extra 			= "size='45'"

[option_12]
type			= text
extra 			= "size='45'"


;----------------------------------------------------------------------------

[tab2]
type			= tab
title			= Properties	

[sections]
type 			= multiple
alt 			= "Display in Sections"
size 			= 5
display 		= "Section"
setValues		= "eval: array_merge (array ('all' => intl_get ('All')),	menu_get_sections ())"
extra			= "id='sections'"

[enable_comments]
type 			= select
setValues 		= "eval: array ('yes' => 'Yes', 'no' => 'No')"
default_value 	= "no"

[enable_voting]
type 			= select
setValues 		= "eval: array ('yes' => 'Yes', 'no' => 'No')"
default_value 	= "no"

[resultdate]
type			= calendar
alt				= "Resultdate"
nullable		= true
showsTime		= true
displayFormat	= "datetime"


;----------------------------------------------------------------------------

[tab3]
type			= tab
title			= State

[sitellite_status]

type			= status
alt				= Status
setValue		= draft
extra			= "id=`sitellite_status` onfocus=`formhelp_show (this, 'The status determines what stage of its lifecycle that your document is in.  Only Approved pages can be viewed on the live site.  Queued pages are set to be approved on the specified Publish On date (below) by the Sitellite scheduler.')` onblur=`formhelp_hide ()`"

[sitellite_access]

type			= access
alt				= Access Level
setValue		= public
extra			= "id=`sitellite_access` onfocus=`formhelp_show (this, 'The access level of a document determines who is allowed to view it.  This allows you to make portions of your site completely private, or only available to specific user roles (ie. members-only).')` onblur=`formhelp_hide ()`"

[sitellite_startdate]

type			= calendar
alt				= "Publish On (If Status is `Queued`)"
nullable		= yes
showsTime		= yes
displayFormat	= "datetime"

[sitellite_expirydate]

type			= calendar
alt				= "Archive On (If Status is `Approved`)"
nullable		= true
showsTime		= true
displayFormat	= "datetime"

[sitellite_owner]

type = owner
alt = Created By

[sitellite_team]

type = team
alt = Owned by Team

[changelog]

type			= textarea
alt				= Change Summary
rows			= 3
labelPosition	= left
extra			= "id=`changelog` onfocus=`formhelp_show (this, 'The change summary helps give other site editors, including yourself, a more complete history of the changes that have been made to this document.')` onblur=`formhelp_hide ()`"


[tab-end]
type			= tab

[submit_button]
type			= msubmit
button 0		= Create
button 1		= Save and continue
button 2		= Cancel

; */ ?>