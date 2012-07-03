; <?php /*

[Form]

error_mode		= all
extra			= "id=`cms-edit-form` onsubmit=`xed_copy_value (this, 'details')`"

[tab1]

type			= tab
title = Edit


[collection]

type			= hidden

[_return]

type			= hidden

[title]

type			= text
alt				= Title
rule 0			= not empty, You must enter a title for your link.
extra			= "size=`40`"

[url]
type			= text
alt				= Link URL
extra			= "size=`40`"

[user_id]
type			= text
alt				= Owner


[tab2]

type			= tab
title = Properties

[rank]
type			= text
extra			= "size=`5`"
default_value	= "0"

;[user_rating]

;type			= hidden

[category]

type			= selector
table			= sitelinks_category

[ctype]

type			= sitelinks.Widget.Linktype
alt				= Type

[ts]

type			= calendar
alt				= "Last Modified"
nullable		= yes
showsTime		= yes
displayFormat	= "datetime"
setValue		= DATETIME
rule 0			= "not empty, The field 'last modified' cannot be left blank."

[summary]
type			= textarea
labelPosition	= left

[tab3]

type			= tab
title = State

[sitellite_status]

type			= status
alt				= Status
setValue		= draft
extra			= "id=`sitellite_status` style=`display: none` onfocus=`formhelp_show (this, 'The status determines what stage of its lifecycle that your document is in.  Only Approved pages can be viewed on the live site.  Queued pages are set to be approved on the specified Publish On date (below) by the Sitellite scheduler.')` onblur=`formhelp_hide ()`"

[sitellite_access]

type			= access
alt				= Access Level
setValue		= public
extra			= "id=`sitellite_access` style=`display: none` onfocus=`formhelp_show (this, 'The access level of a document determines who is allowed to view it.  This allows you to make portions of your site completely private, or only available to specific user roles (ie. members-only).')` onblur=`formhelp_hide ()`"

[sitellite_owner]

type = owner
alt = Created By

[sitellite_team]

type = team
alt = Owned by Team


[tab-end]

type			= tab


[submit_button]

type			= msubmit
button 0		= Create
button 1		= Save and continue
button 2		= Cancel

; */ ?>
