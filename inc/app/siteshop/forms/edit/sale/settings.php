[Form]

error_mode = all

[id]

type = hidden

[name]

type = text
alt = Sale Name
extra = "size=`60`"

[start_date]

type = calendar
alt = "Sale Starts (First Day)"
format			= "%Y-%m-%d"
displayFormat	= "%a, %e %b, %Y"

[until_date]

type = calendar
alt = "Sale Ends (Last Day)"
format			= "%Y-%m-%d"
displayFormat	= "%a, %e %b, %Y"

[product_id]

type = siteshop.Widget.Products
alt = Products

[submit_button]

type			= msubmit
button 0		= Save
button 1		= "Cancel, onclick=`history.go (-1); return false`"
