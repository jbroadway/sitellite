[Form]

error_mode = all

[id]

type = hidden

[offer_number]

type = info
alt = Offer Number

[offer_text]

type = text
alt = Offer Text
extra = "size=`60`"

[product_id]

type = select
alt = Product
setValues = "eval: Product::selectAll ()"

[sale_price]

type = text
alt = "Sale Price ($)"

[submit_button]

type			= msubmit
button 0		= Save
button 1		= "Cancel, onclick=`history.go (-1); return false`"
