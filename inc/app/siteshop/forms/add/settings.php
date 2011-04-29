[Form]

error_mode = all
extra = "onsubmit=`xed_copy_value (this, 'body')` enctype=`multipart/form-data`"
uploadFiles = false

[tab1]

type = tab
title = Edit

[sku]

type = text
alt = SKU

[name]

type = text
alt = Product Name
extra = "size=`60`"

[price]

type = text
alt = "Price ($)"

[category1]

type = select
alt = Category 1
setValues = "eval: Category::listAssoc ()"

[category2]

type = select
alt = Category 2
setValues = "eval: Category::listAssoc ()"

[category3]

type = select
alt = Category 3
setValues = "eval: Category::listAssoc ()"

[body]

type = xed.Widget.Xeditor

[tab2]

type = tab
title = Properties

[shipping]

type = text
alt = "Shipping ($)"
setDefault = "eval: appconf ('shipping_base')"

[availability]

type = select
alt = Availability
setValues = "eval: appconf ('availability')"

[quantity]

type = text
alt = "Quantity (-1 for unlimited)"
setDefault = "-1"

[weight]

type = select
alt = Sorting Weight
setValues = "eval: appconf ('weight')"

[taxable]

type = select
alt = Taxable
setValues = "eval: array ('yes' => 'Yes', 'no' => 'No')"

[keywords]

type = cms.Widget.Keywords
alt = Keywords

[description]

type = textarea
alt = Description
rows = 3
labelPosition = left

[tab3]

type = tab
title = Images

[image1]

type = file
alt = "Image 1 (.jpg)"

[image2]

type = file
alt = "Image 2 (.jpg)"

[image3]

type = file
alt = "Image 3 (.jpg)"

[image4]

type = file
alt = "Image 4 (.jpg)"

[image5]

type = file
alt = "Image 5 (.jpg)"

[image6]

type = file
alt = "Image 6 (.jpg)"

[tab4]

type = tab
title = State

[sitellite_status]

type			= status
alt				= Status
setValue		= draft
extra			= "id=`sitellite_status` onfocus=`formhelp_show (this, 'The status determines what stage of its lifecycle that your document is in.  Only Approved pages can be viewed on the live site.  Queued pages are set to be approved on the specified Publish On date (below) by the scheduler.')` onblur=`formhelp_hide ()`"

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
format			= "%Y-%m-%d %H:%M:%S"
displayFormat	= "%a, %e %b, %Y - %l:%M%P"

[sitellite_expirydate]

type			= calendar
alt				= "Archive On (If Status is `Approved`)"
nullable		= true
showsTime		= true
format			= "%Y-%m-%d %H:%M:%S"
displayFormat	= "%a, %e %b, %Y - %l:%M%P"

[sitellite_owner]

type			= owner
alt				= Created By
advanced		= yes

[sitellite_team]

type			= team
alt				= Owned by Team
extra			= "id=`sitellite_team`"
advanced		= yes

[tab-end]

type			= tab

[submit_button]

type			= msubmit
button 0		= Create
button 1		= "Cancel, onclick=`history.go (-1); return false`"
