; <?php /*

[Form]

error_mode		= all
extra			= "id=`cms-edit-form` onsubmit=`xed_copy_value (this, 'cover')`"

[edit-top]

type			= template
template		= edit-top.spt

[_collection]

type			= hidden

[_key]

type			= hidden

[_return]

type			= hidden

[title]

type			= text
alt				= Presentation Title
rule 0			= not empty, You must enter a title for your presentation.
extra			= "size=`40`"

[cover_heading]

type			= section
title			= Cover Page

[cover]

type			= xed.Widget.Xeditor

[edit-middle]

type			= template
template		= ../../cms/html/page/edit-middle.spt

[theme]

type			= sitepresenter.Widget.Theme
alt				= Theme
extra			= "id=`theme` style=`display: none`"

[category]

type			= selector
alt				= Category
table			= sitepresenter_category
key				= name
extra			= "id=`category` style=`display: none`"

[keywords]

type			= cms.Widget.Keywords
alt				= Keywords
extra			= "id=`keywords` style=`display: none`  onfocus=`formhelp_show (this, 'Select the keywords from the list that describe the current page, or you can add or remove keywords using the Add and Remove buttons.  Keywords help target your page to its intended audience in search engines and site searches.')` onblur=`formhelp_hide ()`"

[description]

type			= textarea
alt				= Description
rows			= 3
labelPosition	= left
extra			= "id=`description` style=`overflow: hidden`  onfocus=`formhelp_show (this, 'A description helps target your page to its intended audience in search engines and site searches performed by visitors.')` onblur=`formhelp_hide ()`"

[edit-middle2]

type			= template
template		= ../../cms/html/page/edit-middle2.spt

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
displayFormat	= "datetime"
;format			= "%Y-%m-%d %H:%M:%S"
;displayFormat	= "%a, %e %b, %Y - %l:%M%P"

[sitellite_expirydate]

type			= calendar
alt				= "Archive On (If Status is `Approved`)"
nullable		= true
showsTime		= true
displayFormat	= "datetime"
;format			= "%Y-%m-%d %H:%M:%S"
;displayFormat	= "%a, %e %b, %Y - %l:%M%P"

[sitellite_owner]

type			= owner
alt				= Created By

[sitellite_team]

type			= team
alt				= Owned by Team
extra			= "id=`sitellite_team` style=`display: none`"

;[changelog]
;
;type			= textarea
;alt				= Change Summary
;rows			= 3
;labelPosition	= left
;extra			= "id=`changelog` style=`display: none` onfocus=`formhelp_show (this, 'The change summary helps give other site editors, including yourself, a more complete history of the changes that have been made to this document.')` onblur=`formhelp_hide ()`"
;setValue		= Story added.

[edit-middle3]

type			= template
template		= ../../cms/html/page/edit-middle3.spt

[submit_button]

type			= msubmit
button 0		= Save
button 1		= Save and continue
button 2		= Cancel

[edit-bottom]

type			= template
template		= ../../cms/html/page/edit-bottom.spt

; */ ?>