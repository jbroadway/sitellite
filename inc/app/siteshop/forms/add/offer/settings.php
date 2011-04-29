[Form]

error_mode = all

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
button 0		= Create
button 1		= "Cancel, onclick=`history.go (-1); return false`"
