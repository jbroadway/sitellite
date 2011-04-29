; <?php /*

[Form]

error_mode		= all
extra			= "id=`cms-edit-form` enctype=`multipart/form-data`"
uploadFiles		= false
;height			= 235
;height_easy		= 235

[tab1]

type			= tab
title			= Edit

[_collection]

type			= hidden

[_key]

type			= hidden

[_return]

type			= hidden

[file]

type			= file
alt				= File Upload
extra			= "size=`30` id=`file`"
web_path		= "/index/cms-filesystem-action?file="
;rule 1			= "func `sitellite_filesystem_rule_extension`, Your file name must have an extension."
;rule 2			= "func `sitellite_filesystem_rule_unique`, A file by this name already exists.  Please edit that file to upload a new copy."

[name]

type			= text
alt				= Rename File

[display_title]

type			= text
alt			= Display Name

[folder]

;type			= cms.Widget.Folder
type			= wffolderbrowser.Widget.Pagebrowser
alt				= Folder
basedir			= inc/data
extra			= "id=`folder`"

[tab2]

type			= tab
title			= Properties

[keywords]

type			= cms.Widget.Keywords
alt				= Keywords
extra			= "id=`keywords`"

[description]

type			= textarea
labelPosition	= left
rows			= 3
extra			= "id=`description`"

[tab3]

type			= tab
title			= State

[sitellite_status]

type			= status
alt				= Status
setDefault		= draft
setValue		= draft
extra			= "id=`sitellite_status` onfocus=`formhelp_show (this, 'The status determines what stage of its lifecycle that your document is in.  Only Approved pages can be viewed on the live site.  Queued pages are set to be approved on the specified Publish On date (below) by the scheduler.')` onblur=`formhelp_hide ()`"

[sitellite_access]

type			= access
alt				= Access Level
setValue		= public
extra			= "id=`sitellite_access` onfocus=`formhelp_show (this, 'The access level of a document determines who is allowed to view it.  This allows you to make portions of your site completely private, or only available to specific user roles (ie. members-only).')` onblur=`formhelp_hide ()`"

[sitellite_owner]

type			= owner
alt				= Created By
advanced		= yes

[sitellite_team]

type			= team
alt				= Owned by Team
extra			= "id=`sitellite_team`"
advanced		= yes

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
button 0		= Save
button 1		= Save and continue
button 2		= Cancel

; */ ?>
