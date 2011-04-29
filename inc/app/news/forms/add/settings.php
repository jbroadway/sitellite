; <?php /*

[Form]

error_mode		= all
extra			= "id=`cms-edit-form` onsubmit=`xed_copy_value (this, 'body')`"

[tab1]

type			= tab
title			= Edit

[collection]

type			= hidden

[_return]

type			= hidden

[title]

type			= text
alt				= Page Title
rule 0			= not empty, You must enter a title for your news story.
extra			= "size=`40`"

[date]

type			= calendar
data_value		= NEWS_TODAY

[summary]

type			= textarea
labelPosition	= left
rows			= 3
extra			= "id=`summary`"

[thumb]

type			= imagechooser
alt				= Summary Thumbnail
attrs			= no

[body]

type			= xed.Widget.Xeditor
rule 0			= not empty, You must enter content into your news story.

[tab2]

type			= tab
title			= Properties

[author]

type			= selector
alt				= Author
table			= sitellite_news_author
key				= name
extra			= "id=`author`"

[category]

type			= selector
alt				= Category
table			= sitellite_news_category
key				= name
extra			= "id=`category`"

[rank]

type			= text
alt				= Rank
setDefault		= "0"

[external]
type            = xed.Widget.Linker
alt             = "Forward to (URL)"
length          = 128
advanced        = true
files           = false
anchors         = false
email           = false

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
extra			= "id=`sitellite_team`"

[changelog]

type			= textarea
alt				= Change Summary
rows			= 3
labelPosition	= left
extra			= "id=`changelog` onfocus=`formhelp_show (this, 'The change summary helps give other site editors, including yourself, a more complete history of the changes that have been made to this document.')` onblur=`formhelp_hide ()`"
data_value		= Story added.

[tab-end]

type			= tab

[submit_button]

type			= msubmit
button 0		= Create
button 1		= Save and continue
button 2		= Cancel

; */ ?>
