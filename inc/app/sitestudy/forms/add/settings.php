; <?php /*

[Form]

error_mode		= all
extra			= "id=`cms-edit-form` onsubmit=`xed_copy_value (this, 'solution')`"

[edit-top]

type			= template
template		= edit-top.spt

[collection]

type			= hidden

[_return]

type			= hidden

[client]

type			= text
alt				= Client
rule 0			= not empty, You must enter a client or project name for your case study.
extra			= "size=`40`"

[problem]

type			= textarea
alt				= Problem
labelPosition	= left
rule 1			= not empty, You must enter a problem for your case study.
extra			= "id=`problem`"
cols			= 50

[solution_header]

type			= section
title			= Solution

[solution]

type			= xed.Widget.Xeditor
rule 1			= not empty, You must enter a solution for your case study.

[edit-middle]

type			= template
template		= ../../cms/html/page/edit-middle.spt

[sort_weight]

type			= text
alt				Sorting Weight

[keywords]

type			= cms.Widget.Keywords
alt				= Keywords
extra			= "id=`keywords`"

[description]

type			= textarea
alt				= Description
extra			= "id=`description`"
labelPosition	= left
cols			= 50

[edit-middle2]

type			= template
template		= ../../cms/html/page/edit-middle2.spt

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

[changelog]

type			= textarea
alt				= Change Summary
rows			= 3
labelPosition	= left
extra			= "id=`changelog` style=`display: none` onfocus=`formhelp_show (this, 'The change summary helps give other site editors, including yourself, a more complete history of the changes that have been made to this document.')` onblur=`formhelp_hide ()`"
data_value		= Case study added.

[edit-middle3]

type			= template
template		= ../../cms/html/page/edit-middle3.spt

[submit_button]

type			= msubmit
button 0		= Create
button 1		= Save and continue
button 2		= Cancel

[edit-bottom]

type			= template
template		= ../../cms/html/page/edit-bottom.spt

; */ ?>