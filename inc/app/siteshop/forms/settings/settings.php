[Form]

error_mode = all

[general]

type = section
title = General

[page_title]

type = text
alt = Store Name
extra = "size=`30`"

[paypal_id]

type = text
alt = Paypal ID
extra = "size=`30`"

[order_notices]

type = text
alt = Email Order Notices
extra = "size=`30`"

[currency_code]

type = text
alt = Local Currency Code
extra = "size=`10`"

[shipping]

type = section
title = Shipping

[base]

type = text
alt = Base Shipping For All Items
extra = "size=`10`"

[max]

type = text
alt = "Max Shipping On Orders (Optional)"
extra = "size=`10`"

[free]

type = text
alt = "Free Shipping On Orders Over (Optional)"
extra = "size=`10`"

[taxes]

type = section
title = Taxes

[tax_input]

type = textarea
label_template = "{filter none}{display_value}{end filter}"
alt = "One per line, e.g.,<br /><pre style=`font-weight: normal`>GST 0.06<br />PST 0.07</pre>"
labelPosition = left
cols = 20
rows = 3

[display]

type = section
title = Display

[default_thumbnail]

type = text
alt = Default Thumbnail Image
extra = "size=`50`"

[default_style]

type = text
alt = Default CSS Stylesheet
extra = "size=`50`"

[page_alias]

type = text
alt = "Page Alias (Page ID)"

[below_page]

type = text
alt = "Below Page (Page ID)"

[page_template]

type = text
label_template = "{filter none}{display_value}{end filter}"
alt = "Page Template<br /><pre style=`font-weight: normal`>For html.default.tpl enter 'default'</pre>"

[checkout_template]

type = text
label_template = "{filter none}{display_value}{end filter}"
alt = "Checkout Template<br /><pre style=`font-weight: normal`>For html.default.tpl enter 'default'</pre>"

[checkout_handler]

type = text
alt = "Checkout Handler"

[alternate_index]

type = text
alt = "Alternate Index (Box)"

[alternate_product]

type = text
alt = "Alternate Product Page (Box)"

[alternate_category]

type = text
alt = "Alternate Product Category Page (Box)"

[customer_registration_return_email]

type = text
alt = "Customer Registration Return Email Address"

[product_comments]

type = select
alt = "Enable product comments"
setValues = "eval: array ('0' => intl_get ('No'), '1' => intl_get ('Yes'))"

[product_ratings]

type = select
alt = "Enable product ratings"
setValues = "eval: array ('0' => intl_get ('No'), '1' => intl_get ('Yes'))"

[submit_button]

type			= msubmit
button 0		= Save
button 1		= "Cancel, onclick=`history.go (-1); return false`"
