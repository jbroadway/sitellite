[Form]

error_mode = all

[code]

type = text
alt = Promo Code

[discount_type]

type = select
alt = Discount Type
setValues = "eval: array ('percent' => 'Percent', 'dollars' => 'Dollars')"

[discount]

type = text
alt = Discount

[expires]

type = calendar
alt = "Expires (Last Day)"
format			= "%Y-%m-%d"
displayFormat	= "%a, %e %b, %Y"

[submit_button]

type			= msubmit
button 0		= Create
button 1		= "Cancel, onclick=`history.go (-1); return false`"
